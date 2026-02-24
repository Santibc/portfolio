@props([
    'variant' => null,
    'color' => null,
    'type' => 'status',
    'text' => null
])

@php
    $badgeColor = $color ?? $variant ?? 'primary';
    $classes = $type === 'category' ? 'category-badge' : 'status-badge';
    $classes .= ' ' . $badgeColor;
    $badgeText = $text ?? $slot;
@endphp

<span class="{{ $classes }}">
    {{ $badgeText }}
</span>
