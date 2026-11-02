# 06 — Bibliotecas e ferramentas

> Versões citadas conforme pesquisa de novembro/2026. Antes de adotar cada pacote: checar licença, data do último release, issues abertas e compatibilidade com Laravel 13 / Filament 5.

## Base

| Pacote | Uso | Observação |
|---|---|---|
| `laravel/framework` 13 | Framework | |
| `livewire/livewire` 4 | Componentes reativos | |
| `filament/filament` 5 | Painel admin, tabelas, formulários, tenancy, notificações | Requer Livewire 4 + Tailwind 4 |
| `laravel/horizon` | Monitoramento de filas Redis | |
| `laravel/reverb` | WebSockets (Kanban em tempo real) | |
| `laravel/pulse` | Saúde da aplicação | |
| `laravel/scout` + Meilisearch | Busca | |
| `laravel/sanctum` | API tokens (API pública futura) | |
| `pestphp/pest`, `larastan/larastan`, `laravel/pint`, `rector/rector` | Qualidade | |

## Integrações / conectores

| Pacote | Uso | Observação |
|---|---|---|
| **`saloonphp/saloon`** (+ `saloonphp/laravel-plugin`, `saloonphp/rate-limit-plugin`) | Base de todos os conectores HTTP: OAuth2, rate limit, retries, mocks em testes | Recomendado como padrão para conectores sem SDK bom |
| `jlevers/selling-partner-api` | Amazon SP-API completo | v7.x, PHP ≥ 8.2, suporta Laravel 11–13 |
| `highsidelabs/laravel-spapi` | Wrapper Laravel do anterior, modo multi-seller | Verificar compatibilidade com a versão atual do core |
| `justbetter/laravel-magento-client` | Magento 2 REST | Sucessor do `grayloon/laravel-magento-api` (descontinuado) |
| `magazord-plataforma/marketplace-sdk` | SDK oficial do marketplace Magazord | Para a API da loja, usar Saloon com a documentação oficial |
| Mercado Livre | SDK oficial PHP é antigo; implementar com Saloon | API REST simples, OAuth padrão |
| Shopee | Implementar com Saloon (assinatura HMAC-SHA256) | SDKs comunitários em PHP pouco mantidos |
| SHEIN, Magalu, TikTok Shop | Implementar com Saloon | Sem SDK PHP oficial conhecido |

## Kanban, agenda e recorrências

| Pacote | Uso | Observação |
|---|---|---|
| **`relaticle/flowforge`** | Kanban para Filament/Livewire | v4.x (mai/2026), Filament 5, PHP ≥ 8.3, paginação por cursor; funciona também fora do Filament |
| `sgvcode/filament-kanban` | Alternativa (fork mantido do mokhosh) | Combina com `spatie/eloquent-sortable` |
| SortableJS (já vem no Filament) | Arrastar e soltar personalizado | Se optarmos por Kanban próprio |
| **`guava/calendar`** | Calendário no Filament (usa vkurko/calendar, alternativa livre ao FullCalendar) | Plugin 3.x ↔ Filament 5 |
| `rlanvin/php-rrule` | Regras de recorrência (RRULE, padrão iCal) | Base das tarefas recorrentes |
| `spatie/icalendar-generator` | Exportar agenda em iCal | |

> Recomendação: usar Flowforge como ponto de partida. Se o Kanban precisar de comportamento muito específico (WIP, painel lateral rico, tempo real), construir componente Livewire próprio com SortableJS — o modelo de dados é nosso de qualquer forma.

## Utilitários

| Pacote | Uso |
|---|---|
| `spatie/laravel-permission` | Papéis e permissões |
| `spatie/laravel-activitylog` | Histórico de alterações |
| `spatie/laravel-medialibrary` | Imagens de produto, conversões por canal |
| `intervention/image` v3 / `spatie/image` | Redimensionar, fundo branco, formato quadrado |
| `spatie/laravel-data` | DTOs tipados entre catálogo e conectores |
| `spatie/laravel-settings` | Configurações por tenant |
| `spatie/laravel-backup` | Backups |
| `openspout/openspout` ou `maatwebsite/excel` | Importação/exportação de planilhas grandes |
| `spatie/laravel-webhook-client` | Recebimento e validação de webhooks dos MPs |
| `stancl/tenancy` | Só se precisar de banco por cliente no futuro |
| `laravel/cashier` (Stripe) ou SDK de gateway nacional (Asaas, Iugu, Pagar.me) | Cobrança do SaaS |
| `prism-php/prism` ou SDK oficial do provedor de IA | Geração de títulos/descrições por canal (F2) |

## Comprar x construir

Construir conector próprio para **todos** os marketplaces é o item mais caro e mais arriscado do projeto. Alternativas que existem no mercado brasileiro:

| Ferramenta | O que é | Como poderia ser usada |
|---|---|---|
| **Base.com** (ex-BaseLinker) | Hub com centenas de integrações (Shopee BR, SHEIN, Kwai, Americanas, Magalu, Casas Bahia…) e API | Conector "via hub" para MPs de cauda longa |
| **ANYMARKET** | Hub de marketplaces brasileiro, integra com ERPs | Idem |
| **Plugg.to** | Hub brasileiro com API | Idem |
| **Bling / Tiny (Olist)** | ERPs com módulo multiloja, NF-e, API v3 (Bling) | Fiscal/NF-e: integrar em vez de construir |
| **UpSeller** | ERP com integrações recentes (TikTok Shop, Kwai, SHEIN) | Referência de concorrente |

**Recomendação:**

1. **Construir nativo** os conectores de maior volume e maior valor: Magazord, Mercado Livre, Shopee (depois Amazon e Magalu). É onde está o diferencial e onde a API é confiável.
2. **Não construir NF-e/etiquetas** no início: integrar com o ERP que o cliente já usa (ou recomendar Bling/Tiny).
3. Para MPs sem API pública, avaliar **conector via hub** — o custo mensal do hub tende a ser menor que manter um conector próprio sem documentação.
4. Estudar concorrentes (UpSeller, Base.com, ANYMARKET, Bling) antes de precificar o SaaS: a proposta de valor tem que ser **gestão da equipe + auditoria inteligente + foco em moda**, não "mais um hub".
