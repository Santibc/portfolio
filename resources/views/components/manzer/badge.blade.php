@props([
    'variant' => 'primary',
    'color' => null,
    'text' => null,
])

@php
    $c = $color ?? $variant;
    $map = [
        'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400',
        'success' => 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400',
        'danger' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
        'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
        'info' => 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-400',
        'secondary' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
    ];
    $cls = $map[$c] ?? $map['primary'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {$cls}"]) }}>
    {{ $text ?? $slot }}
</span>
