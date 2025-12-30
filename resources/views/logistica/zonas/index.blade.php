<x-app-layout>
    <x-slot name="header">Zonas de Cobertura</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de éxito/error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>Zonas de Cobertura
                        </h4>
                        <a href="{{ route('logistica.zonas.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Nueva Zona
                        </a>
                    </div>

                    <p class="text-muted mb-4">
                        Define las zonas geográficas donde realizas entregas. Cada zona puede incluir múltiples ciudades.
                    </p>

                    @if($zonas->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-geo-alt display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay zonas configuradas</h5>
                            <p class="text-muted">Crea tu primera zona de cobertura para empezar a configurar entregas.</p>
                            <a href="{{ route('logistica.zonas.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Crear Primera Zona
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Orden</th>
                                        <th>Nombre</th>
                                        <th>Ciudades</th>
                                        <th>Tarifas</th>
                                        <th>Horarios</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($zonas as $zona)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $zona->orden }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $zona->nombre }}</strong>
                                                @if($zona->descripcion)
                                                    <br><small class="text-muted">{{ Str::limit($zona->descripcion, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $ciudadesCount = count($zona->ciudades_ids ?? []);
                                                @endphp
                                                <span class="badge bg-info">{{ $ciudadesCount }} ciudad(es)</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $zona->tarifas->count() }}</span>
                                                <a href="{{ route('logistica.tarifas.create', $zona->id) }}" class="btn btn-sm btn-link p-0 ms-1" title="Agregar tarifa">
                                                    <i class="bi bi-plus-circle"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">{{ $zona->horarios->count() }}</span>
                                                <a href="{{ route('logistica.horarios.create', $zona->id) }}" class="btn btn-sm btn-link p-0 ms-1" title="Agregar horario">
                                                    <i class="bi bi-plus-circle"></i>
                                                </a>
                                            </td>
                                            <td>
                                                @if($zona->activo)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('logistica.zonas.edit', $zona->id) }}" class="btn btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('logistica.zonas.destroy', $zona->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta zona? Se eliminarán también sus tarifas y horarios.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Accesos rápidos --}}
            <div class="row mt-4">
                <div class="col-md-4">
                    <a href="{{ route('logistica.tarifas.index') }}" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-cash-coin display-4 text-success"></i>
                            <h5 class="mt-2">Tarifas</h5>
                            <p class="text-muted small mb-0">Gestiona costos de envío</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('logistica.horarios.index') }}" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clock display-4 text-warning"></i>
                            <h5 class="mt-2">Horarios</h5>
                            <p class="text-muted small mb-0">Define franjas de entrega</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('logistica.capacidad.index') }}" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-event display-4 text-danger"></i>
                            <h5 class="mt-2">Capacidad</h5>
                            <p class="text-muted small mb-0">Límites por fecha especial</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
