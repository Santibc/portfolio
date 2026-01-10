@extends('layouts.app')

@section('title', 'Fichajes')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Fichajes</h1>
            <p class="text-muted mb-0">Control de entrada y salida de trabajadores</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fichajes.resumen') }}" class="btn btn-outline-secondary">
                <i class="bi bi-bar-chart me-2"></i>Resumen Mensual
            </a>
            @can('crear_fichajes')
            <a href="{{ route('fichajes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Fichaje
            </a>
            @endcan
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-clock-history text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Fichajes</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_fichajes']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h3 class="mb-0">{{ number_format($stats['pendientes_validar']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-hourglass-split text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Horas Mes</h6>
                            <h3 class="mb-0">{{ number_format($stats['horas_totales'], 1) }}h</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-clock-fill text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Horas Extra</h6>
                            <h3 class="mb-0">{{ number_format($stats['horas_extra'], 1) }}h</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('fichajes.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control"
                           value="{{ request('fecha_desde', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control"
                           value="{{ request('fecha_hasta', now()->endOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trabajador</label>
                    <select name="trabajador_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($trabajadores as $trabajador)
                            <option value="{{ $trabajador->id }}" {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>
                                {{ $trabajador->nombre }} {{ $trabajador->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Obra</label>
                    <select name="obra_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                {{ $obra->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="validado" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('validado') === '1' ? 'selected' : '' }}>Validados</option>
                        <option value="0" {{ request('validado') === '0' ? 'selected' : '' }}>Pendientes</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <form id="validarMultipleForm" action="{{ route('fichajes.validar-multiple') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Fecha</th>
                                <th>Trabajador</th>
                                <th>Obra</th>
                                <th class="text-center">Entrada</th>
                                <th class="text-center">Salida</th>
                                <th class="text-center">Horas</th>
                                <th class="text-center">Extra</th>
                                <th class="text-center">Estado</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fichajes as $fichaje)
                                <tr>
                                    <td>
                                        @if(!$fichaje->validado)
                                            <input type="checkbox" class="form-check-input fichaje-checkbox"
                                                   name="fichajes[]" value="{{ $fichaje->id }}">
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $fichaje->fecha->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $fichaje->fecha->translatedFormat('l') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('trabajadores.show', $fichaje->trabajador) }}" class="text-decoration-none">
                                            {{ $fichaje->trabajador->nombre }} {{ $fichaje->trabajador->apellidos }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($fichaje->obra)
                                            <a href="{{ route('obras.show', $fichaje->obra) }}" class="text-decoration-none">
                                                {{ Str::limit($fichaje->obra->nombre, 25) }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->hora_entrada)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                {{ \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') }}
                                            </span>
                                            @if($fichaje->latitud_entrada)
                                                <i class="bi bi-geo-alt text-muted ms-1" title="Con ubicación"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->hora_salida)
                                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                                {{ \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') }}
                                            </span>
                                            @if($fichaje->latitud_salida)
                                                <i class="bi bi-geo-alt text-muted ms-1" title="Con ubicación"></i>
                                            @endif
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->horas_trabajadas)
                                            <strong>{{ number_format($fichaje->horas_trabajadas, 1) }}h</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->horas_extra > 0)
                                            <span class="badge bg-info">+{{ number_format($fichaje->horas_extra, 1) }}h</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->validado)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-lg me-1"></i>Validado
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Pendiente
                                            </span>
                                        @endif
                                        @if($fichaje->corregido)
                                            <br><small class="text-muted"><i class="bi bi-pencil"></i> Corregido</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('editar_fichajes')
                                            <a href="{{ route('fichajes.edit', $fichaje) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan

                                            @if(!$fichaje->validado)
                                                @can('validar_fichajes')
                                                <form action="{{ route('fichajes.validar', $fichaje) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Validar">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif

                                            @can('eliminar_fichajes')
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmarEliminar({{ $fichaje->id }})" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No hay fichajes para mostrar
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($fichajes->hasPages())
                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                        <div>
                            @can('validar_fichajes')
                            <button type="submit" class="btn btn-success" id="btnValidarMultiple" disabled>
                                <i class="bi bi-check-all me-2"></i>Validar Seleccionados
                            </button>
                            @endcan
                        </div>
                        <div>
                            {{ $fichajes->withQueryString()->links() }}
                        </div>
                    </div>
                @else
                    <div class="p-3 border-top">
                        @can('validar_fichajes')
                        <button type="submit" class="btn btn-success" id="btnValidarMultiple" disabled>
                            <i class="bi bi-check-all me-2"></i>Validar Seleccionados
                        </button>
                        @endcan
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    // Select all checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.fichaje-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateValidarButton();
    });

    // Update validar button state
    document.querySelectorAll('.fichaje-checkbox').forEach(cb => {
        cb.addEventListener('change', updateValidarButton);
    });

    function updateValidarButton() {
        const checked = document.querySelectorAll('.fichaje-checkbox:checked').length;
        const btn = document.getElementById('btnValidarMultiple');
        if (btn) {
            btn.disabled = checked === 0;
            btn.innerHTML = checked > 0
                ? '<i class="bi bi-check-all me-2"></i>Validar (' + checked + ')'
                : '<i class="bi bi-check-all me-2"></i>Validar Seleccionados';
        }
    }

    // Delete confirmation
    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿Eliminar fichaje?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/fichajes/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
