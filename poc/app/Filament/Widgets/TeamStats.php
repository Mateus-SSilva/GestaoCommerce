<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Kanban;
use App\Filament\Resources\MonitorIssues\MonitorIssueResource;
use App\Models\Card;
use App\Models\MonitorIssue;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeamStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        $doneByDay = collect(range(6, 0))->map(fn ($i) => Card::whereDate('completed_at', today()->subDays($i))->count())->all();

        return [
            Stat::make('Tarefas em aberto', Card::open()->count())
                ->description('em todos os quadros')
                ->icon('heroicon-o-rectangle-stack')
                ->url(Kanban::getUrl()),
            Stat::make('Atrasadas', Card::overdue()->count())
                ->description('prazo vencido')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            Stat::make('Vencem hoje', Card::open()->whereDate('due_date', today())->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Concluídas (7 dias)', array_sum($doneByDay))
                ->icon('heroicon-o-check-badge')
                ->chart($doneByDay)
                ->color('success'),
            Stat::make('Problemas nos canais', MonitorIssue::where('status', 'aberto')->count())
                ->description(MonitorIssue::where('status', 'aberto')->where('severity', 'critico')->count().' crítico(s) · agente')
                ->icon('heroicon-o-cpu-chip')
                ->color('danger')
                ->url(MonitorIssueResource::getUrl()),
        ];
    }
}
