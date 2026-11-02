@php($comments = $card?->comments()->with('user')->get() ?? collect())
<div class="space-y-3">
    @forelse ($comments as $comment)
        <div class="flex gap-3">
            @if ($comment->user)
                <x-avatar-initials :user="$comment->user" size="sm" />
            @else
                <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm dark:bg-white/10">🤖</div>
            @endif
            <div class="min-w-0 flex-1 rounded-lg bg-gray-50 px-3 py-2 dark:bg-white/5">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $comment->user?->name ?? 'Agente de monitoramento' }}</span>
                    · {{ $comment->created_at->diffForHumans() }}
                </div>
                <div class="mt-0.5 whitespace-pre-line text-sm text-gray-800 dark:text-gray-100">{{ $comment->body }}</div>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum comentário ainda.</p>
    @endforelse
</div>
