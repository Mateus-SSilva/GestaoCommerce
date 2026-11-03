{{-- Alternar tema claro/escuro. Usa o mesmo armazenamento do Filament (localStorage "filament:theme:mode"). --}}
<button type="button"
        x-data
        x-on:click="$dispatch('theme-changed', $store.theme === 'dark' ? 'light' : 'dark')"
        x-bind:title="$store.theme === 'dark' ? 'Usar tema claro' : 'Usar tema escuro'"
        x-bind:aria-label="$store.theme === 'dark' ? 'Usar tema claro' : 'Usar tema escuro'"
        data-theme-toggle
        {{ $attributes->class(['inline-flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-sm font-medium text-gray-600 ring-1 ring-gray-950/10 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:ring-white/15 dark:hover:bg-white/10 dark:hover:text-white']) }}>
    <x-filament::icon icon="heroicon-o-moon" class="size-5" x-show="$store.theme !== 'dark'" />
    <x-filament::icon icon="heroicon-o-sun" class="size-5" x-show="$store.theme === 'dark'" x-cloak />
    <span class="hidden sm:inline" x-text="$store.theme === 'dark' ? 'Tema claro' : 'Tema escuro'">Tema escuro</span>
</button>
