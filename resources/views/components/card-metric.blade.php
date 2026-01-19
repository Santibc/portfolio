{{--
    Componente: Card Metric
    Uso: <x-card-metric title="Total Ventas" value="$5,000" icon="bi-cart" />

    Props:
    - title: string - Título de la métrica
    - value: string - Valor a mostrar
    - icon: string - Clase del icono Bootstrap Icons (ej: bi-cart, bi-people)
    - color: string - Color de la tarjeta (pink, lilac, aqua, gold, success, warning, danger) - default: pink
    - subtitle: string|null - Subtítulo opcional
    - trend: string|null - Tendencia (up, down) - opcional
    - trendValue: string|null - Valor de la tendencia (ej: "+12%")

    Ejemplo:
    <x-card-metric title="Cotizaciones" value="45" icon="bi-file-earmark-text" color="lilac" />
    <x-card-metric title="Ventas Hoy" value="$1,200" icon="bi-currency-dollar" trend="up" trendValue="+15%" />
--}}

@props([
    'title',
    'value',
    'icon' => 'bi-graph-up',
    'color' => 'pink',
    'subtitle' => null,
    'trend' => null,
    'trendValue' => null
])

@php
    // Mapeo de colores a variables CSS de Miracle
    $colorConfig = [
        'pink' => [
            'bg' => 'var(--miracle-pink-light)',
            'border' => 'var(--miracle-pink)',
            'icon' => 'var(--miracle-pink)',
        ],
        'lilac' => [
            'bg' => 'var(--miracle-lilac-light)',
            'border' => 'var(--miracle-lilac)',
            'icon' => 'var(--miracle-lilac)',
        ],
        'aqua' => [
            'bg' => '#e8f6f6',
            'border' => 'var(--miracle-aqua)',
            'icon' => '#5eb5b3',
        ],
        'gold' => [
            'bg' => '#fff8e6',
            'border' => 'var(--miracle-gold)',
            'icon' => 'var(--miracle-gold)',
        ],
        'success' => [
            'bg' => '#d4edda',
            'border' => '#28a745',
            'icon' => '#28a745',
        ],
        'warning' => [
            'bg' => '#fff3cd',
            'border' => '#ffc107',
            'icon' => '#856404',
        ],
        'danger' => [
            'bg' => '#f8d7da',
            'border' => '#dc3545',
            'icon' => '#dc3545',
        ],
    ];

    $config = $colorConfig[$color] ?? $colorConfig['pink'];
@endphp

<div {{ $attributes->merge(['class' => 'card h-100']) }}
     style="background-color: {{ $config['bg'] }}; border-left: 4px solid {{ $config['border'] }};">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <p class="text-muted mb-1 small">{{ $title }}</p>
                <h3 class="mb-0 fw-bold" style="color: var(--miracle-dark);">{{ $value }}</h3>
                @if($subtitle)
                    <small class="text-muted">{{ $subtitle }}</small>
                @endif
                @if($trend && $trendValue)
                    <div class="mt-2">
                        @if($trend === 'up')
                            <span class="text-success small">
                                <i class="bi bi-arrow-up"></i> {{ $trendValue }}
                            </span>
                        @else
                            <span class="text-danger small">
                                <i class="bi bi-arrow-down"></i> {{ $trendValue }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="rounded-circle p-2" style="background-color: white;">
                <i class="{{ $icon }} fs-4" style="color: {{ $config['icon'] }};"></i>
            </div>
        </div>
    </div>
</div>
