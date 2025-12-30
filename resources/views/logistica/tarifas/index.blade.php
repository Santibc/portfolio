<x-app-layout>
    <x-slot name="header">Tarifas de Envio</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-cash-coin text-success me-2"></i>Tarifas por Zona
                        </h4>
                        <div>
                            <a href="{{ route('logistica.zonas.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left me-1"></i> Zonas
                            </a>
                            <a href="{{ route('logistica.tarifas.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Nueva Tarifa
                            </a>
                        </div>
                    </div>

                    @if($zonas->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-cash-coin display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Primero crea una zona</h5>
                            <p class="text-muted">Necesitas tener al menos una zona de cobertura para crear tarifas.</p>
                            <a href="{{ route('logistica.zonas.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-geo-alt me-1"></i> Crear Zona
                            </a>
                        </div>
                    @else
                        @foreach($zonas as $zona)
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                    <div>
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <strong>{{ $zona->nombre }}</strong>
                                        @if(!$zona->activo)
                                            <span class="badge bg-secondary ms-2">Inactiva</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('logistica.tarifas.create', $zona->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-plus me-1"></i> Agregar Tarifa
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($zona->tarifas->isEmpty())
                                        <p class="text-muted mb-0 text-center py-3">
                                            <i class="bi bi-info-circle me-1"></i>
                                            No hay tarifas configuradas para esta zona.
                                        </p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Costo Base</th>
                                                        <th>Min. Compra</th>
                                                        <th>Envio Gratis</th>
                                                        <th>Tiempo</th>
                                                        <th>Estado</th>
                                                        <th class="text-end">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($zona->tarifas as $tarifa)
                                                        <tr>
                                                            <td><strong>{{ $tarifa->nombre }}</strong></td>
                                                            <td>
                                                                <span class="text-success fw-bold">
                                                                    ${{ number_format($tarifa->costo_base, 0) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($tarifa->minimo_compra > 0)
                                                                    ${{ number_format($tarifa->minimo_compra, 0) }}
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($tarifa->envio_gratis_desde && $tarifa->monto_envio_gratis)
                                                                    <span class="badge bg-info">
                                                                        Desde ${{ number_format($tarifa->monto_envio_gratis, 0) }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">No</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $tarifa->tiempo_entrega_horas }}h</td>
                                                            <td>
                                                                @if($tarifa->activo)
                                                                    <span class="badge bg-success">Activo</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactivo</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('logistica.tarifas.edit', $tarifa->id) }}" class="btn btn-outline-primary" title="Editar">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <form action="{{ route('logistica.tarifas.destroy', $tarifa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta tarifa?')">
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
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
