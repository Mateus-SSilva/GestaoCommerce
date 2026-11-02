<?php

namespace App\Filament\Resources\Boards;

use App\Filament\Pages\Kanban;
use App\Filament\Resources\Boards\Pages\ManageBoards;
use App\Models\Board;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BoardResource extends Resource
{
    protected static ?string $model = Board::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string | UnitEnum | null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Quadros e colunas';

    protected static ?string $modelLabel = 'quadro';

    protected static ?string $slug = 'config/quadros';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nome')->required(),
            ColorPicker::make('color')->label('Cor')->default('#6366f1'),
            TextInput::make('description')->label('Descrição')->columnSpanFull(),
            Repeater::make('columns')
                ->label('Colunas')
                ->relationship()
                ->orderColumn('position')
                ->table([
                    TableColumn::make('Nome'),
                    TableColumn::make('Limite WIP')->width('130px'),
                    TableColumn::make('Coluna final')->width('120px'),
                ])
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('wip_limit')->numeric()->minValue(1),
                    Toggle::make('is_done'),
                ])
                ->defaultItems(3)
                ->addActionLabel('Adicionar coluna')
                ->helperText('"Coluna final" marca as tarefas como concluídas. "Limite WIP" alerta quando há tarefas demais em andamento.')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->modifyQueryUsing(fn ($query) => $query->withCount(['columns', 'cards']))
            ->columns([
                ColorColumn::make('color')->label(''),
                TextColumn::make('name')->label('Quadro')->description(fn (Board $r) => $r->description),
                TextColumn::make('columns_count')->label('Colunas')->alignCenter(),
                TextColumn::make('cards_count')->label('Tarefas')->alignCenter(),
            ])
            ->recordActions([
                Action::make('open')->label('Abrir')->icon(Heroicon::OutlinedViewColumns)->url(fn (Board $r) => Kanban::getUrl(['board' => $r->id])),
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBoards::route('/')];
    }
}
