# 07 — Roadmap

> Ordem definida com o cliente: **(1) sistema interno de gestão 100% → (2) agente de monitoramento dos marketplaces mais fáceis, somente leitura, que cria tarefas → (3) cadastro único**.
>
> Racional: entregar valor cedo sem depender de terceiros; começar pelos marketplaces com API madura e permissões simples; usar permissões **somente leitura** antes de escrever nos canais; e usar os dados reais do monitoramento para priorizar o que o cadastro único precisa validar.
>
> Prazos não estimados ainda: dependem do tamanho da equipe e das respostas em [08-duvidas.md](08-duvidas.md).

## Fase 0 — Descoberta (em paralelo ao início da Fase 1)

- Levantamento com o cliente ([08-duvidas.md](08-duvidas.md)), com foco em: processos da equipe, tarefas recorrentes reais e dores de cada marketplace.
- Abrir contas de desenvolvedor dos canais da Fase 2 (**uso próprio**, não integrador ainda): Mercado Livre (self-service), Shopee (app *Seller In-House System*, aprovação ~48 h), credenciais da API Magazord (via suporte).
- Se o cliente vender neles: Amazon (desenvolvedor **privado**) e Magalu (Open API).
- **Entregável:** escopo da Fase 1 fechado e acessos da Fase 2 encaminhados.

## Fase 1 — Sistema interno de gestão (meta: 100%)

- Fundação: Laravel 13 + Filament 5 + Livewire 4, CI, ambientes, deploy, backups.
- Usuários, papéis, permissões, 2FA, log de atividade.
- **Kanban:** quadros, colunas com limite WIP, cartões (responsáveis, prioridade, prazo, etiquetas, checklist, anexos, comentários com @menção), filtros, visão lista, tempo real, modelos de cartão, backlog priorizado.
- **Tarefas recorrentes** (RRULE): modelo de cartão, quadro/coluna, responsável, prazo relativo, pausar/retomar, histórico.
- **Agenda** baseada nos prazos + eventos manuais.
- **Painel do gestor:** feito / fazendo / a fazer por pessoa, atrasos, carga.
- Notificações (no app + e-mail).
- **Preparação para o agente:** cartões já nascem com campo de "origem" (manual, recorrência, agente) e vínculo genérico a entidades externas.
- **Critério de aceite:** a equipe usa o sistema no dia a dia por pelo menos 2–4 semanas; tarefas semanais geradas automaticamente; gestor acompanha tudo pelo painel.

## Fase 2 — Agente de monitoramento (somente leitura)

### Canais, na ordem de facilidade

| Ordem | Canal | Por quê |
|---|---|---|
| 1 | **Mercado Livre** | API pública madura, app criado pelo próprio desenvolvedor, OAuth simples |
| 2 | **Shopee** | Open Platform v2, app de uso próprio com liberação ~48 h, status de itens via API |
| 3 | **Magazord** | Loja principal; serve como **referência** de SKU/estoque/preço |
| 4 | Amazon (se usar) | SP-API madura; desenvolvedor privado para uso próprio |
| 5 | Magalu (se usar) | Open API com OAuth |
| depois | SHEIN, TikTok Shop, Americanas | Exigem revisão/validação — ver [02](02-pesquisa-marketplaces.md) |

### Base de referência ("catálogo espelho")

- Importação **somente leitura** dos SKUs da Magazord (ou ERP): SKU, EAN, estoque, preço.
- Vínculo **anúncio do MP ↔ SKU** (automático por SKU/EAN, com tela para resolver o que não casar).
- Esse vínculo é reaproveitado pelo cadastro único na Fase 3.

### O que o agente verifica

| Verificação | Precisa da referência? |
|---|---|
| Anúncio pausado / inativo / banido / moderado | Não |
| Anúncio sem estoque no canal | Não |
| Aviso de catálogo obrigatório (ML), problemas de listing (Amazon) | Não |
| Atributo obrigatório faltando / qualidade baixa | Não |
| Estoque divergente da referência | Sim |
| Preço divergente da regra/referência | Sim |
| SKU da referência sem anúncio no canal (e vice-versa) | Sim |

### Como o agente cria tarefas

- **Detecção por regras determinísticas** (confiáveis, auditáveis, baratas). IA opcional apenas para **resumir o problema e sugerir a correção** no texto do cartão.
- **Deduplicação:** 1 cartão por problema (chave = canal + anúncio + tipo). Se o problema persistir, o cartão é atualizado, não duplicado.
- **Fechamento automático:** quando o problema some na verificação seguinte, o cartão vai para "Validar" (ou é concluído, conforme configuração).
- **Agrupamento:** muitos problemas do mesmo tipo viram um cartão com lista (evita inundar o quadro na primeira execução).
- **Roteamento:** regra configurável define responsável, quadro, coluna, prioridade e prazo por canal × tipo de problema.
- Execução diária agendada + "verificar agora"; incremental por webhooks quando o canal oferecer.
- Resumo diário por e-mail/notificação.

**Limitação assumida:** nesta fase o sistema **detecta e organiza** a correção, mas não altera nada nos canais. (Opcional: antecipar apenas o envio de estoque para ML/Shopee se a venda sem estoque for uma dor crítica.)

**Critério de aceite:** a conferência manual diária dos canais da fase é substituída pelo agente; problemas viram tarefas com responsável sem intervenção.

## Fase 3 — Cadastro único

- Catálogo central completo (variações cor × tamanho, mídias, tabela de medidas, atributos de moda), partindo do catálogo espelho.
- Permissões de **escrita** nos canais já conectados.
- Mapeamento de categorias/atributos, formulário dinâmico e checklist pré-publicação — **priorizando as validações que o agente mais encontrou na Fase 2**.
- Publicação multicanal, regras de preço por canal, sincronização de estoque.
- Suporte ao modelo **User Products** do Mercado Livre.
- Fonte da verdade do estoque migra (se decidido) da Magazord/ERP para o sistema.

## Fase 4 — Expansão de canais

SHEIN, TikTok Shop, Americanas e demais (nativo ou via hub), cada um entrando primeiro no **monitoramento** e depois no **cadastro**. Pedidos, margem real por canal, imagens por canal, IA para títulos/descrições.

## Fase 5 — Produto SaaS + Magento

- Multi-empresa, onboarding, planos e cobrança.
- Registro dos apps como **integrador/terceiro** nos marketplaces (necessário para atender outros lojistas).
- LGPD, termos de uso, política de privacidade.
- Conector Magento 2.

## Trabalhos contínuos

- Acompanhar changelogs das APIs dos marketplaces.
- Testes por conector com respostas gravadas (mocks).
- Monitoramento de filas e erros.
