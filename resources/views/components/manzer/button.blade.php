@props([
    'variant' => 'primary',
    'type' => 'button',
    'icon' => null,
    'size' => 'md',
    'href' => null,
])

@php
    $base = match ($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'danger' => 'btn-danger',
        'ghost' => 'btn-ghost',
        'outline' => 'btn bg-white text-primary-600 ring-1 ring-inset ring-primary-200 hover:bg-primary-50 dark:bg-transparent dark:text-primary-400 dark:ring-primary-900 dark:hover:bg-primary-950',
        default => 'btn-primary',
    };

    $sizeCls = match ($size) {
        'sm' => 'text-xs px-3 py-1.5',
        'lg' => 'text-base px-5 py-2.5',
        default => '',
    };

    $classes = trim($base.' '.$sizeCls);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<i class="bi bi-{{ $icon }}"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<i class="bi bi-{{ $icon }}"></i>@endif
        {{ $slot }}
    </button>
@endif
