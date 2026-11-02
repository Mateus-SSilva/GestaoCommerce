# 05 — Layout e UX

## Princípios

1. **Um lugar para tudo, mas focado em tarefas.** A tela inicial responde "o que precisa da minha atenção hoje?".
2. **Mostrar o problema e a solução juntos.** Todo erro de canal vem com "por que aconteceu" e "como corrigir".
3. **Status visível por canal.** Ícones/cores por marketplace em todo o sistema (linha do produto, cartão, problema).
4. **Densidade de informação ajustável.** Operadores de cadastro trabalham com tabelas grandes; gestores querem visão resumida.
5. **Modo claro e escuro**, responsivo; Kanban e agenda utilizáveis no celular.

## Estrutura de navegação

```
┌──────────────┬───────────────────────────────────────────────────────┐
│  LOGO        │  🔍 Buscar (produtos, SKUs, cartões)    🔔  👤 Empresa ▾ │
├──────────────┼───────────────────────────────────────────────────────┤
│ 🏠 Início     │                                                       │
│              │                    ÁREA DE CONTEÚDO                   │
│ CATÁLOGO     │                                                       │
│  Produtos    │                                                       │
│  Estoque     │                                                       │
│  Preços      │                                                       │
│              │                                                       │
│ CANAIS       │                                                       │
│  Anúncios    │                                                       │
│  Pedidos     │                                                       │
│  Auditoria ③ │                                                       │
│  Conexões    │                                                       │
│              │                                                       │
│ EQUIPE       │                                                       │
│  Quadros     │                                                       │
│  Minhas tarefas                                                      │
│  Recorrências│                                                       │
│  Agenda      │                                                       │
│              │                                                       │
│ RELATÓRIOS   │                                                       │
│ ⚙ Configurações                                                      │
└──────────────┴───────────────────────────────────────────────────────┘
```

Sidebar recolhível; busca global com atalho `Ctrl+K` (command palette).

## Tela inicial (dashboard)

```
┌───────────────┬───────────────┬───────────────┬───────────────┐
│ Problemas     │ Anúncios      │ Tarefas       │ Sem estoque   │
│ críticos  12  │ ativos  1.284 │ atrasadas  5  │ SKUs   37     │
└───────────────┴───────────────┴───────────────┴───────────────┘
┌─────────────────────────────────┬─────────────────────────────┐
│ Saúde por canal                 │ Minhas tarefas de hoje      │
│ ML      ████████████░  96% ok   │ ☐ Conferir fotos coleção    │
│ Shopee  ██████████░░░  88% ok   │ ☐ Corrigir 3 anúncios SHEIN │
│ SHEIN   ███████░░░░░░  71% ok   │ ☑ Atualizar preços inverno  │
│ Magazord█████████████ 100% ok   │                             │
├─────────────────────────────────┼─────────────────────────────┤
│ Últimos problemas da auditoria  │ Próximas entregas (agenda)  │
└─────────────────────────────────┴─────────────────────────────┘
```

## Produto — edição com abas por canal

```
Pijama Longo Flanela Xadrez                         [Salvar] [Publicar ▾]
─────────────────────────────────────────────────────────────────────────
[ Geral ] [ Variações ] [ Imagens ] [ Medidas ] [ Canais ● ] [ Histórico ]

Canais
┌──────────────┬──────────┬─────────────────────────────┬──────────────┐
│ Canal        │ Status   │ Pendências                  │ Ação         │
├──────────────┼──────────┼─────────────────────────────┼──────────────┤
│ Magazord     │ ✅ Ativo │ —                           │ Ver          │
│ Mercado Livre│ ⚠ Pronto │ Associar ao catálogo        │ Publicar     │
│ Shopee       │ ❌ Falta │ Imagem 1:1; atributo Gênero │ Completar    │
│ SHEIN        │ ⏸ Pausado│ Moderado: imagem com texto  │ Ver motivo   │
└──────────────┴──────────┴─────────────────────────────┴──────────────┘
```

A aba **Variações** mostra a grade cor × tamanho como matriz (estoque e preço editáveis direto na célula).

## Kanban

```
Quadro: Marketplaces       [Filtros ▾] [👤 Todos ▾] [Quadro | Lista | Agenda]
┌─ Backlog (8) ──┐ ┌─ A fazer (5) ──┐ ┌─ Fazendo 3/4 ─┐ ┌─ Revisão ─┐ ┌─ Feito ─┐
│┌──────────────┐│ │┌──────────────┐│ │┌─────────────┐│ │           │ │         │
││🔴 Alta        ││ ││🛒 Shopee      ││ ││ ...         ││ │           │ │         │
││Anúncio SHEIN ││ ││Corrigir 3     ││ │└─────────────┘│ │           │ │         │
││moderado      ││ ││variações      ││ │               │ │           │ │         │
││📅 04/11 👤Ana ││ ││☑ 1/3  👤Bruno ││ │               │ │           │ │         │
│└──────────────┘│ │└──────────────┘│ │               │ │           │ │         │
└────────────────┘ └────────────────┘ └───────────────┘ └───────────┘ └─────────┘
```

- Cartão abre em **painel lateral** (não sai do quadro).
- Cartões gerados pela auditoria têm selo "🤖 Auditoria" e link direto para o anúncio.
- Cor da borda = prioridade; ícone do canal quando vinculado a um MP.

## Agenda

Visão mês/semana/dia (estilo Google Calendar), cores por quadro ou por pessoa, arrastar para alterar prazo. Faixa superior com datas comerciais.

## Identidade visual (sugestão)

- Paleta neutra (cinzas) + 1 cor de marca; cores semânticas consistentes: verde ok, âmbar atenção, vermelho crítico, azul informativo.
- Cada marketplace com **seu ícone** (apenas como identificação do canal, sem imitar a identidade visual deles).
- Fonte: Inter ou Geist; ícones Heroicons (padrão do Filament) ou Lucide.
- Componentes: Filament 5 para CRUD/tabelas/formulários; componentes Livewire próprios para Kanban, grade de variações e dashboard.

## Referências de UX para inspiração

Linear (velocidade, atalhos, command palette), Trello (simplicidade do cartão), Jira (filtros e backlog), Notion (painel lateral), Shopify Admin (edição de produto e variações).
