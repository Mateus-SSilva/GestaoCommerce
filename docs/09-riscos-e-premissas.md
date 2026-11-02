# 09 — Riscos, premissas e o que não prometer

## O que **podemos** prometer

- Kanban, tarefas recorrentes, agenda e painel do gestor — sem dependência de terceiros.
- Catálogo central com variações e importação da Magazord/planilha.
- Publicação, estoque, preço e auditoria via API em **Mercado Livre, Shopee e Amazon**, **após aprovação dos apps** por esses marketplaces.
- Integração com a **Magazord**, após liberação de credenciais pela Magazord.
- Arquitetura modular: novos canais entram como conectores sem reescrever o sistema.

## O que **não** devemos prometer

| Não prometer | Por quê |
|---|---|
| "Robô que entra nos painéis dos MPs" / scraping com login | Viola termos de uso, pode bloquear a conta do lojista, quebra a cada mudança de tela. Auditoria será via API oficial. |
| Integração com MPs sem API pública confirmada (Casas Bahia, Kwai, Dafiti, Netshoes) | Sem documentação confirmada; depende de acordo com o MP ou de hub pago. |
| Prazo de aprovação dos apps (Shopee, Amazon, SHEIN, TikTok, Magalu) | Decisão dos marketplaces. |
| Anúncio aprovado em 100% dos casos | Cada MP tem moderação própria. O sistema valida antes de enviar e mostra o motivo da recusa, mas não controla a moderação. |
| Paridade total de recursos entre canais | Cada MP expõe coisas diferentes (ex.: pedidos SHEIN Fulfillment com limitação de API relatada). |
| Estoque "em tempo real" absoluto | Haverá atraso de segundos a minutos por rate limit e fila. Mitigação: estoque de segurança por canal. |
| Que integrações nunca vão quebrar | MPs mudam APIs (ex.: User Products no ML, migração para Open API no Magalu). Precisa de manutenção contínua — prever no contrato. |
| Emissão de NF-e/etiquetas na primeira versão | Fica no ERP; integração fiscal é projeto à parte. |

## Riscos principais

| Risco | Impacto | Mitigação |
|---|---|---|
| App não aprovado ou aprovado com escopo limitado num MP | Canal fora do produto | Fase 0 começa as solicitações cedo; alternativa via hub |
| Mudança de API de um MP | Sincronização para | Conectores isolados, testes com mocks, monitoramento de erros, contrato de manutenção |
| Rate limit em catálogos grandes | Auditoria/sync lentos | Filas por canal, sincronização incremental, webhooks, pedido de aumento de cota |
| Divergência de estoque entre sistemas | Venda sem estoque, penalização | Definir fonte da verdade única; estoque de segurança; auditoria diária |
| Recriar anúncios existentes por engano | Perda de histórico/reputação | Fluxo de **vínculo** por SKU antes de qualquer publicação |
| Escopo grande demais | Atraso | Entregas por fase, com valor a cada fase |
| Requisitos de segurança/LGPD dos MPs para integradores | Reprovação do app de terceiros | Criptografia de tokens, mínimo de dados pessoais, políticas documentadas |
| Concorrência com hubs/ERPs consolidados | Dificuldade de vender o SaaS | Diferencial: equipe + auditoria + nicho moda |

## Premissas

- O cliente fornece acesso às contas dos MPs (como administrador) e ao suporte da Magazord.
- O cliente tem CNPJ e contas de vendedor **empresa/profissional** nos MPs (exigido por Shopee e Amazon para acesso à API).
- Dados de produtos de amostra são fornecidos na Fase 0.
- Custos de terceiros (hospedagem, hub, IA, gateway de pagamento) são repassados ou orçados à parte.
