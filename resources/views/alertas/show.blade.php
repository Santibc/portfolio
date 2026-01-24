@extends('layouts.app')

@section('title', 'Detalle de Alerta')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                @php
                    $prioridadColors = [
                        'critica' => 'danger',
                        'alta' => 'warning',
                        'media' => 'info',
                        'baja' => 'secondary',
                    ];
                    $prioridadLabels = [
                        'critica' => 'Crítica',
                        'alta' => 'Alta',
                        'media' => 'Media',
                        'baja' => 'Baja',
                    ];
                @endphp
                <span class="badge bg-{{ $prioridadColors[$alerta->prioridad] ?? 'secondary' }}">
                    {{ $prioridadLabels[$alerta->prioridad] ?? ucfirst($alerta->prioridad) }}
                </span>
                @if($alerta->resuelta)
                    <span class="badge bg-success">Resuelta</span>
                @elseif($alerta->leida)
                    <span class="badge bg-info">Leída</span>
                @else
                    <span class="badge bg-warning">Pendiente</span>
                @endif
            </div>
            <h1 class="h3 mb-1">{{ $alerta->titulo }}</h1>
            <p class="text-muted mb-0">
                <i class="bi {{ \App\Services\AlertaService::getTipoIcono($alerta->tipo) }} me-1"></i>
                {{ \App\Services\AlertaService::getTipoLabel($alerta->tipo) }}
                <span class="mx-2">|</span>
                <i class="bi bi-clock me-1"></i>Creada {{ $alerta->created_at->diffForHumans() }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if(!$alerta->leida)
            <button type="button" class="btn btn-outline-primary" id="btnMarcarLeida">
                <i class="bi bi-check me-2"></i>Marcar Leída
            </button>
            @endif
            @if(!$alerta->resuelta)
            <button type="button" class="btn btn-success" id="btnMarcarResuelta">
                <i class="bi bi-check2-circle me-2"></i>Marcar Resuelta
            </button>
            @endif
            <a href="{{ route('alertas.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Mensaje de la Alerta -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-chat-text me-2"></i>Detalle de la Alerta</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $alerta->mensaje }}</p>
                </div>
            </div>

            <!-- Registro Relacionado -->
            @if($registroRelacionado['url'])
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>{{ $registroRelacionado['tipo'] }} Relacionado</h6>
                    <a href="{{ $registroRelacionado['url'] }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Ver Registro
                    </a>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">{{ $registroRelacionado['nombre'] }}</h5>
                    @if(count($registroRelacionado['detalles']) > 0)
                    <table class="table table-sm table-borderless mb-0">
                        @foreach($registroRelacionado['detalles'] as $label => $valor)
                        <tr>
                            <td class="text-muted" style="width: 40%">{{ $label }}</td>
                            <td>{{ $valor ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <!-- Información de la Alerta -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Tipo</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ \App\Services\AlertaService::getTipoLabel($alerta->tipo) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Prioridad</td>
                            <td>
                                <span class="badge bg-{{ $prioridadColors[$alerta->prioridad] ?? 'secondary' }}-subtle text-{{ $prioridadColors[$alerta->prioridad] ?? 'secondary' }}">
                                    {{ $prioridadLabels[$alerta->prioridad] ?? ucfirst($alerta->prioridad) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha Vencimiento</td>
                            <td>
                                @if($alerta->fecha_vencimiento)
                                    @if($alerta->fecha_vencimiento->isPast())
                                        <span class="text-danger fw-semibold">
                                            {{ $alerta->fecha_vencimiento->format('d/m/Y') }}
                                        </span>
                                        <br><small class="text-danger">Vencida hace {{ $alerta->fecha_vencimiento->diffInDays(now()) }} días</small>
                                    @else
                                        <span class="{{ $alerta->fecha_vencimiento->diffInDays(now()) <= 7 ? 'text-warning fw-semibold' : '' }}">
                                            {{ $alerta->fecha_vencimiento->format('d/m/Y') }}
                                        </span>
                                        <br><small class="text-muted">En {{ $alerta->fecha_vencimiento->diffInDays(now()) }} días</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creada</td>
                            <td>{{ $alerta->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($alerta->leida && $alerta->fecha_lectura)
                        <tr>
                            <td class="text-muted">Leída</td>
                            <td>{{ $alerta->fecha_lectura->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                        @if($alerta->resuelta && $alerta->fecha_resolucion)
                        <tr>
                            <td class="text-muted">Resuelta</td>
                            <td>{{ $alerta->fecha_resolucion->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Destinatarios -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>Destinatarios</h6>
                </div>
                <div class="card-body">
                    @if($alerta->para_roles && count($alerta->para_roles) > 0)
                        <p class="text-muted small mb-2">Roles:</p>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($alerta->para_roles as $rol)
                                <span class="badge bg-primary-subtle text-primary">{{ $rol }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($alerta->para_usuario_id)
                        <p class="text-muted small mb-2 {{ $alerta->para_roles ? 'mt-3' : '' }}">Usuario específico:</p>
                        <span class="badge bg-info-subtle text-info">
                            <i class="bi bi-person me-1"></i>ID: {{ $alerta->para_usuario_id }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marcar como leída
    const btnMarcarLeida = document.getElementById('btnMarcarLeida');
    if (btnMarcarLeida) {
        btnMarcarLeida.addEventListener('click', function() {
            fetch('{{ route("alertas.marcar-leida", $alerta) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Alerta marcada como leída',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => location.reload());
                }
            })
            .catch(error => {
                Swal.fire('Error', 'No se pudo marcar la alerta', 'error');
            });
        });
    }

    // Marcar como resuelta
    const btnMarcarResuelta = document.getElementById('btnMarcarResuelta');
    if (btnMarcarResuelta) {
        btnMarcarResuelta.addEventListener('click', function() {
            Swal.fire({
                title: '¿Marcar como resuelta?',
                text: 'Esta acción indica que el problema ha sido solucionado',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, marcar resuelta',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("alertas.marcar-resuelta", $alerta) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Alerta resuelta',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'No se pudo marcar la alerta', 'error');
                    });
                }
            });
        });
    }
});
</script>
@endpush
@endsection
