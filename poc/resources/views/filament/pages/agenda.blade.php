<x-filament-panels::page>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <x-filament::icon-button icon="heroicon-m-chevron-left" wire:click="previousMonth" label="Mês anterior" color="gray" />
            <h2 class="min-w-48 text-center text-lg font-semibold text-gray-900 dark:text-white">{{ $monthLabel }}</h2>
            <x-filament::icon-button icon="heroicon-m-chevron-right" wire:click="nextMonth" label="Próximo mês" color="gray" />
            <x-filament::button size="sm" color="gray" wire:click="today">Hoje</x-filament::button>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-filament::input.wrapper class="w-56">
                <x-filament::input.select wire:model.live="responsavel">
                    <option value="">Todos os responsáveis</option>
                    <option value="eu">Somente minhas</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
            <x-filament::input.wrapper class="w-48">
                <x-filament::input.select wire:model.live="quadro">
                    <option value="">Todos os quadros</option>
                    @foreach ($boards as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="min-w-[56rem] overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
                @foreach (['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'] as $wd)
                    <div class="px-2 py-2 text-center">{{ $wd }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7">
                @foreach ($days as $day)
                    <div @class([
                        'min-h-32 border-b border-r border-gray-100 p-1.5 dark:border-white/5',
                        'bg-gray-50/60 dark:bg-white/[0.02]' => ! $day['inMonth'],
                    ])>
                        <div class="mb-1 flex items-center justify-between px-1">
                            <span @class([
                                'flex size-6 items-center justify-center rounded-full text-xs font-medium',
                                'bg-primary-600 text-white' => $day['date']->isToday(),
                                'text-gray-400 dark:text-gray-500' => ! $day['inMonth'] && ! $day['date']->isToday(),
                                'text-gray-700 dark:text-gray-200' => $day['inMonth'] && ! $day['date']->isToday(),
                            ])>{{ $day['date']->day }}</span>
                            @if ($day['cards']->count() > 3)
                                <span class="text-[10px] text-gray-400">{{ $day['cards']->count() }} tarefas</span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            @foreach ($day['events'] as $event)
                                <div class="truncate rounded px-1.5 py-0.5 text-[11px] font-semibold text-white" style="background-color: {{ $event->color }}" title="{{ $event->title }}">
                                    {{ $event->type === 'comercial' ? '★ ' : '' }}{{ $event->title }}
                                </div>
                            @endforeach
                            @foreach ($day['cards'] as $card)
                                @php($overdue = $card->isOverdue())
                                <button type="button" wire:click="mountAction('editCard', { card: {{ $card->id }} })"
                                        @class([
                                            'flex w-full items-center gap-1.5 truncate rounded border-l-2 px-1.5 py-0.5 text-left text-[11px] transition hover:bg-gray-100 dark:hover:bg-white/10',
                                            'border-danger-500 bg-danger-50 text-danger-800 dark:bg-danger-500/10 dark:text-danger-200' => $overdue,
                                            'text-gray-400 line-through dark:text-gray-500' => $card->completed_at,
                                            'text-gray-700 dark:text-gray-200' => ! $overdue && ! $card->completed_at,
                                        ])
                                        style="{{ $overdue ? '' : 'border-left-color: '.$card->board->color }}"
                                        title="{{ $card->title }} — {{ $card->assignee?->name ?? 'sem responsável' }}">
                                    @if ($card->assignee)
                                        <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $card->assignee->color }}"></span>
                                    @endif
                                    <span class="truncate">{{ $card->title }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
        <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-danger-500"></span> Atrasada</span>
        <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-gray-400"></span> Cor da borda = quadro · bolinha = responsável</span>
        <span class="inline-flex items-center gap-1.5">★ Data comercial</span>
    </div>
</x-filament-panels::page>
