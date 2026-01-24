@extends('layouts.app')

@section('title', 'Historial de Entregas de EPIs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Historial de Entregas de EPIs</h1>
            <p class="text-muted mb-0">Registro completo de entregas y devoluciones</p>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-arrow-left-right text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total_entregas'] }}</h3>
                            <small class="text-muted">Total Entregas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-person-check text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['entregas_activas'] }}</h3>
                            <small class="text-muted">Actualmente Entregados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-check text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['entregas_mes'] }}</h3>
                            <small class="text-muted">Este Mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('epi-entregas.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Trabajador</label>
                    <select name="trabajador_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($trabajadores as $trabajador)
                        <option value="{{ $trabajador->id }}" {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>
                            {{ $trabajador->nombre_completo }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
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
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="solo_activas" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" {{ request('solo_activas') === '1' ? 'selected' : '' }}>Solo activas</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('epi-entregas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Entregas -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Trabajador</th>
                            <th>EPI</th>
                            <th>Fecha Entrega</th>
                            <th>Fecha Devolucion</th>
                            <th>Motivo</th>
                            <th>Entregado por</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Firma</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entregas as $entrega)
                        <tr class="{{ is_null($entrega->fecha_devolucion) ? 'table-info' : '' }}">
                            <td class="ps-4">
                                <a href="{{ route('trabajadores.show', $entrega->trabajador) }}" class="text-decoration-none fw-semibold">
                                    {{ $entrega->trabajador->nombre_completo ?? '-' }}
                                </a>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $entrega->inventario->catalogo->nombre ?? '-' }}</strong>
                                    @if($entrega->inventario->numero_serie)
                                    <br><small class="text-muted">S/N: {{ $entrega->inventario->numero_serie }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                            <td>
                                @if($entrega->fecha_devolucion)
                                    {{ $entrega->fecha_devolucion->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $entrega->motivo_devolucion ?? '-' }}</td>
                            <td>{{ $entrega->entregadoPor->name ?? '-' }}</td>
                            <td class="text-center">
                                @if($entrega->fecha_devolucion)
                                    <span class="badge bg-secondary">Devuelto</span>
                                @else
                                    <span class="badge bg-success">En uso</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($entrega->firma_trabajador_path)
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="verFirma('{{ asset($entrega->firma_trabajador_path) }}')">
                                    <i class="bi bi-pen"></i>
                                </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('epi-inventario.show', $entrega->inventario) }}" class="btn btn-sm btn-outline-info" title="Ver EPI">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-arrow-left-right fs-1 d-block mb-2"></i>
                                No hay entregas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($entregas->hasPages())
        <div class="card-footer bg-transparent">
            {{ $entregas->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Ver Firma -->
<div class="modal fade" id="firmaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pen me-2"></i>Firma del Trabajador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="firmaImage" src="" alt="Firma" class="img-fluid border">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verFirma(url) {
    document.getElementById('firmaImage').src = url;
    new bootstrap.Modal(document.getElementById('firmaModal')).show();
}
</script>
@endpush
@endsection
