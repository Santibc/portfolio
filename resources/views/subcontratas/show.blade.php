@extends('layouts.app')

@section('title', $subcontrata->nombre)

@section('content')
<div class="container-fluid py-4">
    <!-- Alertas de documentos vencidos -->
    @if($stats['documentos_vencidos'] > 0)
    <div class="alert alert-danger d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            <strong>Documentos Vencidos</strong> - Esta subcontrata tiene {{ $stats['documentos_vencidos'] }} documento(s) CAE vencido(s).
        </div>
    </div>
    @elseif($stats['documentos_proximos'] > 0)
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
        <div>
            <strong>Documentos Próximos a Vencer</strong> - {{ $stats['documentos_proximos'] }} documento(s) CAE vencerán en los próximos 30 días.
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <h1 class="h3 mb-0">{{ $subcontrata->nombre }}</h1>
                @if($subcontrata->activa)
                    <span class="badge bg-success">Activa</span>
                @else
                    <span class="badge bg-danger">Inactiva</span>
                @endif
                @if($subcontrata->homologada)
                    <span class="badge bg-info">Homologada</span>
                @endif
            </div>
            @if($subcontrata->razon_social && $subcontrata->razon_social !== $subcontrata->nombre)
                <p class="text-muted mb-0">{{ $subcontrata->razon_social }}</p>
            @endif
            @if($subcontrata->cif)
                <small class="text-muted"><i class="bi bi-card-text me-1"></i>CIF: {{ $subcontrata->cif }}</small>
            @endif
        </div>
        <div class="d-flex gap-2">
            @can('editar_subcontratas')
            <form action="{{ route('subcontratas.toggle-activa', $subcontrata) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-{{ $subcontrata->activa ? 'warning' : 'success' }}">
                    <i class="bi bi-{{ $subcontrata->activa ? 'pause' : 'play' }}-fill me-1"></i>
                    {{ $subcontrata->activa ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
            <form action="{{ route('subcontratas.toggle-homologada', $subcontrata) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-{{ $subcontrata->homologada ? 'secondary' : 'info' }}">
                    <i class="bi bi-award me-1"></i>
                    {{ $subcontrata->homologada ? 'Quitar Homologación' : 'Homologar' }}
                </button>
            </form>
            <a href="{{ route('subcontratas.edit', $subcontrata) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('subcontratas.index') }}" class="btn btn-outline-secondary">
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
                    <h3 class="mb-0 text-info">{{ $stats['obras_total'] }}</h3>
                    <small class="text-muted">Obras Asignadas</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-{{ $stats['documentos_vencidos'] > 0 ? 'danger' : ($stats['documentos_proximos'] > 0 ? 'warning' : 'success') }}">
                        {{ $stats['documentos_cae'] }}
                    </h3>
                    <small class="text-muted">Docs. CAE</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">
                        {{ $subcontrata->tarifa_hora ? number_format($subcontrata->tarifa_hora, 2, ',', '.') . ' €' : '-' }}
                    </h3>
                    <small class="text-muted">Tarifa/Hora</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">
                        {{ $subcontrata->tarifa_dia ? number_format($subcontrata->tarifa_dia, 2, ',', '.') . ' €' : '-' }}
                    </h3>
                    <small class="text-muted">Tarifa/Día</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda - Información -->
        <div class="col-lg-4">
            <!-- Datos de Contacto -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Datos de Contacto</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        @if($subcontrata->persona_contacto)
                        <tr>
                            <td class="text-muted" style="width: 40%"><i class="bi bi-person me-1"></i>Contacto</td>
                            <td>{{ $subcontrata->persona_contacto }}</td>
                        </tr>
                        @endif
                        @if($subcontrata->telefono)
                        <tr>
                            <td class="text-muted"><i class="bi bi-telephone me-1"></i>Teléfono</td>
                            <td><a href="tel:{{ $subcontrata->telefono }}">{{ $subcontrata->telefono }}</a></td>
                        </tr>
                        @endif
                        @if($subcontrata->email)
                        <tr>
                            <td class="text-muted"><i class="bi bi-envelope me-1"></i>Email</td>
                            <td><a href="mailto:{{ $subcontrata->email }}">{{ $subcontrata->email }}</a></td>
                        </tr>
                        @endif
                        @if($subcontrata->direccion)
                        <tr>
                            <td class="text-muted"><i class="bi bi-geo-alt me-1"></i>Dirección</td>
                            <td>{{ $subcontrata->direccion }}</td>
                        </tr>
                        @endif
                    </table>
                    @if(!$subcontrata->persona_contacto && !$subcontrata->telefono && !$subcontrata->email && !$subcontrata->direccion)
                        <p class="text-muted mb-0 text-center">Sin datos de contacto</p>
                    @endif
                </div>
            </div>

            <!-- Información de Homologación -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-award me-2"></i>Homologación</h6>
                </div>
                <div class="card-body">
                    @if($subcontrata->homologada)
                        <div class="d-flex align-items-center text-success">
                            <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                            <div>
                                <strong>Homologada</strong>
                                @if($subcontrata->fecha_homologacion)
                                    <br><small class="text-muted">Desde: {{ $subcontrata->fecha_homologacion->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center text-secondary">
                            <i class="bi bi-dash-circle fs-3 me-3"></i>
                            <div>
                                <strong>No Homologada</strong>
                                <br><small class="text-muted">Pendiente de homologación</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h6>
                </div>
                <div class="card-body">
                    @if($subcontrata->notas)
                        <p class="mb-0">{{ $subcontrata->notas }}</p>
                    @else
                        <p class="text-muted mb-0">Sin notas adicionales.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm content-tabs-card">
                <div class="card-header bg-transparent tabs-header">
                    <ul class="nav nav-pills" id="subcontrataTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button">
                                <i class="bi bi-file-earmark-text me-1"></i>Documentos CAE
                                <span class="badge bg-{{ $stats['documentos_vencidos'] > 0 ? 'danger' : 'secondary' }} ms-1">{{ $stats['documentos_cae'] }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="trabajadores-tab" data-bs-toggle="tab" data-bs-target="#trabajadores" type="button">
                                <i class="bi bi-people me-1"></i>Trabajadores
                                <span class="badge bg-secondary ms-1">{{ $stats['total_trabajadores'] }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="obras-tab" data-bs-toggle="tab" data-bs-target="#obras" type="button">
                                <i class="bi bi-building me-1"></i>Obras
                                <span class="badge bg-secondary ms-1">{{ $stats['obras_total'] }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tabs-content">
                    <div class="tab-content" id="subcontrataTabsContent">
                        <!-- Tab Documentos CAE -->
                        <div class="tab-pane fade show active" id="documentos" role="tabpanel">
                            @if($subcontrata->documentosCae->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Nombre</th>
                                                <th>Fecha Doc.</th>
                                                <th>Caducidad</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subcontrata->documentosCae as $documento)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        {{ $tiposDocumentoCae[$documento->tipo] ?? ucfirst($documento->tipo) }}
                                                    </span>
                                                </td>
                                                <td>{{ $documento->nombre }}</td>
                                                <td>{{ $documento->fecha_documento?->format('d/m/Y') ?? '-' }}</td>
                                                <td>
                                                    @if($documento->fecha_caducidad)
                                                        @if($documento->fecha_caducidad->isPast())
                                                            <span class="text-danger fw-semibold">
                                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $documento->fecha_caducidad->format('d/m/Y') }}
                                                            </span>
                                                        @elseif($documento->fecha_caducidad->diffInDays(now()) <= 30)
                                                            <span class="text-warning fw-semibold">
                                                                <i class="bi bi-clock me-1"></i>{{ $documento->fecha_caducidad->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            {{ $documento->fecha_caducidad->format('d/m/Y') }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Sin caducidad</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($documento->verificado)
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="bi bi-check-circle me-1"></i>Verificado
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">
                                                            <i class="bi bi-clock me-1"></i>Pendiente
                                                        </span>
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
                                                        @can('editar_subcontratas')
                                                        @if(!$documento->verificado)
                                                        <form action="{{ route('subcontratas.documentos-cae.verificar', [$subcontrata, $documento]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Verificar">
                                                                <i class="bi bi-check-lg"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-doc"
                                                                data-url="{{ route('subcontratas.documentos-cae.destroy', [$subcontrata, $documento]) }}" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
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
                                    <p class="mb-0">No hay documentos CAE registrados</p>
                                </div>
                            @endif

                            <!-- Formulario para subir documento CAE -->
                            @can('editar_subcontratas')
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="bi bi-upload me-2"></i>Subir Documento CAE</h6>
                                    <form action="{{ route('subcontratas.documentos-cae.store', $subcontrata) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                                <select name="tipo" class="form-select" required>
                                                    <option value="">Seleccionar...</option>
                                                    @foreach($tiposDocumentoCae as $key => $label)
                                                        <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                                <input type="text" name="nombre" class="form-control" placeholder="Ej: TC1 Enero 2026" required>
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
                                                <input type="file" name="archivo" class="form-control" required>
                                                <small class="text-muted">Máximo 10MB.</small>
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

                        <!-- Tab Trabajadores -->
                        <div class="tab-pane fade" id="trabajadores" role="tabpanel">
                            @if($subcontrata->trabajadores->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Trabajador</th>
                                                <th>DNI</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subcontrata->trabajadores as $trabajador)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                            <i class="bi bi-person text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $trabajador->apellidos }}, {{ $trabajador->nombre }}</strong>
                                                            @if($trabajador->categoria_convenio)
                                                                <br><small class="text-muted">{{ $trabajador->categoria_convenio }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $trabajador->dni }}</td>
                                                <td>{{ $trabajador->telefono ?? '-' }}</td>
                                                <td>
                                                    @if($trabajador->activo)
                                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">Baja</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('trabajadores.show', $trabajador) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay trabajadores asociados a esta subcontrata</p>
                                    <small>Los trabajadores se asignan desde el módulo de Trabajadores.</small>
                                </div>
                            @endif
                        </div>

                        <!-- Tab Obras -->
                        <div class="tab-pane fade" id="obras" role="tabpanel">
                            @if($subcontrata->obras->count() > 0)
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Obra</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Fin</th>
                                                <th class="text-end">Importe</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subcontrata->obras as $obra)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $obra->nombre }}</strong>
                                                        <br><small class="text-muted">{{ $obra->codigo }}</small>
                                                    </div>
                                                </td>
                                                <td>{{ $obra->pivot->fecha_inicio ? \Carbon\Carbon::parse($obra->pivot->fecha_inicio)->format('d/m/Y') : '-' }}</td>
                                                <td>{{ $obra->pivot->fecha_fin ? \Carbon\Carbon::parse($obra->pivot->fecha_fin)->format('d/m/Y') : '-' }}</td>
                                                <td class="text-end">
                                                    @if($obra->pivot->importe_contratado)
                                                        <strong>{{ number_format($obra->pivot->importe_contratado, 2, ',', '.') }} €</strong>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($obra->pivot->activa)
                                                        <span class="badge bg-success-subtle text-success">Activa</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">Finalizada</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <a href="{{ route('obras.show', $obra) }}" class="btn btn-sm btn-outline-info" title="Ver obra">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @can('editar_subcontratas')
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-obra"
                                                                data-url="{{ route('subcontratas.obras.remove', [$subcontrata, $obra]) }}"
                                                                data-obra="{{ $obra->nombre }}" title="Quitar de obra">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
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
                                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay obras asignadas a esta subcontrata</p>
                                </div>
                            @endif

                            <!-- Formulario para asignar a obra -->
                            @can('editar_subcontratas')
                            @if($obrasDisponibles->count() > 0)
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="bi bi-plus-circle me-2"></i>Asignar a Obra</h6>
                                    <form action="{{ route('subcontratas.obras.add', $subcontrata) }}" method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Obra <span class="text-danger">*</span></label>
                                                <select name="obra_id" class="form-select" required>
                                                    <option value="">Seleccionar obra...</option>
                                                    @foreach($obrasDisponibles as $obra)
                                                        <option value="{{ $obra->id }}">{{ $obra->codigo }} - {{ $obra->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                                <input type="date" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Fecha Fin</label>
                                                <input type="date" name="fecha_fin" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Importe Contratado</label>
                                                <div class="input-group">
                                                    <input type="number" name="importe_contratado" class="form-control" step="0.01" min="0">
                                                    <span class="input-group-text">€</span>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Notas</label>
                                                <input type="text" name="notas" class="form-control" placeholder="Notas opcionales...">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-plus-lg me-1"></i>Asignar a Obra
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>No hay más obras disponibles para asignar.
                            </div>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form para eliminar documentos -->
<form id="deleteDocForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
/* Content Tabs - Fix for CSS conflicts */
.content-tabs-card .tab-content,
#subcontrataTabsContent.tab-content {
    display: block !important;
}

#subcontrataTabsContent .tab-pane {
    display: none !important;
}

#subcontrataTabsContent .tab-pane.active {
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
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tabs manualmente
    const tabButtons = document.querySelectorAll('#subcontrataTabs .nav-link');
    const tabPanes = document.querySelectorAll('#subcontrataTabsContent .tab-pane');

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

    // Eliminar documento CAE
    document.querySelectorAll('.btn-delete-doc').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
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
                    const form = document.getElementById('deleteDocForm');
                    form.action = url;
                    form.submit();
                }
            });
        });
    });

    // Quitar de obra
    document.querySelectorAll('.btn-remove-obra').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const obraNombre = this.dataset.obra;
            Swal.fire({
                title: '¿Quitar de obra?',
                text: `¿Estás seguro de quitar esta subcontrata de la obra "${obraNombre}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteDocForm');
                    form.action = url;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
