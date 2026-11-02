<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Painel do gestor';

    protected static ?string $navigationLabel = 'Painel';

    public function getSubheading(): ?string
    {
        return 'Olá, '.str(auth()->user()->name)->before(' ').'! Visão geral da equipe em '.now()->translatedFormat('l, d \d\e F');
    }

    public function getColumns(): int | array
    {
        return ['md' => 2, 'xl' => 3];
    }
}
