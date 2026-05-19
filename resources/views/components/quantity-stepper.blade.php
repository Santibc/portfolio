@props([
    'model' => null,
    'min' => 1,
    'max' => 99,
    'onIncrement' => null,
    'onDecrement' => null,
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => ['btn' => 'w-7 h-7', 'icon' => 'w-3.5 h-3.5', 'value' => 'min-w-[2rem] text-sm'],
        'md' => ['btn' => 'w-9 h-9', 'icon' => 'w-4 h-4',    'value' => 'min-w-[2.5rem] text-base'],
        'lg' => ['btn' => 'w-11 h-11','icon' => 'w-5 h-5',    'value' => 'min-w-[3rem] text-lg'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];

    $btnClass = "inline-flex items-center justify-center {$s['btn']} rounded-lg bg-cream-100 hover:bg-cream-200 text-cream-800 dark:bg-cream-800 dark:hover:bg-cream-700 dark:text-cream-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed";
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 select-none']) }}>
    <button type="button"
        class="{{ $btnClass }}"
        @if ($onDecrement) @click="{{ $onDecrement }}" @else x-on:click="if ({{ $model }} > {{ $min }}) {{ $model }}--" @endif
        @if ($model && !$onDecrement) x-bind:disabled="{{ $model }} <= {{ $min }}" @endif>
        <x-icon name="minus" :class="$s['icon']" />
    </button>

    <span class="{{ $s['value'] }} text-center font-semibold text-cream-900 dark:text-cream-50 tabular-nums"
        @if ($model) x-text="{{ $model }}" @endif>
        @if (!$model){{ $slot }}@endif
    </span>

    <button type="button"
        class="{{ $btnClass }}"
        @if ($onIncrement) @click="{{ $onIncrement }}" @else x-on:click="if ({{ $model }} < {{ $max }}) {{ $model }}++" @endif
        @if ($model && !$onIncrement) x-bind:disabled="{{ $model }} >= {{ $max }}" @endif>
        <x-icon name="plus" :class="$s['icon']" />
    </button>
</div>
