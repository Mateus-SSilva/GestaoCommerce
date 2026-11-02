<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class TeamWorkload extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.team-workload';

    protected int | string | array $columnSpan = ['md' => 2, 'xl' => 2];

    protected function getViewData(): array
    {
        $users = User::query()
            ->withCount([
                'assignedCards as open_count' => fn ($q) => $q->whereNull('completed_at'),
                'assignedCards as overdue_count' => fn ($q) => $q->whereNull('completed_at')->whereDate('due_date', '<', today()),
                'assignedCards as doing_count' => fn ($q) => $q->whereNull('completed_at')->whereHas('column', fn ($c) => $c->whereIn('name', ['Fazendo', 'Fotos', 'Cadastro', 'Revisão', 'Validar'])),
                'assignedCards as done_week_count' => fn ($q) => $q->where('completed_at', '>=', now()->subDays(7)),
            ])
            ->orderByDesc('open_count')
            ->get();

        return ['users' => $users, 'max' => max(1, $users->max('open_count'))];
    }
}
