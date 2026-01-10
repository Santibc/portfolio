@extends('layouts.app')

@section('title', $obra->codigo)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <code class="fs-5 text-primary fw-semibold">{{ $obra->codigo }}</code>
                @php
                    $estadoColors = [
                        'presentada' => 'secondary',
                        'aprobada' => 'info',
                        'en_curso' => 'success',
                        'pausada' => 'warning',
                        'finalizada' => 'primary',
                        'cancelada' => 'danger',
                    ];
                @endphp
                <span class="badge bg-{{ $estadoColors[$obra->estado] ?? 'secondary' }}">
                    {{ ucfirst(str_replace('_', ' ', $obra->estado)) }}
                </span>
                @if($obra->riesgo_operativo === 'alto')
                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Riesgo Alto</span>
                @endif
            </div>
            <h1 class="h3 mb-1">{{ $obra->nombre }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-building me-1"></i>{{ $obra->cliente->nombre_comercial }}
                @if($obra->localidad || $obra->provincia)
                    <span class="mx-2">|</span>
                    <i class="bi bi-geo-alt me-1"></i>{{ $obra->localidad }}{{ $obra->localidad && $obra->provincia ? ', ' : '' }}{{ $obra->provincia }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @can('editar_obras')
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal">
                <i class="bi bi-flag me-2"></i>Cambiar Estado
            </button>
            <a href="{{ route('obras.edit', $obra) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('obras.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['total_trabajadores'] }}</h3>
                    <small class="text-muted">Trabajadores</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">{{ $stats['total_partes'] }}</h3>
                    <small class="text-muted">Partes Diarios</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ number_format($stats['total_ingresos'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Ingresos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ number_format($stats['total_gastos'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Gastos</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    @php $margen = $stats['total_ingresos'] - $stats['total_gastos']; @endphp
                    <h3 class="mb-0 text-{{ $margen >= 0 ? 'success' : 'danger' }}">{{ number_format($margen, 0, ',', '.') }}€</h3>
                    <small class="text-muted">Margen</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['progreso'] }}%</h3>
                    <small class="text-muted">Progreso</small>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: {{ $stats['progreso'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda - Información -->
        <div class="col-lg-4">
            <!-- Información General -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        @if($obra->tipo)
                        <tr>
                            <td class="text-muted" style="width: 40%">Tipo</td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $obra->tipo->nombre }}</span></td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Cliente</td>
                            <td>
                                <a href="{{ route('clientes.show', $obra->cliente) }}">{{ $obra->cliente->nombre_comercial }}</a>
                            </td>
                        </tr>
                        @if($obra->encargado)
                        <tr>
                            <td class="text-muted">Encargado</td>
                            <td>{{ $obra->encargado->name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Riesgo</td>
                            <td>
                                @php
                                    $riesgoColors = ['bajo' => 'success', 'medio' => 'warning', 'alto' => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $riesgoColors[$obra->riesgo_operativo] ?? 'secondary' }}-subtle text-{{ $riesgoColors[$obra->riesgo_operativo] ?? 'secondary' }}">
                                    {{ ucfirst($obra->riesgo_operativo ?? 'bajo') }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Fechas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Fechas</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Inicio Previsto</td>
                            <td>{{ $obra->fecha_inicio_prevista?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fin Previsto</td>
                            <td>{{ $obra->fecha_fin_prevista?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Inicio Real</td>
                            <td>{{ $obra->fecha_inicio_real?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fin Real</td>
                            <td>{{ $obra->fecha_fin_real?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Economía -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Economía</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Presupuesto</td>
                            <td class="fw-semibold">{{ $obra->presupuesto ? number_format($obra->presupuesto, 2, ',', '.') . ' €' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Coste Estimado</td>
                            <td>{{ $obra->coste_estimado ? number_format($obra->coste_estimado, 2, ',', '.') . ' €' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Margen Previsto</td>
                            <td class="text-{{ ($obra->margen_previsto ?? 0) >= 0 ? 'success' : 'danger' }}">
                                {{ $obra->margen_previsto ? number_format($obra->margen_previsto, 2, ',', '.') . ' €' : '-' }}
                            </td>
                        </tr>
                        @if($obra->tiene_penalizaciones)
                        <tr>
                            <td class="text-muted">Penalización</td>
                            <td class="text-danger">{{ number_format($obra->importe_penalizacion_prevista ?? 0, 2, ',', '.') }} €</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Datos ADIF -->
            @if($obra->linea || $obra->trayecto || $obra->pk_inicio)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-train-front me-2"></i>Datos ADIF</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        @if($obra->linea)
                        <tr>
                            <td class="text-muted" style="width: 40%">Línea</td>
                            <td>{{ $obra->linea }}</td>
                        </tr>
                        @endif
                        @if($obra->trayecto)
                        <tr>
                            <td class="text-muted">Trayecto</td>
                            <td>{{ $obra->trayecto }}</td>
                        </tr>
                        @endif
                        @if($obra->pk_inicio || $obra->pk_fin)
                        <tr>
                            <td class="text-muted">PKs</td>
                            <td>{{ $obra->pk_inicio }} - {{ $obra->pk_fin }}</td>
                        </tr>
                        @endif
                        @if($obra->gerencia_jefatura)
                        <tr>
                            <td class="text-muted">Gerencia</td>
                            <td>{{ $obra->gerencia_jefatura }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Derecha - Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <ul class="nav nav-tabs card-header-tabs" id="obraTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="equipo-tab" data-bs-toggle="tab" data-bs-target="#equipo" type="button">
                                <i class="bi bi-people me-1"></i>Equipo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hitos-tab" data-bs-toggle="tab" data-bs-target="#hitos" type="button">
                                <i class="bi bi-flag me-1"></i>Hitos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button">
                                <i class="bi bi-folder me-1"></i>Documentos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button">
                                <i class="bi bi-clock-history me-1"></i>Historial
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="obraTabsContent">
                        <!-- Tab Equipo -->
                        <div class="tab-pane fade show active" id="equipo" role="tabpanel">
                            <!-- Cuadrillas -->
                            <h6 class="text-muted mb-3">Cuadrillas Asignadas</h6>
                            @if($obra->cuadrillas->count() > 0)
                                <div class="row g-2 mb-4">
                                    @foreach($obra->cuadrillas as $cuadrilla)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                                            <div>
                                                <strong>{{ $cuadrilla->nombre }}</strong>
                                                <small class="text-muted d-block">Desde {{ $cuadrilla->pivot->fecha_inicio->format('d/m/Y') }}</small>
                                            </div>
                                            @can('editar_obras')
                                            <form action="{{ route('obras.cuadrillas.remove', [$obra, $cuadrilla]) }}" method="POST" class="d-inline remove-cuadrilla-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-cuadrilla">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-4">No hay cuadrillas asignadas</p>
                            @endif

                            @can('editar_obras')
                            @if($cuadrillasDisponibles->count() > 0)
                            <form action="{{ route('obras.cuadrillas.add', $obra) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="input-group">
                                    <select name="cuadrilla_id" class="form-select" required>
                                        <option value="">Asignar cuadrilla...</option>
                                        @foreach($cuadrillasDisponibles as $cuadrilla)
                                            <option value="{{ $cuadrilla->id }}">{{ $cuadrilla->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </form>
                            @endif
                            @endcan

                            <!-- Trabajadores -->
                            <h6 class="text-muted mb-3">Trabajadores Asignados</h6>
                            @if($obra->trabajadoresActivos->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Trabajador</th>
                                                <th>Rol</th>
                                                <th>Desde</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($obra->trabajadoresActivos as $trabajador)
                                            <tr>
                                                <td>{{ $trabajador->nombre_completo }}</td>
                                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $trabajador->pivot->rol ?? 'Operario' }}</span></td>
                                                <td>{{ $trabajador->pivot->fecha_inicio->format('d/m/Y') }}</td>
                                                <td class="text-end">
                                                    @can('editar_obras')
                                                    <form action="{{ route('obras.trabajadores.remove', [$obra, $trabajador]) }}" method="POST" class="d-inline remove-trabajador-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-trabajador">
                                                            <i class="bi bi-x"></i>
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
                                <p class="text-muted mb-4">No hay trabajadores asignados</p>
                            @endif

                            @can('editar_obras')
                            @if($trabajadoresDisponibles->count() > 0)
                            <form action="{{ route('obras.trabajadores.add', $obra) }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select name="trabajador_id" class="form-select" required>
                                            <option value="">Asignar trabajador...</option>
                                            @foreach($trabajadoresDisponibles as $trabajador)
                                                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre_completo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="rol" class="form-control" placeholder="Rol (opcional)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endif
                            @endcan
                        </div>

                        <!-- Tab Hitos -->
                        <div class="tab-pane fade" id="hitos" role="tabpanel">
                            @if($obra->hitos->count() > 0)
                                <div class="list-group list-group-flush mb-4">
                                    @foreach($obra->hitos as $hito)
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($hito->completado)
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @else
                                                    <i class="bi bi-circle text-muted"></i>
                                                @endif
                                                <strong class="{{ $hito->completado ? 'text-decoration-line-through text-muted' : '' }}">
                                                    {{ $hito->nombre }}
                                                </strong>
                                                @if($hito->porcentaje_obra)
                                                    <span class="badge bg-info-subtle text-info">{{ $hito->porcentaje_obra }}%</span>
                                                @endif
                                            </div>
                                            @if($hito->descripcion)
                                                <small class="text-muted">{{ $hito->descripcion }}</small>
                                            @endif
                                            @if($hito->fecha_prevista)
                                                <small class="d-block text-muted">
                                                    <i class="bi bi-calendar me-1"></i>{{ $hito->fecha_prevista->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1">
                                            @can('editar_obras')
                                            @if(!$hito->completado)
                                            <form action="{{ route('obras.hitos.completar', [$obra, $hito]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Completar">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <form action="{{ route('obras.hitos.destroy', [$obra, $hito]) }}" method="POST" class="d-inline delete-hito-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-hito">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-4">No hay hitos definidos</p>
                            @endif

                            @can('editar_obras')
                            <form action="{{ route('obras.hitos.store', $obra) }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="nombre" class="form-control" placeholder="Nombre del hito" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="porcentaje_obra" class="form-control" placeholder="%" min="0" max="100">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="fecha_prevista" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-plus-lg"></i> Añadir
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endcan
                        </div>

                        <!-- Tab Documentos -->
                        <div class="tab-pane fade" id="documentos" role="tabpanel">
                            @if($obra->documentos->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Documento</th>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($obra->documentos as $documento)
                                            <tr>
                                                <td>
                                                    <a href="{{ asset($documento->archivo_path) }}" target="_blank">
                                                        <i class="bi bi-file-earmark me-1"></i>{{ $documento->nombre }}
                                                    </a>
                                                </td>
                                                <td><span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($documento->tipo) }}</span></td>
                                                <td>{{ $documento->fecha_documento?->format('d/m/Y') }}</td>
                                                <td class="text-end">
                                                    @can('editar_obras')
                                                    <form action="{{ route('obras.documentos.destroy', [$obra, $documento]) }}" method="POST" class="d-inline delete-documento-form">
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
                                <p class="text-muted mb-4">No hay documentos subidos</p>
                            @endif

                            @can('editar_obras')
                            <form action="{{ route('obras.documentos.store', $obra) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <select name="tipo" class="form-select" required>
                                            <option value="">Tipo...</option>
                                            <option value="contrato">Contrato</option>
                                            <option value="plano">Plano</option>
                                            <option value="permiso">Permiso</option>
                                            <option value="acta">Acta</option>
                                            <option value="foto">Foto</option>
                                            <option value="informe">Informe</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" name="archivo" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-upload"></i> Subir
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endcan
                        </div>

                        <!-- Tab Historial -->
                        <div class="tab-pane fade" id="historial" role="tabpanel">
                            @if($obra->historial->count() > 0)
                                <div class="timeline">
                                    @foreach($obra->historial as $item)
                                    <div class="d-flex mb-3">
                                        <div class="me-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-flag text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>
                                                        @if($item->estado_anterior)
                                                            {{ ucfirst(str_replace('_', ' ', $item->estado_anterior)) }}
                                                            <i class="bi bi-arrow-right mx-1"></i>
                                                        @endif
                                                        {{ ucfirst(str_replace('_', ' ', $item->estado_nuevo)) }}
                                                    </strong>
                                                    <small class="text-muted ms-2">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                            @if($item->comentario)
                                                <p class="mb-0 text-muted">{{ $item->comentario }}</p>
                                            @endif
                                            <small class="text-muted">Por {{ $item->cambiadoPor?->name ?? 'Sistema' }}</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No hay historial de cambios</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Estado -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('obras.cambiar-estado', $obra) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado de la Obra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select name="estado" class="form-select form-select-lg" required>
                            @foreach(['presentada' => 'Presentada', 'aprobada' => 'Aprobada', 'en_curso' => 'En Curso', 'pausada' => 'Pausada', 'finalizada' => 'Finalizada', 'cancelada' => 'Cancelada'] as $value => $label)
                                <option value="{{ $value }}" {{ $obra->estado == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comentario (opcional)</label>
                        <textarea name="comentario" class="form-control" rows="3" placeholder="Motivo del cambio..."></textarea>
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

@push('scripts')
<script>
    // Desasignar cuadrilla
    document.querySelectorAll('.btn-remove-cuadrilla').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.remove-cuadrilla-form');
            Swal.fire({
                title: '¿Desasignar cuadrilla?',
                text: 'La cuadrilla será desasignada de esta obra',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, desasignar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Desasignar trabajador
    document.querySelectorAll('.btn-remove-trabajador').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.remove-trabajador-form');
            Swal.fire({
                title: '¿Desasignar trabajador?',
                text: 'El trabajador será desasignado de esta obra',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, desasignar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Eliminar hito
    document.querySelectorAll('.btn-delete-hito').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-hito-form');
            Swal.fire({
                title: '¿Eliminar hito?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
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
                title: '¿Eliminar documento?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
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
