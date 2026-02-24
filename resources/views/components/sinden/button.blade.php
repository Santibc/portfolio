@props([
    'variant' => 'primary',
    'type' => 'button',
    'icon' => null,
    'size' => 'md',
    'href' => null
])

@php
    $classes = match($variant) {
        'primary' => 'btn btn-primary',
        'secondary' => 'btn btn-secondary',
        'success' => 'btn btn-success',
        'danger' => 'btn btn-danger',
        'warning' => 'btn btn-warning',
        'info' => 'btn btn-info',
        'outline' => 'btn btn-outline-primary',
        'outline-primary' => 'btn btn-outline-primary',
        'outline-secondary' => 'btn btn-outline-secondary',
        'outline-danger' => 'btn btn-outline-danger',
        'outline-success' => 'btn btn-outline-success',
        'outline-warning' => 'btn btn-outline-warning',
        'outline-info' => 'btn btn-outline-info',
        'icon' => 'btn-icon',
        default => 'btn btn-primary'
    };

    if ($size === 'sm') $classes .= ' btn-sm';
    if ($size === 'lg') $classes .= ' btn-lg';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }} me-1"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }} me-1"></i>
        @endif
        {{ $slot }}
    </button>
@endif
