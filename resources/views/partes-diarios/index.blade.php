@extends('layouts.app')

@section('title', 'Partes Diarios')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Partes de Producción</h1>
            <p class="text-muted mb-0">Registro de trabajo en obras (diarios y mensuales)</p>
        </div>
        @can('crear_partes')
        <div class="btn-group">
            <a href="{{ route('partes-diarios.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Parte Diario
            </a>
            <a href="{{ route('partes-diarios.create', ['tipo' => 'mensual']) }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-range me-2"></i>Parte Mensual
            </a>
        </div>
        @endcan
    </div>

    <!-- Stats Cards -->
    @php
        $iconosPorCategoria = [
            'desbroce' => ['icon' => 'bi-scissors', 'color' => 'success'],
            'limpieza' => ['icon' => 'bi-stars', 'color' => 'info'],
            'herbicida' => ['icon' => 'bi-droplet', 'color' => 'danger'],
            'tala' => ['icon' => 'bi-tree', 'color' => 'warning'],
            'poda' => ['icon' => 'bi-flower1', 'color' => 'primary'],
            'otro' => ['icon' => 'bi-box', 'color' => 'secondary'],
        ];
        $unidadesFormato = [
            'm2' => 'm²',
            'unidades' => 'uds',
            'hectareas' => 'ha',
            'jornal' => 'j',
        ];
    @endphp
    <div class="row g-3 mb-4">
        <!-- Card Total Partes -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Partes</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_partes']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Borradores -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-pencil text-secondary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Borradores</h6>
                            <h3 class="mb-0">{{ number_format($stats['borradores']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Pendientes -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h3 class="mb-0">{{ number_format($stats['pendientes_validar']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cards dinámicas por categoría de producción -->
        @foreach($stats['categorias_activas'] as $categoria => $unidad)
            @php
                $icono = $iconosPorCategoria[$categoria] ?? $iconosPorCategoria['otro'];
                $cantidad = $stats['produccion_por_categoria'][$categoria] ?? 0;
                $unidadFormato = $unidadesFormato[$unidad] ?? $unidad;
            @endphp
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-{{ $icono['color'] }} bg-opacity-10 p-3 rounded">
                                    <i class="bi {{ $icono['icon'] }} text-{{ $icono['color'] }} fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ ucfirst($categoria) }}</h6>
                                <h3 class="mb-0">{{ number_format($cantidad, 0, ',', '.') }}</h3>
                                <small class="text-muted">{{ $unidadFormato }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('partes-diarios.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control"
                           value="{{ request('fecha_desde', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control"
                           value="{{ request('fecha_hasta', now()->endOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Obra</label>
                    <select name="obra_id" class="form-select">
                        <option value="">Todas las obras</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                {{ $obra->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="validado" {{ request('estado') === 'validado' ? 'selected' : '' }}>Validado</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="diario" {{ request('tipo') === 'diario' ? 'selected' : '' }}>Diario</option>
                        <option value="mensual" {{ request('tipo') === 'mensual' ? 'selected' : '' }}>Mensual</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Obra</th>
                            <th class="text-center">Jornada</th>
                            @foreach($stats['categorias_activas'] as $categoria => $unidad)
                                <th class="text-center">
                                    {{ ucfirst($categoria) }}
                                    <small class="d-block text-muted fw-normal">({{ $unidadesFormato[$unidad] ?? $unidad }})</small>
                                </th>
                            @endforeach
                            <th class="text-center">Estado</th>
                            <th width="140">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partes as $parte)
                            <tr>
                                <td>
                                    @if($parte->es_mensual)
                                        <span class="badge bg-info-subtle text-info mb-1"><i class="bi bi-calendar-range me-1"></i>Mensual</span>
                                        <br><strong>{{ $parte->fecha_display }}</strong>
                                    @else
                                        <strong>{{ $parte->fecha->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $parte->fecha->translatedFormat('l') }}</small>
                                    @endif
                                    @if($parte->documentos_count > 0)
                                        <i class="bi bi-paperclip text-muted ms-1" title="{{ $parte->documentos_count }} adjunto(s)"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('obras.show', $parte->obra) }}" class="text-decoration-none">
                                        {{ Str::limit($parte->obra->nombre, 30) }}
                                    </a>
                                    @if($parte->linea)
                                        <br><small class="text-muted">Línea: {{ $parte->linea }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($parte->es_mensual)
                                        <span class="text-muted">-</span>
                                    @elseif($parte->jornada === 'diurna')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-sun me-1"></i>Diurna
                                        </span>
                                    @else
                                        <span class="badge bg-dark">
                                            <i class="bi bi-moon me-1"></i>Nocturna
                                        </span>
                                    @endif
                                </td>
                                @php $produccionParte = $parte->produccion_por_categoria; @endphp
                                @foreach($stats['categorias_activas'] as $categoria => $unidad)
                                    <td class="text-center">
                                        @if(($produccionParte[$categoria] ?? 0) > 0)
                                            <strong>{{ number_format($produccionParte[$categoria], 0, ',', '.') }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    @switch($parte->estado)
                                        @case('borrador')
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-pencil me-1"></i>Borrador
                                            </span>
                                            @break
                                        @case('completado')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Pendiente
                                            </span>
                                            @break
                                        @case('validado')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-lg me-1"></i>Validado
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('partes-diarios.show', $parte) }}" class="btn btn-outline-secondary" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($parte->estado !== 'validado')
                                            @can('editar_partes')
                                            <a href="{{ route('partes-diarios.edit', $parte) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan

                                            @if($parte->estado === 'borrador')
                                                @can('editar_partes')
                                                <form action="{{ route('partes-diarios.completar', $parte) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning" title="Marcar completado">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif

                                            @if($parte->estado === 'completado')
                                                @can('validar_partes')
                                                <form action="{{ route('partes-diarios.validar', $parte) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Validar">
                                                        <i class="bi bi-check-all"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif

                                            @can('eliminar_partes')
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmarEliminar({{ $parte->id }})" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 5 + count($stats['categorias_activas']) }}" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay partes para mostrar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($partes->hasPages())
                <div class="d-flex justify-content-center p-3 border-top">
                    {{ $partes->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿Eliminar parte diario?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/partes-diarios/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
