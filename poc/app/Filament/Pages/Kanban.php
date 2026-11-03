<?php

namespace App\Filament\Pages;

use App\Filament\Support\CardActions;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Card;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use UnitEnum;

class Kanban extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static string | UnitEnum | null $navigationGroup = 'Equipe';

    protected static ?string $navigationLabel = 'Quadros';

    protected static ?string $title = 'Quadros';

    protected static ?string $slug = 'quadros';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.kanban';

    #[Url]
    public ?int $board = null;

    #[Url]
    public ?string $responsavel = null;

    #[Url]
    public ?string $prioridade = null;

    public string $busca = '';

    public function mount(): void
    {
        $this->board ??= Board::orderBy('position')->value('id');
    }

    public function getTitle(): string
    {
        return $this->currentBoard()?->name ?? 'Quadros';
    }

    public function getSubheading(): ?string
    {
        return $this->currentBoard()?->description;
    }

    public function currentBoard(): ?Board
    {
        return once(fn () => Board::find($this->board));
    }

    protected function getHeaderActions(): array
    {
        return [$this->createCardAction()];
    }

    public function createCardAction(): Action
    {
        return CardActions::create('createCard', fn () => $this->currentBoard()?->columns()->orderBy('position')->value('id'));
    }

    public function editCardAction(): Action
    {
        return CardActions::edit('editCard');
    }

    /** Chamado pelo drag & drop (SortableJS). */
    public function moveCard(int $cardId, int $columnId, array $orderedIds): void
    {
        $column = BoardColumn::where('board_id', $this->board)->findOrFail($columnId);
        $card = Card::where('board_id', $this->board)->findOrFail($cardId);

        DB::transaction(function () use ($card, $column, $orderedIds) {
            if ($card->board_column_id !== $column->id) {
                $card->board_column_id = $column->id;
                $card->save(); // dispara a regra de concluído (coluna final)
            }

            foreach (array_values($orderedIds) as $position => $id) {
                Card::where('board_column_id', $column->id)->whereKey((int) $id)->update(['position' => $position]);
            }
        });

        if ($column->wip_limit && $column->cards()->count() > $column->wip_limit) {
            Notification::make()
                ->title("Limite da coluna \"{$column->name}\" ultrapassado")
                ->body("O limite combinado é de {$column->wip_limit} tarefas em andamento.")
                ->warning()
                ->send();
        }
    }

    protected function getViewData(): array
    {
        $board = $this->currentBoard();

        $columns = $board?->columns()->with(['cards' => function ($q) {
            $q->with(['assignee', 'labels'])
                ->withCount(['comments', 'checklistItems', 'checklistItems as checklist_done_count' => fn ($c) => $c->where('is_done', true)])
                ->when($this->responsavel === 'eu', fn ($q) => $q->where('assignee_id', auth()->id()))
                ->when(is_numeric($this->responsavel), fn ($q) => $q->where('assignee_id', (int) $this->responsavel))
                ->when($this->prioridade, fn ($q) => $q->where('priority', $this->prioridade))
                ->when(filled($this->busca), fn ($q) => $q->where('title', 'like', '%'.$this->busca.'%'))
                ->reorder()
                ->orderByUrgency();
        }])->get() ?? collect();

        return [
            'boards' => Board::orderBy('position')->withCount(['cards as open_count' => fn ($q) => $q->whereNull('completed_at')])->get(),
            'columns' => $columns,
            'users' => User::orderBy('name')->get(),
        ];
    }
}
