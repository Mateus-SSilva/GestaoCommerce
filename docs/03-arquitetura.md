# 03 — Arquitetura e stack

## Stack proposta

| Camada | Escolha | Motivo |
|---|---|---|
| Linguagem | **PHP 8.4** | Suportado por Laravel 13; tipagem moderna, enums, readonly, property hooks |
| Framework | **Laravel 13** | Filas, agendador, eventos, notificações, broadcasting nativos |
| UI reativa | **Livewire 4** + Alpine.js | Pedido do projeto; SPA-like sem API separada |
| Painel administrativo | **Filament 5** (sobre Livewire 4 + Tailwind 4) | CRUDs, tabelas, filtros, formulários, multi-tenant e permissões prontos — economiza meses |
| CSS | **Tailwind CSS 4** | Pedido do projeto; padrão do Filament 5 |
| Banco | **PostgreSQL 17** (ou MySQL 8.4) | JSONB para atributos variáveis por marketplace; índices parciais |
| Filas / cache | **Redis** + **Laravel Horizon** | Milhares de jobs de sincronização com monitoramento |
| Tempo real | **Laravel Reverb** | Kanban colaborativo e notificações ao vivo |
| Busca | Laravel Scout + **Meilisearch** | Busca rápida no catálogo e nos cartões |
| Arquivos | S3 compatível (AWS S3, Cloudflare R2, MinIO) | Imagens de produto e anexos |
| Testes / qualidade | Pest, Larastan, Pint, Rector | Qualidade contínua |
| Infra | Laravel Cloud ou Forge + VPS; Docker (Sail) em dev | Deploy simples, escalável depois |

> Requisitos confirmados: Filament 5 exige Livewire 4, Tailwind 4 e PHP ≥ 8.2; projetos com Laravel 13 usam PHP ≥ 8.3. Confirmar as versões exatas no início do desenvolvimento.

## Visão de módulos

```
┌──────────────────────────────────────────────────────────────────────┐
│                         GestãoCommerce (Laravel)                     │
├───────────────┬────────────────┬──────────────────┬──────────────────┤
│  Catálogo     │  Canais        │  Auditoria       │  Equipe          │
│  (PIM)        │  (Conectores)  │  (Health check)  │  (Kanban/Agenda) │
│ - Produtos    │ - Contas/OAuth │ - Rotina diária  │ - Quadros        │
│ - Variações   │ - Mapeamentos  │ - Regras         │ - Cartões        │
│ - Mídias      │ - Publicações  │ - Problemas      │ - Recorrências   │
│ - Estoque     │ - Sync estoque │ - Relatórios     │ - Agenda         │
│ - Preço/regra │ - Pedidos      │ - Alertas        │ - Painel gestor  │
├───────────────┴────────────────┴──────────────────┴──────────────────┤
│  Núcleo: Tenancy · Usuários/Permissões · Auditoria de ações · Filas  │
└──────────────────────────────────────────────────────────────────────┘
          │                │                 │
     Magazord API    ML · Shopee · Amazon · Magalu · SHEIN · TikTok …
```

## Padrão de conectores (o coração do projeto)

Cada marketplace é um **módulo plugável** que implementa o mesmo contrato. Assim, adicionar um novo canal não mexe no resto do sistema, e cada cliente ativa só os conectores que usa.

```php
interface ChannelConnector
{
    public function capabilities(): ChannelCapabilities;   // o que este canal suporta
    public function authorizeUrl(Account $a): string;      // início do OAuth
    public function handleCallback(Request $r): Account;   // salva tokens
    public function refreshToken(Account $a): void;

    public function categories(?string $parent = null): Collection;
    public function categoryRequirements(string $categoryId): AttributeSchema;

    public function validate(Product $p, ChannelListing $l): ValidationResult; // antes de enviar
    public function publish(ChannelListing $l): PublishResult;
    public function update(ChannelListing $l, array $fields): PublishResult;
    public function pushStock(ChannelListing $l): void;
    public function pushPrice(ChannelListing $l): void;

    public function fetchListingStatus(ChannelListing $l): ListingSnapshot; // auditoria
    public function fetchOrders(Account $a, CarbonInterface $since): Collection;
    public function handleWebhook(Request $r): void;
}
```

- **`capabilities()`** permite à interface esconder o que o canal não suporta (ex.: "este canal não permite editar título após publicado").
- **`categoryRequirements()`** devolve os atributos obrigatórios da categoria, consultados na API do MP e cacheados. Isso alimenta o formulário dinâmico e o "checklist do que falta".
- HTTP de cada conector feito com **Saloon** (rate limit, retries, autenticação e testes com mocks por conector).
- Conectores podem morar em pacotes internos (`packages/connector-mercadolivre`) para versionar e testar isolado.

