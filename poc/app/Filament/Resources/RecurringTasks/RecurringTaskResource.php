<?php

namespace App\Filament\Resources\RecurringTasks;

use App\Filament\Resources\RecurringTasks\Pages\ManageRecurringTasks;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Card;
use App\Models\RecurringTask;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class RecurringTaskResource extends Resource
{
    protected static ?string $model = RecurringTask::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static string | UnitEnum | null $navigationGroup = 'Equipe';

    protected static ?string $navigationLabel = 'Recorrências';

    protected static ?string $modelLabel = 'tarefa recorrente';

    protected static ?string $pluralModelLabel = 'tarefas recorrentes';

    protected static ?string $slug = 'recorrencias';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('O que fazer')->columnSpanFull()->schema([
                TextInput::make('title')->label('Título da tarefa')->required()->placeholder('Ex.: Conferir fotos dos lançamentos da semana'),
                Textarea::make('description')->label('Instruções')->rows(3),
                TagsInput::make('checklist')->label('Checklist')->placeholder('Digite um item e tecle Enter'),
            ]),
            Section::make('Quando')->schema([
                Select::make('frequency')->label('Frequência')->options(RecurringTask::FREQUENCIES)->required()->live()->native(false),
                CheckboxList::make('weekdays')->label('Dias da semana')->options(RecurringTask::WEEKDAYS)->columns(4)
                    ->visible(fn (Get $get) => $get('frequency') === 'semanal')->required(fn (Get $get) => $get('frequency') === 'semanal'),
                TextInput::make('day_of_month')->label('Dia do mês')->numeric()->minValue(1)->maxValue(31)
                    ->visible(fn (Get $get) => $get('frequency') === 'mensal')->required(fn (Get $get) => $get('frequency') === 'mensal'),
                TextInput::make('due_in_days')->label('Prazo (dias após a criação)')->numeric()->minValue(0)->default(0)->required(),
                Toggle::make('is_active')->label('Ativa')->default(true),
            ]),
            Section::make('Para quem e onde')->schema([
                Select::make('assignee_id')->label('Responsável')->options(fn () => User::orderBy('name')->pluck('name', 'id'))->native(false),
                Select::make('priority')->label('Prioridade')->options(Card::PRIORITIES)->default('media')->required()->native(false),
                Select::make('board_id')->label('Quadro')->options(fn () => Board::orderBy('position')->pluck('name', 'id'))->required()->live()->native(false)
                    ->afterStateUpdated(fn (Set $set, $state) => $set('board_column_id', BoardColumn::where('board_id', $state)->orderBy('position')->value('id'))),
                Select::make('board_column_id')->label('Coluna')->required()->native(false)
                    ->options(fn (Get $get) => BoardColumn::where('board_id', $get('board_id'))->orderBy('position')->pluck('name', 'id')),
            ]),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['assignee', 'board', 'column'])->withCount('cards'))
            ->columns([
                TextColumn::make('title')->label('Tarefa')->searchable()->wrap()->description(fn (RecurringTask $r) => $r->board->name.' › '.$r->column->name),
                TextColumn::make('frequency')->label('Regra')->formatStateUsing(fn ($state, RecurringTask $r) => $r->describe()),
                TextColumn::make('assignee.name')->label('Responsável')->placeholder('—'),
                TextColumn::make('next')->label('Próximas')->state(fn (RecurringTask $r) => $r->is_active ? collect($r->nextOccurrences(3))->map->format('d/m')->join(', ') : '—'),
                TextColumn::make('cards_count')->label('Criadas')->numeric()->alignCenter(),
                TextColumn::make('last_generated_on')->label('Última')->date('d/m/Y')->placeholder('—'),
                ToggleColumn::make('is_active')->label('Ativa'),
            ])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageRecurringTasks::route('/')];
    }
}
