<?php

namespace App\Filament\Resources\Cards\Pages;

use App\Filament\Resources\Cards\CardResource;
use App\Filament\Support\CardActions;
use App\Models\Card;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageCards extends ManageRecords
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [CardActions::create('createCard')];
    }

    public function getTabs(): array
    {
        return [
            'minhas' => Tab::make('Minhas tarefas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assignee_id', auth()->id()))
                ->badge(fn () => Card::open()->where('assignee_id', auth()->id())->count()),
            'todas' => Tab::make('Toda a equipe'),
        ];
    }
}
