<?php

namespace App\Services;

use App\Models\Card;
use App\Models\RecurringTask;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Cria os cartões das tarefas recorrentes que vencem na data informada.
 * Idempotente: uma regra gera no máximo um cartão por dia.
 */
class RecurringTaskGenerator
{
    /** @return int quantidade de cartões criados */
    public function run(?CarbonInterface $date = null): int
    {
        $date = ($date ?? today())->copy()->startOfDay();
        $created = 0;

        RecurringTask::query()->where('is_active', true)->each(function (RecurringTask $task) use ($date, &$created) {
            if (! $task->isDueOn($date)) {
                return;
            }
            if ($task->last_generated_on && $task->last_generated_on->gte($date)) {
                return;
            }

            DB::transaction(function () use ($task, $date) {
                Card::where('board_column_id', $task->board_column_id)->increment('position');

                $card = Card::create([
                    'board_id' => $task->board_id,
                    'board_column_id' => $task->board_column_id,
                    'title' => $task->title.' · '.$date->format('d/m'),
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'due_date' => $date->copy()->addDays($task->due_in_days),
                    'assignee_id' => $task->assignee_id,
                    'origin' => 'recorrencia',
                    'recurring_task_id' => $task->id,
                    'position' => 0,
                ]);

                foreach (array_values($task->checklist ?? []) as $i => $item) {
                    $title = is_array($item) ? ($item['title'] ?? null) : $item;
                    if (filled($title)) {
                        $card->checklistItems()->create(['title' => $title, 'position' => $i]);
                    }
                }

                $task->update(['last_generated_on' => $date]);
            });

            $created++;
        });

        return $created;
    }
}
