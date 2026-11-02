<?php

namespace App\Filament\Resources\MonitorIssues\Pages;

use App\Filament\Resources\MonitorIssues\MonitorIssueResource;
use App\Models\MonitorRun;
use App\Services\MonitorAgent;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ListMonitorIssues extends ListRecords
{
    protected static string $resource = MonitorIssueResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Agente de monitoramento';
    }

    public function getSubheading(): string | Htmlable | null
    {
        $last = MonitorRun::latest()->first();

        return new HtmlString(
            '<span class="inline-flex items-center gap-1 rounded-md bg-warning-50 px-2 py-0.5 text-xs font-semibold text-warning-700 ring-1 ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-400">SIMULAÇÃO · dados fictícios</span> '
            .'Todo dia o agente verifica os anúncios em cada canal e cria uma tarefa para o responsável quando encontra um problema. '
            .'Na Fase 2 os dados virão das APIs oficiais (Mercado Livre, Shopee, Magazord).'
            .($last ? ' Última verificação: '.$last->created_at->diffForHumans().'.' : '')
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('run')
                ->label('Executar verificação agora')
                ->icon(Heroicon::OutlinedPlay)
                ->action(function (MonitorAgent $agent) {
                    $run = $agent->run();
                    Notification::make()
                        ->title('Verificação concluída')
                        ->body("{$run->new_issues} novo(s) problema(s) · {$run->updated_issues} ainda aberto(s) · {$run->resolved_issues} resolvido(s) · {$run->cards_created} tarefa(s) criada(s)")
                        ->success()
                        ->send();
                }),
        ];
    }
}
