@props([
    'percentage',
    'showText' => false,
    'color' => 'primary',
    'height' => '8px'
])

@php
    $bgClass = match($color) {
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'danger' => 'bg-danger',
        'info' => 'bg-info',
        default => 'bg-primary'
    };
@endphp

<div class="progress" style="height: {{ $height }};">
    <div class="progress-bar {{ $bgClass }}"
         role="progressbar"
         style="width: {{ $percentage }}%;"
         aria-valuenow="{{ $percentage }}"
         aria-valuemin="0"
         aria-valuemax="100">
        @if($showText)
            {{ round($percentage) }}%
        @endif
    </div>
</div>
@if($showText && $height === '8px')
    <small class="text-muted">{{ round($percentage) }}%</small>
@endif
