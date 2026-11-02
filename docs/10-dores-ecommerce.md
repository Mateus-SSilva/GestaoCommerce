# 10 — Dores do e-commerce multicanal e como o projeto ajuda

> Pesquisa de novembro/2026 para preparar a conversa com o cliente. Para cada dor: **o que é**, **evidência** (com fonte e ressalva), **como o sistema ajuda**, **em que fase** e **uma pergunta para validar com o cliente**.
>
> **Ressalva geral:** boa parte dos números públicos vem de blogs de ferramentas e consultorias (que têm interesse comercial) e varia entre fontes. Na apresentação, usar como **ordem de grandeza** e sempre com a fonte. Os números que realmente vendem são os **do próprio cliente**, levantados na conversa.

## Contexto do mercado

- O e-commerce brasileiro fechou 2025 com cerca de **R$ 235,5 bilhões** (+15,3%), e a projeção da Abiacom (ex-ABComm) para 2026 é de cerca de **R$ 259 bilhões**, **460 milhões de pedidos** e ticket médio de **R$ 562**. O 1º semestre de 2026 somou cerca de R$ 125 bilhões.
- Os grandes marketplaces concentram a maior parte das vendas online, e a SHEIN abriu espaço para vendedores locais, principalmente em **moda**.
- Mensagem para o slide: *o mercado cresce, mas a operação do lojista ficou mais complexa, com mais canais, mais regras e mais penalidades.*

