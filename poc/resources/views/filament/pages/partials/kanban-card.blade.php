@php
    $border = match ($card->priority) {
        'urgente' => 'border-l-danger-500',
        'alta' => 'border-l-warning-500',
        'media' => 'border-l-info-500',
        default => 'border-l-gray-300 dark:border-l-gray-600',
    };
    $overdue = $card->isOverdue();
    $dueToday = ! $card->completed_at && $card->due_date?->isToday();
@endphp
<article wire:key="card-{{ $card->id }}" data-card-id="{{ $card->id }}"
         wire:click="mountAction('editCard', { card: {{ $card->id }} })"
         @class([
             'group cursor-pointer rounded-lg border-l-4 bg-white p-3 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md hover:ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10',
             $border,
             'opacity-70' => $doneColumn,
         ])>
    @if ($card->labels->isNotEmpty() || $card->origin !== 'manual')
        <div class="mb-2 flex flex-wrap gap-1">
            @if ($card->origin === 'agente')
                <x-filament::badge color="danger" size="sm" icon="heroicon-m-cpu-chip">Agente</x-filament::badge>
            @elseif ($card->origin === 'recorrencia')
                <x-filament::badge color="primary" size="sm" icon="heroicon-m-arrow-path">Recorrente</x-filament::badge>
            @endif
            @foreach ($card->labels as $label)
                <x-filament::badge :color="$label->color" size="sm">{{ $label->name }}</x-filament::badge>
            @endforeach
        </div>
    @endif

    <p @class(['text-sm font-medium leading-snug text-gray-900 dark:text-gray-100', 'line-through' => $doneColumn])>{{ $card->title }}</p>

    <div class="mt-3 flex items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
        <div class="flex flex-wrap items-center gap-3">
            @if ($card->due_date)
                <span @class([
                    'inline-flex items-center gap-1 rounded px-1.5 py-0.5',
                    'bg-danger-50 font-semibold text-danger-700 dark:bg-danger-500/15 dark:text-danger-300' => $overdue,
                    'bg-warning-50 font-semibold text-warning-700 dark:bg-warning-500/15 dark:text-warning-300' => $dueToday,
                ]) title="{{ $overdue ? 'Atrasada' : 'Prazo' }}">
                    <x-filament::icon icon="heroicon-m-calendar" class="size-3.5" />
                    {{ $dueToday ? 'Hoje' : $card->due_date->format('d/m') }}
                </span>
            @endif
            @if ($card->checklist_items_count)
                <span @class(['inline-flex items-center gap-1', 'text-success-600 dark:text-success-400' => $card->checklist_done_count === $card->checklist_items_count])>
                    <x-filament::icon icon="heroicon-m-check-circle" class="size-3.5" />
                    {{ $card->checklist_done_count }}/{{ $card->checklist_items_count }}
                </span>
            @endif
            @if ($card->comments_count)
                <span class="inline-flex items-center gap-1">
                    <x-filament::icon icon="heroicon-m-chat-bubble-left" class="size-3.5" />
                    {{ $card->comments_count }}
                </span>
            @endif
        </div>
        @if ($card->assignee)
            <x-avatar-initials :user="$card->assignee" size="xs" />
        @endif
    </div>
</article>
