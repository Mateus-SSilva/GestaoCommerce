<?php

namespace App\Filament\Widgets;

use App\Models\MonitorIssue;
use App\Models\MonitorRun;
use App\Services\MonitorAgent;
use Filament\Widgets\Widget;

class ChannelHealth extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.channel-health';

    protected function getViewData(): array
    {
        $channels = collect(MonitorAgent::ROUTING)->keys()->map(function ($channel) {
            $open = MonitorIssue::where('channel', $channel)->where('status', 'aberto');

            return [
                'name' => $channel,
                'open' => (clone $open)->count(),
                'critical' => (clone $open)->where('severity', 'critico')->count(),
            ];
        });

        return ['channels' => $channels, 'lastRun' => MonitorRun::latest()->first()];
    }
}
