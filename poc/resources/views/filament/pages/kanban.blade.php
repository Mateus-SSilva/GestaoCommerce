<x-filament-panels::page>
    {{-- Seleção de quadro --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($boards as $b)
            <a href="{{ static::getUrl(['board' => $b->id]) }}"
               @class([
                   'inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                   'bg-white shadow-sm ring-1 ring-gray-950/10 dark:bg-white/10 dark:ring-white/20' => $b->id === $board,
                   'text-gray-600 hover:bg-white/60 dark:text-gray-400 dark:hover:bg-white/5' => $b->id !== $board,
               ])>
                <span class="size-2.5 rounded-full" style="background-color: {{ $b->color }}"></span>
                {{ $b->name }}
                <span class="rounded-full bg-gray-100 px-1.5 text-xs text-gray-600 dark:bg-white/10 dark:text-gray-300">{{ $b->open_count }}</span>
            </a>
        @endforeach
    </div>

    {{-- Filtros --}}
    <div class="flex flex-wrap items-center gap-3">
        <x-filament::input.wrapper class="w-64">
            <x-filament::input type="search" wire:model.live.debounce.300ms="busca" placeholder="Buscar tarefa…" />
        </x-filament::input.wrapper>
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
            <x-filament::input.select wire:model.live="prioridade">
                <option value="">Todas as prioridades</option>
                @foreach (\App\Models\Card::PRIORITIES as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
        <span class="text-xs text-gray-500 dark:text-gray-400">Ordenados por prioridade e prazo · arraste entre colunas · clique para abrir</span>
    </div>

    {{-- Colunas --}}
    <div class="-mx-4 flex gap-4 overflow-x-auto px-4 pb-4 md:mx-0 md:px-0">
        @foreach ($columns as $column)
            @php($over = $column->wip_limit && $column->cards->count() > $column->wip_limit)
            <section wire:key="column-{{ $column->id }}" class="flex w-80 shrink-0 flex-col rounded-xl bg-gray-100/80 dark:bg-white/5">
                <header class="flex items-center justify-between px-3 pb-2 pt-3">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $column->name }}</h3>
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs font-medium',
                            'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-300' => $over,
                            'bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-300' => ! $over,
                        ]) title="{{ $column->wip_limit ? 'Limite: '.$column->wip_limit : '' }}">
                            {{ $column->cards->count() }}{{ $column->wip_limit ? ' / '.$column->wip_limit : '' }}
                        </span>
                        @if ($column->is_done)
                            <x-filament::icon icon="heroicon-m-check-circle" class="size-4 text-success-500" />
                        @endif
                    </div>
                    <button type="button" wire:click="mountAction('createCard', { column: {{ $column->id }} })"
                            class="rounded-md p-1 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-white/10 dark:hover:text-gray-200" title="Adicionar tarefa">
                        <x-filament::icon icon="heroicon-m-plus" class="size-4" />
                    </button>
                </header>

                <div class="flex min-h-24 flex-1 flex-col gap-2 px-2 pb-3"
                     data-column-id="{{ $column->id }}"
                     x-data
                     x-init="window.kanbanReady ? window.kanbanInit($el, $wire) : window.addEventListener('kanban:ready', () => window.kanbanInit($el, $wire), { once: true })">
                    @foreach ($column->cards as $card)
                        @include('filament.pages.partials.kanban-card', ['card' => $card, 'doneColumn' => $column->is_done])
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
