@extends('layouts.app')

@section('title', 'Mi Portal - ' . $trabajador->nombre_completo)

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1">Mi Portal</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-person-circle me-1"></i>{{ $trabajador->nombre_completo }}
                <span class="mx-2">|</span>
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRefresh">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </button>
        </div>
    </div>

    {{-- Fila 1: KPIs Principales --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-clock-history text-primary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-horas-mes">-</h4>
                            <small class="text-muted">Horas Mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-vacaciones">-</h4>
                            <small class="text-muted">Vacaciones</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-file-earmark-text text-info fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-documentos">-</h4>
                            <small class="text-muted">Documentos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-shield-check text-warning fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-epis">-</h4>
                            <small class="text-muted">EPIs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-mortarboard text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-formaciones">-</h4>
                            <small class="text-muted">Formaciones</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-bell text-danger fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-alertas">-</h4>
                            <small class="text-muted">Alertas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 2: Mis Horas + Mis Vacaciones --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2 text-primary"></i>Mis Horas del Mes
                        </h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="selectMes" style="width: auto;">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ ucfirst(\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}
                                    </option>
                                @endforeach
                            </select>
                            <select class="form-select form-select-sm" id="selectAnio" style="width: auto;">
                                @foreach(range(now()->year - 1, now()->year + 1) as $a)
                                    <option value="{{ $a }}" {{ $a == now()->year ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <div id="widget-mis-horas">
                        @include('trabajador.dashboard.partials._widget-mis-horas', ['fichajes' => [], 'resumen' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2 text-success"></i>Mis Vacaciones
                    </h5>
                </div>
                <div class="card-body">
                    <div id="widget-mis-vacaciones">
                        @include('trabajador.dashboard.partials._widget-mis-vacaciones', ['vacaciones' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 3: EPIs + Formaciones + Documentos --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2 text-warning"></i>Mis EPIs
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="widget-mis-epis">
                        @include('trabajador.dashboard.partials._widget-mis-epis', ['epis' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-mortarboard me-2 text-secondary"></i>Mis Formaciones
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="widget-mis-formaciones">
                        @include('trabajador.dashboard.partials._widget-mis-formaciones', ['formaciones' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text me-2 text-info"></i>Mis Documentos
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="widget-mis-documentos">
                        @include('trabajador.dashboard.partials._widget-mis-documentos', ['documentos' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 4: Primas y Bonos + Alertas --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gift me-2 text-purple"></i>Mis Primas y Bonos
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="widget-mis-primas">
                        @include('trabajador.dashboard.partials._widget-mis-primas', ['primas' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bell me-2 text-danger"></i>Mis Alertas
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="widget-alertas">
                        @include('trabajador.dashboard.partials._widget-alertas', ['alertas' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}
.text-purple {
    color: #6f42c1 !important;
}
.bg-purple {
    background-color: #6f42c1 !important;
}
.progress-vacaciones {
    height: 12px;
    border-radius: 6px;
}
.documento-pendiente {
    background-color: #fff3cd;
}
.documento-leido {
    background-color: #d1e7dd;
}
.epi-caducado {
    background-color: #f8d7da;
}
.epi-por-caducar {
    background-color: #fff3cd;
}
.formacion-vigente {
    border-left: 3px solid #198754;
}
.formacion-caducada {
    border-left: 3px solid #dc3545;
}
.formacion-por-caducar {
    border-left: 3px solid #ffc107;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAllWidgets();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('btnRefresh').addEventListener('click', () => {
        loadAllWidgets();
    });

    document.getElementById('selectMes').addEventListener('change', loadMisHoras);
    document.getElementById('selectAnio').addEventListener('change', loadMisHoras);
}

async function loadAllWidgets() {
    await Promise.all([
        loadKpis(),
        loadMisHoras(),
        loadMisVacaciones(),
        loadMisDocumentos(),
        loadMisEpis(),
        loadMisFormaciones(),
        loadMisPrimas(),
        loadAlertas(),
    ]);
}

// Cargar KPIs
async function loadKpis() {
    try {
        const response = await fetch(`{{ route('trabajador.dashboard.api.kpis') }}`);
        const data = await response.json();

        document.getElementById('kpi-horas-mes').textContent = data.horas_mes_actual || 0;
        document.getElementById('kpi-vacaciones').textContent = data.dias_vacaciones_disponibles || 0;
        document.getElementById('kpi-documentos').textContent = data.documentos_pendientes || 0;
        document.getElementById('kpi-epis').textContent = data.epis_activos || 0;
        document.getElementById('kpi-formaciones').textContent = data.formaciones_vigentes || 0;
        document.getElementById('kpi-alertas').textContent = data.alertas_no_leidas || 0;

        // Actualizar colores segun valores
        const alertasEl = document.getElementById('kpi-alertas').closest('.card');
        if (data.alertas_no_leidas > 0) {
            alertasEl.querySelector('.bg-danger').classList.add('pulse-animation');
        }
    } catch (error) {
        console.error('Error loading KPIs:', error);
    }
}

// Cargar Mis Horas
async function loadMisHoras() {
    try {
        const mes = document.getElementById('selectMes').value;
        const anio = document.getElementById('selectAnio').value;
        const response = await fetch(`{{ route('trabajador.dashboard.api.mis-fichajes') }}?mes=${mes}&anio=${anio}`);
        const data = await response.json();
        document.getElementById('widget-mis-horas').innerHTML = renderMisHoras(data);
    } catch (error) {
        console.error('Error loading mis horas:', error);
    }
}

// Cargar Mis Vacaciones
async function loadMisVacaciones() {
    try {
        const response = await fetch(`{{ route('trabajador.dashboard.api.mis-vacaciones') }}`);
        const data = await response.json();
        document.getElementById('widget-mis-vacaciones').innerHTML = renderMisVacaciones(data);
    } catch (error) {
        console.error('Error loading vacaciones:', error);
    }
}

// Cargar Mis Documentos
async function loadMisDocumentos() {
    try {
        const response = await fetch(`{{ route('trabajador.dashboard.api.mis-documentos') }}`);
        const data = await response.json();
        document.getElementById('widget-mis-documentos').innerHTML = renderMisDocumentos(data);
    } catch (error) {
        console.error('Error loading documentos:', error);
    }
}

// Cargar Mis EPIs
async function loadMisEpis() {
    try {
        const response = await fetch(`{{ route('trabajador.dashboard.api.mis-epis') }}`);
        const data = await response.json();
        document.getElementById('widget-mis-epis').innerHTML = renderMisEpis(data);
    } catch (error) {
        console.error('Error loading EPIs:', error);
    }
}

// Cargar Mis Formaciones
async function loadMisFormaciones() {
    try {
        const response = await fetch(`{{ route('trabajador.dashboard.api.mis-formaciones') }}`);
        const data = await response.json();
        document.getElementById('widget-mis-formaciones').innerHTML = renderMisFormaciones(data);
    } catch (error) {
        console.error('Error loading formaciones:', error);
    }
}

// Cargar Mis Primas
async function loadMisPrimas() {
    try {
        const response = await fetch(`{{ route('trabajador.dashboard.api.mis-primas') }}`);
        const data = await response.json();
        document.getElementById('widget-mis-primas').innerHTML = renderMisPrimas(data);
    } catch (error) {
        console.error('Error loading primas:', error);
    }
}

// Cargar Alertas
async function loadAlertas() {
    try {
        const response = await fetch(`{{ route('trabajador.dashboard.api.alertas') }}`);
        const data = await response.json();
        document.getElementById('widget-alertas').innerHTML = renderAlertas(data);
    } catch (error) {
        console.error('Error loading alertas:', error);
    }
}

// Confirmar lectura de documento
async function confirmarLecturaDocumento(documentoId) {
    try {
        const response = await fetch(`{{ url('trabajador/dashboard/api/documentos') }}/${documentoId}/confirmar-lectura`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const result = await response.json();

        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Documento confirmado',
                text: 'Se ha registrado la lectura del documento',
                timer: 2000,
                showConfirmButton: false
            });
            loadMisDocumentos();
            loadKpis();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message || 'No se pudo confirmar la lectura'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al confirmar la lectura del documento'
        });
    }
}

// Funciones de renderizado
function renderMisHoras(data) {
    const fichajes = data.fichajes || [];
    const resumen = data.resumen || {};

    if (fichajes.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-clock fs-1 d-block mb-2"></i>
            <p class="mb-0">No hay fichajes en este periodo</p>
        </div>`;
    }

    let html = `
        <div class="p-3 bg-light border-bottom">
            <div class="row text-center">
                <div class="col-4">
                    <h5 class="mb-0 text-primary">${resumen.total_horas || 0}h</h5>
                    <small class="text-muted">Total Horas</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0 text-success">${resumen.dias_trabajados || 0}</h5>
                    <small class="text-muted">Dias Trabajados</small>
                </div>
                <div class="col-4">
                    <h5 class="mb-0 text-info">${resumen.horas_extra || 0}h</h5>
                    <small class="text-muted">Horas Extra</small>
                </div>
            </div>
        </div>
    `;

    html += '<table class="table table-sm table-hover mb-0">';
    html += '<thead class="table-light"><tr><th>Fecha</th><th>Entrada</th><th>Salida</th><th class="text-end">Horas</th><th>Obra</th></tr></thead><tbody>';

    fichajes.forEach(f => {
        html += `
            <tr>
                <td><small>${f.fecha}</small></td>
                <td><span class="badge bg-success">${f.hora_entrada || '-'}</span></td>
                <td><span class="badge bg-secondary">${f.hora_salida || '-'}</span></td>
                <td class="text-end"><strong>${f.horas_trabajadas || '-'}</strong></td>
                <td><small class="text-muted">${truncate(f.obra_codigo || '-', 15)}</small></td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    return html;
}

function renderMisVacaciones(data) {
    if (!data || data.dias_totales === undefined) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-calendar fs-1 d-block mb-2"></i>
            <p class="mb-0">Sin datos de vacaciones</p>
        </div>`;
    }

    const porcentaje = data.dias_totales > 0 ? Math.round((data.dias_disfrutados / data.dias_totales) * 100) : 0;
    const porcentajePendiente = data.dias_totales > 0 ? Math.round((data.dias_pendientes / data.dias_totales) * 100) : 0;

    return `
        <div class="text-center mb-4">
            <div class="display-4 fw-bold text-success">${data.dias_disponibles}</div>
            <small class="text-muted">Dias Disponibles</small>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <small>Disfrutados</small>
                <small class="fw-semibold">${data.dias_disfrutados} de ${data.dias_totales}</small>
            </div>
            <div class="progress progress-vacaciones">
                <div class="progress-bar bg-success" style="width: ${porcentaje}%"></div>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <small>Pendientes de aprobar</small>
                <small class="fw-semibold">${data.dias_pendientes}</small>
            </div>
            <div class="progress progress-vacaciones">
                <div class="progress-bar bg-warning" style="width: ${porcentajePendiente}%"></div>
            </div>
        </div>

        <hr>
        <div class="small text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Periodo: ${data.anio || new Date().getFullYear()}
        </div>
    `;
}

function renderMisDocumentos(documentos) {
    if (!documentos || documentos.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-file-earmark fs-1 d-block mb-2"></i>
            <p class="mb-0">No tienes documentos asignados</p>
        </div>`;
    }

    let html = '<ul class="list-group list-group-flush">';
    documentos.forEach(doc => {
        const pendiente = !doc.leido;
        const rowClass = pendiente ? 'documento-pendiente' : 'documento-leido';

        html += `
            <li class="list-group-item ${rowClass}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${doc.tipo_formateado || doc.tipo}</strong>
                        <br><small class="text-muted">Subido: ${doc.fecha_documento}</small>
                        ${doc.fecha_lectura ? `<br><small class="text-success"><i class="bi bi-check-circle"></i> Leido: ${doc.fecha_lectura}</small>` : ''}
                    </div>
                    <div class="d-flex flex-column gap-1">
                        ${doc.archivo_path ? `<a href="/${doc.archivo_path}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>` : ''}
                        ${pendiente && doc.requiere_lectura ? `<button type="button" class="btn btn-sm btn-success" onclick="confirmarLecturaDocumento(${doc.id})"><i class="bi bi-check2"></i></button>` : ''}
                    </div>
                </div>
            </li>
        `;
    });
    html += '</ul>';
    return html;
}

function renderMisEpis(epis) {
    if (!epis || epis.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-shield fs-1 d-block mb-2"></i>
            <p class="mb-0">No tienes EPIs asignados</p>
        </div>`;
    }

    let html = '<ul class="list-group list-group-flush">';
    epis.forEach(epi => {
        let rowClass = '';
        let estadoBadge = '';

        if (epi.estado === 'caducado') {
            rowClass = 'epi-caducado';
            estadoBadge = '<span class="badge bg-danger">Caducado</span>';
        } else if (epi.estado === 'por_caducar') {
            rowClass = 'epi-por-caducar';
            estadoBadge = '<span class="badge bg-warning">Por caducar</span>';
        } else {
            estadoBadge = '<span class="badge bg-success">Vigente</span>';
        }

        html += `
            <li class="list-group-item ${rowClass}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${epi.nombre}</strong>
                        <br><small class="text-muted">${epi.categoria}</small>
                        <br><small>Entregado: ${epi.fecha_entrega}</small>
                        ${epi.fecha_caducidad ? `<br><small>Caduca: ${epi.fecha_caducidad}</small>` : ''}
                    </div>
                    ${estadoBadge}
                </div>
            </li>
        `;
    });
    html += '</ul>';
    return html;
}

function renderMisFormaciones(formaciones) {
    if (!formaciones || formaciones.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>
            <p class="mb-0">No tienes formaciones registradas</p>
        </div>`;
    }

    let html = '<ul class="list-group list-group-flush">';
    formaciones.forEach(f => {
        let rowClass = 'formacion-vigente';
        let estadoBadge = '<span class="badge bg-success">Vigente</span>';

        if (f.estado === 'caducada') {
            rowClass = 'formacion-caducada';
            estadoBadge = '<span class="badge bg-danger">Caducada</span>';
        } else if (f.estado === 'por_caducar') {
            rowClass = 'formacion-por-caducar';
            estadoBadge = '<span class="badge bg-warning">Por caducar</span>';
        }

        html += `
            <li class="list-group-item ${rowClass}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${f.tipo}</strong>
                        <br><small class="text-muted">Obtenido: ${f.fecha_realizacion}</small>
                        ${f.fecha_caducidad ? `<br><small>Caduca: ${f.fecha_caducidad}</small>` : '<br><small class="text-success">Sin caducidad</small>'}
                    </div>
                    ${estadoBadge}
                </div>
            </li>
        `;
    });
    html += '</ul>';
    return html;
}

function renderMisPrimas(data) {
    const primas = data.primas || [];
    const bonos = data.bonos || [];
    const totalAnio = data.total_anio || 0;

    if (primas.length === 0 && bonos.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-gift fs-1 d-block mb-2"></i>
            <p class="mb-0">No tienes primas ni bonos registrados</p>
        </div>`;
    }

    let html = `
        <div class="p-3 bg-light border-bottom">
            <div class="text-center">
                <h5 class="mb-0 text-purple">${formatCurrency(totalAnio)}</h5>
                <small class="text-muted">Total ${new Date().getFullYear()}</small>
            </div>
        </div>
    `;

    html += '<table class="table table-sm table-hover mb-0">';
    html += '<thead class="table-light"><tr><th>Concepto</th><th>Tipo</th><th>Fecha</th><th class="text-end">Importe</th></tr></thead><tbody>';

    // Combinar y ordenar por fecha
    const items = [
        ...primas.map(p => ({...p, tipo: 'Prima'})),
        ...bonos.map(b => ({...b, tipo: 'Bono', concepto: b.tipo_bono}))
    ].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

    items.slice(0, 10).forEach(item => {
        const tipoClass = item.tipo === 'Prima' ? 'info' : 'success';
        html += `
            <tr>
                <td>${item.concepto || item.descripcion || '-'}</td>
                <td><span class="badge bg-${tipoClass}">${item.tipo}</span></td>
                <td><small>${item.fecha || '-'}</small></td>
                <td class="text-end fw-semibold">${formatCurrency(item.importe || 0)}</td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    return html;
}

function renderAlertas(alertas) {
    if (!alertas || alertas.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
            <p class="mb-0">Sin alertas pendientes</p>
        </div>`;
    }

    let html = '<ul class="list-group list-group-flush">';
    alertas.forEach(alerta => {
        const prioridadClass = {
            'critica': 'danger',
            'alta': 'warning',
            'media': 'info',
            'baja': 'secondary'
        }[alerta.prioridad] || 'secondary';

        html += `
            <li class="list-group-item border-start border-${prioridadClass} border-4 py-2">
                <div class="d-flex justify-content-between">
                    <span class="badge bg-${prioridadClass}">${alerta.prioridad}</span>
                    <small class="text-muted">${alerta.fecha_vencimiento || ''}</small>
                </div>
                <p class="mb-0 mt-1 small">${truncate(alerta.titulo, 50)}</p>
                <small class="text-muted">${alerta.tipo || ''}</small>
            </li>
        `;
    });
    html += '</ul>';
    return html;
}

// Utilidades
function formatNumber(value) {
    return new Intl.NumberFormat('es-ES').format(value || 0);
}

function formatCurrency(value) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value || 0);
}

function truncate(str, length) {
    if (!str) return '';
    return str.length > length ? str.substring(0, length) + '...' : str;
}
</script>
@endpush
