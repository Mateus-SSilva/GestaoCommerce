<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\CalendarEvent;
use App\Models\Card;
use App\Models\Label;
use App\Models\RecurringTask;
use App\Models\User;
use App\Services\MonitorAgent;
use App\Services\RecurringTaskGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Dados de demonstração: loja de pijamas fictícia.
 * Todas as datas são relativas a hoje, para a demo estar sempre "atual".
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('demo1234');
        $u = [];
        foreach ([
            'gestor' => ['Gestor Demo', 'gestor@demo.test', 'gestor', 'Gestor da operação', '#4f46e5'],
            'ana' => ['Ana Souza', 'ana@demo.test', 'operador', 'Fotos e cadastro', '#db2777'],
            'bruno' => ['Bruno Lima', 'bruno@demo.test', 'operador', 'Mercado Livre', '#ca8a04'],
            'carla' => ['Carla Rocha', 'carla@demo.test', 'operador', 'Shopee e SHEIN', '#ea580c'],
            'diego' => ['Diego Alves', 'diego@demo.test', 'operador', 'Site e expedição', '#0891b2'],
        ] as $key => [$name, $email, $role, $job, $color]) {
            $u[$key] = User::create(compact('name', 'email', 'role', 'color') + ['job_title' => $job, 'password' => $password]);
        }

        $l = [];
        foreach ([
            'ml' => ['Mercado Livre', 'warning'], 'shopee' => ['Shopee', 'danger'], 'shein' => ['SHEIN', 'gray'],
            'site' => ['Site Magazord', 'info'], 'fotos' => ['Fotos', 'primary'], 'inverno' => ['Coleção Inverno', 'info'],
            'bf' => ['Black Friday', 'danger'], 'fiscal' => ['Fiscal', 'success'],
        ] as $key => [$name, $color]) {
            $l[$key] = Label::create(compact('name', 'color'));
        }

        $boards = [];
        foreach ([
            'mp' => ['Marketplaces', 'Anúncios, problemas e ajustes nos canais de venda', '#f59e0b', ['Backlog', 'A fazer', 'Fazendo', 'Validar', 'Concluído']],
            'lanc' => ['Lançamentos', 'Do desenvolvimento do produto até a publicação', '#ec4899', ['Ideias', 'Fotos', 'Cadastro', 'Revisão', 'Publicado']],
            'ops' => ['Operação e Expedição', 'Pedidos, estoque, transportadoras e financeiro', '#06b6d4', ['A fazer', 'Fazendo', 'Aguardando terceiros', 'Concluído']],
        ] as $key => [$name, $desc, $color, $cols]) {
            $board = Board::create(['name' => $name, 'description' => $desc, 'color' => $color, 'position' => count($boards)]);
            foreach ($cols as $i => $col) {
                $board->columns()->create([
                    'name' => $col,
                    'position' => $i,
                    'is_done' => $i === count($cols) - 1,
                    'wip_limit' => in_array($col, ['Fazendo', 'Cadastro']) ? 4 : null,
                ]);
            }
            $boards[$key] = $board->load('columns');
        }

        $col = fn (string $board, string $name) => $boards[$board]->columns->firstWhere('name', $name);
        $d = fn (int $days) => today()->addWeekdays($days);

        $cards = [
            // Marketplaces
            ['mp', 'Backlog', 'Padronizar títulos dos anúncios do ML (limite de 60 caracteres)', 'media', null, 'bruno', ['ml'], ['Levantar anúncios com título cortado', 'Definir padrão: tipo + tecido + estampa + público']],
            ['mp', 'Backlog', 'Estudar abertura de loja no TikTok Shop', 'baixa', $d(15), 'gestor', [], []],
            ['mp', 'A fazer', 'Atualizar tabela de medidas em todos os anúncios da Shopee', 'alta', $d(2), 'carla', ['shopee'], ['Exportar anúncios ativos', 'Aplicar tabela padrão P ao GG', 'Conferir infantil (2 ao 14)']],
            ['mp', 'A fazer', 'Ativar campanha 11.11 na Shopee', 'alta', $d(4), 'carla', ['shopee', 'bf'], []],
            ['mp', 'Fazendo', 'Associar anúncios de pijama longo ao catálogo do ML', 'alta', $d(-1), 'bruno', ['ml'], ['Flanela xadrez', 'Malha lua e estrelas', 'Soft ursinhos']],
            ['mp', 'Validar', 'Corrigir fotos reprovadas na SHEIN (fundo com textura)', 'media', $d(0), 'carla', ['shein', 'fotos'], []],
            ['mp', 'Concluído', 'Revisar comissões do ML após mudança de tarifas', 'media', $d(-3), 'bruno', ['ml'], [], true],
            // Lançamentos
            ['lanc', 'Ideias', 'Linha pijama família (pais, filhos e pet) para o Natal', 'media', $d(10), 'gestor', ['bf'], []],
            ['lanc', 'Fotos', 'Sessão de fotos: coleção inverno flanela', 'urgente', $d(1), 'ana', ['fotos', 'inverno'], ['Reservar estúdio', 'Separar peças P ao GG', 'Fotos com fundo branco para os marketplaces', 'Fotos lifestyle para o site']],
            ['lanc', 'Fotos', 'Refazer fotos do robe plush (cor saiu errada)', 'alta', $d(-2), 'ana', ['fotos'], []],
            ['lanc', 'Cadastro', 'Cadastrar pijama infantil dinossauros (site + ML + Shopee)', 'alta', $d(3), 'ana', ['site', 'ml', 'shopee'], ['Site Magazord', 'Mercado Livre', 'Shopee', 'SHEIN']],
            ['lanc', 'Cadastro', 'Cadastrar camisola algodão lisa em 4 cores', 'media', $d(5), 'ana', ['site'], []],
            ['lanc', 'Revisão', 'Revisar descrições da coleção seda', 'media', $d(1), 'gestor', [], []],
            ['lanc', 'Publicado', 'Lançamento pijama soft ursinhos', 'media', $d(-4), 'ana', ['site', 'ml', 'shopee'], [], true],
            // Operação
            ['ops', 'A fazer', 'Negociar nova tabela de frete com a transportadora', 'alta', $d(6), 'gestor', ['fiscal'], ['Levantar volume dos últimos 3 meses', 'Pedir proposta a 2 transportadoras']],
            ['ops', 'A fazer', 'Inventário do estoque de flanela', 'media', $d(2), 'diego', ['inverno'], []],
            ['ops', 'Fazendo', 'Conferir CT-es de outubro com os pedidos', 'alta', $d(-1), 'diego', ['fiscal'], ['Baixar XMLs', 'Comparar com tabela', 'Abrir contestação']],
            ['ops', 'Aguardando terceiros', 'Contestação de frete cobrado em dobro (12 CT-es)', 'media', $d(7), 'diego', ['fiscal'], []],
            ['ops', 'Concluído', 'Pedir credenciais da API à Magazord', 'alta', $d(-5), 'gestor', ['site'], [], true],
        ];

        foreach ($cards as $i => [$b, $c, $title, $priority, $due, $who, $labels, $checklist]) {
            $done = $cards[$i][8] ?? false;
            $card = Card::create([
                'board_id' => $boards[$b]->id,
                'board_column_id' => $col($b, $c)->id,
                'title' => $title,
                'priority' => $priority,
                'due_date' => $due,
                'assignee_id' => $u[$who]->id,
                'created_by' => $u['gestor']->id,
                'position' => $i,
            ]);
            if ($done) {
                $card->forceFill(['completed_at' => Carbon::parse($due)->setTime(16, 0)])->saveQuietly();
            }
            $card->labels()->attach(collect($labels)->map(fn ($k) => $l[$k]->id));
            foreach ($checklist as $j => $item) {
                $card->checklistItems()->create(['title' => $item, 'position' => $j, 'is_done' => $done || $j === 0]);
            }
        }

        // Histórico: tarefas concluídas nas últimas duas semanas (alimenta o gráfico e o painel).
        $history = [
            ['mp', 'Responder moderação de anúncio no ML', 'bruno'], ['mp', 'Ajustar frete grátis na Shopee', 'carla'],
            ['lanc', 'Fotos pijama seda lisa', 'ana'], ['lanc', 'Cadastro robe plush no site', 'ana'],
            ['ops', 'Separar pedidos atrasados da transportadora', 'diego'], ['ops', 'Atualizar estoque após inventário', 'diego'],
            ['mp', 'Revisar títulos da coleção infantil', 'bruno'], ['lanc', 'Tabela de medidas linha soft', 'ana'],
            ['ops', 'Fechamento financeiro de outubro', 'gestor'], ['mp', 'Corrigir variações duplicadas na SHEIN', 'carla'],
            ['lanc', 'Briefing da coleção de verão', 'gestor'], ['ops', 'Cotação de embalagens', 'diego'],
            ['mp', 'Ativar anúncios premium no ML', 'bruno'], ['lanc', 'Fotos lifestyle pijama família', 'ana'],
        ];
        foreach ($history as $i => [$b, $title, $who]) {
            $doneAt = today()->subDays(13 - $i % 13)->setTime(10 + $i % 7, 15);
            if ($doneAt->isWeekend()) {
                $doneAt->subDays($doneAt->isSunday() ? 2 : 1);
            }
            $doneColumn = $boards[$b]->columns->firstWhere('is_done', true);
            $card = Card::create([
                'board_id' => $boards[$b]->id, 'board_column_id' => $doneColumn->id, 'title' => $title,
                'priority' => 'media', 'due_date' => $doneAt->copy()->addDay(), 'assignee_id' => $u[$who]->id,
                'created_by' => $u['gestor']->id, 'position' => 100 + $i,
            ]);
            $card->forceFill(['completed_at' => $doneAt, 'created_at' => $doneAt->copy()->subDays(3 + $i % 4)])->saveQuietly();
        }

        // Datas de criação espalhadas nas últimas duas semanas (demo mais realista).
        Card::whereNull('completed_at')->get()->each(function (Card $card, int $i) {
            $card->forceFill(['created_at' => now()->subDays(1 + $i % 12)->setTime(9 + $i % 8, 0)])->saveQuietly();
        });

        Card::where('title', 'like', 'Sessão de fotos%')->first()?->comments()->create([
            'user_id' => $u['gestor']->id,
            'body' => 'Ana, prioriza as fotos com fundo branco: a SHEIN e a Shopee estão reprovando as atuais.',
        ]);
        Card::where('title', 'like', 'Associar anúncios%')->first()?->comments()->create([
            'user_id' => $u['bruno']->id,
            'body' => 'Dois anúncios já receberam aviso de moderação. Fazendo esses primeiro.',
        ]);

        foreach ([
            ['Conferir fotos dos lançamentos da semana', 'lanc', 'Revisão', 'ana', 'semanal', [1], null, 2, 'alta', ['Fundo branco nas fotos de marketplace', 'Todas as cores fotografadas', 'Tabela de medidas anexada']],
            ['Revisar preços dos concorrentes no Mercado Livre', 'mp', 'A fazer', 'bruno', 'semanal', [3], null, 1, 'media', []],
            ['Relatório semanal de vendas por canal', 'ops', 'A fazer', 'gestor', 'semanal', [5], null, 0, 'media', []],
            ['Conferir pedidos travados no faturamento', 'ops', 'A fazer', 'diego', 'diaria', null, null, 0, 'alta', []],
            ['Conciliar repasses dos marketplaces', 'ops', 'A fazer', 'gestor', 'mensal', null, 5, 3, 'alta', ['Mercado Livre', 'Shopee', 'SHEIN']],
            ['Conferir anúncios pausados na SHEIN', 'mp', 'A fazer', 'carla', 'semanal', [2, 4], null, 0, 'media', []],
        ] as [$title, $b, $c, $who, $freq, $weekdays, $dom, $dueIn, $priority, $checklist]) {
            RecurringTask::create([
                'title' => $title,
                'board_id' => $boards[$b]->id,
                'board_column_id' => $col($b, $c)->id,
                'assignee_id' => $u[$who]->id,
                'frequency' => $freq,
                'weekdays' => $weekdays,
                'day_of_month' => $dom,
                'due_in_days' => $dueIn,
                'priority' => $priority,
                'checklist' => $checklist,
            ]);
        }
        app(RecurringTaskGenerator::class)->run();

        $year = today()->year;
        $blackFriday = Carbon::create($year, 11, 1)->nthOfMonth(4, Carbon::THURSDAY)->addDay();
        foreach ([
            ['11.11 (data dupla dos marketplaces)', Carbon::create($year, 11, 11), 'comercial', '#f97316'],
            ['Black Friday', $blackFriday, 'comercial', '#111827'],
            ['Cyber Monday', $blackFriday->copy()->addDays(3), 'comercial', '#111827'],
            ['12.12 (data dupla dos marketplaces)', Carbon::create($year, 12, 12), 'comercial', '#f97316'],
            ['Natal', Carbon::create($year, 12, 25), 'comercial', '#dc2626'],
            ['Dia das Mães', Carbon::create($year + 1, 5, 1)->nthOfMonth(2, Carbon::SUNDAY), 'comercial', '#db2777'],
            ['Reunião de planejamento semanal', today()->next(Carbon::MONDAY), 'evento', '#4f46e5'],
            ['Visita do fornecedor de flanela', today()->addWeekdays(8), 'evento', '#0891b2'],
        ] as [$title, $date, $type, $color]) {
            CalendarEvent::create(compact('title', 'date', 'type', 'color'));
        }

        // Primeira execução do agente (simulado): gera problemas e tarefas no quadro Marketplaces.
        mt_srand(21);
        app(MonitorAgent::class)->run();
    }
}
