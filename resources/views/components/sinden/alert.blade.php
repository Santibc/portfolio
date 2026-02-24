@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true
])

@php
    $typeConfig = [
        'success' => [
            'class' => 'alert-success',
            'icon' => 'bi bi-check-circle-fill',
        ],
        'error' => [
            'class' => 'alert-danger',
            'icon' => 'bi bi-exclamation-circle-fill',
        ],
        'warning' => [
            'class' => 'alert-warning',
            'icon' => 'bi bi-exclamation-triangle-fill',
        ],
        'info' => [
            'class' => 'alert-info',
            'icon' => 'bi bi-info-circle-fill',
        ],
    ];
    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div class="alert {{ $config['class'] }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    <i class="{{ $config['icon'] }} me-2"></i>
    <span>{{ $message ?: $slot }}</span>
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    @endif
</div>
