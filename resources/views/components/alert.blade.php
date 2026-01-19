{{--
    Componente: Alert
    Uso: <x-alert type="success" message="Operación exitosa" />

    Props:
    - type: string - Tipo de alerta (success, warning, danger, info) - default: info
    - message: string|null - Mensaje a mostrar
    - dismissible: bool - Si se puede cerrar - default: true
    - icon: string|null - Icono personalizado (sobreescribe el icono por defecto)

    Slots:
    - default: Contenido HTML personalizado (alternativa a message)

    Ejemplo:
    <x-alert type="success" message="Guardado correctamente" />
    <x-alert type="danger" :dismissible="false">
        <strong>Error:</strong> No se pudo procesar la solicitud.
    </x-alert>
--}}

@props([
    'type' => 'info',
    'message' => null,
    'dismissible' => true,
    'icon' => null
])

@php
    // Configuración de iconos por tipo
    $icons = [
        'success' => 'bi-check-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'danger' => 'bi-x-circle-fill',
        'info' => 'bi-info-circle-fill',
    ];

    $alertIcon = $icon ?? ($icons[$type] ?? $icons['info']);
    $alertClass = "alert alert-{$type}";

    if ($dismissible) {
        $alertClass .= ' alert-dismissible fade show';
    }
@endphp

<div {{ $attributes->merge(['class' => $alertClass, 'role' => 'alert']) }}>
    <div class="d-flex align-items-center gap-2">
        <i class="{{ $alertIcon }}"></i>
        <div>
            @if($message)
                {{ $message }}
            @else
                {{ $slot }}
            @endif
        </div>
    </div>

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    @endif
</div>
