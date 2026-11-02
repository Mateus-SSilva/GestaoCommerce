# 12 — API da Magazord: o que está disponível

> Levantamento feito na documentação oficial ([docs.api.magazord.com.br](https://docs.api.magazord.com.br/)) em novembro/2026. A página pública lista **64 grupos de endpoints** e tem changelog ativo (última alteração em 12/10/2026).
>
> **Limites deste levantamento:** a documentação pública mostra os grupos e alguns caminhos, mas **não mostra os esquemas completos de resposta** (campos), a autenticação nem os limites de requisição. Esses detalhes só serão confirmados com as credenciais em mãos. Itens com "⚠️" precisam dessa validação.

## Conclusão principal

A Magazord **tem uma API ampla**: pedidos, notas fiscais, estoque, transporte, financeiro, atendimento, carrinhos e mais. O problema relatado pelo cliente provavelmente está na **política de liberação de acesso** (credenciais pedidas ao suporte, possíveis custos ou restrições), não na falta de API. A conversa com a Magazord deve ser objetiva: **quais endpoints, com que limites e com que custo**.

## Acesso

| Item | O que se sabe |
|---|---|
| Autenticação | Há um portal "Magazord Auth" (portal.oauth.magazord.com.br). Integradores relatam credenciais (URL + token + senha) liberadas via suporte (integracao@magazord.com.br) ⚠️ |
| Formato | REST/JSON, caminhos `/v2/site/...`, `/v2/faturamento/...`, `/v2/transporte/...` |
| Requisitos | TLS 1.2+. Chamadas a partir do navegador bloqueadas por CORS (uso apenas servidor a servidor, o que é o nosso caso) |
| Rate limit / paginação | Não documentados publicamente ⚠️ |
| Webhooks | Apenas 2 eventos: **pedido registrado** e **alteração de situação do pedido** |
| API de transporte separada | Portal próprio (docs.api-transporte.magazord.com.br), com autenticação por token, voltado a transportadoras ⚠️ |

## Módulos disponíveis e uso no projeto

Legenda de uso: **M** = monitoramento (Fase 2) · **C** = cadastro único (Fase 3) · **F** = auditoria de frete · **R** = relatórios/oportunidades · — = sem uso previsto.

### Catálogo

| Grupo | Endpoints | Uso | Para quê |
|---|---|---|---|
| Produto | 11 (inclui produto completo/detalhado, PATCH parcial, filtro por tipo) | M, C | Referência do catálogo; depois, escrita pelo cadastro único |
| Derivação do Produto | 15 | M, C | Variações (cor × tamanho), SKU, EAN, MPN, mídias |
| Derivação / Derivação de Item | 3 + 3 | C | Definição das derivações (cores, tamanhos) |
| Grades | 1 | C | Grades de tamanho |
| Características / Características do Produto | 1 + 2 | C | Atributos (tecido, gênero…) |
| Categoria, Marca, Tags | 4, 4, 1 | C | Taxonomia |
| Mídia | 3 | C | Imagens |
| Kit | 3 | C | Kits/combos |
| Relacionamentos do Produto | 2 | — | Produtos relacionados no site |
| Personalização | 1 + 2 | — | Personalização de produto |
| NCM, CEST, Situação Tributária, Benefício Fiscal, Unidade de Medida, ANP | 1–2 cada | C | Dados fiscais do cadastro |
| Garantia | 4 | — | |

### Estoque e preço

| Grupo | Endpoints | Uso | Para quê |
|---|---|---|---|
| **Estoque** | 15 (inclui movimentações com filtro de origem e depósitos) | M, C, R | **Referência de quantidade** para o agente; histórico de movimentações |
| Projeção de Estoque | 4 | R | Previsão e reposição, ruptura de grade |
| **Preço** | 2 (filtro por data de atualização) | M, C | Referência de preço; sincronização incremental |
| Tabela de Preços / Itens | 1 + 2 | C | Preço por tabela |
| Avise-me | 4 | R | **Demanda reprimida**: quem pediu aviso de um produto/tamanho esgotado. Ótimo para priorizar reposição |

### Pedidos e logística

| Grupo | Endpoints | Uso | Para quê |
|---|---|---|---|
| **Pedido** | 15: consulta (com ordenação por última atualização e opção de retornar rastreios), por código, inclusão, itens, observações, tags, situação, **rastreio/histórico**, **ocorrências** (novo em out/2026), **pagamentos**, pedidos de terceiros | F, R | Base da auditoria de frete; atrasos de entrega; margem |
| Pedido × Dados de Conferência | 4 | R | Conferência de separação/expedição |
| **Nota Fiscal** | 11: consulta por id/**chave**, listagem, DANFE em PDF, **etiqueta de transporte**, inserir (inclusive XML), cancelar, situação | F | **Ligação pedido ↔ NF-e ↔ CT-e** (o CT-e referencia a chave da NF-e) |
| Faturamento | 3 (validação antes de faturar, faturar, boletos; erros de integração de pedidos e marketplaces catalogados) | M | Detectar pedidos travados no faturamento |
| **Frete** | 1 (`POST /v2/site/transporte/simulacao`) | F | Recalcular o frete esperado de um pedido ⚠️ (esquema não publicado) |
| **Transportadoras / Serviços** | 2 + 2 | F | Cadastro de transportadoras e serviços |
| **Romaneio de Carga** | 8 | F | Romaneios de expedição por transportadora |
| Movimentações de Processos / Processos | 1 + 2 | — | Processos internos |

### Marketplaces (dentro da Magazord)

| Grupo | Endpoints | Uso | Para quê |
|---|---|---|---|
| Anúncios Marketplace | 4 | M | ⚠️ Indica que a **Magazord também integra com marketplaces**. Perguntar ao cliente se usa e para quais canais |
| Anúncio Catalogado | 4 | M | Idem (anúncios de catálogo) |

### Financeiro

| Grupo | Endpoints | Uso | Para quê |
|---|---|---|---|
| Contas a Receber | 2 (inclui boletos) | R | Conciliação |
| Contas a Pagar | 1 | F, R | Conferir se a fatura da transportadora foi lançada |
| Movimentação de Tesouraria | 1 (novo em ago/2026) | R | Conciliação |
| Formas de Recebimento, Configuração de Pagamento | 1 + 1 | R | |
| Folha de vendas, Caixas PDV, Código do Vendedor | 1 cada | — | Loja física/PDV |

### Clientes e atendimento

| Grupo | Endpoints | Uso | Para quê |
|---|---|---|---|
| **Atendimento** | 9 | R | Chamados de SAC viram tarefas no Kanban |
| **Avaliação** | 1 | R | Monitorar avaliações negativas (tamanho, qualidade) → tarefa |
| Pessoa | 9 | — | Clientes (dados pessoais: usar o mínimo, LGPD) |
| **Carrinho** | 2 (com URL de checkout e filtro por data) | R | Carrinhos abandonados (oportunidade de recuperação) |
| Cupom de Desconto | 6 | R | Campanhas |
| Newsletter | 1 | — | |

### Site e loja

| Grupo | Endpoints | Uso |
|---|---|---|
| Loja, Loja Configuração | 2 + 1 | Configuração |
| Frontend (produtos do site), Menu, Páginas, Conteúdo Adicional, Grupos de Banner | 2, 1, 8, 4, 3 | — (gestão de conteúdo do site) |
| Notificações | 4 (inscrição em tópicos, contagem de não lidas) | ⚠️ Validar se servem como alternativa a webhooks |

## O que a API permite fazer no projeto (resumo)

1. **Monitoramento:** referência de estoque, preço e catálogo para comparar com os marketplaces.
2. **Auditoria de frete:** pedido → NF-e (chave) → CT-e, mais simulação de frete. Ver [13-auditoria-frete.md](13-auditoria-frete.md).
3. **Alertas operacionais:** pedidos travados no faturamento, entregas com ocorrência ou atraso (rastreio/ocorrências), avaliações negativas, chamados de atendimento, tudo virando tarefa no Kanban.
4. **Relatórios de moda:** projeção de estoque e **avise-me** (demanda por tamanho esgotado) para reposição de grade.
5. **Cadastro único:** escrita de produtos, derivações, preço e estoque (Fase 3).
6. **Futuro:** conciliação (contas a receber/pagar, tesouraria), carrinhos abandonados.

## O que perguntar à Magazord

- Como obter credenciais e se há custo por acesso à API.
- Rate limit por minuto/hora e regras de paginação.
- Esquema completo de resposta de **Pedido** (campos de frete: valor cobrado, **custo cotado**, transportadora, serviço, peso, prazo) e de **Nota Fiscal**.
- Se a simulação de frete aceita os dados de um pedido passado ou usa só a tabela vigente.
- Se há planos de mais eventos de webhook (estoque, preço, nota fiscal).
- Se o portal Magazord Auth permite que o próprio lojista gere credenciais.

Fontes: [Documentação da API](https://docs.api.magazord.com.br/) · [Changelog](https://docs.api.magazord.com.br/changelog/) · [Webhooks](https://docs.api.magazord.com.br/webhooks/eventos/webhook) · [Simulação de transporte](https://docs.api.magazord.com.br/api/post-simulacao-transporte) · [Nota Fiscal](https://docs.api.magazord.com.br/api/get-nota-fiscal-by-id) · [Pedidos](https://docs.api.magazord.com.br/api/list-pedido) · [Romaneio de carga](https://docs.api.magazord.com.br/api/romaneio-carga-transportadoras)
