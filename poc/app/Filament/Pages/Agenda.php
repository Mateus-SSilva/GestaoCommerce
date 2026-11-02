<?php

namespace App\Filament\Pages;

use App\Filament\Support\CardActions;
use App\Models\Board;
use App\Models\CalendarEvent;
use App\Models\Card;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use UnitEnum;

class Agenda extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string | UnitEnum | null $navigationGroup = 'Equipe';

    protected static ?string $title = 'Agenda de entregas';

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $slug = 'agenda';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.agenda';

    #[Url]
    public ?string $mes = null;

    #[Url]
    public ?string $responsavel = null;

    #[Url]
    public ?string $quadro = null;

    public function mount(): void
    {
        $this->mes ??= today()->format('Y-m');
    }

    public function month(): Carbon
    {
        return rescue(fn () => Carbon::createFromFormat('Y-m-d', $this->mes.'-01')->startOfDay(), today()->startOfMonth(), false);
    }

    public function previousMonth(): void
    {
        $this->mes = $this->month()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->mes = $this->month()->addMonth()->format('Y-m');
    }

    public function today(): void
    {
        $this->mes = today()->format('Y-m');
    }

    public function editCardAction(): Action
    {
        return CardActions::edit('editCard');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('newEvent')
                ->label('Novo evento')
                ->icon(Heroicon::OutlinedPlus)
                ->modalHeading('Novo evento na agenda')
                ->modalWidth('md')
                ->schema([
                    TextInput::make('title')->label('Título')->required(),
                    DatePicker::make('date')->label('Data')->required()->native(false)->displayFormat('d/m/Y')->default(today()),
                    Select::make('type')->label('Tipo')->options(['evento' => 'Evento interno', 'comercial' => 'Data comercial'])->default('evento')->required(),
                    ColorPicker::make('color')->label('Cor')->default('#0ea5e9'),
                ])
                ->action(function (array $data) {
                    CalendarEvent::create($data + ['user_id' => auth()->id()]);
                    Notification::make()->title('Evento criado')->success()->send();
                }),
        ];
    }

    protected function getViewData(): array
    {
        $month = $this->month();
        $start = $month->copy()->startOfWeek(Carbon::MONDAY);
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $cards = Card::query()
            ->with(['assignee', 'board'])
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->when($this->responsavel === 'eu', fn ($q) => $q->where('assignee_id', auth()->id()))
            ->when(is_numeric($this->responsavel), fn ($q) => $q->where('assignee_id', (int) $this->responsavel))
            ->when(is_numeric($this->quadro), fn ($q) => $q->where('board_id', (int) $this->quadro))
            ->orderByRaw('completed_at is not null')
            ->orderByRaw("case priority when 'urgente' then 0 when 'alta' then 1 when 'media' then 2 else 3 end")
            ->get()
            ->groupBy(fn ($c) => $c->due_date->toDateString());

        $events = CalendarEvent::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy(fn ($e) => $e->date->toDateString());

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->toDateString();
            $days[] = [
                'date' => $d->copy(),
                'inMonth' => $d->month === $month->month,
                'cards' => $cards->get($key, collect()),
                'events' => $events->get($key, collect()),
            ];
        }

        return [
            'days' => $days,
            'monthLabel' => ucfirst($month->translatedFormat('F \d\e Y')),
            'users' => User::orderBy('name')->get(),
            'boards' => Board::orderBy('position')->get(),
        ];
    }
}
