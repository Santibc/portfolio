<x-app-layout>
    <x-slot name="header">Reglas de Capacidad</x-slot>

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
                            <i class="bi bi-calendar-event text-danger me-2"></i>Reglas de Capacidad
                        </h4>
                        <div>
                            <a href="{{ route('logistica.zonas.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left me-1"></i> Zonas
                            </a>
                            <a href="{{ route('logistica.capacidad.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Nueva Regla
                            </a>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Las reglas de capacidad te permiten limitar pedidos en fechas especiales como
                        <strong>San Valentin</strong>, <strong>Dia de la Madre</strong>, <strong>Navidad</strong>, etc.
                    </div>

                    @if($reglas->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-event display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay reglas de capacidad</h5>
                            <p class="text-muted">Crea reglas para fechas especiales donde necesites controlar el volumen de pedidos.</p>
                            <a href="{{ route('logistica.capacidad.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Crear Primera Regla
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Capacidad por Dia</th>
                                        <th>Capacidad por Hora</th>
                                        <th>Notas</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reglas as $regla)
                                        @php
                                            $esPasada = $regla->fecha->isPast();
                                            $esHoy = $regla->fecha->isToday();
                                            $esFutura = $regla->fecha->isFuture();
                                        @endphp
                                        <tr class="{{ $esPasada ? 'table-secondary' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($esHoy)
                                                        <span class="badge bg-warning text-dark me-2">HOY</span>
                                                    @elseif($esFutura)
                                                        <i class="bi bi-calendar-check text-success me-2"></i>
                                                    @else
                                                        <i class="bi bi-calendar-x text-muted me-2"></i>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $regla->fecha->format('d/m/Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $regla->fecha->locale('es')->dayName }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">{{ $regla->capacidad_total_dia }}</span>
                                                <small class="text-muted">pedidos</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info fs-6">{{ $regla->capacidad_por_hora }}</span>
                                                <small class="text-muted">/ hora</small>
                                            </td>
                                            <td>
                                                @if($regla->notas)
                                                    <span title="{{ $regla->notas }}">
                                                        {{ Str::limit($regla->notas, 40) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($regla->activo)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('logistica.capacidad.edit', $regla->id) }}" class="btn btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('logistica.capacidad.destroy', $regla->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta regla de capacidad?')">
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

            {{-- Fechas sugeridas --}}
            <div class="card mt-4">
                <div class="card-header">
                    <i class="bi bi-lightbulb text-warning me-2"></i>Fechas especiales sugeridas
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-auto">
                            <span class="badge bg-danger p-2">14 Feb - San Valentin</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-pink p-2" style="background: #e91e8c;">Dia de la Madre</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-info p-2">Dia del Padre</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-success p-2">24/25 Dic - Navidad</span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-warning text-dark p-2">31 Dic - Año Nuevo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
