# 02 — Pesquisa de marketplaces

> Pesquisa feita em **novembro de 2026**. Cada MP recebe um **nível de confiança**:
>
> - **Alta** — documentação oficial pública e consolidada; vários integradores em produção.
> - **Média** — existe API e integradores usando no Brasil, mas a documentação oficial não é totalmente pública ou depende de aprovação.
> - **Baixa** — não foi encontrada documentação pública; só há indícios por integradores terceiros.
>
> Nada com confiança média ou baixa deve entrar em contrato sem a **Fase 0 (validação de acesso)**.

## Conceitos que valem para todos

- **Autorização OAuth por loja:** o lojista clica em "Conectar", faz login no MP e autoriza o nosso app. Guardamos `access_token` + `refresh_token` (criptografados) por loja. Isso é o que permite ser **modular**: cada cliente conecta apenas os canais que usa.
- **Tipo de app importa para virar SaaS.** Vários MPs diferenciam "app para uso próprio" de "app de terceiro/integrador (ISV)". Para atender **outros clientes**, precisamos do registro como **integrador**, que costuma ter revisão mais rígida (segurança, LGPD, dados pessoais de compradores).
- **Rate limit:** todos limitam requisições. A arquitetura precisa de filas com controle de vazão por canal e por loja (ver [03-arquitetura.md](03-arquitetura.md)).
- **Moderação:** todos os MPs podem recusar ou pausar um anúncio depois de publicado. A API devolve o status, mas a decisão é do MP.
- **"Crawler" = auditoria via API.** A verificação diária deve consultar as APIs oficiais. Raspar páginas com login viola os termos de uso e arrisca a conta do lojista. Raspagem de **página pública** (ver se o anúncio aparece, preço exibido) é tecnicamente possível, mas também pode violar termos e é frágil — tratar como opcional e de baixo valor.

---

## Mercado Livre — Confiança: **Alta**

| Item | Situação |
|---|---|
| API | REST pública, documentação em developers.mercadolivre.com.br |
| Acesso | Criação de app self-service no DevCenter, OAuth 2.0 por vendedor |
| Cadastro de anúncio | Sim (`POST /items`), com categorias e atributos obrigatórios por categoria |
| Estoque / preço | Sim |
| Pedidos / notificações | Sim, com webhooks (notificações por tópico) |
| Auditoria | Sim: status do item, moderações, elegibilidade de catálogo, tags de aviso |

**Limitações e pontos de atenção**

- **Catálogo obrigatório:** a maioria dos domínios hoje exige que o anúncio seja associado a um produto de catálogo (`catalog_required`), e alguns são exclusivos de catálogo (`catalog_only`). Publicar sem considerar isso leva à moderação. A API oferece listas (dumps) dos domínios obrigatórios por site e consulta de elegibilidade por item.
- **Migração para "User Products" (preço por variação):** no modelo novo, cada variação vira um item próprio (um `POST /items` por variação), agrupado por `family_name`. O modelo antigo e o novo convivem e os vendedores são migrados aos poucos. **Impacta diretamente a grade cor x tamanho dos pijamas** — o conector precisa suportar os dois modelos.
- **Rate limit:** aplicado por Client ID e por endpoint; o número exato não é publicado. Aumento de cota é solicitado ao time comercial de integrações com evidência de uso.
- Atributos compartilhados (ex.: tabela de medidas) são replicados de forma assíncrona entre itens do mesmo User Product.

