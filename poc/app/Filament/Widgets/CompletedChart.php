<?php

namespace App\Filament\Widgets;

use App\Models\Card;
use Filament\Widgets\ChartWidget;

class CompletedChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Entregas (últimos 14 dias)';

    protected ?string $maxHeight = '260px';

    protected int | string | array $columnSpan = ['md' => 2, 'xl' => 1];

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn ($i) => today()->subDays($i));

        return [
            'datasets' => [
                [
                    'label' => 'Concluídas',
                    'data' => $days->map(fn ($d) => Card::whereDate('completed_at', $d)->count())->all(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.25)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Criadas',
                    'data' => $days->map(fn ($d) => Card::whereDate('created_at', $d)->count())->all(),
                    'borderColor' => 'rgb(148, 163, 184)',
                    'borderDash' => [4, 4],
                    'tension' => 0.35,
                ],
            ],
            'labels' => $days->map(fn ($d) => $d->format('d/m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
