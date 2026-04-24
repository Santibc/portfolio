@props([
    'id',
    'title',
    'size' => 'md',
])

@php
    $sizeCls = match ($size) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-2xl',
        default => 'max-w-md',
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="$event.detail === '{{ $id }}' ? open = true : null"
    x-on:close-modal.window="$event.detail === '{{ $id }}' ? open = false : null"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
    role="dialog"
    aria-labelledby="{{ $id }}-title"
    aria-modal="true"
>
    {{-- Overlay --}}
    <div
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="absolute inset-0 bg-zinc-900/60 backdrop-blur-sm"
    ></div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-smooth duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-smooth duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="relative w-full {{ $sizeCls }} rounded-2xl bg-white shadow-xl dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-800"
    >
        <div class="flex items-center justify-between border-b border-zinc-200 p-4 dark:border-zinc-800">
            <h2 id="{{ $id }}-title" class="text-lg font-semibold tracking-tight">{{ $title }}</h2>
            <button type="button" @click="open = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200" aria-label="Cerrar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-4">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-800">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
