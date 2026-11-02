# 08 — Dúvidas para o cliente

Respostas a estas perguntas definem escopo, prioridade e preço.

## Negócio e canais

1. Em quais marketplaces vende hoje? Qual a **participação de cada um no faturamento**? (define a ordem dos conectores)
2. Quais marketplaces pretende abrir nos próximos 12 meses?
3. Quantas contas/lojas por marketplace? Há mais de um CNPJ?
4. Usa algum fulfillment (Mercado Envios Full, Shopee Full, SHEIN Fulfillment, FBA)? Isso muda a gestão de estoque.
5. Quantos produtos (modelos) e quantos SKUs (com cor × tamanho) existem hoje? Quantos lançamentos por mês?
6. Os produtos já estão publicados nos MPs? (Se sim, precisamos **vincular** os anúncios existentes, não recriar — recriar perde histórico de vendas e reputação.)

## Sistemas atuais

7. Qual ERP usam (Bling, Tiny, Omie, outro, nenhum)? Quem emite NF-e e etiqueta hoje?
8. Já usam algum hub de integração (ANYMARKET, Plugg.to, Base.com, UpSeller)? O que funciona e o que não funciona nele?
9. **Qual sistema é a fonte da verdade do estoque?** Magazord, ERP ou o novo sistema? (decisão mais importante da arquitetura)
10. Os pedidos dos marketplaces entram na Magazord hoje? Como o estoque é baixado?
11. Existe planilha mestre de produtos? Podem compartilhar uma amostra?
12. Qual o plano para o projeto Magento: substituir a Magazord, rodar em paralelo, ou outra marca?

## Cadastro e regras

13. Quais informações sempre faltam ou dão erro no cadastro de cada MP? (lista de dores reais)
14. Preço é igual em todos os canais ou tem regra (markup por canal, frete embutido)?
15. Usam estoque de segurança por canal (ex.: deixar 2 unidades fora da Shopee)?
16. Títulos e descrições são diferentes por canal?
17. Quem produz as fotos? Há padrão de fundo/tamanho?

## Equipe e Kanban

18. Quantas pessoas vão usar? Quais funções (cadastro, fotos, atendimento, expedição, gestor)?
19. Quais tarefas se repetem toda semana/mês? (exemplos concretos para os modelos de recorrência)
20. Usam algo hoje (WhatsApp, planilha, Trello)? Precisam importar algo?
21. Precisam de controle de horas trabalhadas?
22. Onde preferem receber alertas: e-mail, WhatsApp, Slack, só no sistema?
23. Precisam acessar pelo celular?

## SaaS / produto

24. O cliente de referência será sócio, cliente piloto ou só cliente? (impacta propriedade do código e preço)
25. Já existem outras lojas interessadas? De que segmentos?
26. Modelo de cobrança desejado: mensalidade por loja, por canal conectado, por nº de SKUs?
27. Hospedagem: preferência ou restrição (nuvem nacional, AWS, etc.)?

## Pendências técnicas a validar com os marketplaces

- Shopee: requisitos para app de **integrador/ISV** no Brasil e rate limits por endpoint.
- SHEIN: aprovação de integrador novo no Brasil; API de status/moderação de produto; pedidos SHEIN Fulfillment.
- TikTok Shop: disponibilidade das APIs de produto/estoque/pedido para a região Brasil.
- Magalu: quais status de moderação a Open API expõe.
- Amazon: tipos de produto de vestuário disponíveis no marketplace Brasil via Product Type Definitions.
- Americanas, Casas Bahia, Dafiti, Netshoes, Kwai: documentação oficial e processo para integrador.
- Magazord: documentação completa da API, limites e se há webhooks.
