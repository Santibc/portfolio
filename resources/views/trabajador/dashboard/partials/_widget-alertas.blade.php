@php
    $alertas = $alertas ?? [];
@endphp

@if(count($alertas) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
        <p class="mb-0">Sin alertas pendientes</p>
    </div>
@else
    <ul class="list-group list-group-flush">
        @foreach($alertas as $alerta)
            @php
                $prioridadClass = match($alerta['prioridad'] ?? 'baja') {
                    'critica' => 'danger',
                    'alta' => 'warning',
                    'media' => 'info',
                    default => 'secondary',
                };
            @endphp
            <li class="list-group-item border-start border-{{ $prioridadClass }} border-4 py-2">
                <div class="d-flex justify-content-between">
                    <span class="badge bg-{{ $prioridadClass }}">{{ $alerta['prioridad'] ?? 'Baja' }}</span>
                    <small class="text-muted">{{ $alerta['fecha_vencimiento'] ?? '' }}</small>
                </div>
                <p class="mb-0 mt-1 small">{{ Str::limit($alerta['titulo'] ?? '', 50) }}</p>
                <small class="text-muted">{{ $alerta['tipo'] ?? '' }}</small>
            </li>
        @endforeach
    </ul>
@endif
