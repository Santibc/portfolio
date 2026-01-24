@extends('layouts.app')

@section('title', $vehiculo->matricula)

@section('content')
<div class="container-fluid py-4">
    <!-- Alertas de ITV/Seguro -->
    @if($vehiculo->itv_status === 'vencida')
    <div class="alert alert-danger d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            <strong>ITV Vencida</strong> - La ITV de este vehiculo vencio el {{ $vehiculo->fecha_proxima_itv->format('d/m/Y') }}.
        </div>
    </div>
    @elseif($vehiculo->itv_status === 'proxima')
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
        <div>
            <strong>ITV Proxima</strong> - La ITV vence el {{ $vehiculo->fecha_proxima_itv->format('d/m/Y') }} ({{ $stats['dias_hasta_itv'] }} dias).
        </div>
    </div>
    @endif

    @if($vehiculo->seguro_status === 'vencido')
    <div class="alert alert-danger d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            <strong>Seguro Vencido</strong> - El seguro de este vehiculo vencio el {{ $vehiculo->fecha_vencimiento_seguro->format('d/m/Y') }}.
        </div>
    </div>
    @elseif($vehiculo->seguro_status === 'proximo')
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
        <div>
            <strong>Seguro Proximo a Vencer</strong> - El seguro vence el {{ $vehiculo->fecha_vencimiento_seguro->format('d/m/Y') }} ({{ $stats['dias_hasta_seguro'] }} dias).
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <code class="fs-5 text-primary fw-semibold">{{ $vehiculo->matricula }}</code>
                @php
                    $estadoColors = [
                        'operativo' => 'success',
                        'en_taller' => 'warning',
                        'baja' => 'danger',
                    ];
                    $estadoLabels = [
                        'operativo' => 'Operativo',
                        'en_taller' => 'En Taller',
                        'baja' => 'Baja',
                    ];
                @endphp
                <span class="badge bg-{{ $estadoColors[$vehiculo->estado] ?? 'secondary' }}">
                    {{ $estadoLabels[$vehiculo->estado] ?? ucfirst($vehiculo->estado) }}
                </span>
            </div>
            <h1 class="h3 mb-1">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-tag me-1"></i>{{ $vehiculo->tipo->nombre ?? 'Sin tipo' }}
                @if($vehiculo->conductorHabitual)
                    <span class="mx-2">|</span>
                    <i class="bi bi-person me-1"></i>Conductor: {{ $vehiculo->conductorHabitual->nombre_completo }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @can('editar_vehiculos')
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal">
                <i class="bi bi-flag me-2"></i>Cambiar Estado
            </button>
            <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadisticas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['total_documentos'] }}</h3>
                    <small class="text-muted">Documentos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    @php
                        $itvColors = ['vencida' => 'danger', 'proxima' => 'warning', 'vigente' => 'success', 'sin_datos' => 'secondary'];
                        $itvLabels = ['vencida' => 'Vencida', 'proxima' => 'Proxima', 'vigente' => 'Vigente', 'sin_datos' => 'Sin datos'];
                    @endphp
                    <h3 class="mb-0 text-{{ $itvColors[$vehiculo->itv_status] ?? 'secondary' }}">
                        {{ $itvLabels[$vehiculo->itv_status] ?? '-' }}
                    </h3>
                    <small class="text-muted">ITV</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    @php
                        $seguroColors = ['vencido' => 'danger', 'proximo' => 'warning', 'vigente' => 'success', 'sin_datos' => 'secondary'];
                        $seguroLabels = ['vencido' => 'Vencido', 'proximo' => 'Proximo', 'vigente' => 'Vigente', 'sin_datos' => 'Sin datos'];
                    @endphp
                    <h3 class="mb-0 text-{{ $seguroColors[$vehiculo->seguro_status] ?? 'secondary' }}">
                        {{ $seguroLabels[$vehiculo->seguro_status] ?? '-' }}
                    </h3>
                    <small class="text-muted">Seguro</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">
                        {{ $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual, 0, ',', '.') : '-' }}
                    </h3>
                    <small class="text-muted">Km Actuales</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">
                        {{ $vehiculo->coste_adquisicion ? number_format($vehiculo->coste_adquisicion, 0, ',', '.') . ' EUR' : '-' }}
                    </h3>
                    <small class="text-muted">Valor</small>
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
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $vehiculo->tipo->nombre ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Marca</td>
                            <td>{{ $vehiculo->marca ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modelo</td>
                            <td>{{ $vehiculo->modelo ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">N. Bastidor</td>
                            <td><code>{{ $vehiculo->numero_bastidor ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Matriculacion</td>
                            <td>{{ $vehiculo->fecha_matriculacion?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha Compra</td>
                            <td>{{ $vehiculo->fecha_compra?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ITV -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-card-checklist me-2"></i>ITV</h6>
                    <span class="badge bg-{{ $itvColors[$vehiculo->itv_status] ?? 'secondary' }}-subtle text-{{ $itvColors[$vehiculo->itv_status] ?? 'secondary' }}">
                        {{ $itvLabels[$vehiculo->itv_status] ?? '-' }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Ultima ITV</td>
                            <td>{{ $vehiculo->fecha_ultima_itv?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Proxima ITV</td>
                            <td class="fw-semibold">{{ $vehiculo->fecha_proxima_itv?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @if($stats['dias_hasta_itv'] !== null)
                        <tr>
                            <td class="text-muted">Dias restantes</td>
                            <td>
                                @if($stats['dias_hasta_itv'] < 0)
                                    <span class="text-danger fw-semibold">Vencida hace {{ abs($stats['dias_hasta_itv']) }} dias</span>
                                @else
                                    <span class="{{ $stats['dias_hasta_itv'] <= 30 ? 'text-warning' : 'text-success' }}">{{ $stats['dias_hasta_itv'] }} dias</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Seguro -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-shield me-2"></i>Seguro</h6>
                    <span class="badge bg-{{ $seguroColors[$vehiculo->seguro_status] ?? 'secondary' }}-subtle text-{{ $seguroColors[$vehiculo->seguro_status] ?? 'secondary' }}">
                        {{ $seguroLabels[$vehiculo->seguro_status] ?? '-' }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Compania</td>
                            <td>{{ $vehiculo->compania_seguro ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">N. Poliza</td>
                            <td><code>{{ $vehiculo->numero_poliza ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vencimiento</td>
                            <td class="fw-semibold">{{ $vehiculo->fecha_vencimiento_seguro?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @if($stats['dias_hasta_seguro'] !== null)
                        <tr>
                            <td class="text-muted">Dias restantes</td>
                            <td>
                                @if($stats['dias_hasta_seguro'] < 0)
                                    <span class="text-danger fw-semibold">Vencido hace {{ abs($stats['dias_hasta_seguro']) }} dias</span>
                                @else
                                    <span class="{{ $stats['dias_hasta_seguro'] <= 45 ? 'text-warning' : 'text-success' }}">{{ $stats['dias_hasta_seguro'] }} dias</span>
                                @endif
                            </td>
                        </tr>
                        @endif
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
                            <td class="text-muted" style="width: 40%">Coste Adquis.</td>
                            <td class="fw-semibold">{{ $vehiculo->coste_adquisicion ? number_format($vehiculo->coste_adquisicion, 2, ',', '.') . ' EUR' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Coste/Dia</td>
                            <td>{{ $vehiculo->coste_dia ? number_format($vehiculo->coste_dia, 2, ',', '.') . ' EUR/dia' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kilometraje</td>
                            <td>{{ $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual, 0, ',', '.') . ' km' : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm content-tabs-card">
                <div class="card-header bg-transparent tabs-header">
                    <ul class="nav nav-pills" id="vehiculoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                                <i class="bi bi-info-circle me-1"></i>Informacion
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button">
                                <i class="bi bi-folder me-1"></i>Documentos
                                <span class="badge bg-secondary ms-1">{{ $vehiculo->documentos->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tabs-content">
                    <div class="tab-content" id="vehiculoTabsContent">
                        <!-- Tab Informacion -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <!-- Conductor Habitual -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3"><i class="bi bi-person me-2"></i>Conductor Habitual</h6>
                                @if($vehiculo->conductorHabitual)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="bi bi-person-fill text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $vehiculo->conductorHabitual->nombre_completo }}</h6>
                                            <small class="text-muted">
                                                {{ $vehiculo->conductorHabitual->dni ?? '' }}
                                                @if($vehiculo->conductorHabitual->telefono)
                                                    | <i class="bi bi-telephone me-1"></i>{{ $vehiculo->conductorHabitual->telefono }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>Este vehiculo no tiene conductor habitual asignado.
                                    </p>
                                @endif

                                @can('editar_vehiculos')
                                <div class="mt-3">
                                    <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST" class="row g-2 align-items-end">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="vehiculo_tipo_id" value="{{ $vehiculo->vehiculo_tipo_id }}">
                                        <input type="hidden" name="matricula" value="{{ $vehiculo->matricula }}">
                                        <input type="hidden" name="estado" value="{{ $vehiculo->estado }}">
                                        <div class="col-md-8">
                                            <label class="form-label small">Cambiar conductor habitual</label>
                                            <select name="conductor_habitual_id" class="form-select form-select-sm">
                                                <option value="">Sin asignar</option>
                                                @foreach($conductoresDisponibles as $conductor)
                                                    <option value="{{ $conductor->id }}" {{ $vehiculo->conductor_habitual_id == $conductor->id ? 'selected' : '' }}>
                                                        {{ $conductor->apellidos }}, {{ $conductor->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="bi bi-check me-1"></i>Actualizar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @endcan
                            </div>

                            <hr>

                            <!-- Notas -->
                            <div>
                                <h6 class="text-muted mb-3"><i class="bi bi-sticky me-2"></i>Notas</h6>
                                @if($vehiculo->notas)
                                    <p class="mb-0">{{ $vehiculo->notas }}</p>
                                @else
                                    <p class="text-muted mb-0">Sin notas adicionales.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tab Documentos -->
                        <div class="tab-pane fade" id="documentos" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Documentos del Vehiculo</h6>
                            </div>

                            @if($vehiculo->documentos->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Nombre</th>
                                                <th>Fecha Doc.</th>
                                                <th>Caducidad</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vehiculo->documentos->sortByDesc('created_at') as $documento)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        {{ $documento->tipo_label }}
                                                    </span>
                                                </td>
                                                <td>{{ $documento->nombre }}</td>
                                                <td>{{ $documento->fecha_documento?->format('d/m/Y') ?? '-' }}</td>
                                                <td>
                                                    @if($documento->fecha_caducidad)
                                                        @if($documento->fecha_caducidad->isPast())
                                                            <span class="text-danger">{{ $documento->fecha_caducidad->format('d/m/Y') }}</span>
                                                        @elseif($documento->fecha_caducidad->diffInDays(now()) <= 30)
                                                            <span class="text-warning">{{ $documento->fecha_caducidad->format('d/m/Y') }}</span>
                                                        @else
                                                            {{ $documento->fecha_caducidad->format('d/m/Y') }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <a href="{{ asset($documento->archivo_path) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Ver">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ asset($documento->archivo_path) }}" download class="btn btn-sm btn-outline-primary" title="Descargar">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        @can('editar_vehiculos')
                                                        <form action="{{ route('vehiculos.documentos.destroy', [$vehiculo, $documento]) }}" method="POST" class="d-inline delete-doc-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-doc" title="Eliminar">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted mb-4">
                                    <i class="bi bi-folder fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay documentos registrados</p>
                                </div>
                            @endif

                            <!-- Formulario para subir documento -->
                            @can('editar_vehiculos')
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="bi bi-upload me-2"></i>Subir Documento</h6>
                                    <form action="{{ route('vehiculos.documentos.store', $vehiculo) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                                <select name="tipo" class="form-select" required>
                                                    <option value="">Seleccionar...</option>
                                                    <option value="ficha_tecnica">Ficha Tecnica</option>
                                                    <option value="permiso_circulacion">Permiso de Circulacion</option>
                                                    <option value="seguro">Seguro</option>
                                                    <option value="itv">ITV</option>
                                                    <option value="otro">Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Ficha Tecnica 2024" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Fecha Documento</label>
                                                <input type="date" name="fecha_documento" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Fecha Caducidad</label>
                                                <input type="date" name="fecha_caducidad" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Archivo <span class="text-danger">*</span></label>
                                                <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <small class="text-muted">PDF, JPG, PNG (max 10MB)</small>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-upload me-1"></i>Subir Documento
                                                </button>
                                            </div>
                                        </div>
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

<!-- Modal Cambiar Estado -->
@can('editar_vehiculos')
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('vehiculos.cambiar-estado', $vehiculo) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado del Vehiculo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select name="estado" class="form-select form-select-lg" required>
                            <option value="operativo" {{ $vehiculo->estado == 'operativo' ? 'selected' : '' }}>Operativo</option>
                            <option value="en_taller" {{ $vehiculo->estado == 'en_taller' ? 'selected' : '' }}>En Taller</option>
                            <option value="baja" {{ $vehiculo->estado == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
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
#vehiculoTabsContent.tab-content {
    display: block !important;
}

#vehiculoTabsContent .tab-pane {
    display: none !important;
}

#vehiculoTabsContent .tab-pane.active {
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
        const tabButtons = document.querySelectorAll('#vehiculoTabs .nav-link');
        const tabPanes = document.querySelectorAll('#vehiculoTabsContent .tab-pane');

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

    // Eliminar documento
    document.querySelectorAll('.btn-delete-doc').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-doc-form');
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
