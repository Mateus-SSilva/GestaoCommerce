# 13 — Auditoria de frete (pedido × CT-e)

## A dor

No checkout, o frete é cotado conforme a tabela da transportadora. Depois, a transportadora emite o **CT-e** (Conhecimento de Transporte Eletrônico) e cobra na fatura. Para saber se cobrou o valor certo, alguém precisa comparar pedido por pedido, à mão. Com centenas de envios por mês, ninguém confere, e diferenças pequenas e frequentes (peso cubado, taxas extras, tabela desatualizada) viram dinheiro perdido.

## É possível automatizar? **Sim.**

Todas as peças existem:

| Peça | De onde vem |
|---|---|
| Pedido, valor do frete, transportadora, serviço, CEP | API Magazord: Pedido ⚠️ (confirmar campos) |
| Chave da NF-e do pedido | API Magazord: Nota Fiscal (consulta por id/chave) |
| CT-e (XML) com valor cobrado e chave da NF-e transportada | SEFAZ (Distribuição DF-e), e-mail da transportadora ou EDI |
| Valor esperado | Tabela da transportadora cadastrada no sistema e/ou simulação de frete da Magazord |

**O elo é a chave da NF-e:** o CT-e informa as chaves das NF-es que transporta, e a NF-e pertence a um pedido. Assim, o casamento **pedido ↔ NF-e ↔ CT-e** é automático e exato.

## Como obter os CT-es

| Opção | Como funciona | Prós | Contras |
|---|---|---|---|
| **1. SEFAZ, Distribuição DF-e (CTeDistribuicaoDFe)** ⭐ | Com o **certificado digital A1** do lojista, o sistema consulta periodicamente o web service nacional e baixa os XMLs dos CT-es em que o CNPJ da empresa aparece | Oficial, completo, não depende da transportadora; para CT-e **não exige manifestação** (ao contrário da NF-e) | Exige certificado A1 (guardado com segurança); a distribuição pode trazer documentos de terceiros, então é preciso filtrar; validar em quais papéis (remetente, tomador) o CNPJ recebe o documento ⚠️ |
| 2. Serviço de captura pago (ex.: NFe.io inbound, TecnoSpeed, Qive) | API que já entrega os XMLs | Mais simples | Custo mensal |
| 3. XML por e-mail | A transportadora envia o XML para uma caixa dedicada, e o sistema lê | Simples | Depende da transportadora enviar sempre |
| 4. EDI PROCEDA (CONEMB/DOCCOB) | Arquivos-padrão de conhecimentos e cobrança enviados pela transportadora | Traz também a **fatura** | Cada transportadora implementa de um jeito |

**Recomendação:** opção 1 como principal (biblioteca PHP `nfephp-org/sped-cte`, ⚠️ confirmar o suporte à distribuição, ou implementação direta do web service), com a opção 3 como complemento.

> ⚠️ **Correios (PAC/SEDEX):** os Correios geralmente **não emitem CT-e** para encomendas. A conferência desses envios seria feita pela fatura/relatório de postagens do contrato. Confirmar quais transportadoras o cliente usa. Envios por **intermediadores** (Melhor Envio, Frenet, Kangu etc.) e por **logística de marketplace** (Mercado Envios, Shopee) têm outra lógica de cobrança e ficam fora deste escopo inicial.

## Como a conferência funciona

```
 Magazord (pedido + NF-e)          SEFAZ / e-mail (CT-e XML)
            │                                  │
            └────────► Casamento pela chave da NF-e ◄────────┘
                                  │
                   Cálculo do frete esperado
       (tabela da transportadora: faixa de CEP × peso, ad valorem,
          GRIS, pedágio, TDE/TRT, ICMS; ou simulação Magazord)
                                  │
              Comparação com o valor do CT-e (vTPrest)
                                  │
         ┌────────────────┬───────┴──────────┬───────────────────┐
     Dentro da         Fora da margem     CT-e sem pedido     Pedido sem CT-e
     margem ✅          ❌ → tarefa         ⚠ → tarefa          ⚠ (após N dias)
```

### Margem de erro configurável

- Por transportadora e/ou serviço: **percentual** (ex.: 3%) **e/ou valor absoluto** (ex.: R$ 2,00). Regra: divergência só se ultrapassar os dois, ou qualquer um (configurável).
- Ignorar diferenças a favor do lojista, ou também sinalizá-las (útil para detectar tabela desatualizada).

### Verificações

