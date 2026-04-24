@props([
    'percentage',
    'showText' => false,
    'color' => 'primary',
    'height' => 'h-2',
])

@php
    $barCls = match ($color) {
        'success' => 'bg-green-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        'info' => 'bg-sky-500',
        default => 'bg-primary-500',
    };
    $pct = max(0, min(100, (float) $percentage));
@endphp

<div>
    <div class="w-full overflow-hidden rounded-full bg-zinc-200 {{ $height }} dark:bg-zinc-800">
        <div
            class="{{ $barCls }} {{ $height }} rounded-full transition-all duration-300 ease-smooth"
            role="progressbar"
            style="width: {{ $pct }}%;"
            aria-valuenow="{{ $pct }}"
            aria-valuemin="0"
            aria-valuemax="100"
        ></div>
    </div>
    @if ($showText)
        <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ round($pct) }}%</div>
    @endif
</div>
