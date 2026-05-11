@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'iconRight' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary'   => 'bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white shadow-soft hover:shadow-glow border border-primary-500',
        'secondary' => 'bg-white hover:bg-cream-100 active:bg-cream-200 text-cream-900 border border-cream-300 dark:bg-cream-900 dark:hover:bg-cream-800 dark:text-cream-100 dark:border-cream-700',
        'ghost'     => 'bg-transparent hover:bg-cream-100 text-cream-800 dark:hover:bg-cream-800 dark:text-cream-200',
        'danger'    => 'bg-red-500 hover:bg-red-600 active:bg-red-700 text-white shadow-soft border border-red-500',
        'success'   => 'bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white shadow-soft border border-emerald-500',
        'link'      => 'bg-transparent text-primary-700 hover:text-primary-800 underline underline-offset-2 dark:text-primary-300',
    ];

    $sizes = [
        'xs' => 'text-xs px-2.5 py-1.5 gap-1.5',
        'sm' => 'text-sm px-3 py-1.5 gap-1.5',
        'md' => 'text-sm px-4 py-2.5 gap-2',
        'lg' => 'text-base px-6 py-3 gap-2',
    ];

    $base = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-200 ease-out disabled:opacity-50 disabled:cursor-not-allowed select-none';
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif
        {{ $slot }}
        @if ($iconRight)<x-icon :name="$iconRight" class="w-4 h-4" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif
        {{ $slot }}
        @if ($iconRight)<x-icon :name="$iconRight" class="w-4 h-4" />@endif
    </button>
@endif
