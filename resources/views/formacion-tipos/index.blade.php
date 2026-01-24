@extends('layouts.app')

@section('title', 'Tipos de Formacion')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tipos de Formacion</h1>
            <p class="text-muted mb-0">Catalogo de formaciones y certificaciones disponibles</p>
        </div>
        @can('crear_formaciones')
        <a href="{{ route('formacion-tipos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Tipo
        </a>
        @endcan
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-mortarboard text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total_tipos'] }}</h3>
                            <small class="text-muted">Tipos de Formacion</small>
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
                            <h3 class="mb-0">{{ $stats['tipos_obligatorios'] }}</h3>
                            <small class="text-muted">Obligatorias</small>
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
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['proximas_caducar'] }}</h3>
                            <small class="text-muted">Proximas a Caducar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-x-circle text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['caducadas'] }}</h3>
                            <small class="text-muted">Caducadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('formacion-tipos.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre o descripcion..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo</label>
                    <select name="obligatoria" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" {{ request('obligatoria') === '1' ? 'selected' : '' }}>Solo Obligatorias</option>
                        <option value="0" {{ request('obligatoria') === '0' ? 'selected' : '' }}>Solo Opcionales</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('formacion-tipos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Tipos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th class="text-center">Duracion</th>
                            <th class="text-center">Periodicidad</th>
                            <th class="text-center">Obligatoria</th>
                            <th class="text-center">Registros</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipos as $tipo)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                        <i class="bi bi-mortarboard text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $tipo->nombre }}</h6>
                                        @if($tipo->descripcion)
                                        <small class="text-muted">{{ Str::limit($tipo->descripcion, 60) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($tipo->duracion_horas)
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $tipo->duracion_horas }}h
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tipo->periodicidad_meses)
                                    <span class="badge bg-info-subtle text-info">
                                        {{ $tipo->periodicidad_meses }} meses
                                    </span>
                                @else
                                    <span class="text-muted">Sin caducidad</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tipo->obligatoria)
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Obligatoria
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success">Opcional</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tipo->formaciones_count > 0)
                                    <a href="{{ route('formacion-tipos.show', $tipo) }}"
                                       class="badge bg-primary text-white text-decoration-none">
                                        {{ $tipo->formaciones_count }} registros
                                    </a>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('formacion-tipos.show', $tipo) }}" class="btn btn-sm btn-outline-secondary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_formaciones')
                                    <a href="{{ route('formacion-tipos.edit', $tipo) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_formaciones')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteTipo({{ $tipo->id }}, '{{ $tipo->nombre }}')" title="Eliminar"
                                            {{ $tipo->formaciones_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>
                                No hay tipos de formacion registrados
                                @can('crear_formaciones')
                                <br>
                                <a href="{{ route('formacion-tipos.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="bi bi-plus-lg me-1"></i>Crear primer tipo
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tipos->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $tipos->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteTipoForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteTipo(tipoId, tipoNombre) {
    Swal.fire({
        title: '¿Eliminar tipo de formacion?',
        text: `¿Estas seguro de eliminar "${tipoNombre}"? Esta accion no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteTipoForm');
            form.action = `{{ url('formacion-tipos') }}/${tipoId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
