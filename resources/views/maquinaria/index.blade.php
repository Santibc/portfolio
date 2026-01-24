@extends('layouts.app')

@section('title', 'Gestión de Maquinaria')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Maquinaria</h1>
            <p class="text-muted mb-0">Administra el inventario de maquinaria y equipos</p>
        </div>
        @can('crear_maquinaria')
        <a href="{{ route('maquinaria.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nueva Maquinaria
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
                            <i class="bi bi-tools text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Maquinaria</small>
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
                            <h3 class="mb-0">{{ $stats['operativas'] }}</h3>
                            <small class="text-muted">Operativas</small>
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
                            <i class="bi bi-wrench text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['en_reparacion'] }}</h3>
                            <small class="text-muted">En Reparación</small>
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
                            <i class="bi bi-currency-euro text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['valor_total'], 0, ',', '.') }}€</h3>
                            <small class="text-muted">Valor Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('maquinaria.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Código, marca, modelo, serie..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="maquinaria_tipo_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('maquinaria_tipo_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="operativa" {{ request('estado') == 'operativa' ? 'selected' : '' }}>Operativa</option>
                        <option value="en_reparacion" {{ request('estado') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                        <option value="baja" {{ request('estado') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Obra Asignada</label>
                    <select name="obra_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($obras as $obra)
                        <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                            {{ $obra->codigo }} - {{ Str::limit($obra->nombre, 20) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Disponibilidad</label>
                    <select name="disponible" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" {{ request('disponible') == '1' ? 'selected' : '' }}>Solo disponibles</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('maquinaria.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Maquinaria -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Maquinaria</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Obra Asignada</th>
                            <th>Trabajador</th>
                            <th class="text-end">Valor</th>
                            <th class="text-center">Registros</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maquinarias as $maquinaria)
                        <tr>
                            <td class="ps-4">
                                <div>
                                    @if($maquinaria->codigo_interno)
                                        <code class="text-primary fw-semibold">{{ $maquinaria->codigo_interno }}</code>
                                    @endif
                                    <h6 class="mb-0 mt-1">
                                        {{ $maquinaria->marca }} {{ $maquinaria->modelo }}
                                    </h6>
                                    @if($maquinaria->numero_serie)
                                        <small class="text-muted">
                                            <i class="bi bi-upc me-1"></i>S/N: {{ $maquinaria->numero_serie }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $maquinaria->tipo->nombre ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $estadoColors = [
                                        'operativa' => 'success',
                                        'en_reparacion' => 'warning',
                                        'baja' => 'danger',
                                    ];
                                    $estadoIcons = [
                                        'operativa' => 'check-circle',
                                        'en_reparacion' => 'wrench',
                                        'baja' => 'x-circle',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $estadoColors[$maquinaria->estado] ?? 'secondary' }}-subtle text-{{ $estadoColors[$maquinaria->estado] ?? 'secondary' }}">
                                    <i class="bi bi-{{ $estadoIcons[$maquinaria->estado] ?? 'circle' }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $maquinaria->estado)) }}
                                </span>
                            </td>
                            <td>
                                @if($maquinaria->obraAsignada)
                                    <a href="{{ route('obras.show', $maquinaria->obraAsignada) }}" class="text-decoration-none">
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $maquinaria->obraAsignada->codigo }}
                                        </span>
                                    </a>
                                @else
                                    <span class="text-muted">Disponible</span>
                                @endif
                            </td>
                            <td>
                                @if($maquinaria->trabajadorAsignado)
                                    <small>{{ $maquinaria->trabajadorAsignado->nombre_completo }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($maquinaria->coste_adquisicion)
                                    <strong>{{ number_format($maquinaria->coste_adquisicion, 2, ',', '.') }} €</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($maquinaria->inspecciones_count > 0)
                                    <span class="badge bg-info-subtle text-info" title="Inspecciones">
                                        <i class="bi bi-clipboard-check"></i> {{ $maquinaria->inspecciones_count }}
                                    </span>
                                @endif
                                @if($maquinaria->mantenimientos_count > 0)
                                    <span class="badge bg-warning-subtle text-warning" title="Mantenimientos">
                                        <i class="bi bi-gear"></i> {{ $maquinaria->mantenimientos_count }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('maquinaria.show', $maquinaria) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_maquinaria')
                                    <a href="{{ route('maquinaria.edit', $maquinaria) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_maquinaria')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteMaquinaria({{ $maquinaria->id }}, '{{ $maquinaria->codigo_interno ?? $maquinaria->id }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-tools fs-1 d-block mb-2"></i>
                                No hay maquinaria registrada
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
<form id="deleteMaquinariaForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteMaquinaria(maquinariaId, maquinariaCodigo) {
    Swal.fire({
        title: '¿Eliminar maquinaria?',
        text: `¿Estás seguro de eliminar "${maquinariaCodigo}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteMaquinariaForm');
            form.action = `{{ url('maquinaria') }}/${maquinariaId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
