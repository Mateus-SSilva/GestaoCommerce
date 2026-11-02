# 07 — Roadmap

> Prazos não estão estimados ainda: dependem do tamanho da equipe e das respostas em [08-duvidas.md](08-duvidas.md). As fases são ordenadas para **entregar valor cedo** e **reduzir risco** — o que depende de terceiros (aprovação de apps nos MPs) começa em paralelo, logo na Fase 0.

## Fase 0 — Descoberta e validação de acessos

**Objetivo:** confirmar o que é possível antes de investir em desenvolvimento.

- Reunião de levantamento com o cliente (responder [08-duvidas.md](08-duvidas.md)).
- Mapear o processo atual de cadastro e conferência (quanto tempo leva, quem faz, erros mais comuns).
- Criar contas de desenvolvedor e **solicitar aprovação dos apps**: Mercado Livre, Shopee (Open Platform), Amazon SP-API, Magalu, SHEIN, TikTok Shop.
- Solicitar credenciais da API à Magazord.
- Pedir documentação ao time de integração de Americanas, Casas Bahia, Dafiti, Netshoes, Kwai (o que o cliente usar).
- Exportar amostra real do catálogo (10–20 produtos com variações) para testes.
- **Entregável:** matriz de viabilidade confirmada (atualização do doc 02) e escopo fechado.

## Fase 1 — Fundação + gestão da equipe

- Projeto Laravel 13 + Filament 5 + Livewire 4, CI, ambientes, deploy.
- Usuários, papéis, permissões, 2FA, log de atividade.
- Kanban completo (quadros, colunas, cartões, comentários, anexos, tempo real).
- Tarefas recorrentes (RRULE).
- Agenda baseada nos prazos.
- Painel do gestor.
- **Critério de aceite:** a equipe do cliente usa o Kanban no dia a dia; tarefas semanais são geradas automaticamente.

## Fase 2 — Catálogo central + primeiros conectores

- Catálogo com variações, mídias, tabela de medidas, atributos de moda.
- Importação do catálogo da Magazord e por planilha.
- Conector **Magazord** (estoque/preço/produtos).
- Conector **Mercado Livre** (OAuth, categorias, atributos, catálogo, User Products, publicação, estoque, preço).
- Conector **Shopee** (OAuth, categorias, atributos, variações, publicação, estoque, preço).
- Vínculo de anúncios já existentes por SKU.
- Regras de preço por canal; sincronização de estoque.
- **Critério de aceite:** produto cadastrado uma vez é publicado em ML e Shopee; alteração de estoque chega aos canais em minutos.

## Fase 3 — Auditoria diária

- Rotina diária + execução manual.
- Verificações de status, moderação, estoque e preço.
- Painel de problemas; problema → cartão no Kanban; resumo por e-mail.
- **Critério de aceite:** a conferência manual diária é substituída pelo relatório.

## Fase 4 — Expansão de canais

Ordem sugerida (ajustar pelo faturamento do cliente em cada canal): **SHEIN → Amazon → Magalu → TikTok Shop → Americanas**, depois avaliar Casas Bahia/Kwai/Dafiti/Netshoes (nativo ou via hub).

Também nesta fase: importação de pedidos, margem real por canal, imagens ajustadas automaticamente por canal, IA para títulos/descrições.

## Fase 5 — Produto SaaS + Magento

- Onboarding de novas empresas, planos, limites e cobrança.
- Registro dos apps como **integrador/terceiro** nos MPs (necessário para atender outros lojistas).
- Termos de uso, política de privacidade e adequação à LGPD.
- Conector **Magento 2**.
- Site comercial e documentação de uso.

## Trabalhos contínuos

- Acompanhar changelogs das APIs dos MPs (mudanças quebram integrações — ex.: User Products no ML, Open API do Magalu).
- Testes automatizados por conector com respostas gravadas (mocks).
- Suporte e monitoramento de filas/erros.
