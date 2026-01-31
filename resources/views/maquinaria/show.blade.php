@extends('layouts.app')

@section('title', $maquinaria->codigo_interno)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <code class="fs-5 text-primary fw-semibold">{{ $maquinaria->codigo_interno }}</code>
                @php
                    $estadoColors = [
                        'operativa' => 'success',
                        'en_reparacion' => 'warning',
                        'baja' => 'danger',
                    ];
                    $estadoLabels = [
                        'operativa' => 'Operativa',
                        'en_reparacion' => 'En Reparacion',
                        'baja' => 'Baja',
                    ];
                @endphp
                <span class="badge bg-{{ $estadoColors[$maquinaria->estado] ?? 'secondary' }}">
                    {{ $estadoLabels[$maquinaria->estado] ?? ucfirst($maquinaria->estado) }}
                </span>
            </div>
            <h1 class="h3 mb-1">{{ $maquinaria->nombre_completo }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-tag me-1"></i>{{ $maquinaria->tipo->nombre ?? 'Sin tipo' }}
                @if($maquinaria->obraAsignada)
                    <span class="mx-2">|</span>
                    <i class="bi bi-geo-alt me-1"></i>Asignada a: <a href="{{ route('obras.show', $maquinaria->obraAsignada) }}">{{ $maquinaria->obraAsignada->nombre }}</a>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @can('editar_maquinaria')
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal">
                <i class="bi bi-flag me-2"></i>Cambiar Estado
            </button>
            <a href="{{ route('maquinaria.edit', $maquinaria) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('maquinaria.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadisticas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['total_inspecciones'] }}</h3>
                    <small class="text-muted">Inspecciones</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">{{ $stats['ultima_inspeccion'] ?? '-' }}</h3>
                    <small class="text-muted">Ultima Inspeccion</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ $stats['total_mantenimientos'] }}</h3>
                    <small class="text-muted">Mantenimientos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-warning">{{ number_format($stats['coste_mantenimientos'], 0, ',', '.') }} EUR</h3>
                    <small class="text-muted">Coste Mantenim.</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ number_format($stats['amortizacion_acumulada'], 2, ',', '.') }} EUR</h3>
                    <small class="text-muted">Amortizacion Acum.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda - Informacion -->
        <div class="col-lg-4">
            <!-- Informacion General -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informacion General</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Tipo</td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $maquinaria->tipo->nombre ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Marca</td>
                            <td>{{ $maquinaria->marca ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modelo</td>
                            <td>{{ $maquinaria->modelo ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">N Serie</td>
                            <td><code>{{ $maquinaria->numero_serie ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">N Bastidor</td>
                            <td><code>{{ $maquinaria->numero_bastidor ?? '-' }}</code></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Datos Economicos -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Datos Economicos</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Fecha Compra</td>
                            <td>{{ $maquinaria->fecha_compra?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Coste Adquisicion</td>
                            <td class="fw-semibold">{{ $maquinaria->coste_adquisicion ? number_format($maquinaria->coste_adquisicion, 2, ',', '.') . ' EUR' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vida Util</td>
                            <td>{{ $maquinaria->vida_util_meses ? $maquinaria->vida_util_meses . ' meses' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Amort. Diaria</td>
                            <td>
                                @if($maquinaria->coste_adquisicion && $maquinaria->vida_util_meses)
                                    {{ number_format($maquinaria->coste_adquisicion / ($maquinaria->vida_util_meses * 30), 4, ',', '.') }} EUR/dia
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Coste/Hora</td>
                            <td>{{ $maquinaria->coste_hora ? number_format($maquinaria->coste_hora, 2, ',', '.') . ' EUR/h' : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Documentacion -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-check me-2"></i>Documentacion</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center">
                            @if($maquinaria->tiene_marcado_ce)
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger me-2"></i>
                            @endif
                            <span>Marcado CE</span>
                        </div>
                        <div class="d-flex align-items-center">
                            @if($maquinaria->tiene_manual)
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger me-2"></i>
                            @endif
                            <span>Manual de Usuario</span>
                        </div>
                    </div>
                    @if($maquinaria->notas)
                        <hr>
                        <p class="text-muted mb-0 small">{{ $maquinaria->notas }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm content-tabs-card">
                <div class="card-header bg-transparent tabs-header">
                    <ul class="nav nav-pills" id="maquinariaTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="asignaciones-tab" data-bs-toggle="tab" data-bs-target="#asignaciones" type="button">
                                <i class="bi bi-geo-alt me-1"></i>Asignaciones
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inspecciones-tab" data-bs-toggle="tab" data-bs-target="#inspecciones" type="button">
                                <i class="bi bi-clipboard-check me-1"></i>Inspecciones
                                <span class="badge bg-secondary ms-1">{{ $maquinaria->inspecciones->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="mantenimientos-tab" data-bs-toggle="tab" data-bs-target="#mantenimientos" type="button">
                                <i class="bi bi-wrench me-1"></i>Mantenimientos
                                <span class="badge bg-secondary ms-1">{{ $maquinaria->mantenimientos->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button">
                                <i class="bi bi-file-earmark me-1"></i>Documentos
                                <span class="badge bg-secondary ms-1">{{ $maquinaria->documentos->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tabs-content">
                    <div class="tab-content" id="maquinariaTabsContent">
                        <!-- Tab Asignaciones -->
                        <div class="tab-pane fade show active" id="asignaciones" role="tabpanel">
                            <!-- Asignacion Actual -->
                            @if($maquinaria->obraAsignada)
                                <div class="alert alert-success d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <strong><i class="bi bi-geo-alt-fill me-2"></i>Asignada actualmente a:</strong>
                                        <a href="{{ route('obras.show', $maquinaria->obraAsignada) }}" class="ms-2">
                                            {{ $maquinaria->obraAsignada->codigo }} - {{ $maquinaria->obraAsignada->nombre }}
                                        </a>
                                        @if($maquinaria->trabajadorAsignado)
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>Operario: {{ $maquinaria->trabajadorAsignado->nombre_completo }}
                                            </small>
                                        @endif
                                    </div>
                                    @can('editar_maquinaria')
                                    <form action="{{ route('maquinaria.desasignar-obra', $maquinaria) }}" method="POST" class="desasignar-form">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-desasignar">
                                            <i class="bi bi-x-lg me-1"></i>Desasignar
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            @else
                                <div class="alert alert-secondary mb-4">
                                    <i class="bi bi-info-circle me-2"></i>Esta maquinaria no esta asignada a ninguna obra actualmente.
                                </div>

                                @can('editar_maquinaria')
                                @if($maquinaria->estado === 'operativa')
                                <form action="{{ route('maquinaria.asignar-obra', $maquinaria) }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <select name="obra_id" class="form-select" required>
                                                <option value="">Seleccionar obra...</option>
                                                @foreach($obrasDisponibles as $obra)
                                                    <option value="{{ $obra->id }}">{{ $obra->codigo }} - {{ $obra->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <select name="trabajador_id" class="form-select">
                                                <option value="">Operario (opcional)...</option>
                                                @foreach($trabajadoresDisponibles as $trabajador)
                                                    <option value="{{ $trabajador->id }}">{{ $trabajador->nombre_completo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-plus-lg"></i> Asignar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                @endif
                                @endcan
                            @endif

                            <!-- Historial de Asignaciones -->
                            <h6 class="text-muted mb-3">Historial de Asignaciones</h6>
                            @if($maquinaria->asignaciones->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Obra</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Fin</th>
                                                <th>Estado</th>
                                                <th>Notas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($maquinaria->asignaciones->sortByDesc('fecha_inicio') as $asignacion)
                                            <tr>
                                                <td>
                                                    @if($asignacion->obra)
                                                        <a href="{{ route('obras.show', $asignacion->obra) }}">
                                                            {{ $asignacion->obra->codigo }} - {{ $asignacion->obra->nombre }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Obra eliminada</span>
                                                    @endif
                                                </td>
                                                <td>{{ $asignacion->fecha_inicio?->format('d/m/Y') }}</td>
                                                <td>{{ $asignacion->fecha_fin?->format('d/m/Y') ?? '-' }}</td>
                                                <td>
                                                    @if(!$asignacion->fecha_fin)
                                                        <span class="badge bg-success-subtle text-success">Activa</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">Finalizada</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($asignacion->notas, 30) ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No hay historial de asignaciones</p>
                            @endif
                        </div>

                        <!-- Tab Inspecciones -->
                        <div class="tab-pane fade" id="inspecciones" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Inspecciones Realizadas</h6>
                                @can('editar_maquinaria')
                                <a href="{{ route('maquinaria.inspecciones.create', $maquinaria) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i>Nueva Inspeccion
                                </a>
                                @endcan
                            </div>

                            @if($maquinaria->inspecciones->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Plantilla</th>
                                                <th>Resultado</th>
                                                <th>Realizado por</th>
                                                <th>Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($maquinaria->inspecciones->sortByDesc('fecha_inspeccion') as $inspeccion)
                                            <tr>
                                                <td>{{ $inspeccion->fecha_inspeccion->format('d/m/Y') }}</td>
                                                <td>{{ $inspeccion->plantilla->nombre ?? '-' }}</td>
                                                <td>
                                                    @if($inspeccion->resultado === 'apto')
                                                        <span class="badge bg-success">Apto</span>
                                                    @else
                                                        <span class="badge bg-danger">No Apto</span>
                                                    @endif
                                                </td>
                                                <td>{{ $inspeccion->realizadoPor->name ?? '-' }}</td>
                                                <td>
                                                    @if($inspeccion->observaciones)
                                                        <small class="text-muted">{{ Str::limit($inspeccion->observaciones, 50) }}</small>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay inspecciones registradas</p>
                                </div>
                            @endif
                        </div>

                        <!-- Tab Mantenimientos -->
                        <div class="tab-pane fade" id="mantenimientos" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Registro de Mantenimientos</h6>
                            </div>

                            @if($maquinaria->mantenimientos->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Descripcion</th>
                                                <th class="text-end">Coste</th>
                                                <th>Proveedor</th>
                                                <th>Proxima</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($maquinaria->mantenimientos->sortByDesc('fecha') as $mantenimiento)
                                            <tr>
                                                <td>{{ $mantenimiento->fecha->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($mantenimiento->tipo === 'preventivo')
                                                        <span class="badge bg-info-subtle text-info">Preventivo</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">Correctivo</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($mantenimiento->descripcion, 40) }}</td>
                                                <td class="text-end fw-semibold">{{ $mantenimiento->coste ? number_format($mantenimiento->coste, 2, ',', '.') . ' EUR' : '-' }}</td>
                                                <td>{{ $mantenimiento->proveedor ?? '-' }}</td>
                                                <td>{{ $mantenimiento->proxima_revision?->format('d/m/Y') ?? '-' }}</td>
                                                <td class="text-end">
                                                    @can('editar_maquinaria')
                                                    <form action="{{ route('maquinaria.mantenimientos.destroy', [$maquinaria, $mantenimiento]) }}" method="POST" class="d-inline delete-mantenimiento-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-mantenimiento">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3">Total</th>
                                                <th class="text-end">{{ number_format($maquinaria->mantenimientos->sum('coste'), 2, ',', '.') }} EUR</th>
                                                <th colspan="3"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-4">No hay mantenimientos registrados</p>
                            @endif

                            <!-- Formulario para agregar mantenimiento -->
                            @can('editar_maquinaria')
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="bi bi-plus-circle me-2"></i>Registrar Mantenimiento</h6>
                                    <form action="{{ route('maquinaria.mantenimientos.store', $maquinaria) }}" method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                                <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                                <select name="tipo" class="form-select" required>
                                                    <option value="preventivo">Preventivo</option>
                                                    <option value="correctivo">Correctivo</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Descripcion <span class="text-danger">*</span></label>
                                                <input type="text" name="descripcion" class="form-control" placeholder="Ej: Cambio de aceite, reparacion de cadena..." required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Coste</label>
                                                <div class="input-group">
                                                    <input type="number" name="coste" class="form-control" step="0.01" min="0" placeholder="0.00">
                                                    <span class="input-group-text">EUR</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Proveedor</label>
                                                <input type="text" name="proveedor" class="form-control" placeholder="Nombre del proveedor">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Proxima Revision</label>
                                                <input type="date" name="fecha_proxima" class="form-control">
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-plus-lg me-1"></i>Registrar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endcan
                        </div>

                        <!-- Tab Documentos -->
                        <div class="tab-pane fade" id="documentos" role="tabpanel">
                            @if($maquinaria->documentos->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Documento</th>
                                                <th>Subido por</th>
                                                <th>Fecha</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($maquinaria->documentos as $documento)
                                            <tr>
                                                <td>
                                                    <a href="{{ asset($documento->archivo_path) }}" target="_blank">
                                                        <i class="bi bi-file-earmark me-1"></i>{{ $documento->nombre }}
                                                    </a>
                                                </td>
                                                <td>{{ $documento->subidoPor->name ?? '-' }}</td>
                                                <td>{{ $documento->created_at->format('d/m/Y') }}</td>
                                                <td class="text-end">
                                                    @canany(['editar_maquinaria', 'subir_documentos_maquinaria'])
                                                    <form action="{{ route('maquinaria.documentos.destroy', [$maquinaria, $documento]) }}" method="POST" class="d-inline delete-documento-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-documento">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcanany
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-4">No hay documentos subidos</p>
                            @endif

                            @canany(['editar_maquinaria', 'subir_documentos_maquinaria'])
                            <form action="{{ route('maquinaria.documentos.store', $maquinaria) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="nombre" class="form-control" placeholder="Nombre del documento" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="file" name="archivo" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-upload"></i> Subir
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endcanany
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Estado -->
@can('editar_maquinaria')
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('maquinaria.cambiar-estado', $maquinaria) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado de la Maquinaria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select name="estado" class="form-select form-select-lg" required>
                            <option value="operativa" {{ $maquinaria->estado == 'operativa' ? 'selected' : '' }}>Operativa</option>
                            <option value="en_reparacion" {{ $maquinaria->estado == 'en_reparacion' ? 'selected' : '' }}>En Reparacion</option>
                            <option value="baja" {{ $maquinaria->estado == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Si la maquinaria esta asignada a una obra y se cambia a "Baja", sera desasignada automaticamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('styles')
<style>
/* Content Tabs - Fix for CSS conflicts */
.content-tabs-card .tab-content,
#maquinariaTabsContent.tab-content {
    display: block !important;
}

#maquinariaTabsContent .tab-pane {
    display: none !important;
}

#maquinariaTabsContent .tab-pane.active {
    display: block !important;
}

.tabs-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, rgba(74, 124, 89, 0.05) 0%, rgba(74, 124, 89, 0.1) 100%);
    border-bottom: 1px solid #e5e7eb;
}

.tabs-header .nav-pills {
    gap: 0.5rem;
}

.tabs-header .nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 10px;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.2s;
    background: transparent;
}

.tabs-header .nav-link:hover {
    background: rgba(74, 124, 89, 0.1);
    color: #4A7C59;
}

.tabs-header .nav-link.active {
    background: #4A7C59;
    color: white;
}

.tabs-content {
    padding: 1.5rem;
    min-height: 300px;
    background: #fff;
}
</style>
@endpush

@push('scripts')
<script>
    // Inicializar tabs manualmente
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('#maquinariaTabs .nav-link');
        const tabPanes = document.querySelectorAll('#maquinariaTabsContent .tab-pane');

        tabButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Remover active de todos los botones
                tabButtons.forEach(btn => btn.classList.remove('active'));
                // Agregar active al boton clickeado
                this.classList.add('active');

                // Obtener el target
                const targetId = this.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetId);

                // Ocultar todos los panes
                tabPanes.forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Mostrar el pane objetivo
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });
    });

    // Desasignar maquinaria
    document.querySelectorAll('.btn-desasignar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.desasignar-form');
            const formAction = form.getAttribute('action');
            const csrfToken = form.querySelector('input[name="_token"]').value;

            Swal.fire({
                title: 'Desasignar maquinaria?',
                text: 'La maquinaria sera desasignada de la obra actual',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, desasignar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear un formulario temporal y enviarlo
                    const tempForm = document.createElement('form');
                    tempForm.method = 'POST';
                    tempForm.action = formAction;
                    tempForm.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
                    document.body.appendChild(tempForm);
                    tempForm.submit();
                }
            });
        });
    });

    // Eliminar mantenimiento
    document.querySelectorAll('.btn-delete-mantenimiento').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-mantenimiento-form');
            Swal.fire({
                title: 'Eliminar mantenimiento?',
                text: 'Esta accion no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Eliminar documento
    document.querySelectorAll('.btn-delete-documento').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-documento-form');
            Swal.fire({
                title: 'Eliminar documento?',
                text: 'Esta accion no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
