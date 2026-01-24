{{-- Widget: Alertas Críticas --}}
@php
    $alertas = $alertas ?? [];
@endphp

@if(empty($alertas))
    <div class="text-center text-muted py-4">
        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
        <p class="mb-0">Sin alertas pendientes</p>
    </div>
@else
    <ul class="list-group list-group-flush">
        @foreach($alertas as $alerta)
            @php
                $prioridadClass = match($alerta['prioridad'] ?? 'media') {
                    'critica' => 'danger',
                    'alta' => 'warning',
                    'media' => 'info',
                    'baja' => 'secondary',
                    default => 'secondary',
                };
            @endphp
            <li class="list-group-item border-start border-{{ $prioridadClass }} border-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-{{ $prioridadClass }} me-2">
                            {{ strtoupper($alerta['prioridad'] ?? 'MEDIA') }}
                        </span>
                        <small class="text-muted">{{ $alerta['tipo'] ?? 'Alerta' }}</small>
                    </div>
                    @if(!empty($alerta['fecha_vencimiento']))
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($alerta['fecha_vencimiento'])->format('d/m/Y') }}
                        </small>
                    @endif
                </div>
                <p class="mb-0 mt-1 small">{{ $alerta['mensaje'] ?? $alerta['descripcion'] ?? '' }}</p>
            </li>
        @endforeach
    </ul>
@endif
