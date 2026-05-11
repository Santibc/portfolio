@props([
    'text' => '',
    'position' => 'top', // top | bottom | left | right
])

@php
    $positions = [
        'top'    => 'hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible bottom-full mb-2 left-1/2 -translate-x-1/2',
        'bottom' => 'hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible top-full mt-2 left-1/2 -translate-x-1/2',
        'left'   => 'hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible right-full mr-2 top-1/2 -translate-y-1/2',
        'right'  => 'hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible left-full ml-2 top-1/2 -translate-y-1/2',
    ];
    $posClass = $positions[$position] ?? $positions['top'];
@endphp

<span class="hs-tooltip inline-flex relative">
    <span class="hs-tooltip-toggle">
        {{ $slot }}
    </span>
    <span class="hs-tooltip-content invisible opacity-0 transition-opacity absolute z-30 px-2.5 py-1.5 bg-cream-900 text-white text-xs rounded-lg shadow-soft whitespace-nowrap pointer-events-none {{ $posClass }} dark:bg-cream-100 dark:text-cream-900" role="tooltip">
        {{ $text }}
    </span>
</span>
