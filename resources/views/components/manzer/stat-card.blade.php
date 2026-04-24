@props([
    'icon',
    'value',
    'label' => null,
    'title' => null,
    'description' => null,
    'change' => null,
    'changeType' => 'neutral',
    'variant' => 'primary',
    'color' => null,
])

@php
    $c = $color ?? $variant;
    $iconCls = match ($c) {
        'success' => 'bg-green-100 text-green-600 dark:bg-green-950 dark:text-green-400',
        'danger' => 'bg-red-100 text-red-600 dark:bg-red-950 dark:text-red-400',
        'warning' => 'bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400',
        'info' => 'bg-sky-100 text-sky-600 dark:bg-sky-950 dark:text-sky-400',
        'secondary' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
        default => 'bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400',
    };

    $changeCls = match ($changeType) {
        'positive' => 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400',
        'negative' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
        default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
    };

    $cardTitle = $title ?? $label ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'card transition hover:-translate-y-0.5 hover:shadow-lg']) }}>
    <div class="flex items-start justify-between">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $iconCls }}">
            <i class="bi bi-{{ $icon }} text-lg"></i>
        </div>
        @if ($change)
            <span class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-xs font-medium {{ $changeCls }}">
                @if ($changeType === 'positive')<i class="bi bi-arrow-up text-[10px]"></i>@endif
                @if ($changeType === 'negative')<i class="bi bi-arrow-down text-[10px]"></i>@endif
                {{ $change }}
            </span>
        @endif
    </div>
    <div class="mt-4">
        <div class="text-2xl font-bold tracking-tight">{{ $value }}</div>
        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $cardTitle }}</div>
        @if ($description)
            <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $description }}</p>
        @endif
    </div>
</div>
