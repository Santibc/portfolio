@props([
    'value' => 0,
    'max' => 100,
    'label' => null,
    'showValue' => false,
    'color' => 'primary', // primary | accent | emerald | rose
])

@php
    $colors = [
        'primary' => 'bg-primary-500',
        'accent'  => 'bg-accent-500',
        'emerald' => 'bg-emerald-500',
        'rose'    => 'bg-rose-500',
    ];
    $bar = $colors[$color] ?? $colors['primary'];
    $pct = min(100, max(0, ($value / max(1, $max)) * 100));
@endphp

<div {{ $attributes }}>
    @if ($label || $showValue)
        <div class="flex justify-between items-center mb-1.5 text-xs">
            @if ($label)<span class="text-cream-700 dark:text-cream-300 font-medium">{{ $label }}</span>@endif
            @if ($showValue)<span class="text-cream-600 dark:text-cream-400">{{ round($pct) }}%</span>@endif
        </div>
    @endif
    <div class="w-full h-2 rounded-full bg-cream-200 dark:bg-cream-800 overflow-hidden">
        <div class="h-full {{ $bar }} rounded-full transition-all duration-500 ease-out" style="width: {{ $pct }}%"></div>
    </div>
</div>