| Verificação | O que pega |
|---|---|
| Valor do CT-e × valor esperado pela tabela | Cobrança acima do contrato |
| **Peso cobrado × peso do pedido** (o CT-e traz peso real/cubado) | Cubagem inflada, o motivo mais comum de divergência |
| Componentes do frete (o CT-e detalha frete-peso, ad valorem, GRIS, pedágio, taxas) | Taxa não contratada |
| CT-e duplicado para a mesma NF-e | Cobrança em dobro |
| CT-e sem pedido correspondente | Cobrança indevida ou envio não registrado |
| Pedido despachado sem CT-e após N dias | Documento faltando (ou Correios/intermediador) |
| CT-e complementar / de reentrega / devolução | Custos extras para analisar |
| Valor cobrado do cliente × custo real | Margem de frete (frete grátis e subsídio por pedido) |
| (Futuro) Soma dos CT-es × fatura da transportadora | Fatura divergente |

### Resultado

- **Painel de auditoria de frete:** total auditado, total divergente em R$, por transportadora e por mês.
- **Cada divergência vira uma tarefa** no Kanban para o responsável validar e contestar (mesmo agente da Fase 2: deduplicação, agrupamento e fechamento automático).
- **Relatório de contestação** por transportadora (lista de CT-es com valor esperado × cobrado e diferença) para enviar e pedir abatimento.

## Ponto de atenção: qual é o valor "certo"?

O frete **cobrado do cliente** nem sempre é o **custo** da transportadora (frete grátis, promoções, frete com margem). Para comparar corretamente, precisamos do **custo esperado**, que pode vir de:

1. A Magazord guardar o **custo cotado** no pedido (separado do valor cobrado do cliente). ⚠️ Confirmar se o campo existe na API.
2. **Recalcular pela tabela da transportadora** cadastrada no nosso sistema (importação da planilha de tabela de cada transportadora, com vigência por data). É o mais confiável, porque usa o contrato.
3. Simulação de frete da Magazord: só serve se a tabela não mudou desde o pedido.

Recomendação: **opção 2 como referência principal**, com a 1 como conferência adicional.

## Requisitos

- Credenciais da API Magazord (pedidos e notas fiscais) ou exportação equivalente.
- **Certificado digital A1** do CNPJ da loja (armazenado criptografado).
- **Tabelas de frete** vigentes de cada transportadora (planilha), com as regras de taxas.
- Lista de transportadoras e serviços usados.

## Onde entra no roadmap

É um módulo **independente dos marketplaces** e de alto retorno financeiro direto. Sugestão: **Fase 2B**, em paralelo ao agente de monitoramento, reaproveitando o mesmo motor de "problema → tarefa". Ver [07-roadmap.md](07-roadmap.md).

## Perguntas para o cliente

1. Quais transportadoras usam no site? Algum envio pelos Correios ou por intermediadores (Melhor Envio, Frenet…)?
2. Quantos envios por mês? Qual o gasto mensal com frete?
3. Têm as tabelas de frete vigentes em planilha? Com quais taxas (GRIS, ad valorem, TDE…)?
4. Quem é o tomador do frete no CT-e: a loja ou o cliente?
5. Têm certificado A1? Quem cuida (contador, TI)?
6. As transportadoras já enviam XML ou EDI hoje? Para qual e-mail?
7. Já perceberam cobranças erradas? Qual o tipo mais comum?
8. Qual margem de erro consideram aceitável?

Fontes: [NFe.io: distribuição de NF-e/CT-e inbound](https://nfe.io/docs/distribuicao-nfe-cte-inbound/index.md) · [Senior: baixa automática de CT-es (Distribuição DF-e)](https://documentacao.senior.com.br/documentoseletronicos/5.8.16/html_ajuda/documentacaoprocesso/ct-e/baixa-automatica-ct-e.htm) · [TecnoSpeed: automatizar download do XML do CT-e](https://blog.tecnospeed.com.br/?p=6859) · [Fórum ACBr: download de CT-e sem manifestação](https://www.projetoacbr.com.br/forum/topic/46023-download-de-cte-destinadas-manifesta%C3%A7%C3%A3o/) · [sped-cte (NFePHP)](https://www.github.com/nfephp-org/sped-cte) · [Senior: auditoria de frete](https://www.senior.com.br/?p=48801) · [Fretefy: auditoria de fretes](https://www.fretefy.com.br/blog/auditoria-de-fretes-como-funciona-e-quais-as-vantagens-descubra) · [Bsoft: EDI PROCEDA](https://bsoft.com.br/bsoft-tms/edi-proceda)
