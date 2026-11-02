# 04 — Funcionalidades

Prioridade: **MVP** (primeira entrega), **F2** (segunda onda), **Futuro** (avaliar demanda).

## 1. Catálogo central (PIM)

| Funcionalidade | Prioridade |
|---|---|
| Cadastro de produto com variações (grade cor × tamanho) e geração automática de SKUs | MVP |
| Campos fiscais/logísticos: EAN/GTIN, NCM, peso, dimensões, origem | MVP |
| Galeria de imagens por cor, com ordenação, e validação de tamanho/formato por canal | MVP |
| Tabela de medidas reutilizável (P, M, G, GG…) vinculada ao produto | MVP |
| Atributos de moda: composição/tecido, gênero, faixa etária, estampa, estação, tipo de manga, comprimento | MVP |
| Importação inicial a partir da Magazord e por planilha (XLSX/CSV) | MVP |
| Edição em massa (preço, estoque, atributos, status) | MVP |
| Kits/combos (ex.: "kit 2 pijamas") com estoque derivado | F2 |
| Descrição gerada/reescrita por IA por canal (títulos dentro do limite de cada MP) | F2 |
| Ajuste automático de imagem por canal (fundo branco, quadrado, resolução mínima) | F2 |
| Versionamento do produto (histórico de alterações) | F2 |

## 2. Canais e publicação

| Funcionalidade | Prioridade |
|---|---|
| Tela "Conectar canal" com OAuth por marketplace | MVP |
| Mapeamento de categorias (nossa → MP) com sugestão automática quando o MP oferece "category predictor" | MVP |
| Formulário dinâmico com atributos obrigatórios da categoria do MP | MVP |
| **Checklist pré-publicação**: o que falta para cada canal antes de enviar | MVP |
| Publicar em vários canais com um clique, acompanhando o resultado por canal | MVP |
| Sobrescrever campos por canal (título, descrição, preço) | MVP |
| Vincular anúncios já existentes nos MPs ao produto central (por SKU) — essencial para quem já vende | MVP |
| Regras de preço por canal (markup %, valor fixo, arredondamento ,90) | MVP |
| Sincronização de estoque em tempo quase real com estoque de segurança por canal | MVP |
| Importação de pedidos e baixa de estoque | F2 |
| Ações em massa (pausar todos os anúncios de uma coleção, ativar campanha) | F2 |
| Precificação considerando comissão e frete de cada MP (margem real) | F2 |
| Emissão de NF-e e etiquetas | Futuro (geralmente fica no ERP — ver dúvidas) |

## 3. Auditoria diária

| Funcionalidade | Prioridade |
|---|---|
| Rotina diária por conta conectada, com execução manual sob demanda | MVP |
| Verificações: anúncio inexistente, pausado, moderado/banido, estoque divergente, preço divergente, atributo faltando, aviso de catálogo (ML) | MVP |
| Painel de problemas com filtros por canal, severidade e status | MVP |
| Problema → cartão no Kanban automaticamente (com regra configurável) | MVP |
| Resumo diário por e-mail | MVP |
| Histórico/tendência (quantos problemas por semana, por canal) | F2 |
| Correção em um clique quando possível (ex.: reenviar estoque) | F2 |
| Notificação por WhatsApp/Slack/Telegram | F2 |
| Monitoramento de concorrência/preço público | Futuro (risco de termos de uso) |

## 4. Kanban (equipe)

| Funcionalidade | Prioridade |
|---|---|
| Múltiplos quadros (ex.: Marketplaces, Fotos, Lançamentos, Atendimento) | MVP |
| Colunas configuráveis, arrastar e soltar, limite WIP por coluna | MVP |
| Cartão: título, descrição (markdown), responsável(is), prioridade, prazo, etiquetas, checklist, anexos, comentários com @menção | MVP |
| Vincular cartão a produto, anúncio ou problema de auditoria | MVP |
| Filtros (meus cartões, atrasados, por etiqueta, por prioridade) e busca | MVP |
| Visões alternativas: lista/tabela e calendário | MVP |
| Atualização em tempo real entre usuários | MVP |
| Fila de prioridade (backlog ordenado pelo gestor) | MVP |
| Modelos de cartão (templates) | MVP |
| Subtarefas e dependências ("bloqueado por") | F2 |
| Apontamento de tempo | F2 |
| Automações simples ("ao mover para Concluído, notificar gestor") | F2 |

## 5. Tarefas recorrentes

| Funcionalidade | Prioridade |
|---|---|
| Regra de recorrência: diária, semanal (dias específicos), mensal, "toda 1ª segunda" (padrão RRULE) | MVP |
| Cria o cartão a partir de um modelo, no quadro/coluna definidos, com responsável e prazo relativo | MVP |
| Rodízio de responsável (cada semana uma pessoa) | F2 |
| Pausar/retomar recorrência; histórico de execuções | MVP |
| Alertar quando a tarefa recorrente da semana anterior não foi concluída | F2 |

## 6. Agenda

| Funcionalidade | Prioridade |
|---|---|
| Calendário mês/semana/dia com prazos dos cartões | MVP |
| Filtro por pessoa, quadro e etiqueta | MVP |
| Arrastar evento para mudar o prazo do cartão | MVP |
| Eventos manuais (reunião, sessão de fotos, data comercial) | MVP |
| Calendário comercial pré-carregado (Black Friday, Dia das Mães, Dia dos Namorados, Natal, datas de campanha dos MPs) | F2 |
| Exportação iCal / sincronização com Google Calendar | F2 |

## 7. Painel do gestor e relatórios

| Funcionalidade | Prioridade |
|---|---|
| Visão "o que está sendo feito / feito / a fazer" por pessoa | MVP |
| Cartões atrasados e carga por pessoa | MVP |
| Saúde dos canais (problemas abertos por canal, anúncios ativos x pausados) | MVP |
| Relatório semanal automático | F2 |
| Vendas por canal, produto, cor e tamanho (curva ABC, ruptura de grade) | F2 |

## 8. Administração / SaaS

| Funcionalidade | Prioridade |
|---|---|
| Usuários, papéis e permissões | MVP |
| Log de atividades | MVP |
| 2FA | MVP |
| Múltiplas empresas (tenants) com onboarding guiado | F2 / Fase 5 |
| Planos, limites (nº de canais, SKUs, usuários) e cobrança | Fase 5 |
| API própria e webhooks para ERPs dos clientes | Futuro |
| App mobile/PWA para equipe acompanhar o Kanban | F2 (PWA) |
