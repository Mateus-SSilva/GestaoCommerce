<x-filament-widgets::widget>
    <x-filament::section heading="Saúde dos canais" description="Problemas abertos encontrados pelo agente">
        <div class="space-y-3">
            @foreach ($channels as $c)
                <a href="{{ \App\Filament\Resources\MonitorIssues\MonitorIssueResource::getUrl('index', ['filters' => ['channel' => ['value' => $c['name']]]]) }}"
                   class="flex items-center justify-between rounded-lg px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-white/5">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $c['name'] }}</span>
                    <span class="flex items-center gap-2">
                        @if ($c['critical'])
                            <x-filament::badge color="danger" size="sm">{{ $c['critical'] }} crítico</x-filament::badge>
                        @endif
                        <x-filament::badge :color="$c['open'] ? 'warning' : 'success'" size="sm">
                            {{ $c['open'] ? $c['open'].' aberto(s)' : 'OK' }}
                        </x-filament::badge>
                    </span>
                </a>
            @endforeach
        </div>
        @if ($lastRun)
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">Última verificação: {{ $lastRun->created_at->diffForHumans() }} (simulação)</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
