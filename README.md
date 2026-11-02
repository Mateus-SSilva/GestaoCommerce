# GestãoCommerce

> Plataforma de gestão para operações de e-commerce multicanal: **catálogo único, publicação em vários marketplaces, auditoria diária dos anúncios e gestão da equipe (Kanban, tarefas recorrentes e agenda)**.

**Status:** proposta documentada + **POC do sistema de gestão da equipe** em [`poc/`](poc/README.md) (Kanban, recorrências, agenda, painel do gestor e agente de monitoramento simulado).
**Cliente de referência:** e-commerce de pijamas (loja principal na Magazord + Shopee, Mercado Livre, SHEIN e outros).
**Objetivo de produto:** SaaS multi-cliente, em que cada empresa conecta apenas os marketplaces que usa.

## Documentos

| # | Documento | Para quê |
|---|-----------|----------|
| 01 | [Apresentação do projeto](docs/01-apresentacao.md) | Pitch para o cliente: problema, solução, benefícios e fases |
| 02 | [Pesquisa de marketplaces](docs/02-pesquisa-marketplaces.md) | O que cada MP permite automatizar via API, limitações e nível de confiança |
| 03 | [Arquitetura e stack](docs/03-arquitetura.md) | Tecnologias, módulos, conectores, filas, multi-tenant, integração futura com Magento |
| 04 | [Funcionalidades](docs/04-funcionalidades.md) | Lista de funcionalidades por módulo e priorização (MVP x fases seguintes) |
| 05 | [Layout e UX](docs/05-layout-ux.md) | Sugestões de layout, navegação e wireframes |
| 06 | [Bibliotecas e ferramentas](docs/06-bibliotecas.md) | Pacotes que aceleram o desenvolvimento e onde comprar em vez de construir |
| 07 | [Roadmap](docs/07-roadmap.md) | Fases de entrega e critérios de aceite |
| 08 | [Dúvidas para o cliente](docs/08-duvidas.md) | Perguntas que precisam de resposta antes do desenvolvimento |
| 09 | [Riscos, premissas e o que não prometer](docs/09-riscos-e-premissas.md) | Limites reais do projeto para uma proposta honesta |
| 10 | [Dores do e-commerce multicanal](docs/10-dores-ecommerce.md) | Pesquisa das dores, evidências e como o projeto ajuda (base para vender) |
| 11 | [Roteiro da apresentação](docs/11-roteiro-apresentacao.md) | Esboço slide a slide para a reunião com o cliente |
| 12 | [API da Magazord](docs/12-api-magazord.md) | Tudo que a API oferece e como cada módulo é usado no projeto |
| 13 | [Auditoria de frete](docs/13-auditoria-frete.md) | Conferência automática pedido × CT-e com margem de erro |

## Resumo em uma linha por marketplace

| Marketplace | Cadastro de anúncio via API | Estoque/preço via API | Auditoria via API | Confiança da pesquisa |
|---|---|---|---|---|
| Mercado Livre | Sim | Sim | Sim | Alta |
| Shopee | Sim | Sim | Sim | Alta |
| Amazon BR | Sim | Sim | Sim | Alta |
| Magalu | Sim | Sim | Sim (parcial) | Média-alta |
| SHEIN | Sim (com aprovação) | Sim | Parcial | Média — validar |
| TikTok Shop | Provável (Partner API) | Provável | Provável | Média — validar |
| Americanas | Sim (via credencial do painel) | Sim | Parcial | Média — validar |
| Magazord (loja própria) | Sim | Sim | Sim | Média-alta |
| Casas Bahia, Kwai, Dafiti, Netshoes | Não confirmado | Não confirmado | Não confirmado | Baixa — via hub ou fase futura |

Detalhes, fontes e ressalvas em [02-pesquisa-marketplaces.md](docs/02-pesquisa-marketplaces.md).

_Pesquisa realizada em novembro de 2026. As APIs de marketplaces mudam com frequência; tudo marcado como "validar" precisa ser confirmado com o próprio marketplace antes de entrar em contrato._
