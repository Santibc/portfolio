@extends('layouts.app')

@section('title', 'Catalogo de EPIs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Catalogo de EPIs</h1>
            <p class="text-muted mb-0">Tipos de Equipos de Proteccion Individual disponibles</p>
        </div>
        @can('crear_epis')
        <a href="{{ route('epi-catalogo.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Tipo de EPI
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
                            <i class="bi bi-shield-check text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Tipos</small>
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
                            <i class="bi bi-calendar-event text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['con_caducidad'] }}</h3>
                            <small class="text-muted">Con Caducidad</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-clipboard-check text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['requieren_revision'] }}</h3>
                            <small class="text-muted">Requieren Revision</small>
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
                            <i class="bi bi-tags text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['categorias'] }}</h3>
                            <small class="text-muted">Categorias</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('epi-catalogo.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre del EPI..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Caducidad</label>
                    <select name="tiene_caducidad" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('tiene_caducidad') === '1' ? 'selected' : '' }}>Con caducidad</option>
                        <option value="0" {{ request('tiene_caducidad') === '0' ? 'selected' : '' }}>Sin caducidad</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Revision</label>
                    <select name="requiere_revision" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('requiere_revision') === '1' ? 'selected' : '' }}>Requiere revision</option>
                        <option value="0" {{ request('requiere_revision') === '0' ? 'selected' : '' }}>No requiere</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('epi-catalogo.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Catalogo -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th>Categoria</th>
                            <th class="text-center">Caducidad</th>
                            <th class="text-center">Revision</th>
                            <th class="text-center">Periodicidad</th>
                            <th class="text-center">Unidades</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($catalogos as $catalogo)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                        <i class="bi bi-shield-check text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $catalogo->nombre }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($catalogo->categoria)
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $catalogo->categoria }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($catalogo->tiene_caducidad)
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bi bi-calendar-event me-1"></i>Si
                                    </span>
                                @else
                                    <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($catalogo->requiere_revision)
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-clipboard-check me-1"></i>Si
                                    </span>
                                @else
                                    <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($catalogo->periodicidad_revision_meses)
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $catalogo->periodicidad_revision_meses }} meses
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($catalogo->inventario_count > 0)
                                    <a href="{{ route('epi-inventario.index', ['epi_catalogo_id' => $catalogo->id]) }}"
                                       class="badge bg-primary text-white text-decoration-none">
                                        {{ $catalogo->inventario_count }} unidades
                                    </a>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    @can('editar_epis')
                                    <a href="{{ route('epi-catalogo.edit', $catalogo) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_epis')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteCatalogo({{ $catalogo->id }}, '{{ $catalogo->nombre }}')" title="Eliminar"
                                            {{ $catalogo->inventario_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-shield-check fs-1 d-block mb-2"></i>
                                No hay tipos de EPIs registrados
                                @can('crear_epis')
                                <br>
                                <a href="{{ route('epi-catalogo.create') }}" class="btn btn-primary btn-sm mt-2">
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
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteCatalogoForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteCatalogo(catalogoId, catalogoNombre) {
    Swal.fire({
        title: '¿Eliminar tipo de EPI?',
        text: `¿Estas seguro de eliminar "${catalogoNombre}"? Esta accion no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteCatalogoForm');
            form.action = `{{ url('epi-catalogo') }}/${catalogoId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
