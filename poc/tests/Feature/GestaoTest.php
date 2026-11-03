<?php

namespace Tests\Feature;

use App\Filament\Pages\Kanban;
use App\Models\Board;
use App\Models\Card;
use App\Models\MonitorIssue;
use App\Models\RecurringTask;
use App\Models\User;
use App\Services\MonitorAgent;
use App\Services\RecurringTaskGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class GestaoTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_home_redirects_to_panel_and_login_is_prefilled(): void
    {
        $this->get('/')->assertRedirect('/app');
        $this->get('/app/login')->assertOk()->assertSee('Versão experimental');
    }

    public function test_all_pages_render_for_the_manager(): void
    {
        $this->actingAs(User::where('email', 'gestor@demo.test')->first());

        foreach (['/app', '/app/quadros', '/app/tarefas', '/app/agenda', '/app/recorrencias', '/app/monitoramento',
            '/app/config/quadros', '/app/config/equipe', '/app/config/etiquetas', '/app/config/eventos'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_recurring_task_creates_one_card_per_day_only(): void
    {
        $board = Board::first();
        $task = RecurringTask::create([
            'title' => 'Teste semanal', 'board_id' => $board->id, 'board_column_id' => $board->columns()->first()->id,
            'frequency' => 'semanal', 'weekdays' => [Carbon::parse('2026-11-04')->dayOfWeekIso], 'due_in_days' => 2,
            'checklist' => ['Item A', 'Item B'],
        ]);
        $generator = app(RecurringTaskGenerator::class);

        $generator->run(Carbon::parse('2026-11-03')); // dia errado
        $this->assertSame(0, $task->cards()->count());

        $generator->run(Carbon::parse('2026-11-04'));
        $generator->run(Carbon::parse('2026-11-04')); // idempotente
        $this->assertSame(1, $task->cards()->count());

        $card = $task->cards()->first();
        $this->assertSame('recorrencia', $card->origin);
        $this->assertSame('2026-11-06', $card->due_date->toDateString());
        $this->assertSame(2, $card->checklistItems()->count());
    }

    public function test_monthly_rule_falls_back_to_last_day_of_short_months(): void
    {
        $task = new RecurringTask(['frequency' => 'mensal', 'day_of_month' => 31]);
        $this->assertTrue($task->isDueOn(Carbon::parse('2026-11-30')));
        $this->assertFalse($task->isDueOn(Carbon::parse('2026-11-29')));
    }

    public function test_agent_deduplicates_and_moves_card_to_validation_when_resolved(): void
    {
        $agent = new class extends MonitorAgent
        {
            public array $next = [];

            protected function detect(): array
            {
                return $this->next;
            }
        };
        $issue = MonitorAgent::issuePool()[0];

        MonitorIssue::query()->delete();
        $cardsBefore = Card::count();

        $agent->next = [$issue];
        $agent->run();
        $agent->run(); // mesmo problema: não cria outra tarefa
        $this->assertSame($cardsBefore + 1, Card::count());

        $found = MonitorIssue::firstOrFail();
        $this->assertSame(2, $found->occurrences);
        $this->assertSame('A fazer', $found->card->column->name);
        $this->assertSame('bruno@demo.test', $found->card->assignee->email);
        $this->assertSame('urgente', $found->card->priority);

        $agent->next = [];
        $agent->run(); // problema sumiu
        $found->refresh();
        $this->assertSame('resolvido', $found->status);
        $this->assertSame('Validar', $found->card->fresh()->column->name);
        $this->assertSame(1, $found->card->comments()->count());
    }

    public function test_moving_card_to_final_column_completes_it(): void
    {
        $this->actingAs(User::first());
        $board = Board::where('name', 'Marketplaces')->first();
        $done = $board->columns()->where('is_done', true)->first();
        $card = $board->cards()->whereNull('completed_at')->first();

        Livewire::test(Kanban::class, ['board' => $board->id])
            ->call('moveCard', $card->id, $done->id, [$card->id]);

        $this->assertNotNull($card->fresh()->completed_at);

        $todo = $board->columns()->where('name', 'A fazer')->first();
        Livewire::test(Kanban::class, ['board' => $board->id])
            ->call('moveCard', $card->id, $todo->id, [$card->id]);
        $this->assertNull($card->fresh()->completed_at);
    }

    public function test_kanban_lists_cards_by_priority_then_due_date(): void
    {
        $this->actingAs(User::first());
        $board = Board::where('name', 'Marketplaces')->first();
        $column = $board->columns()->where('name', 'Backlog')->first();
        $column->cards()->delete();

        $make = fn ($title, $priority, $due, $position) => Card::create([
            'board_id' => $board->id, 'board_column_id' => $column->id, 'title' => $title,
            'priority' => $priority, 'due_date' => $due, 'position' => $position,
        ]);
        $make('baixa', 'baixa', '2026-11-01', 0);
        $make('media sem prazo', 'media', null, 1);
        $make('media com prazo', 'media', '2026-11-20', 2);
        $make('urgente', 'urgente', '2026-12-01', 3);
        $make('alta', 'alta', null, 4);

        $columns = Livewire::test(Kanban::class, ['board' => $board->id])->viewData('columns');

        $this->assertSame(
            ['urgente', 'alta', 'media com prazo', 'media sem prazo', 'baixa'],
            $columns->firstWhere('id', $column->id)->cards->pluck('title')->all(),
        );
    }

    public function test_cannot_move_card_to_a_column_of_another_board(): void
    {
        $this->actingAs(User::first());
        [$a, $b] = Board::orderBy('position')->take(2)->get();
        $card = $a->cards()->first();

        Livewire::test(Kanban::class, ['board' => $a->id])
            ->call('moveCard', $card->id, $b->columns()->first()->id, [$card->id])
            ->assertStatus(404);
    }
}
