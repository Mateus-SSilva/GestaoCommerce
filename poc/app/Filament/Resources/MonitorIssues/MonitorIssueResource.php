<?php

namespace App\Filament\Resources\MonitorIssues;

use App\Filament\Pages\Kanban;
use App\Filament\Resources\MonitorIssues\Pages\ListMonitorIssues;
use App\Models\MonitorIssue;
use App\Services\MonitorAgent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class MonitorIssueResource extends Resource
{
    protected static ?string $model = MonitorIssue::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static string | UnitEnum | null $navigationGroup = 'Marketplaces';

    protected static ?string $navigationLabel = 'Agente de monitoramento';

    protected static ?string $modelLabel = 'problema';

    protected static ?string $pluralModelLabel = 'problemas encontrados';

    protected static ?string $slug = 'monitoramento';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = MonitorIssue::where('status', 'aberto')->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('card.assignee'))
            ->defaultSort(fn ($query) => $query->orderByRaw("case severity when 'critico' then 0 when 'alto' then 1 when 'medio' then 2 else 3 end")->orderByDesc('last_seen_at'))
            ->columns([
                TextColumn::make('severity')->label('Severidade')->badge()
                    ->formatStateUsing(fn ($state) => MonitorIssue::SEVERITIES[$state] ?? $state)
                    ->color(fn ($state) => MonitorIssue::SEVERITY_COLORS[$state] ?? 'gray'),
                TextColumn::make('channel')->label('Canal')->badge()->color('gray'),
                TextColumn::make('message')->label('Problema')->wrap()->searchable()->tooltip(fn (MonitorIssue $r) => 'Sugestão: '.$r->suggestion)
                    ->description(fn (MonitorIssue $r) => "{$r->product_name} · SKU {$r->sku} · {$r->listing_ref}"),
                TextColumn::make('suggestion')->label('Sugestão do agente')->wrap()->toggleable(isToggledHiddenByDefault: true)->color('gray'),
                TextColumn::make('card.assignee.name')->label('Responsável')->placeholder('—'),
                TextColumn::make('occurrences')->label('Verificações')->alignCenter()
                    ->tooltip('Em quantas verificações seguidas o problema apareceu'),
                TextColumn::make('first_seen_at')->label('Desde')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->label('Situação')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'aberto' ? 'Aberto' : 'Resolvido')
                    ->color(fn ($state) => $state === 'aberto' ? 'warning' : 'success'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Situação')->options(['aberto' => 'Aberto', 'resolvido' => 'Resolvido'])->default('aberto'),
                SelectFilter::make('channel')->label('Canal')->options(array_combine(array_keys(MonitorAgent::ROUTING), array_keys(MonitorAgent::ROUTING))),
                SelectFilter::make('severity')->label('Severidade')->options(MonitorIssue::SEVERITIES),
            ])
            ->recordActions([
                Action::make('openCard')
                    ->label('Ver tarefa')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->visible(fn (MonitorIssue $r) => (bool) $r->card)
                    ->url(fn (MonitorIssue $r) => Kanban::getUrl(['board' => $r->card?->board_id])),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListMonitorIssues::route('/')];
    }
}
