@extends('layouts.app')

@section('title', 'Centro de Alertas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Centro de Alertas</h1>
            <p class="text-muted mb-0">Gestiona las alertas y caducidades del sistema</p>
        </div>
        <div class="d-flex gap-2">
            @role('Administrador|RRHH')
            <a href="{{ route('alertas.configuracion.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-sliders me-2"></i>Configuración
            </a>
            @endrole
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-bell text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                            <small class="text-muted">Total Alertas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['criticas'] ?? 0 }}</h3>
                            <small class="text-muted">Críticas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-envelope text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['no_leidas'] ?? 0 }}</h3>
                            <small class="text-muted">No Leídas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['resueltas_hoy'] ?? 0 }}</h3>
                            <small class="text-muted">Resueltas Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('alertas.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposAlerta as $key => $label)
                            <option value="{{ $key }}" {{ ($filtros['tipo'] ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select">
                        <option value="">Todas</option>
                        <option value="critica" {{ ($filtros['prioridad'] ?? '') == 'critica' ? 'selected' : '' }}>Crítica</option>
                        <option value="alta" {{ ($filtros['prioridad'] ?? '') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="media" {{ ($filtros['prioridad'] ?? '') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="baja" {{ ($filtros['prioridad'] ?? '') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ ($filtros['estado'] ?? '') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="leida" {{ ($filtros['estado'] ?? '') == 'leida' ? 'selected' : '' }}>Leídas</option>
                        <option value="resuelta" {{ ($filtros['estado'] ?? '') == 'resuelta' ? 'selected' : '' }}>Resueltas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] ?? '' }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('alertas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Acciones masivas -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label" for="selectAll">Seleccionar todo</label>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnMarcarLeidasMultiple" disabled>
            <i class="bi bi-check2-all me-1"></i>Marcar como leídas
        </button>
    </div>

    <!-- Tabla de Alertas -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="ps-4"></th>
                            <th>Alerta</th>
                            <th>Tipo</th>
                            <th>Prioridad</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alertas as $alerta)
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
                        <tr class="{{ !$alerta->leida ? 'table-light fw-semibold' : '' }}">
                            <td class="ps-4">
                                <input class="form-check-input alerta-checkbox" type="checkbox"
                                       value="{{ $alerta->id }}" data-id="{{ $alerta->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-start">
                                    @if(!$alerta->leida)
                                        <span class="badge bg-primary rounded-circle p-1 me-2 mt-1" style="width: 8px; height: 8px;"></span>
                                    @endif
                                    <div>
                                        <h6 class="mb-1">{{ $alerta->titulo }}</h6>
                                        <small class="text-muted">{{ Str::limit($alerta->mensaje, 60) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <i class="bi {{ \App\Services\AlertaService::getTipoIcono($alerta->tipo) }} me-1"></i>
                                    {{ $tiposAlerta[$alerta->tipo] ?? ucfirst(str_replace('_', ' ', $alerta->tipo)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $prioridadColors[$alerta->prioridad] ?? 'secondary' }}-subtle text-{{ $prioridadColors[$alerta->prioridad] ?? 'secondary' }}">
                                    {{ $prioridadLabels[$alerta->prioridad] ?? ucfirst($alerta->prioridad) }}
                                </span>
                            </td>
                            <td>
                                @if($alerta->fecha_vencimiento)
                                    @if($alerta->fecha_vencimiento->isPast())
                                        <span class="text-danger fw-semibold">
                                            {{ $alerta->fecha_vencimiento->format('d/m/Y') }}
                                            <small class="d-block">Vencida</small>
                                        </span>
                                    @elseif($alerta->fecha_vencimiento->diffInDays(now()) <= 7)
                                        <span class="text-warning fw-semibold">
                                            {{ $alerta->fecha_vencimiento->format('d/m/Y') }}
                                            <small class="d-block">{{ $alerta->fecha_vencimiento->diffInDays(now()) }} días</small>
                                        </span>
                                    @else
                                        {{ $alerta->fecha_vencimiento->format('d/m/Y') }}
                                        <small class="d-block text-muted">{{ $alerta->fecha_vencimiento->diffInDays(now()) }} días</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($alerta->resuelta)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Resuelta
                                    </span>
                                @elseif($alerta->leida)
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-eye me-1"></i>Leída
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bi bi-clock me-1"></i>Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('alertas.show', $alerta) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!$alerta->leida)
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-marcar-leida"
                                            data-id="{{ $alerta->id }}" title="Marcar como leída">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    @endif
                                    @if(!$alerta->resuelta)
                                    <button type="button" class="btn btn-sm btn-outline-success btn-marcar-resuelta"
                                            data-id="{{ $alerta->id }}" title="Marcar como resuelta">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                                <p class="mb-0">No hay alertas que mostrar</p>
                                <small>Las alertas aparecerán aquí cuando se detecten caducidades próximas</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($alertas->hasPages())
        <div class="card-footer bg-transparent">
            {{ $alertas->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.alerta-checkbox');
    const btnMarcarLeidasMultiple = document.getElementById('btnMarcarLeidasMultiple');

    // Seleccionar/deseleccionar todos
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBtnState();
    });

    // Actualizar estado del botón
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBtnState);
    });

    function updateBtnState() {
        const checked = document.querySelectorAll('.alerta-checkbox:checked');
        btnMarcarLeidasMultiple.disabled = checked.length === 0;
    }

    // Marcar como leída individual
    document.querySelectorAll('.btn-marcar-leida').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`{{ url('alertas') }}/${id}/marcar-leida`, {
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
    });

    // Marcar como resuelta individual
    document.querySelectorAll('.btn-marcar-resuelta').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
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
                    fetch(`{{ url('alertas') }}/${id}/marcar-resuelta`, {
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
    });

    // Marcar múltiples como leídas
    btnMarcarLeidasMultiple.addEventListener('click', function() {
        const checked = document.querySelectorAll('.alerta-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);

        if (ids.length === 0) return;

        Swal.fire({
            title: '¿Marcar como leídas?',
            text: `Se marcarán ${ids.length} alertas como leídas`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, marcar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("alertas.marcar-leidas-multiple") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ alertas: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => location.reload());
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'No se pudieron marcar las alertas', 'error');
                });
            }
        });
    });
});
</script>
@endpush
@endsection
