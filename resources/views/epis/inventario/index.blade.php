@extends('layouts.app')

@section('title', 'Inventario de EPIs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Inventario de EPIs</h1>
            <p class="text-muted mb-0">Gestiona el inventario de Equipos de Proteccion Individual</p>
        </div>
        @can('crear_epis')
        <a href="{{ route('epi-inventario.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo EPI
        </a>
        @endcan
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 d-inline-block mb-2">
                        <i class="bi bi-shield-check text-primary fs-4"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-3 p-2 d-inline-block mb-2">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['disponibles'] }}</h3>
                    <small class="text-muted">Disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-3 p-2 d-inline-block mb-2">
                        <i class="bi bi-person-check text-info fs-4"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['asignados'] }}</h3>
                    <small class="text-muted">Asignados</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-3 p-2 d-inline-block mb-2">
                        <i class="bi bi-clock text-warning fs-4"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['en_revision'] }}</h3>
                    <small class="text-muted">En Revision</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-3 p-2 d-inline-block mb-2">
                        <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['proximos_caducar'] }}</h3>
                    <small class="text-muted">Por Caducar</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-secondary bg-opacity-10 rounded-3 p-2 d-inline-block mb-2">
                        <i class="bi bi-currency-euro text-secondary fs-4"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($stats['valor_total'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Valor Total</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('epi-inventario.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Numero de serie, nombre..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de EPI</label>
                    <select name="epi_catalogo_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($catalogos as $catalogo)
                        <option value="{{ $catalogo->id }}" {{ request('epi_catalogo_id') == $catalogo->id ? 'selected' : '' }}>
                            {{ $catalogo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="asignado" {{ request('estado') == 'asignado' ? 'selected' : '' }}>Asignado</option>
                        <option value="en_revision" {{ request('estado') == 'en_revision' ? 'selected' : '' }}>En revision</option>
                        <option value="baja" {{ request('estado') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Caducidad</label>
                    <select name="proximos_caducar" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('proximos_caducar') === '1' ? 'selected' : '' }}>Proximos 30 dias</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('epi-inventario.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Inventario -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">EPI</th>
                            <th>N Serie</th>
                            <th>Estado</th>
                            <th>Asignado a</th>
                            <th>Fecha Compra</th>
                            <th>Caducidad</th>
                            <th class="text-end">Coste</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventario as $epi)
                        @php
                            $entregaActual = $epi->entregas->first();
                            $estadoColors = [
                                'disponible' => 'success',
                                'asignado' => 'info',
                                'en_revision' => 'warning',
                                'baja' => 'danger',
                            ];
                            $estadoIcons = [
                                'disponible' => 'check-circle',
                                'asignado' => 'person-check',
                                'en_revision' => 'clock',
                                'baja' => 'x-circle',
                            ];
                            $caducidadProxima = $epi->fecha_caducidad && $epi->fecha_caducidad->lte(now()->addDays(30));
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                        <i class="bi bi-shield-check text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $epi->catalogo->nombre ?? 'Sin tipo' }}</h6>
                                        @if($epi->catalogo->categoria)
                                        <small class="text-muted">{{ $epi->catalogo->categoria }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($epi->numero_serie)
                                    <code>{{ $epi->numero_serie }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $estadoColors[$epi->estado] ?? 'secondary' }}-subtle text-{{ $estadoColors[$epi->estado] ?? 'secondary' }}">
                                    <i class="bi bi-{{ $estadoIcons[$epi->estado] ?? 'circle' }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $epi->estado)) }}
                                </span>
                            </td>
                            <td>
                                @if($entregaActual && $entregaActual->trabajador)
                                    <a href="{{ route('trabajadores.show', $entregaActual->trabajador) }}" class="text-decoration-none">
                                        {{ $entregaActual->trabajador->nombre_completo }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $epi->fecha_compra?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>
                                @if($epi->fecha_caducidad)
                                    <span class="{{ $caducidadProxima ? 'text-danger fw-semibold' : '' }}">
                                        @if($caducidadProxima)
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                        @endif
                                        {{ $epi->fecha_caducidad->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Sin caducidad</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($epi->coste)
                                    {{ number_format($epi->coste, 2, ',', '.') }} €
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('epi-inventario.show', $epi) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_epis')
                                    <a href="{{ route('epi-inventario.edit', $epi) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_epis')
                                    @if($epi->estado === 'disponible')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteEpi({{ $epi->id }}, '{{ $epi->catalogo->nombre ?? 'EPI' }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-shield-check fs-1 d-block mb-2"></i>
                                No hay EPIs en el inventario
                                @can('crear_epis')
                                <br>
                                <a href="{{ route('epi-inventario.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="bi bi-plus-lg me-1"></i>Registrar primer EPI
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
<form id="deleteEpiForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteEpi(epiId, epiNombre) {
    Swal.fire({
        title: '¿Eliminar EPI?',
        text: `¿Estas seguro de eliminar "${epiNombre}"? Esta accion no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteEpiForm');
            form.action = `{{ url('epi-inventario') }}/${epiId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
