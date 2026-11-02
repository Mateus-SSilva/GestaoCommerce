<?php

namespace App\Filament\Resources\RecurringTasks\Pages;

use App\Filament\Resources\RecurringTasks\RecurringTaskResource;
use App\Services\RecurringTaskGenerator;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageRecurringTasks extends ManageRecords
{
    protected static string $resource = RecurringTaskResource::class;

    public function getSubheading(): ?string
    {
        return 'As tarefas são criadas automaticamente todos os dias às 06:00, no quadro e coluna definidos, já com responsável e prazo.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Gerar tarefas de hoje')
                ->icon(Heroicon::OutlinedBolt)
                ->color('gray')
                ->action(function (RecurringTaskGenerator $generator) {
                    $count = $generator->run();
                    Notification::make()
                        ->title($count ? "{$count} tarefa(s) criada(s)" : 'Nada a gerar hoje')
                        ->body($count ? 'Confira nos quadros.' : 'As tarefas de hoje já foram criadas ou nenhuma regra vence hoje.')
                        ->success()->send();
                }),
            CreateAction::make()->label('Nova recorrência')->slideOver(),
        ];
    }
}
