@props([
    'icon' => 'activity',
    'label' => '',
    'value' => '',
    'trend' => null,         // numeric (e.g. 12.5)
    'trendLabel' => null,    // text shown next to trend
    'color' => 'primary',    // primary | accent | emerald | rose | sky
])

@php
    $colors = [
        'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-200',
        'accent'  => 'bg-accent-100 text-accent-700 dark:bg-accent-900/40 dark:text-accent-200',
        'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
        'rose'    => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200',
        'sky'     => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-200',
    ];
    $iconBg = $colors[$color] ?? $colors['primary'];
    $isUp = $trend !== null && $trend >= 0;
@endphp

<div {{ $attributes->merge(['class' => 'surface-card hover-lift p-5']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="space-y-1 min-w-0">
            <p class="text-xs uppercase tracking-wider font-semibold text-cream-600 dark:text-cream-400 truncate">
                {{ $label }}
            </p>
            <p class="text-3xl font-bold text-cream-900 dark:text-cream-50 leading-none">
                {{ $value }}
            </p>
            @if ($trend !== null)
                <p class="text-xs flex items-center gap-1 mt-2 font-medium {{ $isUp ? 'text-emerald-600' : 'text-rose-600' }}">
                    <x-icon :name="$isUp ? 'trending-up' : 'trending-down'" class="w-3.5 h-3.5" />
                    {{ $isUp ? '+' : '' }}{{ $trend }}%
                    @if ($trendLabel)<span class="text-cream-500 font-normal">{{ $trendLabel }}</span>@endif
                </p>
            @endif
        </div>
        <div class="shrink-0 w-12 h-12 rounded-2xl {{ $iconBg }} flex items-center justify-center">
            <x-icon :name="$icon" class="w-6 h-6" />
        </div>
    </div>
</div>
