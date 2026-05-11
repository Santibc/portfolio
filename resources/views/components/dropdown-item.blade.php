@props([
    'href' => null,
    'icon' => null,
])

@php
    $classes = 'flex items-center gap-x-2.5 py-2 px-3 rounded-lg text-sm text-cream-800 hover:bg-cream-100 dark:text-cream-200 dark:hover:bg-cream-800 transition-colors';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="w-4 h-4 text-cream-600 dark:text-cream-400" />@endif
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $classes . ' w-full text-left']) }}>
        @if ($icon)<x-icon :name="$icon" class="w-4 h-4 text-cream-600 dark:text-cream-400" />@endif
        {{ $slot }}
    </button>
@endif
