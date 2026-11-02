@props(['user', 'size' => 'md'])
@php
    $initials = collect(explode(' ', $user->name))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('');
    $classes = match ($size) {
        'sm' => 'size-7 text-[11px]',
        'xs' => 'size-6 text-[10px]',
        default => 'size-8 text-xs',
    };
@endphp
<span {{ $attributes->class(["inline-flex shrink-0 items-center justify-center rounded-full font-semibold text-white ring-2 ring-white dark:ring-gray-900 $classes"]) }}
      style="background-color: {{ $user->color ?? '#6366f1' }}" title="{{ $user->name }}">{{ mb_strtoupper($initials) }}</span>
