<x-app-layout>
    <x-slot name="header">Detalle del Cliente</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <p class="text-muted mb-3">Información completa del cliente y sus equipos</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('st.clientes.edit', $cliente->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="{{ route('st.clientes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

    {{-- Información del Cliente --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Información General</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Tipo de Cliente</h6>
                    <p class="mb-0">
                        @if($cliente->tipo_cliente === 'empresa')
                            <span class="badge bg-primary">Empresa</span>
                        @else
                            <span class="badge bg-info">Persona Natural</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Estado</h6>
                    <p class="mb-0">
                        @if($cliente->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">{{ $cliente->tipo_cliente === 'empresa' ? 'Razón Social' : 'Nombre Completo' }}</h6>
                    <p class="mb-0"><strong>{{ $cliente->nombre_completo_formateado }}</strong></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Documento</h6>
                    <p class="mb-0">{{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-1">Email</h6>
                    <p class="mb-0">
                        @if($cliente->email)
                            <a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                        @else
                            <span class="text-muted">No especificado</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-1">Teléfono</h6>
                    <p class="mb-0">{{ $cliente->telefono ?? 'No especificado' }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-1">Celular</h6>
                    <p class="mb-0">{{ $cliente->celular ?? 'No especificado' }}</p>
                </div>
                <div class="col-md-12">
                    <h6 class="text-muted mb-1">Dirección</h6>
                    <p class="mb-0">{{ $cliente->direccion ?? 'No especificada' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Ciudad</h6>
                    <p class="mb-0">{{ $cliente->ciudad ?? 'No especificada' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Departamento</h6>
                    <p class="mb-0">{{ $cliente->departamento ?? 'No especificado' }}</p>
                </div>
                @if($cliente->observaciones)
                <div class="col-12">
                    <h6 class="text-muted mb-1">Observaciones</h6>
                    <p class="mb-0">{{ $cliente->observaciones }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Equipos del Cliente --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-camera-video me-2"></i>Equipos Registrados</h5>
            <a href="{{ route('st.equipos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-sm btn-light">
                <i class="bi bi-plus-circle"></i> Nuevo Equipo
            </a>
        </div>
        <div class="card-body">
            @if($cliente->equipos && $cliente->equipos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Marca/Modelo</th>
                                <th>N° Serie</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th>Garantía</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cliente->equipos as $equipo)
                                <tr>
                                    <td>{{ $equipo->tipo_equipo }}</td>
                                    <td>{{ $equipo->marca }} {{ $equipo->modelo }}</td>
                                    <td><code>{{ $equipo->numero_serie }}</code></td>
                                    <td>{{ $equipo->ubicacion_instalacion ?? '-' }}</td>
                                    <td>
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
                                    </td>
                                    <td>
                                        @if($equipo->en_garantia)
                                            <span class="badge bg-success">Sí</span>
                                            @if($equipo->vencimiento_garantia)
                                                <br><small class="text-muted">Hasta: {{ $equipo->vencimiento_garantia->format('d/m/Y') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('st.equipos.show', $equipo->id) }}" class="btn btn-info" title="Ver Detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('st.equipos.edit', $equipo->id) }}" class="btn btn-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-camera-video-off" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-2">No hay equipos registrados para este cliente</p>
                    <a href="{{ route('st.equipos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Registrar Primer Equipo
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Órdenes de Servicio del Cliente --}}
    <div class="card shadow">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-wrench me-2"></i>Órdenes de Servicio</h5>
            <a href="{{ route('st.ordenes.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-sm btn-dark">
                <i class="bi bi-plus-circle"></i> Nueva Orden
            </a>
        </div>
        <div class="card-body">
            @if($cliente->ordenesServicio && $cliente->ordenesServicio->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>N° Orden</th>
                                <th>Equipo</th>
                                <th>Tipo Servicio</th>
                                <th>Técnico</th>
                                <th>Estado</th>
                                <th>Fecha Recepción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cliente->ordenesServicio->take(10) as $orden)
                                <tr>
                                    <td><strong>{{ $orden->numero_orden }}</strong></td>
                                    <td>{{ $orden->equipo->tipo_equipo ?? 'N/A' }} - {{ $orden->equipo->modelo ?? '' }}</td>
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
                                    <td>{{ $orden->fecha_recepcion->format('d/m/Y') }}</td>
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
                @if($cliente->ordenesServicio->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('st.ordenes.index', ['cliente_id' => $cliente->id]) }}" class="btn btn-sm btn-outline-primary">
                            Ver todas las órdenes ({{ $cliente->ordenesServicio->count() }})
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-2">No hay órdenes de servicio para este cliente</p>
                </div>
            @endif
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
        </div>
    </div>
</x-app-layout>