### Integração via hub (opcional)

Um conector pode apontar para um **hub** (Base.com, ANYMARKET, Plugg.to, Bling) em vez do MP diretamente. Útil para MPs sem API pública (Casas Bahia, Kwai, Dafiti, Netshoes) — o hub cobra por isso, mas evita meses de desenvolvimento.

## Modelo de dados (resumo)

```
tenants ─┬─ users (roles/permissions)
         ├─ products ── product_variants (cor, tamanho, SKU, EAN, estoque, preço base)
         │      └─ media (imagens, ordem, por cor)
         ├─ attribute_definitions / product_attribute_values (atributos internos)
         ├─ channel_accounts (canal, loja, tokens criptografados, status)
         ├─ channel_category_mappings (nossa categoria → categoria do MP)
         ├─ channel_attribute_mappings (nosso atributo → atributo do MP, por categoria)
         ├─ channel_listings (produto/variação × conta: id externo, status, último sync, payload)
         ├─ price_rules (por canal: markup, arredondamento, preço fixo)
         ├─ stock_movements (entrada, saída, reserva, ajuste — histórico completo)
         ├─ orders / order_items (importados dos canais)
         ├─ audit_runs ── audit_issues (tipo, severidade, anúncio, sugestão, tarefa vinculada)
         ├─ boards ── columns ── cards (responsável, prioridade, prazo, labels, checklist)
         │                         └─ comments, attachments, activity
         ├─ recurring_tasks (regra RRULE, modelo do cartão, responsável)
         └─ calendar_events (derivados dos cartões + manuais)
```

Atributos específicos de cada MP ficam em **JSONB** em `channel_listings.overrides`, permitindo exceção por canal (ex.: título diferente para a Shopee) sem poluir o produto.

## Filas e sincronização

- Filas separadas por canal (`sync-mercadolivre`, `sync-shopee`…) com **rate limiter por conta** (`RateLimited` middleware + Redis). Um canal lento ou fora do ar não trava os outros.
- **Estoque é prioridade máxima:** fila `stock-high` com debounce (várias alterações em segundos viram um único envio).
- **Webhooks** dos MPs (pedidos, alterações) entram por rota dedicada, são validados e enfileirados.
- Retentativa com backoff exponencial + jitter; após N falhas o problema vira `audit_issue` e, opcionalmente, um cartão no Kanban.
- Horizon para monitorar; alertas quando a fila acumula.

## Auditoria diária ("crawler")

Rotina agendada (ex.: 05:00) por tenant e por conta:

1. Lista todos os anúncios do canal via API (paginado, respeitando rate limit).
2. Compara com o catálogo central: **existe? está ativo? estoque bate? preço bate com a regra? foi moderado/banido? tem aviso de catálogo?**
3. Gera `audit_issues` com severidade (crítico / alto / médio / informativo) e **sugestão de correção**.
4. Regras configuráveis: "criar cartão no quadro *Marketplaces* para problemas críticos, atribuído a X".
5. Envia resumo (e-mail / notificação no app; WhatsApp/Slack como opcional).

Também roda sob demanda ("auditar agora") e de forma incremental via webhooks quando o MP oferece.

## Multi-tenant (SaaS)

- **Banco único com `tenant_id`** usando o tenancy nativo do Filament (painel por equipe). Mais simples de operar e suficiente para dezenas/centenas de clientes.
- Alternativa futura: `stancl/tenancy` com banco por cliente, se surgirem exigências de isolamento.
- Credenciais dos MPs criptografadas (`encrypted` cast + chave por ambiente). Logs sem dados pessoais de compradores (**LGPD**).
- Planos e cobrança na Fase 5 (Laravel Cashier com Stripe, ou gateway nacional como Asaas/Iugu/Pagar.me — decidir).

## Integração futura com Magento

Recomendação: **o GestãoCommerce é o "cérebro" (catálogo, estoque, regras, equipe) e o Magento é mais um canal de venda**, como a Magazord. Assim:

- Migrar da Magazord para Magento (ou usar os dois) vira só ativar/desativar conectores.
- O Magento continua cuidando de vitrine, checkout, SEO e pagamentos — que é onde ele é forte.
- Conector via REST/GraphQL do Magento, com pacote `justbetter/laravel-magento-client`, incluindo MSI (multi-source inventory).
- Evitar "fundir" os dois códigos: manter sistemas separados, integrados por API, reduz acoplamento e risco.

## Segurança

- 2FA para usuários; permissões por papel (gestor, operador, visualizador) e por quadro.
- Log de atividade (quem alterou preço/estoque/anúncio e quando).
- Backups diários com restauração testada.
- Segredos fora do repositório; rotação de tokens.
