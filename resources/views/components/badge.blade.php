@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
])

@php
    $variants = [
        'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-200',
        'accent'  => 'bg-accent-100 text-accent-800 dark:bg-accent-900/40 dark:text-accent-200',
        'success' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
        'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
        'danger'  => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
        'neutral' => 'bg-cream-200 text-cream-800 dark:bg-cream-800 dark:text-cream-200',
        'sky'     => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200',
    ];
    $sizes = [
        'sm' => 'text-[11px] px-2 py-0.5 gap-1',
        'md' => 'text-xs px-2.5 py-1 gap-1.5',
        'lg' => 'text-sm px-3 py-1 gap-1.5',
    ];
    $base = 'inline-flex items-center font-semibold rounded-full';
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)<x-icon :name="$icon" class="w-3 h-3" />@endif
    {{ $slot }}
</span>
