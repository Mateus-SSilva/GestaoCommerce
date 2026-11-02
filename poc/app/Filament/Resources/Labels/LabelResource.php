<?php

namespace App\Filament\Resources\Labels;

use App\Filament\Resources\Labels\Pages\ManageLabels;
use App\Models\Label;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LabelResource extends Resource
{
    protected static ?string $model = Label::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTag;

    protected static string | UnitEnum | null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Etiquetas';

    protected static ?string $modelLabel = 'etiqueta';

    protected static ?string $slug = 'config/etiquetas';

    public const COLORS = ['primary' => 'Roxo', 'info' => 'Azul', 'success' => 'Verde', 'warning' => 'Amarelo', 'danger' => 'Vermelho', 'gray' => 'Cinza'];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nome')->required(),
            Select::make('color')->label('Cor')->options(self::COLORS)->required()->default('gray'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('cards'))
            ->columns([
                TextColumn::make('name')->label('Etiqueta')->badge()->color(fn (Label $r) => $r->color),
                TextColumn::make('cards_count')->label('Tarefas')->alignCenter(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageLabels::route('/')];
    }
}
