@extends('layouts.app')

@section('title', 'Partes Diarios')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Partes Diarios</h1>
            <p class="text-muted mb-0">Registro diario de trabajo en obras</p>
        </div>
        @can('crear_partes')
        <a href="{{ route('partes-diarios.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Parte
        </a>
        @endcan
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Partes</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_partes']) }}</h3>
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
                            <div class="bg-secondary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-pencil text-secondary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Borradores</h6>
                            <h3 class="mb-0">{{ number_format($stats['borradores']) }}</h3>
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
                                <i class="bi bi-clock text-warning fs-4"></i>
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
                                <i class="bi bi-rulers text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Desbroce (m²)</h6>
                            <h3 class="mb-0">{{ number_format($stats['desbroce_total'], 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('partes-diarios.index') }}" method="GET" class="row g-3">
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
                    <label class="form-label">Obra</label>
                    <select name="obra_id" class="form-select">
                        <option value="">Todas las obras</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                {{ $obra->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="validado" {{ request('estado') === 'validado' ? 'selected' : '' }}>Validado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jornada</label>
                    <select name="jornada" class="form-select">
                        <option value="">Todas</option>
                        <option value="diurna" {{ request('jornada') === 'diurna' ? 'selected' : '' }}>Diurna</option>
                        <option value="nocturna" {{ request('jornada') === 'nocturna' ? 'selected' : '' }}>Nocturna</option>
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
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Obra</th>
                            <th class="text-center">Jornada</th>
                            <th class="text-center">Desbroce</th>
                            <th class="text-center">Herbicida</th>
                            <th class="text-center">Talas</th>
                            <th class="text-center">Estado</th>
                            <th width="140">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partes as $parte)
                            <tr>
                                <td>
                                    <strong>{{ $parte->fecha->format('d/m/Y') }}</strong>
                                    <br><small class="text-muted">{{ $parte->fecha->translatedFormat('l') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('obras.show', $parte->obra) }}" class="text-decoration-none">
                                        {{ Str::limit($parte->obra->nombre, 30) }}
                                    </a>
                                    @if($parte->linea)
                                        <br><small class="text-muted">Línea: {{ $parte->linea }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($parte->jornada === 'diurna')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-sun me-1"></i>Diurna
                                        </span>
                                    @else
                                        <span class="badge bg-dark">
                                            <i class="bi bi-moon me-1"></i>Nocturna
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($parte->desbroce_total_m2 > 0)
                                        <strong>{{ number_format($parte->desbroce_total_m2, 0, ',', '.') }}</strong> m²
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($parte->herbicida_p4_m2 > 0)
                                        {{ number_format($parte->herbicida_p4_m2, 0, ',', '.') }} m²
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($parte->talas_unidades > 0 || $parte->podas_unidades > 0)
                                        {{ $parte->talas_unidades }} / {{ $parte->podas_unidades }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @switch($parte->estado)
                                        @case('borrador')
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-pencil me-1"></i>Borrador
                                            </span>
                                            @break
                                        @case('completado')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Pendiente
                                            </span>
                                            @break
                                        @case('validado')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-lg me-1"></i>Validado
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('partes-diarios.show', $parte) }}" class="btn btn-outline-secondary" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($parte->estado !== 'validado')
                                            @can('editar_partes')
                                            <a href="{{ route('partes-diarios.edit', $parte) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan

                                            @if($parte->estado === 'borrador')
                                                @can('editar_partes')
                                                <form action="{{ route('partes-diarios.completar', $parte) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning" title="Marcar completado">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif

                                            @if($parte->estado === 'completado')
                                                @can('validar_partes')
                                                <form action="{{ route('partes-diarios.validar', $parte) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Validar">
                                                        <i class="bi bi-check-all"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif

                                            @can('eliminar_partes')
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmarEliminar({{ $parte->id }})" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay partes diarios para mostrar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($partes->hasPages())
                <div class="d-flex justify-content-center p-3 border-top">
                    {{ $partes->withQueryString()->links() }}
                </div>
            @endif
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
    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿Eliminar parte diario?',
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
                form.action = '/partes-diarios/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
