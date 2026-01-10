@extends('layouts.app')

@section('title', 'Gestión de Obras')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Obras</h1>
            <p class="text-muted mb-0">Administra los proyectos y obras de la empresa</p>
        </div>
        @can('crear_obras')
        <a href="{{ route('obras.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nueva Obra
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
                            <i class="bi bi-building text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Obras</small>
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
                            <i class="bi bi-play-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['en_curso'] }}</h3>
                            <small class="text-muted">En Curso</small>
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
                            <i class="bi bi-check2-circle text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['aprobadas'] }}</h3>
                            <small class="text-muted">Aprobadas</small>
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
                            <i class="bi bi-currency-euro text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['presupuesto_total'], 0, ',', '.') }}€</h3>
                            <small class="text-muted">Presupuesto Activo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('obras.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Código, nombre, cliente..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="presentada" {{ request('estado') == 'presentada' ? 'selected' : '' }}>Presentada</option>
                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="en_curso" {{ request('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                        <option value="pausada" {{ request('estado') == 'pausada' ? 'selected' : '' }}>Pausada</option>
                        <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="obra_tipo_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('obra_tipo_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre_comercial }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Encargado</label>
                    <select name="encargado_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($encargados as $encargado)
                        <option value="{{ $encargado->id }}" {{ request('encargado_id') == $encargado->id ? 'selected' : '' }}>
                            {{ $encargado->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('obras.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Obras -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Obra</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fechas</th>
                            <th class="text-end">Presupuesto</th>
                            <th class="text-center">Equipo</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($obras as $obra)
                        <tr>
                            <td class="ps-4">
                                <div>
                                    <code class="text-primary fw-semibold">{{ $obra->codigo }}</code>
                                    <h6 class="mb-0 mt-1">{{ $obra->nombre }}</h6>
                                    @if($obra->localidad || $obra->provincia)
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $obra->localidad }}{{ $obra->localidad && $obra->provincia ? ', ' : '' }}{{ $obra->provincia }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $obra->cliente->tipo === 'publico' ? 'info' : 'warning' }}-subtle text-{{ $obra->cliente->tipo === 'publico' ? 'info' : 'warning' }}">
                                    {{ $obra->cliente->nombre_comercial }}
                                </span>
                            </td>
                            <td>
                                @if($obra->tipo)
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $obra->tipo->nombre }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $estadoColors = [
                                        'presentada' => 'secondary',
                                        'aprobada' => 'info',
                                        'en_curso' => 'success',
                                        'pausada' => 'warning',
                                        'finalizada' => 'primary',
                                        'cancelada' => 'danger',
                                    ];
                                    $estadoIcons = [
                                        'presentada' => 'file-earmark',
                                        'aprobada' => 'check-circle',
                                        'en_curso' => 'play-circle',
                                        'pausada' => 'pause-circle',
                                        'finalizada' => 'check-all',
                                        'cancelada' => 'x-circle',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $estadoColors[$obra->estado] ?? 'secondary' }}-subtle text-{{ $estadoColors[$obra->estado] ?? 'secondary' }}">
                                    <i class="bi bi-{{ $estadoIcons[$obra->estado] ?? 'circle' }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $obra->estado)) }}
                                </span>
                            </td>
                            <td>
                                @if($obra->fecha_inicio_prevista)
                                    <small>
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $obra->fecha_inicio_prevista->format('d/m/Y') }}
                                        @if($obra->fecha_fin_prevista)
                                            - {{ $obra->fecha_fin_prevista->format('d/m/Y') }}
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($obra->presupuesto)
                                    <strong>{{ number_format($obra->presupuesto, 2, ',', '.') }} €</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($obra->trabajadores_activos_count > 0)
                                    <span class="badge bg-primary" title="Trabajadores">
                                        <i class="bi bi-people"></i> {{ $obra->trabajadores_activos_count }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('obras.show', $obra) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_obras')
                                    <a href="{{ route('obras.edit', $obra) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_obras')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteObra({{ $obra->id }}, '{{ $obra->codigo }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No hay obras que mostrar
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
<form id="deleteObraForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteObra(obraId, obraCodigo) {
    Swal.fire({
        title: '¿Eliminar obra?',
        text: `¿Estás seguro de eliminar la obra "${obraCodigo}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteObraForm');
            form.action = `{{ url('obras') }}/${obraId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
