<?php

namespace App\Filament\Widgets;

use App\Filament\Support\CardActions;
use App\Models\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingDeadlines extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = ['md' => 2, 'xl' => 2];

    protected static ?string $heading = 'Próximas entregas e atrasos';

    public function table(Table $table): Table
    {
        return $table
            ->query(Card::query()->open()->whereNotNull('due_date')->whereDate('due_date', '<=', today()->addDays(7))->with(['assignee', 'board']))
            ->defaultSort('due_date')
            ->paginated([5])
            ->columns([
                TextColumn::make('title')->label('Tarefa')->limit(70)->tooltip(fn (Card $r) => $r->title)
                    ->description(fn (Card $r) => $r->board->name.' · '.($r->assignee?->name ?? 'sem responsável')),
                TextColumn::make('priority')->label('Prioridade')->badge()
                    ->formatStateUsing(fn ($state) => Card::PRIORITIES[$state] ?? $state)
                    ->color(fn ($state) => Card::PRIORITY_COLORS[$state] ?? 'gray'),
                TextColumn::make('due_date')->label('Prazo')->date('d/m')
                    ->badge()
                    ->color(fn (Card $r) => $r->isOverdue() ? 'danger' : ($r->due_date->isToday() ? 'warning' : 'gray')),
            ])
            ->recordAction('editCard')
            ->recordActions([CardActions::edit('editCard')]);
    }
}
