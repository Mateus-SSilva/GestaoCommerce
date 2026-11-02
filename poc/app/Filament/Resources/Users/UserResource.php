<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string | UnitEnum | null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Equipe';

    protected static ?string $modelLabel = 'pessoa';

    protected static ?string $pluralModelLabel = 'equipe';

    protected static ?string $slug = 'config/equipe';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nome')->required(),
            TextInput::make('email')->label('E-mail')->email()->required()->unique(ignoreRecord: true),
            TextInput::make('job_title')->label('Função')->placeholder('Ex.: Fotos e cadastro'),
            Select::make('role')->label('Perfil')->options(User::ROLES)->default('operador')->required(),
            ColorPicker::make('color')->label('Cor do avatar')->default('#6366f1'),
            TextInput::make('password')->label('Senha')->password()->revealable()
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount(['assignedCards as open_count' => fn ($c) => $c->whereNull('completed_at')]))
            ->columns([
                ColorColumn::make('color')->label(''),
                TextColumn::make('name')->label('Nome')->description(fn (User $r) => $r->job_title)->searchable(),
                TextColumn::make('email')->label('E-mail'),
                TextColumn::make('role')->label('Perfil')->badge()->formatStateUsing(fn ($state) => User::ROLES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'gestor' ? 'primary' : 'gray'),
                TextColumn::make('open_count')->label('Tarefas abertas')->alignCenter(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageUsers::route('/')];
    }
}
