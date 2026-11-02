<?php

namespace App\Filament\Support;

use App\Models\BoardColumn;
use App\Models\Card;
use App\Models\Label;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

/**
 * Ações de cartão reutilizadas no Kanban, na Agenda e na lista de tarefas.
 */
class CardActions
{
    /** Abrir/editar um cartão. Recebe o cartão por $record (tabelas) ou por argumento ['card' => id]. */
    public static function edit(string $name = 'editCard'): Action
    {
        $resolve = fn ($record, array $arguments): ?Card => $record instanceof Card ? $record : Card::find($arguments['card'] ?? null);

        return Action::make($name)
            ->label('Abrir')
            ->icon(Heroicon::OutlinedPencilSquare)
            ->slideOver()
            ->modalWidth(Width::TwoExtraLarge)
            ->modalHeading(fn ($record, array $arguments) => $resolve($record, $arguments)?->title ?? 'Tarefa')
            ->modalDescription(fn ($record, array $arguments) => self::subtitle($resolve($record, $arguments)))
            ->modalSubmitActionLabel('Salvar')
            ->fillForm(function ($record, array $arguments) use ($resolve) {
                $card = $resolve($record, $arguments);

                return $card ? [
                    'title' => $card->title,
                    'board_column_id' => $card->board_column_id,
                    'assignee_id' => $card->assignee_id,
                    'priority' => $card->priority,
                    'due_date' => $card->due_date?->toDateString(),
                    'labels' => $card->labels->pluck('id')->all(),
                    'description' => $card->description,
                    'checklist' => $card->checklistItems->map(fn ($i) => ['title' => $i->title, 'is_done' => $i->is_done])->all(),
                ] : [];
            })
            ->schema(fn ($record, array $arguments) => self::schema($resolve($record, $arguments)))
            ->action(function (array $data, $record, array $arguments) use ($resolve) {
                $card = $resolve($record, $arguments);
                if (! $card) {
                    return;
                }
                self::save($card, $data);
                Notification::make()->title('Tarefa atualizada')->success()->send();
            });
    }

    /** Criar um cartão em uma coluna (argumento ['column' => id]) ou no quadro informado. */
    public static function create(string $name = 'createCard', ?\Closure $defaultColumn = null): Action
    {
        return Action::make($name)
            ->label('Nova tarefa')
            ->icon(Heroicon::OutlinedPlus)
            ->slideOver()
            ->modalWidth(Width::TwoExtraLarge)
            ->modalHeading('Nova tarefa')
            ->modalSubmitActionLabel('Criar')
            ->fillForm(fn (array $arguments) => [
                'board_column_id' => $arguments['column'] ?? ($defaultColumn ? $defaultColumn() : null),
                'priority' => 'media',
                'assignee_id' => auth()->id(),
            ])
            ->schema(fn (array $arguments) => self::schema(null, BoardColumn::find($arguments['column'] ?? ($defaultColumn ? $defaultColumn() : null))?->board_id))
            ->action(function (array $data) {
                $column = BoardColumn::findOrFail($data['board_column_id']);
                $card = new Card(['board_id' => $column->board_id, 'created_by' => auth()->id(), 'position' => 0]);
                Card::where('board_column_id', $column->id)->increment('position');
                self::save($card, $data);
                Notification::make()->title('Tarefa criada')->success()->send();
            });
    }

    public static function schema(?Card $card, ?int $boardId = null): array
    {
        $boardId ??= $card?->board_id;

        $columns = BoardColumn::query()
            ->with('board')
            ->when($boardId, fn ($q) => $q->where('board_id', $boardId))
            ->orderBy('board_id')->orderBy('position')
            ->get()
            ->mapWithKeys(fn ($c) => [$c->id => $boardId ? $c->name : $c->board->name.' › '.$c->name]);

        return [
            TextInput::make('title')->label('Título')->required()->maxLength(255)->columnSpanFull(),
            Grid::make(3)->schema([
                Select::make('board_column_id')->label('Coluna')->options($columns)->required()->native(false),
                Select::make('assignee_id')->label('Responsável')->options(User::orderBy('name')->pluck('name', 'id'))->searchable()->native(false),
                Select::make('priority')->label('Prioridade')->options(Card::PRIORITIES)->required()->native(false),
            ]),
            Grid::make(2)->schema([
                DatePicker::make('due_date')->label('Prazo')->displayFormat('d/m/Y')->native(false)->closeOnDateSelection(),
                Select::make('labels')->label('Etiquetas')->multiple()->options(Label::orderBy('name')->pluck('name', 'id'))->native(false),
            ]),
            Textarea::make('description')->label('Descrição')->rows(4)->columnSpanFull(),
            Repeater::make('checklist')
                ->label('Checklist')
                ->table([
                    TableColumn::make('Feito')->width('70px'),
                    TableColumn::make('Item'),
                ])
                ->schema([
                    Checkbox::make('is_done'),
                    TextInput::make('title')->required(),
                ])
                ->defaultItems(0)
                ->reorderable()
                ->addActionLabel('Adicionar item')
                ->columnSpanFull(),
            Section::make('Comentários')
                ->schema([
                    View::make('filament.components.card-comments')->viewData(['card' => $card]),
                    Textarea::make('new_comment')->label('Novo comentário')->rows(2)->placeholder('Escreva um comentário…'),
                ])
                ->collapsible()
                ->visible((bool) $card)
                ->columnSpanFull(),
        ];
    }

    public static function save(Card $card, array $data): Card
    {
        return DB::transaction(function () use ($card, $data) {
            $movedColumn = $card->exists && (int) $card->board_column_id !== (int) $data['board_column_id'];
            if ($movedColumn) {
                Card::where('board_column_id', $data['board_column_id'])->increment('position');
                $card->position = 0;
            }

            $column = BoardColumn::find($data['board_column_id']);
            $card->fill([
                'title' => $data['title'],
                'board_id' => $column->board_id,
                'board_column_id' => $column->id,
                'assignee_id' => $data['assignee_id'] ?? null,
                'priority' => $data['priority'],
                'due_date' => $data['due_date'] ?? null,
                'description' => $data['description'] ?? null,
            ])->save();

            $card->labels()->sync($data['labels'] ?? []);

            if (array_key_exists('checklist', $data)) {
                $card->checklistItems()->delete();
                foreach (array_values($data['checklist'] ?? []) as $i => $item) {
                    if (filled($item['title'] ?? null)) {
                        $card->checklistItems()->create(['title' => $item['title'], 'is_done' => (bool) ($item['is_done'] ?? false), 'position' => $i]);
                    }
                }
            }

            if (filled($data['new_comment'] ?? null)) {
                $card->comments()->create(['user_id' => auth()->id(), 'body' => $data['new_comment']]);
            }

            return $card;
        });
    }

    protected static function subtitle(?Card $card): ?string
    {
        if (! $card) {
            return null;
        }

        $parts = [$card->board?->name.' › '.$card->column?->name, 'Origem: '.(Card::ORIGINS[$card->origin] ?? $card->origin)];
        if ($card->creator) {
            $parts[] = 'Criada por '.$card->creator->name;
        }
        if ($card->completed_at) {
            $parts[] = 'Concluída em '.$card->completed_at->format('d/m/Y');
        }

        return implode(' · ', $parts);
    }
}
