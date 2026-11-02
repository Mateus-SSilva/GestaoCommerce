<x-filament-widgets::widget>
    <x-filament::section heading="Carga da equipe" description="Tarefas em aberto por pessoa">
        <div class="space-y-4">
            @foreach ($users as $user)
                <div class="flex items-center gap-3">
                    <x-avatar-initials :user="$user" />
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline justify-between gap-2">
                            <div class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                {{ $user->name }} <span class="font-normal text-gray-500 dark:text-gray-400">· {{ $user->job_title }}</span>
                            </div>
                            <div class="flex shrink-0 gap-3 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $user->doing_count }} em andamento</span>
                                @if ($user->overdue_count)
                                    <span class="font-semibold text-danger-600 dark:text-danger-400">{{ $user->overdue_count }} atrasada(s)</span>
                                @endif
                                <span class="text-success-600 dark:text-success-400">{{ $user->done_week_count }} feita(s) na semana</span>
                            </div>
                        </div>
                        <div class="mt-1.5 flex h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                            @php($w = fn ($n) => round($n / $max * 100, 1))
                            <div class="bg-danger-500" style="width: {{ $w($user->overdue_count) }}%"></div>
                            <div class="bg-primary-500" style="width: {{ $w(max(0, $user->open_count - $user->overdue_count)) }}%"></div>
                        </div>
                    </div>
                    <div class="w-8 text-right text-sm font-semibold text-gray-900 dark:text-white">{{ $user->open_count }}</div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