Fontes: [Publicações obrigatórias no catálogo](https://developers.mercadolivre.com.br/pt-br/publicacoes-necessarias-do-catalogo) · [Elegibilidade de catálogo](https://developers.mercadolivre.com.br/en_us/catalog-eligibility) · [Rate limit / erro 429](https://developers.mercadolivre.com.br/pt-br/rate-limit-erro-429) · [Preço por variação](https://developers.mercadolivre.com.br/preco-por-variacao) · [User Products](https://developers.mercadolivre.com.br/pt_br/lojas-oficiais/user-products)

---

## Shopee — Confiança: **Alta** (detalhes de limites: validar)

| Item | Situação |
|---|---|
| API | Shopee Open Platform v2 (REST, assinatura HMAC-SHA256) |
| Acesso | Cadastro no Open Platform; tipos de app como **Seller In-House System** (uso próprio) e **ISV/integrador** (terceiros) |
| Cadastro de anúncio | Sim (`product/add_item`, modelos/variações via `init_tier_variation`/`add_model`) |
| Estoque / preço | Sim (`update_stock`, `update_price`) |
| Pedidos | Sim |
| Auditoria | Sim: `get_item_list` filtra por status (NORMAL, BANNED, UNLIST, DELETED); `get_item_base_info` / `get_model_list` para detalhes |

**Limitações e pontos de atenção**

- Conta de vendedor precisa ser **empresa (CNPJ)**; conta individual não é aprovada para Open Platform.
- Segundo o guia da Shopee Brasil, a liberação de apps costuma levar **cerca de 48 h**.
- Para o modelo **SaaS (atender outros lojistas)**, o registro correto é como integrador/terceiro, não "In-House". Confirmar requisitos.
- Rate limits variam por endpoint e permissão; números oficiais não foram confirmados na pesquisa.
- Para itens com variação, preço e estoque vêm vazios no item e precisam ser lidos por modelo.

Fontes: [Guia prático de integração Open Platform (Shopee BR)](https://seller.shopee.com.br/edu/article/27314/Guia-Pratico-de-Integracao-Open-Plataform) · [Processo de solicitação de acesso à API](https://seller.shopee.com.br/edu/article/3445/API--Access-Request-Process) · [Autorização de loja ao app](https://seller.br.shopee.cn/edu/article/15334) · [Notas técnicas sobre item_status/get_model_list](https://ithelp.ithome.com.tw/articles/10286041)

---

## Amazon (amazon.com.br) — Confiança: **Alta**

| Item | Situação |
|---|---|
| API | Selling Partner API (SP-API) |
| Acesso | Registro como desenvolvedor **privado** (só para a própria empresa) ou **público** (app listado na Appstore, para terceiros) |
| Cadastro de anúncio | Sim — Listings Items API + **Product Type Definitions** (JSON Schema por tipo de produto para validar antes de enviar) |
| Estoque / preço | Sim (Listings / Feeds `JSON_LISTINGS_FEED` para lote) |
| Pedidos | Sim (dados pessoais do comprador exigem papéis restritos) |
| Auditoria | Sim: status do listing e problemas (issues) retornados pela própria API |

**Limitações**

- Exige conta **Profissional** de vendedor; o registro é feito pelo usuário principal.
- Desenvolvedor público passa por revisão de arquitetura e de fluxo de dados pessoais (PII) se pedir papéis restritos. Prazo depende da Amazon.
- Tipos de produto suportados variam por marketplace — confirmar a categoria de roupas/pijamas no `marketplaceId` do Brasil.

Fontes: [Listings Items API](https://developer-docs.amazon.com/sp-api/lang-pt_BR/docs/listings-items-api) · [Product Type Definitions](https://developer-docs.amazon.com/sp-api/reference/product-type-definitions-v2020-09-01) · [Registro como desenvolvedor privado](https://developer-docs.amazon/sp-api/lang-pt_BR/docs/register-as-a-private-developer) · [Registro como desenvolvedor público](https://developer-docs.amazon/sp-api/docs/register-as-a-public-developer)

---

## Magalu — Confiança: **Média-alta**

| Item | Situação |
|---|---|
| API | **Magalu Open API** (developers.magalu.com) |
| Acesso | OAuth com consentimento do vendedor; permissões de catálogo, pedidos, preços, relatórios e estoque |
| Cadastro | Sim (API de Portfolio/SKU) |
| Estoque / preço | Sim (inclui API de automação de preço) |
| Pedidos | Sim |
| Auditoria | Parcial — validar quais status de moderação são expostos |

**Atenção:** está em andamento a migração da API antiga do Magalu Marketplace para a Open API; integradores pediram reautenticação dos vendedores. Construir direto na Open API.

Fontes: [Magalu Developer APIs](https://docsearch.algolia.com/mcp/docs/website/developers.magalu.com) · [Migração para Open API (Base.com)](https://base.com/pt-BR/blog/?p=17554)

---

## SHEIN — Confiança: **Média** (validar)

| Item | Situação |
|---|---|
| API | **SHEIN Open Platform** (open.sheincorp.com) — OpenAPI + webhooks |
| Acesso | Candidatura → criação do app → **revisão** → autorização do vendedor. Contato: openapi@shein.com |
| Cadastro | Sim ("Product Management: publish products through OpenAPI") |
| Estoque / preço | Sim, segundo integradores brasileiros |
| Pedidos | Sim — três modelos: SHEIN Fulfill, Seller Fulfill e Stock Preparation |
| Auditoria | Parcial — validar |

**Evidência de que funciona no Brasil:** UpSeller, Olist, Expedy, Omie.Hub e Base.com integram lojas SHEIN Brasil.

**Limitações conhecidas**

- UpSeller só autoriza a **conta principal** do vendedor (contas secundárias não).
- Olist relata que pedidos **SHEIN Fulfillment** só podem ser importados manualmente, um a um, por limitação da API.
- Regras de imagem rígidas (tipos de imagem, até 3 MB, 900–2200 px).
- A página oficial não menciona regiões; aprovação para um integrador novo no Brasil precisa ser confirmada.

Fontes: [SHEIN Open Platform](https://open.sheincorp.com/) · [UpSeller — integrar SHEIN](https://www.upseller.com/pt/help-doc-article-572) · [Olist — integrar SHEIN](https://ajuda.olist.com/marketplaces/como-integrar-o-erp-com-a-shein) · [Expedy — integrar SHEIN](https://ajuda.expedy.com.br/integracoes/como-integrar-a-shein-na-expedy/) · [Guia de imagens SHEIN (UpSeller)](https://upseller.com/pt/blog-article-69)

---

## TikTok Shop Brasil — Confiança: **Média** (validar)

- API oficial via **TikTok Shop Partner Center** (portal global para mercados fora dos EUA). Tokens de acesso de curta duração (~24 h, fonte secundária).
- A disponibilidade de cada API **varia por região** — a própria documentação diz para checar as categorias de app por mercado.
- Evidência no Brasil: Bling/Tray (parceria LWSA), UpSeller (suporta lojas BR) e TecnoSpeed/Plug4Market anunciam integração de produtos, estoque e pedidos.
- Termos do vendedor no Brasil (jan/2026) preveem anexo específico para uso de API.

Fontes: [Partner Center — Affiliate Seller API overview](https://partner.tiktokshop.com/docv2/page/affiliate-seller-api-overview) · [UpSeller — TikTok Shop](https://upseller.com/pt/help-doc-article-2852) · [Bling — TikTok Shop](https://blog.bling.com.br/tiktok-shop/) · [TecnoSpeed — integração TikTok Shop](https://blog.tecnospeed.com.br/integracao-tiktok-shop/) · [Termos do vendedor BR](https://seller-br.tiktok.com/university/essay?knowledge_id=3268441302615809)

---

## Americanas (Americanas, Submarino, Shoptime) — Confiança: **Média** (validar)

- Credencial gerada pelo próprio painel do vendedor (Integrações → Credenciais API → tipo Seller). Historicamente a integração passava pela **SkyHub**, que foi migrada.
- Integradores (UpSeller, Base.com) fazem cadastro, estoque, pedidos.
- Documentação oficial atualizada não foi localizada publicamente — pedir ao time de integração da Americanas.

Fontes: [UpSeller — integrar Americanas](https://upseller.com/pt/help-doc-article-307) · [UpSeller — conta SkyHub](https://upseller.com/pt/help-doc-article-444)

---

## Magazord (loja principal do cliente) — Confiança: **Média-alta**

- API REST documentada em **docs.api.magazord.com.br** (há uma v1 antiga em descontinuação e changelog público).
- Credenciais (URL, token e senha) **não são autoatendidas**: solicitadas ao suporte/integração da Magazord.
- Existe SDK PHP oficial para o **marketplace** da Magazord (`magazord-plataforma/marketplace-sdk`) e um repositório público de diretrizes de API.
- Papel no sistema: canal de venda **e/ou** fonte inicial do catálogo (importar produtos já cadastrados no site para não começar do zero). **Decidir com o cliente** qual sistema é a fonte da verdade do estoque (ver [08-duvidas.md](08-duvidas.md)).
- **Relato do cliente:** a Magazord costuma resistir a liberar acesso à API. Por isso o sistema não deve **depender** dela. Ver o plano B abaixo.

### Plano B: referência de dados sem a API da Magazord

Os dados são do lojista, e o site é dele. As fontes abaixo podem ser combinadas, em ordem de preferência:

| # | Fonte | O que entrega | Limitações |
|---|---|---|---|
| 1 | **API oficial** (pedido formal do lojista à Magazord) | Tudo: produtos, SKUs, estoque, preço, pedidos | Depende da Magazord liberar |
| 2 | **Feed de produtos XML** (Google Merchant Center / catálogo Meta), se a loja já anuncia no Google Shopping ou Instagram | SKU/ID, título, preço, preço promocional, disponibilidade, imagens, link, às vezes GTIN | Normalmente **não traz quantidade** de estoque, só "em estoque/esgotado". Validar se a Magazord gera e com que frequência atualiza |
| 3 | **Exportação de planilha** pelo painel (manual ou agendada) | Normalmente inclui quantidade de estoque | Processo manual ou semiautomático (upload ou e-mail) |
| 4 | **Leitura do site público** (sitemap.xml + dados estruturados schema.org `Product`/`Offer` em JSON-LD nas páginas de produto) | Lista de produtos, preço, disponibilidade, variações (depende do tema) | Sem quantidade de estoque e sem pedidos; quebra se o tema mudar; precisa de rotina "educada" (baixa frequência, fora do horário de pico) para não ser bloqueada pelo firewall da loja; conferir se o contrato com a Magazord restringe acesso automatizado |
| 5 | **ERP** do cliente, se existir (Bling, Tiny etc. têm API aberta) | Estoque e pedidos com confiabilidade | Só se o ERP for usado como controle de estoque |

**Recomendação:** para o monitoramento (Fase 2), usar a **leitura do site público ou o feed** para catálogo, preço e disponibilidade, e a **planilha ou o ERP** para quantidade de estoque. Ao mesmo tempo, o lojista pede formalmente o acesso à API. Essa dependência também reforça o valor do **cadastro único** (Fase 3): com ele, a fonte da verdade passa a ser o nosso sistema, e a Magazord vira só mais um canal.

Fontes: [Documentação API Magazord](https://docs-v1.api.magazord.com.br/) · [Changelog](https://docs.api.magazord.com.br/changelog/) · [api-guidelines](https://github.com/magazord-plataforma/api-guidelines) · [marketplace-sdk](https://packagist.org/packages/magazord-plataforma/marketplace-sdk) · [Guia de integração (Adstart)](https://intercom.help/adstart-ajuda/pt-BR/articles/11029698-magazord)

---

## Magento (projeto futuro do cliente) — Confiança: **Alta**

- REST e GraphQL completos, incluindo Multi-Source Inventory (MSI).
- Pacote Laravel mantido: `justbetter/laravel-magento-client`.
- Entra como **mais um conector de canal** — ver [03-arquitetura.md](03-arquitetura.md#integração-futura-com-magento).

---

## Casas Bahia, Kwai Shop, Dafiti, Netshoes — Confiança: **Baixa**

| MP | O que foi encontrado |
|---|---|
| Casas Bahia | Integradores (VTEX, Omie.Hub) usam **token emitido pela Casas Bahia** no cadastro do seller. Sem documentação pública. |
| Kwai Shop | UpSeller (abr/2026), Base.com e Bling anunciam integração. Sem documentação pública. |
| Dafiti | Pertence ao Global Fashion Group. Sem documentação pública encontrada. (A Falabella usa uma "Seller Center API" em XML de outra família — **não** confirmar que é a mesma.) |
| Netshoes / Zattini | Grupo Magalu. Sem documentação pública de seller encontrada. |

**Recomendação:** não prometer no contrato inicial. Opções: (a) solicitar documentação diretamente ao marketplace durante a Fase 0; (b) atender via **hub** (Base.com, ANYMARKET, Plugg.to, Bling) que já tenha o conector, com nosso sistema conectado ao hub.

Fontes: [VTEX — Casas Bahia](https://help.vtex.com/en/docs/tracks/casas-bahia-marketplace-integration) · [Omie — Casas Bahia](https://ajuda.omie.com.br/pt-BR/articles/4701739-configurando-a-integracao-com-o-marketplace-casas-bahia) · [UpSeller — Kwai](https://upseller.com/pt/help-doc-article-1625) · [Base.com — Kwai](https://base.com/pt-BR/integracoes/kwai/)

## Não pesquisados a fundo (avaliar demanda)

AliExpress, Temu, Mercado Livre em outros países, Enjoei, Elo7, Carrefour, Olist Store, Madeira Madeira (não se aplica a pijamas). Incluir apenas se o cliente vender neles.

---

## Matriz consolidada

| MP | API oficial | Self-service | App p/ terceiros (SaaS) | Cadastro | Estoque | Preço | Pedidos | Status/moderação | Confiança |
|---|---|---|---|---|---|---|---|---|---|
| Mercado Livre | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | Alta |
| Shopee | ✅ | Aprovação (~48 h) | ✅ (ISV) | ✅ | ✅ | ✅ | ✅ | ✅ | Alta |
| Amazon BR | ✅ | Aprovação | ✅ (público, revisão rígida) | ✅ | ✅ | ✅ | ✅ | ✅ | Alta |
| Magalu | ✅ | OAuth | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ | Média-alta |
| Magazord | ✅ | Via suporte | ⚠️ por loja | ✅ | ✅ | ✅ | ✅ | n/a | Média-alta |
| SHEIN | ✅ | Revisão | ⚠️ validar | ✅ | ✅ | ✅ | ⚠️ Fulfillment manual | ⚠️ | Média |
| TikTok Shop | ✅ | Revisão | ⚠️ validar região | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | Média |
| Americanas | ✅ | Credencial no painel | ⚠️ | ✅ | ✅ | ✅ | ✅ | ⚠️ | Média |
| Casas Bahia | ⚠️ | Token via MP | ❓ | ❓ | ❓ | ❓ | ❓ | ❓ | Baixa |
| Kwai / Dafiti / Netshoes | ❓ | ❓ | ❓ | ❓ | ❓ | ❓ | ❓ | ❓ | Baixa |

Legenda: ✅ confirmado · ⚠️ existe mas com ressalva/validar · ❓ não confirmado.
