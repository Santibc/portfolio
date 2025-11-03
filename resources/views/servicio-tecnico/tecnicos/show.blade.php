<x-app-layout>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-person-gear me-2"></i>Detalle del Técnico</h2>
                    <p class="text-muted">Información completa y órdenes asignadas</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('st.tecnicos.edit', $tecnico->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Editar
                    </a>
                    <a href="{{ route('st.tecnicos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Información Personal --}}
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Información Personal</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Código:</th>
                            <td><span class="badge bg-primary">{{ $tecnico->codigo }}</span></td>
                        </tr>
                        <tr>
                            <th>Nombre Completo:</th>
                            <td>{{ $tecnico->nombre_completo }}</td>
                        </tr>
                        <tr>
                            <th>Documento:</th>
                            <td>{{ $tecnico->documento }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>
                                @if($tecnico->telefono)
                                    <i class="bi bi-telephone"></i> {{ $tecnico->telefono }}
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Celular:</th>
                            <td>
                                @if($tecnico->celular)
                                    <i class="bi bi-phone"></i> {{ $tecnico->celular }}
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>
                                @if($tecnico->email)
                                    <i class="bi bi-envelope"></i> {{ $tecnico->email }}
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($tecnico->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Información Profesional --}}
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-tools me-2"></i>Información Profesional</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Especialidad:</th>
                            <td>
                                @if($tecnico->especialidad)
                                    <span class="badge bg-secondary">{{ $tecnico->especialidad }}</span>
                                @else
                                    <span class="text-muted">No especificada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Ingreso:</th>
                            <td>
                                @if($tecnico->fecha_ingreso)
                                    {{ $tecnico->fecha_ingreso->format('d/m/Y') }}
                                    <small class="text-muted">({{ $tecnico->fecha_ingreso->diffForHumans() }})</small>
                                @else
                                    <span class="text-muted">No especificada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Órdenes Activas:</th>
                            <td>
                                <span class="badge bg-{{ $tecnico->ordenesServicio->count() > 5 ? 'danger' : 'success' }} fs-6">
                                    {{ $tecnico->ordenesServicio->count() }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    @if($tecnico->certificaciones)
                        <div class="mt-3">
                            <h6><strong>Certificaciones:</strong></h6>
                            <div class="p-3 bg-light rounded">
                                {{ $tecnico->certificaciones }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Órdenes de Servicio Asignadas --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Órdenes de Servicio Asignadas</h6>
                </div>
                <div class="card-body">
                    @if($tecnico->ordenesServicio->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>N° Orden</th>
                                        <th>Cliente</th>
                                        <th>Equipo</th>
                                        <th>Tipo Servicio</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Fecha Ingreso</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tecnico->ordenesServicio as $orden)
                                        <tr>
                                            <td><strong>{{ $orden->numero_orden }}</strong></td>
                                            <td>{{ $orden->cliente->nombre_completo }}</td>
                                            <td>
                                                @if($orden->equipo)
                                                    {{ $orden->equipo->marca }} {{ $orden->equipo->modelo }}
                                                @else
                                                    <span class="text-muted">Sin equipo</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($orden->tipo_servicio) }}</td>
                                            <td>
                                                @php
                                                    $prioridadBadge = [
                                                        'baja' => 'secondary',
                                                        'media' => 'info',
                                                        'alta' => 'warning',
                                                        'urgente' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $prioridadBadge[$orden->prioridad] ?? 'secondary' }}">
                                                    {{ ucfirst($orden->prioridad) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $estadoBadge = [
                                                        'recibida' => 'secondary',
                                                        'asignada' => 'info',
                                                        'en_proceso' => 'primary',
                                                        'pendiente_repuestos' => 'warning',
                                                        'completada' => 'success',
                                                        'entregada' => 'success',
                                                        'cancelada' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $estadoBadge[$orden->estado] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                                </span>
                                            </td>
                                            <td>{{ $orden->fecha_recepcion ? $orden->fecha_recepcion->format('d/m/Y') : 'N/A' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('st.ordenes.show', $orden->id) }}" class="btn btn-sm btn-info" title="Ver orden">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>Este técnico no tiene órdenes de servicio asignadas actualmente.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>