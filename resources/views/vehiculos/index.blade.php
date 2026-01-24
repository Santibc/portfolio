@extends('layouts.app')

@section('title', 'Gestión de Vehículos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Vehículos</h1>
            <p class="text-muted mb-0">Administra la flota de vehículos de la empresa</p>
        </div>
        @can('crear_vehiculos')
        <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Vehículo
        </a>
        @endcan
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-truck text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['operativos'] }}</h3>
                            <small class="text-muted">Operativos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-wrench text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['en_taller'] }}</h3>
                            <small class="text-muted">En Taller</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-currency-euro text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['valor_total'], 0, ',', '.') }}</h3>
                            <small class="text-muted">Valor Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['itv_proxima'] }}</h3>
                            <small class="text-muted">ITV Próxima</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-shield-exclamation text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['seguro_proximo'] }}</h3>
                            <small class="text-muted">Seguro Próx.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('vehiculos.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Matrícula, marca, modelo..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="vehiculo_tipo_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('vehiculo_tipo_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="operativo" {{ request('estado') == 'operativo' ? 'selected' : '' }}>Operativo</option>
                        <option value="en_taller" {{ request('estado') == 'en_taller' ? 'selected' : '' }}>En Taller</option>
                        <option value="baja" {{ request('estado') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Alertas</label>
                    <div class="d-flex flex-column gap-1">
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="checkbox" name="itv_proxima" value="1" id="itv_proxima"
                                   {{ request('itv_proxima') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="itv_proxima">ITV próxima</label>
                        </div>
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="checkbox" name="seguro_proximo" value="1" id="seguro_proximo"
                                   {{ request('seguro_proximo') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="seguro_proximo">Seguro próximo</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Vehículos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Vehículo</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>ITV</th>
                            <th>Seguro</th>
                            <th>Conductor</th>
                            <th class="text-end">Valor</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiculos as $vehiculo)
                        @php
                            $estadoColors = [
                                'operativo' => 'success',
                                'en_taller' => 'warning',
                                'baja' => 'danger',
                            ];
                            $estadoLabels = [
                                'operativo' => 'Operativo',
                                'en_taller' => 'En Taller',
                                'baja' => 'Baja',
                            ];
                            $itvColors = [
                                'vencida' => 'danger',
                                'proxima' => 'warning',
                                'vigente' => 'success',
                                'sin_datos' => 'secondary',
                            ];
                            $itvLabels = [
                                'vencida' => 'Vencida',
                                'proxima' => 'Próxima',
                                'vigente' => 'Vigente',
                                'sin_datos' => 'Sin datos',
                            ];
                            $seguroColors = [
                                'vencido' => 'danger',
                                'proximo' => 'warning',
                                'vigente' => 'success',
                                'sin_datos' => 'secondary',
                            ];
                            $seguroLabels = [
                                'vencido' => 'Vencido',
                                'proximo' => 'Próximo',
                                'vigente' => 'Vigente',
                                'sin_datos' => 'Sin datos',
                            ];
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div>
                                    <code class="text-primary fw-semibold">{{ $vehiculo->matricula }}</code>
                                    <h6 class="mb-0 mt-1">
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                    </h6>
                                    @if($vehiculo->numero_bastidor)
                                        <small class="text-muted">
                                            <i class="bi bi-upc me-1"></i>{{ Str::limit($vehiculo->numero_bastidor, 20) }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $vehiculo->tipo->nombre ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $estadoColors[$vehiculo->estado] ?? 'secondary' }}-subtle text-{{ $estadoColors[$vehiculo->estado] ?? 'secondary' }}">
                                    {{ $estadoLabels[$vehiculo->estado] ?? ucfirst($vehiculo->estado) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $itvColors[$vehiculo->itv_status] ?? 'secondary' }}-subtle text-{{ $itvColors[$vehiculo->itv_status] ?? 'secondary' }}">
                                    <i class="bi bi-card-checklist me-1"></i>
                                    {{ $itvLabels[$vehiculo->itv_status] ?? '-' }}
                                </span>
                                @if($vehiculo->fecha_proxima_itv)
                                    <br><small class="text-muted">{{ $vehiculo->fecha_proxima_itv->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $seguroColors[$vehiculo->seguro_status] ?? 'secondary' }}-subtle text-{{ $seguroColors[$vehiculo->seguro_status] ?? 'secondary' }}">
                                    <i class="bi bi-shield me-1"></i>
                                    {{ $seguroLabels[$vehiculo->seguro_status] ?? '-' }}
                                </span>
                                @if($vehiculo->fecha_vencimiento_seguro)
                                    <br><small class="text-muted">{{ $vehiculo->fecha_vencimiento_seguro->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                @if($vehiculo->conductorHabitual)
                                    <small>{{ $vehiculo->conductorHabitual->nombre_completo }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($vehiculo->coste_adquisicion)
                                    <strong>{{ number_format($vehiculo->coste_adquisicion, 2, ',', '.') }} €</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('editar_vehiculos')
                                    <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar_vehiculos')
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteVehiculo({{ $vehiculo->id }}, '{{ $vehiculo->matricula }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-truck fs-1 d-block mb-2"></i>
                                No hay vehículos registrados
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
<form id="deleteVehiculoForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteVehiculo(vehiculoId, matricula) {
    Swal.fire({
        title: '¿Eliminar vehículo?',
        text: `¿Estás seguro de eliminar el vehículo "${matricula}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteVehiculoForm');
            form.action = `{{ url('vehiculos') }}/${vehiculoId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
