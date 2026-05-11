@props([
    'variant' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $variants = [
        'info'    => ['bg' => 'bg-sky-50 border-sky-200 text-sky-900 dark:bg-sky-900/30 dark:border-sky-800 dark:text-sky-100', 'icon' => 'info'],
        'success' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-900 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-100', 'icon' => 'check-circle-2'],
        'warning' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-100', 'icon' => 'alert-triangle'],
        'danger'  => ['bg' => 'bg-rose-50 border-rose-200 text-rose-900 dark:bg-rose-900/30 dark:border-rose-800 dark:text-rose-100', 'icon' => 'alert-circle'],
    ];
    $cfg = $variants[$variant] ?? $variants['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition.opacity
    {{ $attributes->merge(['class' => 'flex items-start gap-3 px-4 py-3 rounded-xl border ' . $cfg['bg']]) }}
>
    <x-icon :name="$cfg['icon']" class="w-5 h-5 shrink-0 mt-0.5" />

    <div class="flex-1 min-w-0 text-sm">
        @if ($title)
            <p class="font-semibold mb-0.5">{{ $title }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>

    @if ($dismissible)
        <button type="button" @click="show = false" class="shrink-0 -mr-1 p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/5">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    @endif
</div>
