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
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['total_trabajadores'] }}</h3>
                    <small class="text-muted">Trabajadores</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">{{ $stats['total_partes'] }}</h3>
                    <small class="text-muted">Partes</small>
                </div>
            </div>
        </div>
        @can('ver_rentabilidad_obras')
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ number_format($stats['total_producido'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Producido</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-warning">{{ number_format($stats['total_pendiente'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Pendiente</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ number_format($stats['total_coste_personal'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Coste personal</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    @php $margen = $stats['total_ingresos'] - $stats['total_gastos'] - $stats['total_coste_personal']; @endphp
                    <h3 class="mb-0 text-{{ $margen >= 0 ? 'success' : 'danger' }}">{{ number_format($margen, 0, ',', '.') }}€</h3>
                    <small class="text-muted">Margen <span class="text-muted">(sin IVA)</span></small>
                </div>
            </div>
        </div>
        @endcan
        <div class="col-6 col-md">
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
            @can('ver_rentabilidad_obras')
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
            @endcan

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
            <div class="card border-0 shadow-sm content-tabs-card">
                <div class="card-header bg-transparent tabs-header">
                    <ul class="nav nav-pills" id="obraTabs" role="tablist">
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
                        @can('ver_rentabilidad_obras')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="costes-personal-tab" data-bs-toggle="tab" data-bs-target="#costes-personal" type="button">
                                <i class="bi bi-cash-coin me-1"></i>Costes personal
                                @if(($obra->bonos->count() + $obra->primas->count()) > 0)
                                <span class="badge bg-secondary ms-1">{{ $obra->bonos->count() + $obra->primas->count() }}</span>
                                @endif
                            </button>
                        </li>
                        @endcan
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="conceptos-tab" data-bs-toggle="tab" data-bs-target="#conceptos" type="button">
                                <i class="bi bi-list-columns me-1"></i>Conceptos
                                <span class="badge bg-secondary ms-1">{{ $stats['conceptos_activos'] }}</span>
                            </button>
                        </li>
                        @role('Administrador|Contabilidad')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="discrepancias-tab" data-bs-toggle="tab" data-bs-target="#discrepancias" type="button">
                                <i class="bi bi-exclamation-triangle me-1"></i>Discrepancias
                                @if($obra->discrepancias->where('estado', '!=', 'resuelto')->count() > 0)
                                <span class="badge bg-warning ms-1">{{ $obra->discrepancias->where('estado', '!=', 'resuelto')->count() }}</span>
                                @endif
                            </button>
                        </li>
                        @endrole
                    </ul>
                </div>
                <div class="card-body tabs-content">
                    <div class="tab-content" id="obraTabsContent">
                        <!-- Tab Equipo -->
                        <div class="tab-pane fade show active" id="equipo" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span></span>
                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportEquipoModal">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Exportar Equipo
                                </button>
                            </div>
                            <!-- Cuadrillas -->
                            <h6 class="text-muted mb-3">Cuadrillas Asignadas</h6>
                            @if($obra->cuadrillas->count() > 0)
                                <div class="row g-2 mb-4">
                                    @foreach($obra->cuadrillas as $cuadrilla)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                                            <div>
                                                <strong>{{ $cuadrilla->nombre }}</strong>
                                                <small class="text-muted d-block">Desde {{ $cuadrilla->pivot->fecha_inicio ? \Carbon\Carbon::parse($cuadrilla->pivot->fecha_inicio)->format('d/m/Y') : '-' }}</small>
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
                                                <td>{{ $trabajador->pivot->fecha_inicio ? \Carbon\Carbon::parse($trabajador->pivot->fecha_inicio)->format('d/m/Y') : '-' }}</td>
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
                                @can('editar_obras')
                                {{-- Form para generar ingresos de hitos seleccionados (checkbox asociados por atributo form) --}}
                                <form id="generarHitosForm" method="POST" action="{{ route('obras.hitos.generar-ingresos', $obra) }}"
                                      onsubmit="return confirm('¿Generar los ingresos de los hitos seleccionados?');">@csrf</form>
                                @endcan
                                <div class="list-group list-group-flush mb-3">
                                    @foreach($obra->hitos as $hito)
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-start gap-2">
                                            @can('editar_obras')
                                                @if(!$hito->ingreso_id && $hito->importe_cobro > 0)
                                                    <input class="form-check-input mt-1" type="checkbox" name="hitos[]" value="{{ $hito->id }}" form="generarHitosForm" title="Seleccionar para generar ingreso">
                                                @else
                                                    <span style="display:inline-block;width:1rem;"></span>
                                                @endif
                                            @endcan
                                            <div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    @if($hito->completado)
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                    @else
                                                        <i class="bi bi-circle text-muted"></i>
                                                    @endif
                                                    <strong class="{{ $hito->completado ? 'text-muted' : '' }}">
                                                        {{ $hito->nombre }}
                                                    </strong>
                                                    @if($hito->porcentaje_obra)
                                                        <span class="badge bg-info-subtle text-info">{{ $hito->porcentaje_obra }}%</span>
                                                    @endif
                                                    @if($hito->importe_cobro > 0)
                                                        <span class="badge bg-light text-dark border">{{ number_format($hito->importe_cobro, 2, ',', '.') }} €</span>
                                                    @endif
                                                    @if($hito->ingreso_id)
                                                        <a href="{{ route('ingresos.show', $hito->ingreso_id) }}" class="badge bg-success-subtle text-success text-decoration-none">
                                                            <i class="bi bi-check2-circle me-1"></i>Ingreso generado
                                                        </a>
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
                                @can('editar_obras')
                                <div class="d-flex justify-content-end mb-4">
                                    <button type="submit" form="generarHitosForm" class="btn btn-success btn-sm">
                                        <i class="bi bi-cash-coin me-1"></i>Generar ingreso de los hitos seleccionados
                                    </button>
                                </div>
                                @endcan
                            @else
                                <p class="text-muted mb-4">No hay hitos definidos</p>
                            @endif

                            @can('editar_obras')
                            <form action="{{ route('obras.hitos.store', $obra) }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" name="nombre" class="form-control" placeholder="Nombre del hito" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="porcentaje_obra" class="form-control" placeholder="%" min="0" max="100">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="importe_cobro" class="form-control" placeholder="Importe €" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-2">
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

                        <!-- Tab Costes de personal -->
                        @can('ver_rentabilidad_obras')
                        <div class="tab-pane fade" id="costes-personal" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-muted small">Bonos / horas</div>
                                        <div class="fs-5 fw-bold text-danger">{{ number_format($stats['coste_bonos'], 2, ',', '.') }} €</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-muted small">Primas</div>
                                        <div class="fs-5 fw-bold text-danger">{{ number_format($stats['coste_primas'], 2, ',', '.') }} €</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <div class="text-muted small">Total coste personal</div>
                                        <div class="fs-5 fw-bold">{{ number_format($stats['total_coste_personal'], 2, ',', '.') }} €</div>
                                    </div>
                                </div>
                            </div>

                            @if($obra->bonos->count() || $obra->primas->count())
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead class="table-light">
                                        <tr><th>Fecha</th><th>Trabajador</th><th>Concepto</th><th>Tipo</th><th class="text-end">Importe</th><th class="text-center">Estado</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($obra->bonos as $b)
                                        <tr>
                                            <td>{{ optional($b->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ $b->trabajador?->nombre }} {{ $b->trabajador?->apellidos }}</td>
                                            <td>{{ $b->concepto }}</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $b->tipo_formateado }}</span></td>
                                            <td class="text-end">{{ number_format($b->importe, 2, ',', '.') }} €</td>
                                            <td class="text-center">
                                                @if($b->pagado)<span class="badge bg-success">Pagado</span>@else<span class="badge bg-warning text-dark">Pendiente</span>@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @foreach($obra->primas as $p)
                                        <tr>
                                            <td>{{ optional($p->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ $p->trabajador?->nombre }} {{ $p->trabajador?->apellidos }}</td>
                                            <td>Prima de producción</td>
                                            <td><span class="badge bg-info-subtle text-info">Prima</span></td>
                                            <td class="text-end">{{ number_format($p->importe_prima, 2, ',', '.') }} €</td>
                                            <td class="text-center">
                                                @if($p->pagada)<span class="badge bg-success">Pagada</span>@else<span class="badge bg-warning text-dark">Pendiente</span>@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No hay bonos ni primas imputados a esta obra.</p>
                            @endif
                            <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i>Estos costes de personal se incluyen en el "Coste personal" y el "Margen" de la obra.</small>
                        </div>
                        @endcan

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

                        <!-- Tab Conceptos de Producción -->
                        <div class="tab-pane fade" id="conceptos" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Conceptos de Producción</h6>
                                @can('editar_obras')
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addConceptoModal">
                                    <i class="bi bi-plus-lg me-1"></i>Añadir Concepto
                                </button>
                                @endcan
                            </div>

                            @if($obra->conceptosProduccion->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Categoría</th>
                                                <th>Unidad</th>
                                                @can('ver_rentabilidad_obras')
                                                <th class="text-end">Precio Unit.</th>
                                                @endcan
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="conceptosTableBody">
                                            @foreach($obra->conceptosProduccion as $concepto)
                                            <tr class="{{ !$concepto->activo ? 'table-secondary' : '' }}">
                                                <td><code class="fw-bold">{{ $concepto->codigo }}</code></td>
                                                <td>{{ $concepto->nombre }}</td>
                                                <td>
                                                    @php
                                                        $catColors = [
                                                            'desbroce' => 'success',
                                                            'limpieza' => 'info',
                                                            'herbicida' => 'warning',
                                                            'tala' => 'danger',
                                                            'poda' => 'primary',
                                                            'otro' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $catColors[$concepto->categoria] ?? 'secondary' }}-subtle text-{{ $catColors[$concepto->categoria] ?? 'secondary' }}">
                                                        {{ ucfirst($concepto->categoria) }}
                                                    </span>
                                                </td>
                                                <td>{{ $concepto->unidad }}</td>
                                                @can('ver_rentabilidad_obras')
                                                <td class="text-end fw-semibold">{{ $concepto->precio_formateado }}</td>
                                                @endcan
                                                <td>
                                                    @if($concepto->activo)
                                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @can('editar_obras')
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-concepto"
                                                            data-concepto="{{ json_encode($concepto) }}"
                                                            title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-concepto"
                                                            data-id="{{ $concepto->id }}"
                                                            data-nombre="{{ $concepto->nombre }}"
                                                            title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-list-columns fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay conceptos de producción definidos</p>
                                    <small>Añade conceptos para poder valorar los partes diarios</small>
                                </div>
                            @endif
                        </div>

                        <!-- Tab Discrepancias -->
                        @role('Administrador|Contabilidad')
                        <div class="tab-pane fade" id="discrepancias" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Discrepancias de Valoración</h6>
                                <a href="{{ route('obras.discrepancias.create', $obra) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i>Registrar Discrepancia
                                </a>
                            </div>

                            @if($obra->discrepancias->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Período</th>
                                                <th class="text-end">Producido</th>
                                                <th class="text-end">Aceptado</th>
                                                <th class="text-end">Pendiente</th>
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($obra->discrepancias as $discrepancia)
                                            <tr>
                                                <td><strong>{{ $discrepancia->periodo_formateado }}</strong></td>
                                                <td class="text-end">{{ number_format($discrepancia->importe_producido_manzer, 2, ',', '.') }} €</td>
                                                <td class="text-end">{{ $discrepancia->importe_aceptado_cliente ? number_format($discrepancia->importe_aceptado_cliente, 2, ',', '.') . ' €' : '-' }}</td>
                                                <td class="text-end fw-semibold text-{{ $discrepancia->importe_pendiente > 0 ? 'danger' : 'success' }}">
                                                    {{ number_format($discrepancia->importe_pendiente, 2, ',', '.') }} €
                                                </td>
                                                <td>
                                                    @php
                                                        $estadoColors = ['pendiente' => 'warning', 'parcial' => 'info', 'resuelto' => 'success'];
                                                    @endphp
                                                    <span class="badge bg-{{ $estadoColors[$discrepancia->estado] }}-subtle text-{{ $estadoColors[$discrepancia->estado] }}">
                                                        {{ ucfirst($discrepancia->estado) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('obras.discrepancias.edit', [$obra, $discrepancia]) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>Total</th>
                                                <th class="text-end">{{ number_format($obra->discrepancias->sum('importe_producido_manzer'), 2, ',', '.') }} €</th>
                                                <th class="text-end">{{ number_format($obra->discrepancias->sum('importe_aceptado_cliente'), 2, ',', '.') }} €</th>
                                                <th class="text-end text-danger">{{ number_format($obra->discrepancias->sum('importe_pendiente'), 2, ',', '.') }} €</th>
                                                <th colspan="2"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                                    <p class="mb-0">No hay discrepancias registradas</p>
                                </div>
                            @endif
                        </div>
                        @endrole
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Añadir/Editar Concepto -->
@can('editar_obras')
<div class="modal fade" id="addConceptoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="conceptoForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="conceptoMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="conceptoModalTitle">Añadir Concepto de Producción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" id="conceptoCodigo" class="form-control" maxlength="20" required placeholder="Ej: P5, BOSQUE1">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="conceptoNombre" class="form-control" maxlength="150" required placeholder="Nombre descriptivo del concepto">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria" id="conceptoCategoria" class="form-select" required>
                                <option value="desbroce">Desbroce</option>
                                <option value="limpieza">Limpieza</option>
                                <option value="herbicida">Herbicida</option>
                                <option value="tala">Tala</option>
                                <option value="poda">Poda</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                            <select name="unidad" id="conceptoUnidad" class="form-select" required>
                                <option value="m2">m² (metros cuadrados)</option>
                                <option value="unidades">Unidades</option>
                                <option value="hectareas">Hectáreas</option>
                                <option value="jornal">Jornal</option>
                            </select>
                        </div>
                        @can('ver_rentabilidad_obras')
                        <div class="col-md-4">
                            <label class="form-label">Precio Unitario (€) <span class="text-danger">*</span></label>
                            <input type="number" name="precio_unitario" id="conceptoPrecio" class="form-control" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        @else
                        <input type="hidden" name="precio_unitario" id="conceptoPrecio" value="0">
                        @endcan
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="conceptoDescripcion" class="form-control" rows="2" placeholder="Descripción opcional"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" id="conceptoOrden" class="form-control" min="0" value="0" placeholder="Orden de visualización">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="activo" id="conceptoActivo" value="1" checked>
                                <label class="form-check-label" for="conceptoActivo">Concepto activo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="conceptoSubmitBtn">Guardar Concepto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

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

<!-- Modal Exportar Equipo -->
<div class="modal fade" id="exportEquipoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('obras.equipo.export', $obra) }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-excel me-2"></i>Exportar Equipo Histórico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Exporta todos los trabajadores que formaron parte de esta obra en el periodo seleccionado, incluyendo trabajadores ya desasignados y horas trabajadas desde fichajes.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" required
                                   value="{{ $obra->fecha_inicio_real?->format('Y-m-d') ?? $obra->fecha_inicio_prevista?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" required
                                   value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download me-1"></i>Descargar Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Content Tabs - Fix for CSS conflicts */
.content-tabs-card .tab-content,
#obraTabsContent.tab-content {
    display: block !important;
}

#obraTabsContent .tab-pane {
    display: none !important;
}

#obraTabsContent .tab-pane.active {
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
        const tabButtons = document.querySelectorAll('#obraTabs .nav-link');
        const tabPanes = document.querySelectorAll('#obraTabsContent .tab-pane');

        tabButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Remover active de todos los botones
                tabButtons.forEach(btn => btn.classList.remove('active'));
                // Agregar active al botón clickeado
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

    // ============================
    // Conceptos de Producción
    // ============================
    const obraId = {{ $obra->id }};
    const conceptoForm = document.getElementById('conceptoForm');
    const conceptoModal = document.getElementById('addConceptoModal');
    let editingConceptoId = null;
    let isEditingConcepto = false; // Flag para controlar si se está editando

    // Reset form when modal opens for new concept (only if not editing)
    if (conceptoModal) {
        conceptoModal.addEventListener('show.bs.modal', function(e) {
            // Solo resetear si NO estamos en modo edición
            if (!isEditingConcepto) {
                resetConceptoForm();
            }
            // Resetear la bandera después de abrir el modal
            isEditingConcepto = false;
        });
    }

    function resetConceptoForm() {
        editingConceptoId = null;
        document.getElementById('conceptoModalTitle').textContent = 'Añadir Concepto de Producción';
        document.getElementById('conceptoMethod').value = 'POST';
        conceptoForm.action = '{{ route("obras.conceptos.store", $obra) }}';
        conceptoForm.reset();
        document.getElementById('conceptoActivo').checked = true;
    }

    // Edit concept
    document.querySelectorAll('.btn-edit-concepto').forEach(btn => {
        btn.addEventListener('click', function() {
            const concepto = JSON.parse(this.dataset.concepto);
            editingConceptoId = concepto.id;

            // Marcar que estamos editando ANTES de abrir el modal
            isEditingConcepto = true;

            document.getElementById('conceptoModalTitle').textContent = 'Editar Concepto: ' + concepto.codigo;
            document.getElementById('conceptoMethod').value = 'PUT';
            conceptoForm.action = `/obras/${obraId}/conceptos/${concepto.id}`;

            document.getElementById('conceptoCodigo').value = concepto.codigo;
            document.getElementById('conceptoNombre').value = concepto.nombre;
            document.getElementById('conceptoCategoria').value = concepto.categoria;
            document.getElementById('conceptoUnidad').value = concepto.unidad;
            document.getElementById('conceptoPrecio').value = concepto.precio_unitario;
            document.getElementById('conceptoDescripcion').value = concepto.descripcion || '';
            document.getElementById('conceptoOrden').value = concepto.orden || 0;
            document.getElementById('conceptoActivo').checked = concepto.activo;

            const modal = new bootstrap.Modal(conceptoModal);
            modal.show();
        });
    });

    // Delete concept
    document.querySelectorAll('.btn-delete-concepto').forEach(btn => {
        btn.addEventListener('click', function() {
            const conceptoId = this.dataset.id;
            const conceptoNombre = this.dataset.nombre;

            Swal.fire({
                title: '¿Eliminar concepto?',
                html: `Se eliminará el concepto <strong>${conceptoNombre}</strong>.<br><small class="text-muted">Si tiene producciones asociadas, se marcará como inactivo.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/obras/${obraId}/conceptos/${conceptoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: data.message,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Error al eliminar el concepto', 'error');
                    });
                }
            });
        });
    });

    // Submit form via AJAX
    if (conceptoForm) {
        conceptoForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const method = document.getElementById('conceptoMethod').value;

            // Handle checkbox
            formData.set('activo', document.getElementById('conceptoActivo').checked ? '1' : '0');

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado',
                        text: data.message,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error al guardar el concepto', 'error');
            });
        });
    }
</script>
@endpush
@endsection
