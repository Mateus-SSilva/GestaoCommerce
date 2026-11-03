# GestãoCommerce — POC (versão experimental)

Prova de conceito do **sistema interno de gestão** (Fase 1 do [roadmap](../docs/07-roadmap.md)), com uma prévia **simulada** do agente de monitoramento (Fase 2). Feita para apresentar ao cliente; não é a versão de produção.

**Stack:** Laravel 13 · Filament 5 · Livewire 4 · Tailwind CSS 4 · SQLite · SortableJS

## O que dá para ver na demo

| Tela | O que mostra |
|---|---|
| **Painel do gestor** | Tarefas abertas, atrasadas, que vencem hoje, concluídas na semana; carga por pessoa; próximas entregas; saúde dos canais; gráfico de entregas |
| **Quadros (Kanban)** | 3 quadros (Marketplaces, Lançamentos, Operação e Expedição), arrastar e soltar entre colunas, limite WIP por coluna, filtros por responsável/prioridade/busca, cartão com responsável, prioridade, prazo, etiquetas, checklist e comentários |
| **Tarefas** | Lista "Minhas tarefas" / "Toda a equipe" com filtros (atrasadas, origem, quadro…) |
| **Agenda** | Calendário mensal com os prazos dos cartões, datas comerciais (11.11, Black Friday, Natal…) e eventos |
| **Recorrências** | Regras diária / semanal / mensal que criam a tarefa automaticamente, com responsável, prazo e checklist |
| **Agente de monitoramento** | **Simulação:** encontra problemas fictícios nos canais (anúncio pausado, banido, estoque divergente…), cria a tarefa para o responsável, não duplica e move para "Validar" quando o problema some |
| **Configurações** | Quadros e colunas, equipe, etiquetas, datas e eventos |
| **Tema claro/escuro** | Botão no topo (e na tela de login); a escolha fica salva no navegador |

Login de demonstração (já vem preenchido): **gestor@demo.test / demo1234**. Os outros usuários (ana, bruno, carla, diego @demo.test) usam a mesma senha.

## Acessar pelo navegador sem instalar nada (GitHub Codespaces)

1. No GitHub, abra o repositório e escolha a branch da POC.
2. Clique em **Code → Codespaces → Create codespace on …**.
3. Aguarde a primeira preparação (alguns minutos: instala dependências e cria os dados de demonstração). A demo abre sozinha numa URL do tipo `https://<nome>-8000.app.github.dev`.
4. **Para compartilhar com o cliente:** na aba **Ports**, clique com o botão direito na porta 8000 → **Port Visibility → Public**. Sem isso, só quem tem acesso ao repositório consegue abrir.

O Codespace hiberna após um tempo sem uso; ao reabrir, o servidor sobe de novo. Para recriar os dados de demonstração: `cd poc && composer demo:reset`.

## Como rodar localmente

Requisitos: PHP 8.3+ (extensões `pdo_sqlite`, `intl`, `mbstring`, `zip`), Composer 2 e Node 20+.

```bash
cd poc
composer demo        # instala dependências, cria o banco SQLite com dados de demonstração e compila o front
php artisan serve    # abre em http://127.0.0.1:8000
```

Outros comandos úteis:

```bash
composer demo:reset              # recria os dados de demonstração (datas sempre relativas a hoje)
php artisan gestao:recorrentes   # gera as tarefas recorrentes do dia
php artisan gestao:monitorar     # executa uma verificação do agente (simulado)
php artisan schedule:work        # roda o agendador (recorrências 06:00, agente 05:00)
php artisan test                 # testes automatizados
```

## Roteiro sugerido para a apresentação (10 min)

1. **Painel do gestor:** "o que está sendo feito, o que foi feito, o que está atrasado".
2. **Quadros → Lançamentos:** arrastar um cartão, abrir e mostrar checklist e comentários.
3. **Recorrências:** mostrar "toda segunda, conferir fotos → Ana" e clicar em *Gerar tarefas de hoje*.
4. **Agenda:** prazos da equipe + Black Friday no mesmo calendário.
5. **Agente de monitoramento:** clicar em *Executar verificação agora* e mostrar as tarefas criadas no quadro Marketplaces, com responsável definido pelo canal. Deixar claro que é **simulação** e que na Fase 2 os dados vêm das APIs oficiais.

## Onde está o quê

| Caminho | Conteúdo |
|---|---|
| `app/Models` | Board, BoardColumn, Card, Label, ChecklistItem, Comment, RecurringTask, CalendarEvent, MonitorIssue, MonitorRun |
| `app/Services/RecurringTaskGenerator.php` | Regras de recorrência (idempotente: no máximo 1 cartão por regra por dia) |
| `app/Services/MonitorAgent.php` | Agente: deduplicação por *fingerprint*, roteamento canal → responsável, severidade → prioridade, fechamento automático. O método `detect()` é o **único ponto simulado**: na Fase 2 ele chama os conectores reais |
| `app/Filament/Pages/Kanban.php` + `resources/js/kanban.js` | Kanban com SortableJS |
| `app/Filament/Pages/Agenda.php` | Calendário mensal |
| `app/Filament/Support/CardActions.php` | Formulário/ação de cartão compartilhado (Kanban, Agenda, Tarefas, Painel) |
| `app/Filament/Resources` | Tarefas, Recorrências, Agente, Quadros, Equipe, Etiquetas, Eventos |
| `database/seeders/DatabaseSeeder.php` | Dados de demonstração (loja de pijamas fictícia) |
| `tests/Feature/GestaoTest.php` | Testes das regras principais |

## Limitações conhecidas da POC

- Agente de monitoramento **simulado** (sem conexão com marketplaces).
- Sem multiempresa (SaaS), permissões finas por quadro, notificações por e-mail/WhatsApp, anexos e tempo real entre usuários (previstos no roadmap).
- Banco SQLite, para facilitar a demo; produção usará PostgreSQL/MySQL, Redis e filas.
