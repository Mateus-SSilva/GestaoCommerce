# 11 — Roteiro da apresentação (esboço)

> Esboço para a conversa com o cliente. A ideia é **ouvir primeiro e apresentar depois**: as perguntas de descoberta vêm antes da solução, e os slides de dores são ajustados com os números que o cliente der. Base de pesquisa: [10-dores-ecommerce.md](10-dores-ecommerce.md).
>
> Duração sugerida: 40–60 min (15 de conversa, 25 de apresentação, 10–20 de perguntas).

---

### Slide 1 — Capa
**GestãoCommerce: sua operação multicanal e sua equipe no mesmo lugar**
Subtítulo: proposta para [nome do cliente] · [data]

### Slide 2 — Antes de começar: queremos ouvir você
Abrir a conversa com 5 perguntas (anotar as respostas, que alimentam os slides 4 e 12):
1. Quantas horas por semana a equipe gasta conferindo os anúncios nos canais?
2. Quantos cancelamentos por falta de estoque aconteceram no último mês?
3. Algum anúncio importante já ficou pausado ou moderado sem ninguém perceber?
4. Quanto tempo leva para cadastrar um lançamento em todos os canais?
5. O que mais se perde ou se esquece na rotina da equipe?

### Slide 3 — O mercado cresceu, a operação ficou mais difícil
- E-commerce Brasil: ~R$ 259 bi projetados para 2026, cerca de 460 milhões de pedidos.
- Mais canais, mais regras, mais penalidades: reputação no ML, pontos de penalidade na Shopee, catálogo obrigatório, mudanças de comissão.
- Moda tem desafios extras: grade de tamanhos, tabela de medidas, trocas.

### Slide 4 — As dores que ouvimos (personalizar com as respostas do slide 2)
Grade com 6 cartões:
- 🔁 Cadastro repetido em cada canal
- 👀 Conferência manual de cada painel
- ⛔ Anúncio pausado sem ninguém ver
- 📦 Venda sem estoque → cancelamento → reputação
- 💸 Preço e margem diferentes por canal sem controle
- 🧩 Equipe sem um lugar para organizar o trabalho

### Slide 5 — Quanto isso custa (preencher com números do cliente)
Exemplo de cálculo ao vivo:
- Horas de conferência por semana × custo/hora × 4 semanas
- Cancelamentos/mês × ticket médio, mais o risco à reputação
- Dias de anúncio parado × vendas médias/dia do anúncio

### Slide 6 — A solução em uma frase
> Um sistema que **organiza a equipe**, **vigia os marketplaces todos os dias** e, por fim, **centraliza o cadastro** em um lugar só.

Três pilares, na ordem em que serão entregues:
1. **Gestão da equipe**
2. **Agente de monitoramento**
3. **Cadastro único**

### Slide 7 — Pilar 1: gestão da equipe
- Kanban com responsável, prioridade, prazo e checklist
- Tarefas recorrentes automáticas ("toda segunda: conferir fotos dos lançamentos → Ana")
- Agenda de entregas e calendário comercial (inverno, Dia das Mães, Black Friday)
- Painel do gestor: feito, fazendo, a fazer, atrasado
- *Imagem: wireframe do Kanban ([05-layout-ux.md](05-layout-ux.md))*

### Slide 8 — Pilar 2: agente de monitoramento
- Todo dia, consulta **pelas APIs oficiais** cada anúncio em cada canal
- Encontra: anúncio pausado, moderado ou banido, sem estoque, estoque ou preço divergente, aviso de catálogo, grade quebrada
- **Cria a tarefa para o responsável validar**, sem duplicar, e fecha sozinho quando o problema some
- Resumo diário do que precisa de atenção
- *Imagem: fluxo "canal → agente → tarefa → responsável → resolvido"*

### Slide 9 — Pilar 3: cadastro único
- Cadastra uma vez, publica em vários canais
- Mostra **o que falta para cada canal** antes de enviar
- Estoque sincronizado e regra de preço por canal
- Tabela de medidas padronizada em todos os canais (menos troca por tamanho)
- Seus dados deixam de depender de uma plataforma: o sistema vira a fonte da verdade

### Slide 10 — Canais: o que é possível hoje (transparência)
| Agora | Em seguida | Em validação |
|---|---|---|
| Mercado Livre, Shopee, site Magazord* | Amazon, Magalu | SHEIN, TikTok Shop, Americanas, Casas Bahia, Kwai, Dafiti, Netshoes |

\* Se a Magazord não liberar a API, usamos feed, planilha ou leitura do próprio site como referência.

Mensagem: *"Só prometemos o que as APIs oficiais permitem. Sem robôs no seu painel, sem risco para a sua conta."*

### Slide 11 — Como vamos entregar
Linha do tempo:
**Fase 1** Gestão da equipe → **Fase 2** Agente de monitoramento (ML, Shopee, site) → **Fase 3** Cadastro único → **Fase 4** Novos canais e relatórios → **Fase 5** Plataforma para outras lojas + Magento
- Valor em cada fase: a equipe começa a usar na Fase 1, sem esperar o resto.

### Slide 12 — O que vem depois (oportunidades)
Escolher 3 ou 4 conforme as dores que aparecerem na conversa:
- Caixa de perguntas unificada com sugestão de resposta por IA
- Margem real por canal e por produto
- Alerta de prazo de envio (penalidades)
- Relatórios de moda: giro por cor e tamanho, devolução por tamanho
- Conciliação dos repasses dos marketplaces
- Modelos de projeto para datas comerciais

### Slide 13 — O que precisamos de vocês
- Acesso de administrador às contas dos marketplaces (para autorizar o app)
- Pedido formal de acesso à API da Magazord (e, em paralelo, feed ou planilha)
- 1 pessoa de referência na equipe para validar cada entrega
- Amostra do catálogo (10–20 produtos)

### Slide 14 — Próximos passos
1. Workshop de descoberta (processos, tarefas recorrentes, dores por canal)
2. Abertura dos acessos às APIs (em paralelo)
3. Início da Fase 1
4. Primeira entrega para a equipe usar

### Slide 15 — Encerramento / perguntas

---

## Notas para quem apresenta

- **Não citar números de terceiros como verdade absoluta.** Usar "fontes de mercado apontam…". Os números que convencem são os do cliente (slide 5).
- **Ser transparente sobre os limites** (slide 10). Isso gera confiança e evita promessa que não pode ser cumprida. Ver [09-riscos-e-premissas.md](09-riscos-e-premissas.md).
- Se o cliente perguntar por um "robô que entra no painel": explicar o risco de bloqueio da conta e que o agente usa as APIs oficiais.
- Se perguntar sobre preço ou prazo: só depois do workshop de descoberta.
- Levar os wireframes de [05-layout-ux.md](05-layout-ux.md). Depois da conversa, transformar em protótipo visual.
