@php
    $vacaciones = $vacaciones ?? [];
@endphp

@if(empty($vacaciones) || !isset($vacaciones['dias_totales']))
    <div class="text-center text-muted py-4">
        <i class="bi bi-calendar fs-1 d-block mb-2"></i>
        <p class="mb-0">Sin datos de vacaciones</p>
    </div>
@else
    @php
        $porcentaje = $vacaciones['dias_totales'] > 0
            ? round(($vacaciones['dias_disfrutados'] / $vacaciones['dias_totales']) * 100)
            : 0;
        $porcentajePendiente = $vacaciones['dias_totales'] > 0
            ? round(($vacaciones['dias_pendientes'] / $vacaciones['dias_totales']) * 100)
            : 0;
    @endphp

    <div class="text-center mb-4">
        <div class="display-4 fw-bold text-success">{{ $vacaciones['dias_disponibles'] ?? 0 }}</div>
        <small class="text-muted">Dias Disponibles</small>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between mb-1">
            <small>Disfrutados</small>
            <small class="fw-semibold">{{ $vacaciones['dias_disfrutados'] ?? 0 }} de {{ $vacaciones['dias_totales'] ?? 0 }}</small>
        </div>
        <div class="progress progress-vacaciones">
            <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between mb-1">
            <small>Pendientes de aprobar</small>
            <small class="fw-semibold">{{ $vacaciones['dias_pendientes'] ?? 0 }}</small>
        </div>
        <div class="progress progress-vacaciones">
            <div class="progress-bar bg-warning" style="width: {{ $porcentajePendiente }}%"></div>
        </div>
    </div>

    <hr>
    <div class="small text-muted">
        <i class="bi bi-info-circle me-1"></i>
        Periodo: {{ $vacaciones['anio'] ?? now()->year }}
    </div>
@endif
