# 01 — Apresentação do projeto

## O problema

Uma loja que vende em vários canais (site próprio + marketplaces) hoje enfrenta:

1. **Cadastro repetido.** Cada marketplace (MP) tem regras próprias: categorias, atributos obrigatórios, tabela de medidas, tamanho de imagem, limite de caracteres no título, forma de tratar variações (cor x tamanho). O mesmo pijama é cadastrado 5, 6, 7 vezes, à mão.
2. **Conferência manual.** Alguém precisa entrar em cada painel para ver se o anúncio está ativo, se foi moderado/bloqueado, se o estoque bate, se o preço está certo.
3. **Erros que custam dinheiro.** Venda sem estoque gera cancelamento e penalização na reputação; anúncio pausado por infração fica dias sem vender sem ninguém perceber.
4. **Equipe sem organização.** Não existe um lugar único para ver o que está sendo feito, o que foi feito, o que é prioridade e quem é responsável. Tarefas que se repetem toda semana dependem da memória de alguém.

## A solução

Um **webapp único** com dois grandes pilares:

### Pilar 1 — Operação multicanal

- **Catálogo central (fonte da verdade):** o produto é cadastrado uma vez, com todas as informações (fotos, variações cor/tamanho, medidas, composição, EAN, NCM, peso/dimensões).
- **Publicação por canal:** o usuário marca em quais canais o produto vai ser publicado. O sistema mostra, antes de enviar, **o que falta para cada MP** (ex.: "Mercado Livre exige o atributo *Gênero*", "Shopee exige imagem quadrada").
- **Sincronização de estoque e preço:** alteração no estoque central é propagada para todos os canais conectados. Preço pode ter regra por canal (ex.: +12% na Shopee).
- **Auditoria diária automática:** todo dia o sistema consulta, **pelas APIs oficiais**, cada anúncio em cada canal e gera um relatório: anúncios pausados, moderados, com estoque divergente, com preço divergente, com erro de cadastro. Cada problema pode virar uma tarefa no Kanban automaticamente.

### Pilar 2 — Gestão da equipe

- **Kanban** (estilo Trello/Jira): quadros, colunas, cartões, responsáveis, prioridade, prazos, etiquetas, checklist, comentários e anexos.
- **Tarefas recorrentes:** "toda segunda-feira, criar a tarefa *Conferir fotos dos lançamentos* para a Ana".
- **Agenda de entregas:** calendário montado a partir dos prazos do Kanban, com visão por pessoa e por semana.
- **Painel do gestor:** o que está atrasado, carga por pessoa, o que foi entregue na semana.

## Por que é diferente de um hub/ERP do mercado

Hubs como Bling, Tiny/Olist, ANYMARKET, Plugg.to e Base.com já fazem publicação e sincronização. O diferencial proposto aqui é **unir a operação de anúncios com a gestão da equipe**: o problema encontrado na auditoria vira tarefa com responsável e prazo, e o gestor acompanha tudo no mesmo lugar. Também é um produto pensado para o nicho de **moda/vestuário** (grade cor x tamanho, tabela de medidas, sazonalidade), que é onde os cadastros genéricos mais falham.

> Ponto de decisão importante: em alguns MPs menores, pode valer mais a pena **integrar com um hub existente** do que construir o conector do zero. Ver [06-bibliotecas.md](06-bibliotecas.md#comprar-x-construir).

## O que podemos prometer (com base na pesquisa)

- Publicação, estoque, preço e auditoria via API oficial em **Mercado Livre, Shopee e Amazon**.
- Integração com a **Magazord** (loja atual do cliente) para estoque/pedidos/produtos.
- **Magalu, SHEIN, TikTok Shop e Americanas**: viáveis, mas dependem de aprovação/credenciais junto ao marketplace — entram como fase 2, após validação.
- **Casas Bahia, Kwai, Dafiti, Netshoes**: sem documentação pública confirmada — não prometer no contrato inicial.
- Kanban, recorrências, agenda e painel do gestor: **sem dependência externa**, totalmente sob nosso controle.

## O que não vamos prometer

- "Robô que entra no painel do marketplace" (scraping com login). Isso viola os termos de uso dos MPs, quebra a cada mudança de tela e pode levar ao **bloqueio da conta do lojista**. A auditoria será feita pelas APIs oficiais.
- Cadastro 100% automático sem revisão humana: cada MP tem moderação própria e pode recusar o anúncio. O sistema reduz o trabalho e aponta o erro; não elimina a moderação.
- Prazos de aprovação de apps junto aos MPs (Amazon, Shopee, SHEIN, TikTok): dependem do marketplace, não de nós.

Ver a lista completa em [09-riscos-e-premissas.md](09-riscos-e-premissas.md).

## Fases (resumo)

| Fase | Entrega | Valor para o cliente |
|---|---|---|
| 0 | Descoberta e validação de acessos às APIs | Confirma o que é possível antes de investir |
| 1 | Gestão da equipe (Kanban, recorrências, agenda) + base do sistema | Resolve a desorganização interna imediatamente, sem depender de MPs |
| 2 | Catálogo central + conectores Magazord, Mercado Livre e Shopee | Cadastro único e estoque sincronizado nos canais principais |
| 3 | Auditoria diária + problemas virando tarefas | Fim da conferência manual |
| 4 | Novos conectores (Amazon, Magalu, SHEIN, TikTok Shop…) | Expansão de canais |
| 5 | SaaS multi-cliente (planos, cobrança, onboarding) e integração Magento | Produto vendável para outras lojas |

Detalhes em [07-roadmap.md](07-roadmap.md).

## Sugestão de nomes

GestãoCommerce (nome do repositório), **Vitrinia**, **Multicanal Hub**, **Grade** (referência à grade de tamanhos), **Orquestra**. Verificar registro de marca/domínio antes de escolher.
