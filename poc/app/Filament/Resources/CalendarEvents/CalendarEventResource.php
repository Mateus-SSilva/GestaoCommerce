<?php

namespace App\Filament\Resources\CalendarEvents;

use App\Filament\Resources\CalendarEvents\Pages\ManageCalendarEvents;
use App\Models\CalendarEvent;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedStar;

    protected static string | UnitEnum | null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Datas e eventos';

    protected static ?string $modelLabel = 'evento';

    protected static ?string $slug = 'config/eventos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Título')->required(),
            DatePicker::make('date')->label('Data')->required()->native(false)->displayFormat('d/m/Y'),
            Select::make('type')->label('Tipo')->options(['evento' => 'Evento interno', 'comercial' => 'Data comercial'])->required(),
            ColorPicker::make('color')->label('Cor'),
            Textarea::make('description')->label('Descrição')->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date')
            ->columns([
                ColorColumn::make('color')->label(''),
                TextColumn::make('title')->label('Evento')->searchable(),
                TextColumn::make('date')->label('Data')->date('d/m/Y')->sortable(),
                TextColumn::make('type')->label('Tipo')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'comercial' ? 'Data comercial' : 'Evento interno')
                    ->color(fn ($state) => $state === 'comercial' ? 'warning' : 'info'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageCalendarEvents::route('/')];
    }
}
