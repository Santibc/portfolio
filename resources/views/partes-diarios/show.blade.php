@extends('layouts.app')

@section('title', 'Parte Diario - ' . $partes_diario->fecha->format('d/m/Y'))

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Parte Diario</h1>
            <p class="text-muted mb-0">
                {{ $partes_diario->obra->nombre }} - {{ $partes_diario->fecha->format('d/m/Y') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($partes_diario->estado !== 'validado')
                @can('editar_partes')
                <a href="{{ route('partes-diarios.edit', $partes_diario) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Editar
                </a>
                @endcan

                @if($partes_diario->estado === 'borrador')
                    @can('editar_partes')
                    <form action="{{ route('partes-diarios.completar', $partes_diario) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check me-2"></i>Marcar Completado
                        </button>
                    </form>
                    @endcan
                @endif

                @if($partes_diario->estado === 'completado')
                    @can('validar_partes')
                    <form action="{{ route('partes-diarios.validar', $partes_diario) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-all me-2"></i>Validar
                        </button>
                    </form>
                    @endcan
                @endif
            @endif

            <!-- Duplicar -->
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#duplicarModal">
                <i class="bi bi-copy me-2"></i>Duplicar
            </button>

            <a href="{{ route('partes-diarios.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Información General -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Información General</h5>
                    @switch($partes_diario->estado)
                        @case('borrador')
                            <span class="badge bg-secondary fs-6">Borrador</span>
                            @break
                        @case('completado')
                            <span class="badge bg-warning text-dark fs-6">Pendiente Validación</span>
                            @break
                        @case('validado')
                            <span class="badge bg-success fs-6">Validado</span>
                            @break
                    @endswitch
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="40%">Obra:</td>
                                    <td>
                                        <a href="{{ route('obras.show', $partes_diario->obra) }}">
                                            {{ $partes_diario->obra->nombre }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Cliente:</td>
                                    <td>{{ $partes_diario->obra->cliente->nombre_comercial ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Fecha:</td>
                                    <td><strong>{{ $partes_diario->fecha->format('d/m/Y') }}</strong> ({{ $partes_diario->fecha->translatedFormat('l') }})</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jornada:</td>
                                    <td>
                                        @if($partes_diario->jornada === 'diurna')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-sun me-1"></i>Diurna</span>
                                        @else
                                            <span class="badge bg-dark"><i class="bi bi-moon me-1"></i>Nocturna</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="40%">Línea:</td>
                                    <td>{{ $partes_diario->linea ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Trayecto:</td>
                                    <td>{{ $partes_diario->trayecto ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Gerencia:</td>
                                    <td>{{ $partes_diario->gerencia_jefatura ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Brigada:</td>
                                    <td>{{ $partes_diario->brigada ?: 'MANZER' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Producción Valorada -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Producción Valorada
                    </h5>
                    @unless(auth()->user()->hasRole('Encargado'))
                        @if($partes_diario->producciones->count() > 0)
                            <span class="badge bg-success fs-6">{{ $partes_diario->importe_total_formateado }}</span>
                        @else
                            <span class="badge bg-secondary fs-6">0,00 €</span>
                        @endif
                    @else
                        @if($partes_diario->producciones->count() > 0)
                            <span class="badge bg-info fs-6">{{ $partes_diario->producciones->count() }} conceptos</span>
                        @else
                            <span class="badge bg-secondary fs-6">Sin producción</span>
                        @endif
                    @endunless
                </div>
                <div class="card-body">
                    @if($partes_diario->producciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Concepto</th>
                                        <th class="text-center">Unidad</th>
                                        <th class="text-end">Cantidad</th>
                                        @unless(auth()->user()->hasRole('Encargado'))
                                            <th class="text-end">Precio Unit.</th>
                                            <th class="text-end">Importe</th>
                                        @endunless
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partes_diario->producciones as $produccion)
                                    <tr>
                                        <td><code class="fw-bold">{{ $produccion->concepto->codigo }}</code></td>
                                        <td>{{ $produccion->concepto->nombre }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $produccion->concepto->unidad }}</span>
                                        </td>
                                        <td class="text-end fw-semibold">{{ number_format($produccion->cantidad, 2, ',', '.') }}</td>
                                        @unless(auth()->user()->hasRole('Encargado'))
                                            <td class="text-end">{{ number_format($produccion->precio_unitario, 2, ',', '.') }} €</td>
                                            <td class="text-end fw-bold text-success">{{ number_format($produccion->importe_calculado, 2, ',', '.') }} €</td>
                                        @endunless
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        @unless(auth()->user()->hasRole('Encargado'))
                                            <th colspan="5" class="text-end">Total del Día:</th>
                                            <th class="text-end text-success fs-5">{{ $partes_diario->importe_total_formateado }}</th>
                                        @else
                                            <th colspan="4" class="text-end">Total de Producción Registrada</th>
                                        @endunless
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <!-- Sin producción registrada -->
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">No hay producción registrada en este parte</p>
                            @if($partes_diario->estado !== 'validado')
                                <a href="{{ route('partes-diarios.edit', $partes_diario) }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar Producción
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Observaciones -->
            @if($partes_diario->observaciones || $partes_diario->incidencias)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Observaciones e Incidencias</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @if($partes_diario->observaciones)
                        <div class="col-md-6">
                            <h6 class="text-muted">Observaciones</h6>
                            <p class="mb-0">{{ $partes_diario->observaciones }}</p>
                        </div>
                        @endif
                        @if($partes_diario->incidencias)
                        <div class="col-md-6">
                            <h6 class="text-danger">Incidencias</h6>
                            <p class="mb-0">{{ $partes_diario->incidencias }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Trabajadores -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Trabajadores</h5>
                    <span class="badge bg-primary">{{ $partes_diario->trabajadores->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($partes_diario->trabajadores->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($partes_diario->trabajadores as $pt)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('trabajadores.show', $pt->trabajador) }}" class="text-decoration-none">
                                            {{ $pt->trabajador->nombre }} {{ $pt->trabajador->apellidos }}
                                        </a>
                                        @if($pt->es_aplicador)
                                            <br><small class="text-info"><i class="bi bi-droplet"></i> Aplicador</small>
                                        @endif
                                    </div>
                                    @if($pt->horas_trabajadas)
                                        <span class="badge bg-light text-dark">{{ $pt->horas_trabajadas }}h</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3 text-center text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            No hay trabajadores asignados
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadatos -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Información del Registro</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Creado por:</td>
                            <td>{{ $partes_diario->creadoPor->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creado:</td>
                            <td>{{ $partes_diario->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Actualizado:</td>
                            <td>{{ $partes_diario->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Duplicar -->
<div class="modal fade" id="duplicarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('partes-diarios.duplicar', $partes_diario) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Duplicar Parte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Se creará una copia de este parte para la nueva fecha seleccionada.</p>
                    <div class="mb-3">
                        <label class="form-label">Nueva Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-copy me-2"></i>Duplicar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
