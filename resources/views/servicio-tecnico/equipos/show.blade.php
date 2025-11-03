<x-app-layout>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-camera-video me-2"></i>Detalle del Equipo</h2>
                    <p class="text-muted">Información completa del equipo y su historial</p>
                </div>
                <div>
                    <a href="{{ route('st.equipos.edit', $equipo->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="{{ route('st.equipos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            {{-- Información General --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Tipo de Equipo</h6>
                            <p class="mb-0"><strong>{{ $equipo->tipo_equipo }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Estado</h6>
                            <p class="mb-0">
                                @php
                                    $estadoBadge = [
                                        'operativo' => 'success',
                                        'en_reparacion' => 'warning',
                                        'fuera_servicio' => 'danger',
                                        'en_bodega' => 'secondary'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $estadoBadge[$equipo->estado] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $equipo->estado)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Marca</h6>
                            <p class="mb-0">{{ $equipo->marca ?? 'No especificada' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Modelo</h6>
                            <p class="mb-0">{{ $equipo->modelo ?? 'No especificado' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Número de Serie</h6>
                            <p class="mb-0"><code>{{ $equipo->numero_serie }}</code></p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Dirección MAC</h6>
                            <p class="mb-0">{{ $equipo->mac_address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Dirección IP</h6>
                            <p class="mb-0">{{ $equipo->ip_address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Ubicación</h6>
                            <p class="mb-0">{{ $equipo->ubicacion_instalacion ?? 'No especificada' }}</p>
                        </div>
                        @if($equipo->especificaciones)
                        <div class="col-12">
                            <h6 class="text-muted mb-1">Especificaciones Técnicas</h6>
                            <p class="mb-0">{{ $equipo->especificaciones }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Fechas y Garantía --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Fechas y Garantía</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Fecha de Compra</h6>
                            <p class="mb-0">{{ $equipo->fecha_compra ? $equipo->fecha_compra->format('d/m/Y') : 'No especificada' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Fecha de Instalación</h6>
                            <p class="mb-0">{{ $equipo->fecha_instalacion ? $equipo->fecha_instalacion->format('d/m/Y') : 'No especificada' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">En Garantía</h6>
                            <p class="mb-0">
                                @if($equipo->en_garantia)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Sí</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> No</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Vencimiento de Garantía</h6>
                            <p class="mb-0">
                                @if($equipo->vencimiento_garantia)
                                    {{ $equipo->vencimiento_garantia->format('d/m/Y') }}
                                    @if($equipo->en_garantia && $equipo->vencimiento_garantia->isFuture())
                                        <small class="text-muted">({{ $equipo->vencimiento_garantia->diffForHumans() }})</small>
                                    @endif
                                @else
                                    No especificada
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Historial de Órdenes de Servicio --}}
            <div class="card shadow">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-wrench me-2"></i>Historial de Servicio</h5>
                    <a href="{{ route('st.ordenes.create', ['equipo_id' => $equipo->id]) }}" class="btn btn-sm btn-dark">
                        <i class="bi bi-plus-circle"></i> Nueva Orden
                    </a>
                </div>
                <div class="card-body">
                    @if($equipo->ordenesServicio && $equipo->ordenesServicio->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>N° Orden</th>
                                        <th>Fecha</th>
                                        <th>Tipo Servicio</th>
                                        <th>Técnico</th>
                                        <th>Estado</th>
                                        <th>Costo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipo->ordenesServicio as $orden)
                                        <tr>
                                            <td><strong>{{ $orden->numero_orden }}</strong></td>
                                            <td>{{ $orden->fecha_recepcion->format('d/m/Y') }}</td>
                                            <td>{{ $orden->tipo_servicio }}</td>
                                            <td>{{ $orden->tecnico->nombre_completo ?? 'No asignado' }}</td>
                                            <td>
                                                @php
                                                    $estadoBadge = [
                                                        'recibida' => 'secondary',
                                                        'en_diagnostico' => 'info',
                                                        'esperando_repuestos' => 'warning',
                                                        'en_proceso' => 'primary',
                                                        'completada' => 'success',
                                                        'entregada' => 'dark',
                                                        'cancelada' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $estadoBadge[$orden->estado] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                                </span>
                                            </td>
                                            <td>${{ number_format($orden->costo_total ?? 0, 0, ',', '.') }}</td>
                                            <td>
                                                <a href="{{ route('st.ordenes.show', $orden->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">No hay órdenes de servicio registradas para este equipo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Cliente Propietario --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Cliente Propietario</h5>
                </div>
                <div class="card-body">
                    @if($equipo->cliente)
                        <h6 class="mb-2"><strong>{{ $equipo->cliente->nombre_completo_formateado }}</strong></h6>
                        <p class="mb-1 small text-muted">{{ $equipo->cliente->tipo_documento }}: {{ $equipo->cliente->numero_documento }}</p>
                        @if($equipo->cliente->email)
                            <p class="mb-1 small"><i class="bi bi-envelope"></i> {{ $equipo->cliente->email }}</p>
                        @endif
                        @if($equipo->cliente->telefono)
                            <p class="mb-1 small"><i class="bi bi-telephone"></i> {{ $equipo->cliente->telefono }}</p>
                        @endif
                        @if($equipo->cliente->celular)
                            <p class="mb-1 small"><i class="bi bi-phone"></i> {{ $equipo->cliente->celular }}</p>
                        @endif
                        <div class="mt-3">
                            <a href="{{ route('st.clientes.show', $equipo->cliente->id) }}" class="btn btn-sm btn-outline-info w-100">
                                <i class="bi bi-eye"></i> Ver Cliente
                            </a>
                        </div>
                    @else
                        <p class="text-muted">No hay cliente asociado</p>
                    @endif
                </div>
            </div>

            {{-- Estadísticas del Equipo --}}
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Servicios:</span>
                        <strong>{{ $equipo->ordenesServicio->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Servicios Completados:</span>
                        <strong class="text-success">{{ $equipo->ordenesServicio->where('estado', 'completada')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">En Proceso:</span>
                        <strong class="text-warning">{{ $equipo->ordenesServicio->whereIn('estado', ['recibida', 'en_diagnostico', 'en_proceso'])->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Costo Total Servicios:</span>
                        <strong>${{ number_format($equipo->ordenesServicio->sum('costo_total'), 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.table code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
}
</style>
@endpush
</x-app-layout>
