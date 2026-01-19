{{--
    Componente: Status Badge
    Uso: <x-status-badge status="pendiente" />

    Props:
    - status: string - El estado a mostrar (pendiente, aplicada, rechazada, activo, inactivo, etc.)
    - size: string - Tamaño del badge (sm, md, lg) - default: md

    Ejemplo:
    <x-status-badge status="aplicada" />
    <x-status-badge status="pendiente" size="lg" />
--}}

@props([
    'status',
    'size' => 'md'
])

@php
    // Mapeo de estados a colores y textos
    $statusConfig = [
        // Estados de cotización
        'pendiente' => ['bg' => 'bg-warning', 'text' => 'text-dark', 'label' => 'Pendiente'],
        'aplicada' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'Aplicada'],
        'aprobada' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'Aprobada'],
        'rechazada' => ['bg' => 'bg-danger', 'text' => 'text-white', 'label' => 'Rechazada'],
        'cancelada' => ['bg' => 'bg-secondary', 'text' => 'text-white', 'label' => 'Cancelada'],

        // Estados generales
        'activo' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'Activo'],
        'inactivo' => ['bg' => 'bg-secondary', 'text' => 'text-white', 'label' => 'Inactivo'],

        // Estados de stock
        'disponible' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'Disponible'],
        'agotado' => ['bg' => 'bg-danger', 'text' => 'text-white', 'label' => 'Agotado'],
        'bajo' => ['bg' => 'bg-warning', 'text' => 'text-dark', 'label' => 'Stock Bajo'],

        // Estados de proceso
        'procesando' => ['bg' => 'bg-info', 'text' => 'text-white', 'label' => 'Procesando'],
        'completado' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'Completado'],
        'error' => ['bg' => 'bg-danger', 'text' => 'text-white', 'label' => 'Error'],

        // Estados de envío
        'preparando' => ['bg' => 'bg-info', 'text' => 'text-white', 'label' => 'Preparando'],
        'despachado' => ['bg' => 'bg-primary', 'text' => 'text-white', 'label' => 'Despachado'],
        'en_transito' => ['bg' => 'bg-info', 'text' => 'text-white', 'label' => 'En Tránsito'],
        'entregado' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'Entregado'],

        // Estados de pago
        'pagado' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'Pagado'],
        'pendiente_pago' => ['bg' => 'bg-warning', 'text' => 'text-dark', 'label' => 'Pendiente de Pago'],

        // Default
        'default' => ['bg' => 'bg-secondary', 'text' => 'text-white', 'label' => ucfirst($status ?? 'Desconocido')],
    ];

    $config = $statusConfig[strtolower($status)] ?? $statusConfig['default'];

    // Clases de tamaño
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-2 py-1',
        'lg' => 'px-3 py-2 fs-6',
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<span {{ $attributes->merge(['class' => "badge rounded-pill {$config['bg']} {$config['text']} {$sizeClass}"]) }}>
    {{ $config['label'] }}
</span>
