<?php

namespace App\Filament\Resources\Cards;

use App\Filament\Resources\Cards\Pages\ManageCards;
use App\Filament\Support\CardActions;
use App\Models\Board;
use App\Models\Card;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string | UnitEnum | null $navigationGroup = 'Equipe';

    protected static ?string $navigationLabel = 'Tarefas';

    protected static ?string $modelLabel = 'tarefa';

    protected static ?string $pluralModelLabel = 'tarefas';

    protected static ?string $slug = 'tarefas';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = Card::overdue()->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Tarefas atrasadas';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['assignee', 'board', 'column', 'labels']))
            ->defaultSort('due_date')
            ->columns([
                TextColumn::make('title')->label('Tarefa')->searchable()->wrap()
                    ->description(fn (Card $r) => $r->labels->pluck('name')->join(' · ') ?: null),
                TextColumn::make('board.name')->label('Quadro')->description(fn (Card $r) => $r->column?->name)->sortable(),
                TextColumn::make('assignee.name')->label('Responsável')->placeholder('—')->sortable(),
                TextColumn::make('priority')->label('Prioridade')->badge()
                    ->formatStateUsing(fn ($state) => Card::PRIORITIES[$state] ?? $state)
                    ->color(fn ($state) => Card::PRIORITY_COLORS[$state] ?? 'gray'),
                TextColumn::make('due_date')->label('Prazo')->date('d/m/Y')->sortable()->placeholder('—')
                    ->color(fn (Card $r) => $r->isOverdue() ? 'danger' : null)
                    ->icon(fn (Card $r) => $r->isOverdue() ? Heroicon::ExclamationTriangle : null),
                TextColumn::make('origin')->label('Origem')->badge()
                    ->formatStateUsing(fn ($state) => Card::ORIGINS[$state] ?? $state)
                    ->color(fn ($state) => match ($state) { 'agente' => 'danger', 'recorrencia' => 'primary', default => 'gray' }),
                TextColumn::make('completed_at')->label('Concluída em')->dateTime('d/m/Y H:i')->placeholder('Em aberto')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('assignee_id')->label('Responsável')->options(fn () => User::orderBy('name')->pluck('name', 'id')),
                SelectFilter::make('board_id')->label('Quadro')->options(fn () => Board::orderBy('position')->pluck('name', 'id')),
                SelectFilter::make('priority')->label('Prioridade')->options(Card::PRIORITIES),
                SelectFilter::make('origin')->label('Origem')->options(Card::ORIGINS),
                TernaryFilter::make('situacao')->label('Situação')
                    ->placeholder('Todas')->trueLabel('Em aberto')->falseLabel('Concluídas')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('completed_at'),
                        false: fn (Builder $query) => $query->whereNotNull('completed_at'),
                        blank: fn (Builder $query) => $query,
                    )->default(true),
                Filter::make('atrasadas')->label('Somente atrasadas')->toggle()
                    ->query(fn (Builder $query) => $query->whereNull('completed_at')->whereDate('due_date', '<', today())),
            ])
            ->recordAction('editCard')
            ->recordActions([
                CardActions::edit('editCard'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCards::route('/'),
        ];
    }
}
