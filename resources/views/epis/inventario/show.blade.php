@extends('layouts.app')

@section('title', 'Detalle EPI')

@section('content')
<div class="container-fluid py-4">
    @php
        $estadoColors = [
            'disponible' => 'success',
            'asignado' => 'info',
            'en_revision' => 'warning',
            'baja' => 'danger',
        ];
        $estadoIcons = [
            'disponible' => 'check-circle',
            'asignado' => 'person-check',
            'en_revision' => 'clock',
            'baja' => 'x-circle',
        ];
    @endphp

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3">
                <h1 class="h3 mb-0">{{ $epiInventario->catalogo->nombre ?? 'EPI' }}</h1>
                <span class="badge bg-{{ $estadoColors[$epiInventario->estado] ?? 'secondary' }}">
                    <i class="bi bi-{{ $estadoIcons[$epiInventario->estado] ?? 'circle' }} me-1"></i>
                    {{ ucfirst(str_replace('_', ' ', $epiInventario->estado)) }}
                </span>
            </div>
            <p class="text-muted mb-0">
                @if($epiInventario->numero_serie)
                    S/N: {{ $epiInventario->numero_serie }} |
                @endif
                {{ $epiInventario->catalogo->categoria ?? 'Sin categoria' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @can('editar_epis')
            <a href="{{ route('epi-inventario.edit', $epiInventario) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('epi-inventario.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda - Info -->
        <div class="col-lg-4">
            <!-- Informacion del EPI -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Informacion</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Tipo</small>
                            <strong>{{ $epiInventario->catalogo->nombre ?? '-' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Categoria</small>
                            <strong>{{ $epiInventario->catalogo->categoria ?? '-' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">N Serie</small>
                            <strong>{{ $epiInventario->numero_serie ?? '-' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Coste</small>
                            <strong>{{ $epiInventario->coste ? number_format($epiInventario->coste, 2, ',', '.') . ' €' : '-' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Fecha Compra</small>
                            <strong>{{ $epiInventario->fecha_compra?->format('d/m/Y') ?? '-' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Caducidad</small>
                            @if($epiInventario->fecha_caducidad)
                                @php
                                    $caducidadProxima = $epiInventario->fecha_caducidad->lte(now()->addDays(30));
                                    $caducado = $epiInventario->fecha_caducidad->lt(now());
                                @endphp
                                <strong class="{{ $caducado ? 'text-danger' : ($caducidadProxima ? 'text-warning' : '') }}">
                                    @if($caducado)
                                        <i class="bi bi-exclamation-triangle me-1"></i>CADUCADO
                                    @elseif($caducidadProxima)
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                    @endif
                                    {{ $epiInventario->fecha_caducidad->format('d/m/Y') }}
                                </strong>
                            @else
                                <strong>Sin caducidad</strong>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asignacion Actual -->
            @if($entregaActual)
            <div class="card border-0 shadow-sm mb-4 border-start border-4 border-info">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-person-check me-2"></i>Asignado a</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-person-fill text-info fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">
                                <a href="{{ route('trabajadores.show', $entregaActual->trabajador) }}" class="text-decoration-none">
                                    {{ $entregaActual->trabajador->nombre_completo ?? 'Trabajador' }}
                                </a>
                            </h6>
                            <small class="text-muted">Desde {{ $entregaActual->fecha_entrega->format('d/m/Y') }}</small>
                        </div>
                    </div>
                    @can('editar_epis')
                    <button type="button" class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#devolverModal">
                        <i class="bi bi-arrow-return-left me-2"></i>Devolver EPI
                    </button>
                    @endcan
                </div>
            </div>
            @endif

            <!-- Configuracion del tipo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Configuracion</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tiene caducidad</span>
                        @if($epiInventario->catalogo->tiene_caducidad)
                            <span class="badge bg-warning-subtle text-warning">Si</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">No</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Requiere revision</span>
                        @if($epiInventario->catalogo->requiere_revision)
                            <span class="badge bg-info-subtle text-info">Cada {{ $epiInventario->catalogo->periodicidad_revision_meses }} meses</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">No</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            @can('editar_epis')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($epiInventario->estado === 'disponible')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#entregarModal">
                            <i class="bi bi-box-arrow-right me-2"></i>Entregar a Trabajador
                        </button>
                        @endif

                        @if($epiInventario->catalogo->requiere_revision)
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#revisionModal">
                            <i class="bi bi-clipboard-check me-2"></i>Registrar Revision
                        </button>
                        @endif

                        @if($epiInventario->estado !== 'baja' && $epiInventario->estado !== 'asignado')
                        <form id="formDarBaja" action="{{ route('epi-inventario.baja', $epiInventario) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-outline-danger w-100" onclick="confirmarBaja()">
                                <i class="bi bi-x-circle me-2"></i>Dar de Baja
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endcan
        </div>

        <!-- Columna Derecha - Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#entregas" type="button">
                                <i class="bi bi-arrow-left-right me-2"></i>Entregas
                                <span class="badge bg-primary ms-1">{{ $epiInventario->entregas->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#revisiones" type="button">
                                <i class="bi bi-clipboard-check me-2"></i>Revisiones
                                <span class="badge bg-info ms-1">{{ $epiInventario->revisiones->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentos" type="button">
                                <i class="bi bi-file-earmark me-2"></i>Documentos
                                <span class="badge bg-secondary ms-1">{{ $epiInventario->documentos->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="epiTabsContent">
                        <!-- Tab Entregas -->
                        <div class="tab-pane active" id="entregas" role="tabpanel">
                            @if($epiInventario->entregas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Trabajador</th>
                                            <th>Fecha Entrega</th>
                                            <th>Fecha Devolucion</th>
                                            <th>Motivo</th>
                                            <th>Entregado por</th>
                                            <th class="text-center">Firma</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($epiInventario->entregas->sortByDesc('fecha_entrega') as $entrega)
                                        <tr class="{{ is_null($entrega->fecha_devolucion) ? 'table-info' : '' }}">
                                            <td>
                                                <a href="{{ route('trabajadores.show', $entrega->trabajador) }}" class="text-decoration-none">
                                                    {{ $entrega->trabajador->nombre_completo ?? '-' }}
                                                </a>
                                            </td>
                                            <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                                            <td>
                                                @if($entrega->fecha_devolucion)
                                                    {{ $entrega->fecha_devolucion->format('d/m/Y') }}
                                                @else
                                                    <span class="badge bg-info">En uso</span>
                                                @endif
                                            </td>
                                            <td>{{ $entrega->motivo_devolucion ?? '-' }}</td>
                                            <td>{{ $entrega->entregadoPor->name ?? '-' }}</td>
                                            <td class="text-center">
                                                @if($entrega->firma_trabajador_path)
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="verFirma('{{ asset($entrega->firma_trabajador_path) }}')">
                                                    <i class="bi bi-pen"></i>
                                                </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-arrow-left-right fs-1 d-block mb-2"></i>
                                <p class="mb-0">Este EPI nunca ha sido entregado</p>
                            </div>
                            @endif
                        </div>

                        <!-- Tab Revisiones -->
                        <div class="tab-pane" id="revisiones" role="tabpanel">
                            @if($epiInventario->revisiones->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Resultado</th>
                                            <th>Proxima</th>
                                            <th>Observaciones</th>
                                            <th>Realizado por</th>
                                            <th class="text-center">Doc</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($epiInventario->revisiones->sortByDesc('fecha_revision') as $revision)
                                        @php
                                            $resultadoColors = [
                                                'apto' => 'success',
                                                'no_apto' => 'danger',
                                                'requiere_reparacion' => 'warning',
                                            ];
                                        @endphp
                                        <tr>
                                            <td>{{ $revision->fecha_revision->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $resultadoColors[$revision->resultado] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $revision->resultado)) }}
                                                </span>
                                            </td>
                                            <td>{{ $revision->proxima_revision?->format('d/m/Y') ?? '-' }}</td>
                                            <td>{{ Str::limit($revision->observaciones, 50) ?? '-' }}</td>
                                            <td>{{ $revision->realizadoPor->name ?? '-' }}</td>
                                            <td class="text-center">
                                                @if($revision->documento_path)
                                                <a href="{{ asset($revision->documento_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                                @else
                                                    <span class="text-muted">-</span>
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
                                <p class="mb-0">No hay revisiones registradas</p>
                                @if($epiInventario->catalogo->requiere_revision)
                                    @can('editar_epis')
                                    <button type="button" class="btn btn-info btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#revisionModal">
                                        <i class="bi bi-plus-lg me-1"></i>Registrar primera revision
                                    </button>
                                    @endcan
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Tab Documentos -->
                        <div class="tab-pane" id="documentos" role="tabpanel">
                            @if($epiInventario->documentos->count() > 0)
                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Documento</th>
                                            <th>Subido por</th>
                                            <th>Fecha</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($epiInventario->documentos->sortByDesc('created_at') as $documento)
                                        <tr>
                                            <td>
                                                <a href="{{ asset($documento->archivo_path) }}" target="_blank" class="text-decoration-none">
                                                    <i class="bi bi-file-earmark me-1"></i>{{ $documento->nombre }}
                                                </a>
                                            </td>
                                            <td>{{ $documento->subidoPor->name ?? '-' }}</td>
                                            <td>{{ $documento->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                <a href="{{ asset($documento->archivo_path) }}" target="_blank" class="btn btn-sm btn-outline-info me-1">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @can('editar_epis')
                                                <form action="{{ route('epi-inventario.documentos.destroy', [$epiInventario, $documento]) }}" method="POST" class="d-inline delete-documento-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-documento">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark fs-1 d-block mb-2"></i>
                                <p class="mb-0">No hay documentos subidos</p>
                            </div>
                            @endif

                            <!-- Formulario subir documento -->
                            @can('editar_epis')
                            <div class="card bg-light border-0 mt-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="bi bi-upload me-2"></i>Subir Documento</h6>
                                    <form action="{{ route('epi-inventario.documentos.store', $epiInventario) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <input type="text" name="nombre" class="form-control" placeholder="Nombre del documento" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="file" name="archivo" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-upload me-1"></i>Subir
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Máximo 10MB.</small>
                                    </form>
                                </div>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Entregar EPI -->
@can('editar_epis')
@if($epiInventario->estado === 'disponible')
<div class="modal fade" id="entregarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('epi-inventario.entregar', $epiInventario) }}" method="POST" id="formEntrega">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-right me-2"></i>Entregar EPI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Trabajador <span class="text-danger">*</span></label>
                            <select name="trabajador_id" class="form-select" required>
                                <option value="">Seleccionar trabajador...</option>
                                @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Entrega <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_entrega" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Firma del Trabajador <span class="text-danger">*</span></label>
                            <div class="border rounded p-2 bg-white">
                                <canvas id="signaturePadEntrega" width="600" height="200" class="w-100" style="border: 1px dashed #ccc;"></canvas>
                            </div>
                            <input type="hidden" name="firma" id="firmaDataEntrega" required>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature('Entrega')">
                                    <i class="bi bi-eraser me-1"></i>Borrar firma
                                </button>
                            </div>
                            <small class="text-muted">El trabajador debe firmar en el recuadro de arriba</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-2"></i>Confirmar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endcan

<!-- Modal Devolver EPI -->
@can('editar_epis')
@if($entregaActual)
<div class="modal fade" id="devolverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('epi-inventario.devolver', $epiInventario) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Devolver EPI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Trabajador:</strong> {{ $entregaActual->trabajador->nombre_completo ?? '-' }}<br>
                        <strong>Entregado:</strong> {{ $entregaActual->fecha_entrega->format('d/m/Y') }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Devolucion <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_devolucion" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo de Devolucion <span class="text-danger">*</span></label>
                        <select name="motivo_devolucion" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="Fin de uso">Fin de uso</option>
                            <option value="Deterioro">Deterioro</option>
                            <option value="Caducidad">Caducidad</option>
                            <option value="Baja del trabajador">Baja del trabajador</option>
                            <option value="Cambio de EPI">Cambio de EPI</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado del EPI tras devolucion <span class="text-danger">*</span></label>
                        <select name="nuevo_estado" class="form-select" required>
                            <option value="disponible">Disponible (puede volver a entregarse)</option>
                            <option value="en_revision">En revision (necesita inspeccion)</option>
                            <option value="baja">Baja (deteriorado/caducado)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-2"></i>Confirmar Devolucion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endcan

<!-- Modal Revision -->
@can('editar_epis')
<div class="modal fade" id="revisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('epi-inventario.revisiones.store', $epiInventario) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-clipboard-check me-2"></i>Registrar Revision</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha de Revision <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_revision" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resultado <span class="text-danger">*</span></label>
                        <select name="resultado" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="apto">Apto</option>
                            <option value="no_apto">No Apto (dar de baja)</option>
                            <option value="requiere_reparacion">Requiere reparacion</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proxima Revision</label>
                        <input type="date" name="proxima_revision" class="form-control">
                        <small class="text-muted">
                            @if($epiInventario->catalogo->periodicidad_revision_meses)
                                Se sugiere cada {{ $epiInventario->catalogo->periodicidad_revision_meses }} meses
                            @endif
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Documento (opcional)</label>
                        <input type="file" name="documento" class="form-control">
                        <small class="text-muted">Máximo 10MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-check-lg me-2"></i>Guardar Revision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modal Ver Firma -->
<div class="modal fade" id="firmaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pen me-2"></i>Firma del Trabajador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="firmaImage" src="" alt="Firma" class="img-fluid border">
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* CRITICAL: Override gva-components.css that hides .tab-content */
#epiTabsContent.tab-content {
    display: block !important;
}

#epiTabsContent .tab-pane {
    display: none !important;
}

#epiTabsContent .tab-pane.active {
    display: block !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
<script>
// Signature Pad para entrega
let signaturePadEntrega = null;

document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signaturePadEntrega');
    if (canvas) {
        signaturePadEntrega = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Actualizar input hidden cuando cambie la firma
        signaturePadEntrega.addEventListener('endStroke', function() {
            document.getElementById('firmaDataEntrega').value = signaturePadEntrega.toDataURL('image/png');
        });

        // Redimensionar canvas cuando se abre el modal
        const modal = document.getElementById('entregarModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = 200 * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePadEntrega.clear();
            });
        }
    }

    // Validar firma antes de enviar
    const formEntrega = document.getElementById('formEntrega');
    if (formEntrega) {
        formEntrega.addEventListener('submit', function(e) {
            if (signaturePadEntrega && signaturePadEntrega.isEmpty()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Firma requerida',
                    text: 'El trabajador debe firmar antes de continuar.'
                });
            }
        });
    }
});

function clearSignature(tipo) {
    if (tipo === 'Entrega' && signaturePadEntrega) {
        signaturePadEntrega.clear();
        document.getElementById('firmaDataEntrega').value = '';
    }
}

function verFirma(url) {
    document.getElementById('firmaImage').src = url;
    new bootstrap.Modal(document.getElementById('firmaModal')).show();
}

// Manejar cambio de tabs manualmente
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tabBtn) {
    tabBtn.addEventListener('click', function(e) {
        e.preventDefault();

        // Quitar active de todos los botones y panes
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(btn) {
            btn.classList.remove('active');
        });
        document.querySelectorAll('#epiTabsContent .tab-pane').forEach(function(pane) {
            pane.classList.remove('active');
        });

        // Activar el botón y pane seleccionado
        this.classList.add('active');
        const target = this.getAttribute('data-bs-target');
        const pane = document.querySelector(target);
        if (pane) {
            pane.classList.add('active');
        }
    });
});

// Confirmar dar de baja con SweetAlert2
function confirmarBaja() {
    Swal.fire({
        title: '¿Dar de baja este EPI?',
        text: 'Esta accion marcara el EPI como dado de baja.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, dar de baja',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formDarBaja').submit();
        }
    });
}

// Eliminar documento con confirmacion
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