Fontes: [Mercado & Consumo: projeção 2026](https://mercadoeconsumo.com.br/16/02/2026/ecommerce/e-commerce-cresce-15-no-brasil-e-se-aproxima-de-r-260-bilhoes-em-2026/) · [GiroNews: fechamento 2025](https://gironews.com/varejo-digital/e-commerce-brasileiro-fatura-r-2355-bilhoes-em-2025/) · [Mercado & Consumo: 1º semestre 2026](https://mercadoeconsumo.com.br/11/08/2026/ecommerce/e-commerce-brasileiro-movimenta-r-125-bilhoes-e-cresce-2437-no-1o-semestre/) · [Business Wire: relatório B2C Brasil](https://www.businesswire.com/news/home/20260129960153/en/Brazil-B2C-Ecommerce-Market-Report-2025-2029-Mercado-Livre-Amazon-Magazine-Luiza-Americanas-Shopee-Via-Dominate-Core-Demand-While-Cross-Border-Entrants-Such-as-Shein-and-AliExpress-Expand---ResearchAndMarkets.com)

---

## Mapa rápido

| # | Dor | Impacto | O projeto ajuda? | Fase |
|---|---|---|---|---|
| 1 | Equipe desorganizada, tarefas esquecidas | Alto | ✅ Resolve | 1 |
| 2 | Conferência manual dos anúncios em cada painel | Alto | ✅ Resolve | 2 |
| 3 | Anúncio pausado, moderado ou banido sem ninguém perceber | Alto | ✅ Resolve | 2 |
| 4 | Venda sem estoque → cancelamento → reputação | Muito alto | 🟡 Detecta (F2), corrige (F3) | 2–3 |
| 5 | Cadastro repetido em cada marketplace | Alto | ✅ Resolve | 3 |
| 6 | Preço inconsistente e margem real desconhecida | Alto | 🟡 Detecta (F2), regras (F3), margem (F4) | 2–4 |
| 7 | Penalidades por atraso de envio | Alto | 🟡 Alerta e tarefa | 4 (pedidos) |
| 8 | Perguntas de compradores sem resposta rápida | Médio-alto | 🔵 Sugestão futura | Futuro |
| 9 | Trocas e devoluções altas em moda (tamanho) | Alto em moda | 🟡 Tabela de medidas padronizada; relatórios | 3–4 |
| 10 | Conciliação financeira dos repasses | Médio-alto | 🔵 Sugestão futura (ou integrar ferramenta) | Futuro |
| 11 | Dependência de plataforma (Magazord, um canal só) | Estratégico | ✅ Cadastro único como fonte da verdade | 3 |
| 12 | Sazonalidade e datas comerciais sem planejamento | Médio | ✅ Agenda + recorrências | 1 |
| 13 | Ruptura de grade (falta tamanho M da estampa mais vendida) | Alto em moda | 🟡 Relatórios + avise-me da Magazord | 4 |
| 14 | Frete cobrado pela transportadora (CT-e) diferente do contratado | Alto (financeiro direto) | ✅ Resolve — ver [13-auditoria-frete.md](13-auditoria-frete.md) | 2B |

Legenda: ✅ resolve no escopo · 🟡 ajuda parcialmente ou em fase posterior · 🔵 oportunidade de módulo futuro.

---

## 1. Equipe sem organização

**O que é:** tarefas combinadas por WhatsApp, esquecidas ou feitas duas vezes; o gestor não sabe o que está parado; tarefas semanais dependem da memória de alguém.

**Evidência:** guias de operação multicanal apontam que esperar que uma pessoa cuide de anúncios, estoque, preço, atendimento e logística em 3 ou 4 plataformas é irreal. É preciso dividir funções e ter rotina. *(Relato do próprio cliente: hoje não existe nenhum sistema.)*

**Como ajudamos:** Kanban com responsáveis e prazos, tarefas recorrentes automáticas, agenda e painel do gestor.

**Fase:** 1. **Perguntar:** quais tarefas se repetem toda semana? Quantas vezes algo importante foi esquecido no último mês?

Fonte: [GEFF: multi-marketplace 2026](https://gefersonalencar.com.br/2026/02/21/multi-marketplace-otimizar-vendas-2026/)

## 2. Conferência manual dos anúncios

**O que é:** alguém abre cada painel (ML, Shopee, SHEIN…) para ver se está tudo certo. Com centenas de SKUs, a conferência não é completa, e o que não é conferido vira prejuízo.

**Como ajudamos:** agente de monitoramento diário via API; cada problema vira uma tarefa para o responsável, com deduplicação e fechamento automático.

**Fase:** 2. **Perguntar:** quantas horas por semana a equipe gasta conferindo os canais? Quais problemas mais aparecem?

## 3. Anúncio pausado, moderado ou banido sem ninguém perceber

**O que é:** o marketplace pausa ou modera o anúncio (imagem fora do padrão, atributo faltando, catálogo obrigatório no ML) e ele fica dias sem vender.

**Evidência:** a Shopee expõe status como BANNED e UNLIST pela API; o Mercado Livre modera anúncios fora do catálogo obrigatório e emite avisos prévios (`catalog_forewarning`). Ver [02-pesquisa-marketplaces.md](02-pesquisa-marketplaces.md).

**Como ajudamos:** o agente detecta no mesmo dia, explica o motivo e cria a tarefa.

**Fase:** 2. **Perguntar:** algum anúncio importante já ficou parado sem ninguém notar? Por quanto tempo?

## 4. Venda sem estoque, cancelamento e reputação

**O que é:** o mesmo item vende em dois canais ao mesmo tempo e não há peça para atender. O lojista cancela, e o marketplace penaliza.

**Evidência:**
- No Mercado Livre, o termômetro de reputação considera reclamações, **cancelamentos feitos pelo vendedor** e atrasos, nos últimos 60 dias. Fontes de mercado citam limites para o nível verde na faixa de **~1% a 1,5% de cancelamentos** e **~2% de reclamações**. Os percentuais variam entre as fontes e o ML os ajusta. Conferir no painel oficial.
- Vendedores relatam que a penalização é quase imediata e que recuperar a medalha leva semanas.
- O Bling cita possível suspensão temporária acima de ~2,5% de cancelamentos.

**Como ajudamos:** na Fase 2, o agente detecta divergência de estoque e anúncio ativo sem estoque de referência. Na Fase 3, a sincronização de estoque e o estoque de segurança por canal evitam o problema. (Opção: antecipar só o envio de estoque para ML/Shopee.)

**Perguntar:** quantos cancelamentos por falta de estoque no último mês? Qual a cor do termômetro hoje?

Fontes: [Tecnoblog: ranqueamento ML](https://tecnoblog.net/noticias/mercado-livre-muda-ranqueamento-e-vai-exigir-mais-vendas-de-lojistas/) · [Nubimetrics: reputação ML](https://academia.nubimetrics.com/br/reputacao-mercado-livre) · [Bling: cancelar venda no ML](https://blog.bling.com.br/cancelar-venda-no-mercado-livre/) · [ML Developers: reputação de vendedores](https://developers.mercadolivre.com.br/pt-br/reputacao-de-vendedores) · [Integração multicanal 2026](https://www.bxtdata.com/pt/insights/51/Integra%C3%A7%C3%A3o%20Marketplace%20Brasil%202026%3A%20Vendas%20Multicanal)

## 5. Cadastro repetido em cada marketplace

**O que é:** cada canal tem categorias, atributos, limites de título, padrões de imagem e modelos de variação diferentes (ex.: User Products no ML, regras de imagem da SHEIN). O mesmo pijama é cadastrado várias vezes.

**Como ajudamos:** cadastro único com checklist do que falta em cada canal e publicação em vários canais.

**Fase:** 3. **Perguntar:** quanto tempo leva para cadastrar um lançamento em todos os canais? Quantos lançamentos por mês?

## 6. Preço inconsistente e margem real desconhecida

**O que é:** comissões, taxas fixas e frete mudam com frequência e variam por canal. O lojista não sabe quanto sobra em cada venda.

**Evidência (2026, fontes de mercado, conferir nos simuladores oficiais):**
- Mercado Livre: comissões citadas entre ~11% e ~20% conforme categoria e tipo de anúncio; mudanças na cobrança de logística em 2026.
- Shopee: removeu o teto de comissão por item e aumentou a taxa fixa.
- SHEIN: comissão de referência citada em torno de 16%.
- Comparativos apontam que a "comissão real" (com frete e taxas extras) quase sempre fica acima da tabela.

**Como ajudamos:** o agente detecta preço fora da regra (F2), as regras de preço por canal (F3) e a **margem real por canal e por SKU** (F4, com dados de pedidos).

**Perguntar:** como o preço é definido em cada canal hoje? Sabem a margem por canal?

Fontes: [Yav: guia de marketplaces 2026](https://yav.com.br/blog/guia-marketplaces-brasil-2026/) · [Yav: comissões](https://yav.com.br/comissoes-marketplace/) · [Tray: Shopee ou Mercado Livre](https://tray.com.br/escola/shopee-ou-mercado-livre/)

## 7. Penalidades por atraso ou falta de envio

**O que é:** cada marketplace mede prazo de postagem. Atrasos geram pontos de penalidade, perda de benefícios e, em casos graves, restrição da conta.

**Evidência (fontes de mercado, não oficiais):** a Shopee monitora Taxa de Não Envio e Taxa de Envio Atrasado, com pontos de penalidade (de 1 a 2 por pedido, segundo consultorias). A partir de certos níveis, a loja perde benefícios de marketing ou tem anúncios restringidos. Há relato (não confirmado) de cobrança financeira por ciclo de penalização desde jul/2026, e a Shopee vem reduzindo o prazo de envio para 24 h. No ML, o atraso entra no termômetro.

**Como ajudamos:** com a importação de pedidos (F4), alerta de pedidos perto do prazo e tarefa para a expedição. Os indicadores de penalidade entram no painel do gestor quando a API expuser.

**Perguntar:** já receberam pontos de penalidade na Shopee? Quem cuida da expedição?

Fontes: [UpSeller: penalidades Shopee](https://upseller.com/pt/blog-article-946) · [GoSmarter: penalidade Shopee](https://gosmarter.com.br/penalidade-shopee/) · [GiroNews: Shopee 24 h](https://gironews.com/varejo-digital/shopee-vai-reduzir-tempo-de-entrega-para-24-horas/)

## 8. Perguntas de compradores sem resposta rápida

**O que é:** no ML, as perguntas são públicas e a resposta em até 1 hora é incentivada pela própria plataforma (alerta de "resposta atrasada"). A demora reduz conversão e posição no ranking.

**Evidência:** ferramentas de mercado associam resposta rápida a mais conversão. Os números exatos ("+30%") não têm fonte independente.

**Como ajudamos (sugestão de módulo futuro):** caixa de perguntas unificada de todos os canais, com respostas-padrão e sugestão de resposta por IA usando os dados do produto (medidas, tecido). Tecnicamente viável no ML e na Shopee via API.

**Perguntar:** quem responde as perguntas hoje? Quantas por dia? Qual o tempo médio?

Fontes: [Nubimetrics: responder perguntas](https://academia.nubimetrics.com/br/responder-perguntas-marketplaces) · [UpSeller: tempo de resposta ML](https://upseller.com/pt/help-doc-article-1577)

## 9. Trocas e devoluções em moda (tamanho errado)

**O que é:** a moda tem uma das maiores taxas de devolução do e-commerce, e **tamanho/caimento** é um dos motivos principais. Cada devolução custa frete reverso, manuseio e, às vezes, a peça.

**Evidência (fontes secundárias):** dados atribuídos a Ebit/Nielsen citam devolução de **20% a 40% em moda**. Outras fontes falam em ~10% no geral e até 30% em roupas. Pesquisa da PowerReviews aponta tamanho/medida incorreta como motivo em 75% dos casos de devolução, e cerca de 26% das compras de vestuário seriam de vários tamanhos para testar.

**Como ajudamos:** **tabela de medidas padronizada** no cadastro único e enviada igual para todos os canais (F3). Relatório de devolução por produto e tamanho, para achar a peça com "forma pequena" (F4, depende dos dados de pedidos e devoluções da API de cada canal).

**Perguntar:** qual a taxa de troca hoje? Qual o motivo mais comum? A tabela de medidas é igual em todos os canais?

Fontes: [Shopify: devoluções no e-commerce](https://www.shopify.com/br/blog/devolucoes-no-e-commerce) · [Melhor Envio: logística reversa em lojas de roupa](https://melhorenvio.com.br/blog/frete-e-logistica/logistica-reversa-loja-roupas/) · [Ruacep: custo da logística reversa](https://www.ruacep.com.br/guias/ecommerce/logistica-reversa-ecommerce/)

## 10. Conciliação financeira dos repasses

**O que é:** cada canal repassa em prazos diferentes, descontando comissão, frete, taxas e antecipações. O valor depositado raramente bate com a venda, e cobranças indevidas passam despercebidas. Cabe ao vendedor provar o erro para pedir estorno.

**Evidência:** ferramentas de conciliação listam como itens típicos pedidos não repassados, comissões divergentes, fretes divergentes e descontos indevidos.

**Como ajudamos (sugestão de módulo futuro):** conferência automática de repasse × pedido × taxa esperada, com divergências virando tarefa (mesma lógica do agente). Alternativa: integrar com ferramenta especializada (ex.: TrackCash, Koncili) em vez de construir.

**Perguntar:** alguém confere os repasses hoje? Já encontraram cobrança indevida?

Fontes: [Anymarket: conciliação de repasses](https://marketplace.anymarket.com.br/por-que-quando-e-como-conciliar-os-repasses-dos-marketplaces/) · [Bling: conciliação financeira](https://blog.bling.com.br/?p=18230) · [NFe.io: conciliação no e-commerce](https://nfe.io/blog/?p=14448)

## 11. Dependência de plataforma

**O que é:** o catálogo "mora" na Magazord, que segundo o cliente resiste a liberar API, e as regras de cada canal podem mudar da noite para o dia. Guias de mercado apontam a dependência de um único canal como o maior risco operacional.

**Como ajudamos:** o cadastro único faz do nosso sistema a fonte da verdade. Trocar de plataforma (ex.: ir para o Magento) ou abrir canal novo vira só ativar um conector. Ver o plano B de dados em [02-pesquisa-marketplaces.md](02-pesquisa-marketplaces.md#plano-b-referência-de-dados-sem-a-api-da-magazord).

**Perguntar:** quais problemas já tiveram com a Magazord? O que os impede de mudar hoje?

Fonte: [Yav: guia de marketplaces 2026](https://yav.com.br/blog/guia-marketplaces-brasil-2026/)

## 12. Datas comerciais sem planejamento

**O que é:** Black Friday (projeção de mais de R$ 16 bilhões no e-commerce em 2026), Dia das Mães, Natal, as datas duplas dos marketplaces (11.11, 12.12) e, no caso de pijamas, a **entrada do inverno**. Fotos, cadastro, estoque e campanhas precisam estar prontos antes.

**Como ajudamos:** agenda com calendário comercial pré-carregado e **modelos de projeto** (ex.: "Black Friday": conjunto de tarefas com prazos relativos à data, criado com um clique).

**Fase:** 1 (agenda e recorrências) e F2 (calendário comercial). **Perguntar:** quais datas são mais fortes para pijamas? Com quanta antecedência começam a preparar?

Fontes: [Mercado & Consumo: Black Friday 2026](https://mercadoeconsumo.com.br/22/10/2026/black-friday/black-friday-2026-deve-movimentar-mais-de-r-16-bilhoes-no-e-commerce/) · [ABC da Comunicação: datas duplas](https://www.abcdacomunicacao.com.br/datas-duplas-redesenham-o-consumo-online-e-aceleram-a-disputa-entre-marketplaces-no-brasil/)

## 13. Ruptura de grade

**O que é:** específico de moda. A estampa campeã acaba no tamanho M enquanto sobra o GG. O anúncio continua no ar, mas vende menos, e o lojista só percebe tarde.

**Como ajudamos:** o agente pode alertar quando a grade de um produto ativo ficar "quebrada" (tamanhos centrais zerados). Com os pedidos (F4), entram relatório de giro por cor e tamanho e sugestão de reposição.

**Perguntar:** como decidem a reposição hoje? Já perderam vendas por falta de um tamanho específico?

---

## Oportunidades para propor depois (upsell)

| Módulo | Dor que resolve | Viabilidade |
|---|---|---|
| Caixa de perguntas unificada + IA | #8 | Alta em ML/Shopee (API de perguntas/chat); validar demais |
| Conciliação financeira | #10 | Média (APIs de pagamentos/repasses variam); ou integrar ferramenta existente |
| Margem real por canal/SKU | #6 | Alta após importação de pedidos |
| Alerta de prazo de envio | #7 | Alta após importação de pedidos |
| Relatórios de moda (grade, devolução por tamanho) | #9, #13 | Alta após pedidos/devoluções |
| Modelos de projeto para datas comerciais | #12 | Alta (interno) |
| Geração de títulos/descrições por canal com IA | #5 | Alta |
| Monitoramento de preço de concorrentes | #6 | Baixa/arriscada (termos de uso); usar APIs oficiais quando houver |
