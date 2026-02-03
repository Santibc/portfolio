@extends('layouts.app')

@section('title', 'Mi Panel - Encargado')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1">Mi Panel</h1>
            <p class="text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        {{-- Filtros de fecha --}}
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form method="GET" action="{{ route('encargado.dashboard') }}" class="d-flex gap-2">
                <div>
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm"
                           value="{{ request('fecha_desde', now()->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm"
                           value="{{ request('fecha_hasta', now()->format('Y-m-d')) }}">
                </div>
                <div class="d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('encargado.dashboard') }}" class="btn btn-outline-secondary btn-sm ms-1">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
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
                            <i class="bi bi-building text-primary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-obras">{{ $kpis['obras_total'] ?? 0 }}</h4>
                            <small class="text-muted">Mis Obras</small>
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
                            <i class="bi bi-people text-success fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-trabajadores">{{ $kpis['trabajadores_activos'] ?? 0 }}</h4>
                            <small class="text-muted">Trabajadores</small>
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
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-fichajes">{{ $kpis['fichajes_pendientes'] ?? 0 }}</h4>
                            <small class="text-muted">Fichajes Pend.</small>
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
                            <h4 class="mb-0" id="kpi-partes">{{ ($kpis['partes_borrador'] ?? 0) + ($kpis['partes_completados'] ?? 0) }}</h4>
                            <small class="text-muted">Partes Pend.</small>
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
                            <i class="bi bi-rulers text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-produccion">{{ number_format($kpis['produccion_hoy_m2'] ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">m² Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-{{ ($kpis['alertas_no_leidas'] ?? 0) > 0 ? 'danger' : 'secondary' }} bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-bell text-{{ ($kpis['alertas_no_leidas'] ?? 0) > 0 ? 'danger' : 'secondary' }} fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-alertas">{{ $kpis['alertas_no_leidas'] ?? 0 }}</h4>
                            <small class="text-muted">Alertas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 2: Mis Obras + Produccion Diaria --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building me-2 text-primary"></i>Mis Obras
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <div id="widget-mis-obras">
                        @include('encargado.dashboard.partials._widget-mis-obras', ['obras' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-speedometer2 me-2 text-success"></i>Produccion Hoy
                    </h5>
                </div>
                <div class="card-body">
                    <div id="widget-produccion-diaria">
                        @include('encargado.dashboard.partials._widget-produccion-diaria', ['produccion' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 2.5: Métricas por Estado --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>Métricas por Estado
                    </h5>
                </div>
                <div class="card-body">
                    <div id="widget-metricas-estado">
                        @include('encargado.dashboard.partials._widget-metricas-estado', ['metricas' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 3: Horas Trabajadores + Partes Pendientes --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock me-2 text-info"></i>Horas por Trabajador
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <div id="widget-horas-trabajadores">
                        @include('encargado.dashboard.partials._widget-horas-trabajadores', ['trabajadores' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text me-2 text-warning"></i>Partes Pendientes
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <div id="widget-partes-pendientes">
                        @include('encargado.dashboard.partials._widget-partes-pendientes', ['partes' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 4: Maquinaria + Calendario + Alertas --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2 text-secondary"></i>Maquinaria
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="widget-maquinaria-asignada">
                        @include('encargado.dashboard.partials._widget-maquinaria-asignada', ['maquinaria' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-week me-2 text-primary"></i>Calendario Semanal
                    </h5>
                </div>
                <div class="card-body">
                    <div id="widget-calendario-semanal">
                        @include('encargado.dashboard.partials._widget-calendario-semanal', ['calendario' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bell me-2 text-danger"></i>Alertas
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="widget-alertas-encargado">
                        @include('encargado.dashboard.partials._widget-alertas-encargado', ['alertas' => []])
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
.table-ranking th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}
.variation-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}
.calendario-dia {
    border-radius: 8px;
    padding: 0.75rem;
    text-align: center;
    border: 1px solid #e9ecef;
    transition: all 0.2s;
}
.calendario-dia:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}
.calendario-dia.hoy {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
.calendario-dia.hoy .calendario-eventos span {
    background-color: rgba(255,255,255,0.3) !important;
    color: white !important;
}
.calendario-eventos {
    display: flex;
    gap: 3px;
    justify-content: center;
    margin-top: 0.5rem;
}
.calendario-eventos span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.fichaje-activo {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
// Inicializacion al cargar la pagina
document.addEventListener('DOMContentLoaded', function() {
    loadAllWidgets();
    setupEventListeners();
});

function setupEventListeners() {
    // Refresh
    document.getElementById('btnRefresh').addEventListener('click', () => {
        loadAllWidgets();
    });
}

async function loadAllWidgets() {
    await Promise.all([
        loadKpis(),
        loadMisObras(),
        loadProduccionDiaria(),
        loadMetricasEstado(),
        loadHorasTrabajadores(),
        loadMaquinariaAsignada(),
        loadCalendarioSemanal(),
        loadPartesPendientes(),
        loadAlertas(),
    ]);
}

// Cargar KPIs
async function loadKpis() {
    try {
        const response = await fetch(`{{ route('encargado.dashboard.api.kpis') }}`);
        const data = await response.json();

        document.getElementById('kpi-obras').textContent = data.obras_total || 0;
        document.getElementById('kpi-trabajadores').textContent = data.trabajadores_activos || 0;
        document.getElementById('kpi-fichajes').textContent = data.fichajes_pendientes || 0;
        document.getElementById('kpi-partes').textContent = (data.partes_borrador || 0) + (data.partes_completados || 0);
        document.getElementById('kpi-produccion').textContent = formatNumber(data.produccion_hoy_m2 || 0);
        document.getElementById('kpi-alertas').textContent = data.alertas_no_leidas || 0;
    } catch (error) {
        console.error('Error loading KPIs:', error);
    }
}

// Cargar Mis Obras
async function loadMisObras() {
    try {
        const response = await fetch(`{{ route('encargado.dashboard.api.mis-obras') }}`);
        const data = await response.json();
        document.getElementById('widget-mis-obras').innerHTML = renderMisObras(data);
    } catch (error) {
        console.error('Error loading mis obras:', error);
    }
}

// Cargar Produccion Diaria
async function loadProduccionDiaria() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const fechaDesde = urlParams.get('fecha_desde') || '';
        const fechaHasta = urlParams.get('fecha_hasta') || '';

        const url = `{{ route('encargado.dashboard.api.produccion-diaria') }}?fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
        const response = await fetch(url);
        const data = await response.json();
        document.getElementById('widget-produccion-diaria').innerHTML = renderProduccionDiaria(data);
    } catch (error) {
        console.error('Error loading produccion:', error);
    }
}

// Cargar Métricas por Estado
async function loadMetricasEstado() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const fechaDesde = urlParams.get('fecha_desde') || '';
        const fechaHasta = urlParams.get('fecha_hasta') || '';

        const url = `{{ route('encargado.dashboard.api.metricas-estado') }}?fecha_desde=${fechaDesde}&fecha_hasta=${fechaHasta}`;
        const response = await fetch(url);
        const data = await response.json();
        document.getElementById('widget-metricas-estado').innerHTML = renderMetricasEstado(data);
    } catch (error) {
        console.error('Error loading metricas estado:', error);
    }
}

// Cargar Horas Trabajadores
async function loadHorasTrabajadores() {
    try {
        const response = await fetch(`{{ route('encargado.dashboard.api.horas-trabajadores') }}`);
        const data = await response.json();
        document.getElementById('widget-horas-trabajadores').innerHTML = renderHorasTrabajadores(data);
    } catch (error) {
        console.error('Error loading horas:', error);
    }
}

// Cargar Maquinaria Asignada
async function loadMaquinariaAsignada() {
    try {
        const response = await fetch(`{{ route('encargado.dashboard.api.maquinaria-asignada') }}`);
        const data = await response.json();
        document.getElementById('widget-maquinaria-asignada').innerHTML = renderMaquinariaAsignada(data);
    } catch (error) {
        console.error('Error loading maquinaria:', error);
    }
}

// Cargar Calendario Semanal
async function loadCalendarioSemanal() {
    try {
        const response = await fetch(`{{ route('encargado.dashboard.api.calendario-semanal') }}`);
        const data = await response.json();
        document.getElementById('widget-calendario-semanal').innerHTML = renderCalendarioSemanal(data);
    } catch (error) {
        console.error('Error loading calendario:', error);
    }
}

// Cargar Partes Pendientes
async function loadPartesPendientes() {
    try {
        const response = await fetch(`{{ route('encargado.dashboard.api.partes-pendientes') }}`);
        const data = await response.json();
        document.getElementById('widget-partes-pendientes').innerHTML = renderPartesPendientes(data);
    } catch (error) {
        console.error('Error loading partes:', error);
    }
}

// Cargar Alertas
async function loadAlertas() {
    try {
        const response = await fetch(`{{ route('encargado.dashboard.api.alertas') }}`);
        const data = await response.json();
        document.getElementById('widget-alertas-encargado').innerHTML = renderAlertas(data);
    } catch (error) {
        console.error('Error loading alertas:', error);
    }
}

// Funciones de renderizado
function renderMisObras(obras) {
    if (!obras || obras.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p class="mb-0">No tienes obras asignadas</p>
        </div>`;
    }

    let html = '<table class="table table-sm table-hover mb-0">';
    html += '<thead class="table-light"><tr><th>Obra</th><th>Cliente</th><th class="text-center">Trab.</th><th>Ultimo Parte</th><th>Estado</th></tr></thead><tbody>';

    obras.forEach(obra => {
        const estadoClass = {
            'aprobada': 'info',
            'en_curso': 'success',
            'pausada': 'warning',
            'finalizada': 'secondary'
        }[obra.estado] || 'secondary';

        html += `
            <tr>
                <td>
                    <a href="/obras/${obra.id}" class="text-decoration-none fw-semibold">${obra.codigo}</a>
                    <br><small class="text-muted">${truncate(obra.nombre, 25)}</small>
                </td>
                <td><small>${truncate(obra.cliente, 20)}</small></td>
                <td class="text-center"><span class="badge bg-secondary">${obra.trabajadores_activos}</span></td>
                <td><small>${obra.ultimo_parte_fecha || '-'}</small></td>
                <td><span class="badge bg-${estadoClass}">${obra.estado}</span></td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    return html;
}

function renderProduccionDiaria(data) {
    if (!data || !data.hoy || !data.hoy.categorias) {
        return '<div class="text-center text-muted py-4">Sin datos de hoy</div>';
    }

    const hoy = data.hoy;
    const categorias = hoy.categorias;
    const variaciones = data.variaciones || {};

    // Mapeo de categorías a iconos (mismo que widget Blade)
    const iconosCat = {
        'desbroce': { icon: 'bi-scissors', color: 'success' },
        'limpieza': { icon: 'bi-stars', color: 'info' },
        'herbicida': { icon: 'bi-droplet', color: 'danger' },
        'tala': { icon: 'bi-tree', color: 'warning' },
        'poda': { icon: 'bi-flower1', color: 'primary' },
        'otro': { icon: 'bi-box', color: 'secondary' }
    };

    const unidadesFormato = {
        'm2': 'm²',
        'unidades': 'uds',
        'hectareas': 'ha',
        'jornal': 'j'
    };

    const renderVariation = (v) => {
        if (!v || v.valor === 0) return '';
        const icon = v.tipo === 'positive' ? 'bi-arrow-up' : (v.tipo === 'negative' ? 'bi-arrow-down' : 'bi-dash');
        const color = v.tipo === 'positive' ? 'success' : (v.tipo === 'negative' ? 'danger' : 'secondary');
        return `<span class="badge bg-${color} variation-badge"><i class="bi ${icon}"></i> ${v.valor}%</span>`;
    };

    let html = `
        <div class="text-center mb-3">
            <small class="text-muted">${data.fecha || 'Hoy'}</small>
        </div>
        <div class="row g-2">
    `;

    // Iterar dinámicamente sobre todas las categorías
    for (const [categoria, datos] of Object.entries(categorias)) {
        const icono = iconosCat[categoria] || iconosCat['otro'];
        const unidad = unidadesFormato[datos.unidad] || datos.unidad;

        html += `
            <div class="col-6">
                <div class="border rounded p-2 text-center">
                    <i class="bi ${icono.icon} text-${icono.color} fs-4"></i>
                    <h5 class="mb-0">${formatNumber(datos.cantidad)}</h5>
                    <small class="text-muted">${capitalize(categoria)} (${unidad})</small>
                    <div>${renderVariation(variaciones[categoria])}</div>
                </div>
            </div>
        `;
    }

    // Card de número de partes
    html += `
            <div class="col-6">
                <div class="border rounded p-2 text-center">
                    <i class="bi bi-file-text text-secondary fs-4"></i>
                    <h5 class="mb-0">${hoy.num_partes}</h5>
                    <small class="text-muted">Partes</small>
                </div>
            </div>
        </div>
    `;

    return html;
}

// Función auxiliar para capitalizar
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function renderHorasTrabajadores(trabajadores) {
    if (!trabajadores || trabajadores.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-people fs-1 d-block mb-2"></i>
            <p class="mb-0">Sin trabajadores asignados</p>
        </div>`;
    }

    let html = '<table class="table table-sm table-hover mb-0">';
    html += '<thead class="table-light"><tr><th>Trabajador</th><th>Obra</th><th class="text-end">Hoy</th><th class="text-end">Semana</th><th class="text-center">Estado</th></tr></thead><tbody>';

    trabajadores.forEach(t => {
        const estadoHtml = t.fichaje_activo
            ? '<span class="badge bg-success fichaje-activo"><i class="bi bi-play-circle"></i> Activo</span>'
            : '<span class="badge bg-secondary"><i class="bi bi-stop-circle"></i></span>';

        html += `
            <tr>
                <td>${t.nombre_completo}</td>
                <td><small class="text-muted">${t.obra_actual || '-'}</small></td>
                <td class="text-end"><strong>${t.horas_hoy}h</strong></td>
                <td class="text-end">${t.horas_semana}h</td>
                <td class="text-center">${estadoHtml}</td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    return html;
}

function renderMaquinariaAsignada(maquinaria) {
    if (!maquinaria || maquinaria.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-gear fs-1 d-block mb-2"></i>
            <p class="mb-0">Sin maquinaria asignada</p>
        </div>`;
    }

    let html = '<ul class="list-group list-group-flush">';
    maquinaria.forEach(m => {
        const estadoClass = {
            'operativa': 'success',
            'en_reparacion': 'warning',
            'baja': 'danger'
        }[m.estado] || 'secondary';

        html += `
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <strong>${m.nombre}</strong>
                    <br><small class="text-muted">${m.marca_modelo}</small>
                    <br><small>Obra: ${m.obra_codigo} | Op: ${truncate(m.operador, 15)}</small>
                </div>
                <span class="badge bg-${estadoClass}">${m.estado}</span>
            </li>
        `;
    });
    html += '</ul>';
    return html;
}

function renderCalendarioSemanal(data) {
    if (!data || !data.dias) {
        return '<div class="text-center text-muted py-4">Sin datos</div>';
    }

    let html = `<div class="text-center mb-3"><small class="text-muted fw-semibold">${data.semana}</small></div>`;
    html += '<div class="row g-2">';

    data.dias.forEach(dia => {
        const hoyClass = dia.es_hoy ? 'hoy' : '';
        const tieneEventos = dia.partes > 0 || dia.inspecciones > 0 || dia.vencimientos > 0;

        html += `
            <div class="col">
                <div class="calendario-dia ${hoyClass}">
                    <small class="d-block text-uppercase" style="font-size:0.65rem">${dia.dia}</small>
                    <strong class="fs-5">${dia.dia_mes}</strong>
                    ${tieneEventos ? `
                    <div class="calendario-eventos">
                        ${dia.partes > 0 ? `<span class="bg-primary" title="${dia.partes} partes"></span>` : ''}
                        ${dia.inspecciones > 0 ? `<span class="bg-warning" title="${dia.inspecciones} inspecciones"></span>` : ''}
                        ${dia.vencimientos > 0 ? `<span class="bg-danger" title="${dia.vencimientos} vencimientos"></span>` : ''}
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    });

    html += '</div>';
    html += `
        <div class="mt-3 d-flex justify-content-center gap-3">
            <small><span class="badge bg-primary">&nbsp;</span> Partes</small>
            <small><span class="badge bg-warning">&nbsp;</span> Inspecciones</small>
            <small><span class="badge bg-danger">&nbsp;</span> Vencimientos</small>
        </div>
    `;

    return html;
}

function renderPartesPendientes(partes) {
    if (!partes || partes.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
            <p class="mb-0">Sin partes pendientes</p>
        </div>`;
    }

    let html = '<table class="table table-sm table-hover mb-0">';
    html += '<thead class="table-light"><tr><th>Obra</th><th>Fecha</th><th>Jornada</th><th>Estado</th></tr></thead><tbody>';

    partes.forEach(p => {
        const estadoClass = p.estado === 'borrador' ? 'warning' : 'info';
        html += `
            <tr>
                <td>
                    <a href="/partes-diarios/${p.id}/edit" class="text-decoration-none">${p.obra_codigo}</a>
                </td>
                <td><small>${p.fecha}</small></td>
                <td><small>${p.jornada}</small></td>
                <td><span class="badge bg-${estadoClass}">${p.estado}</span></td>
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
            <p class="mb-0">Sin alertas</p>
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
                <p class="mb-0 mt-1 small">${truncate(alerta.titulo, 40)}</p>
            </li>
        `;
    });
    html += '</ul>';
    return html;
}

function renderMetricasEstado(data) {
    if (!data || (!data.pendiente && !data.por_aprobar && !data.aprobada)) {
        return '<div class="text-center text-muted py-4">Sin datos</div>';
    }

    // Mapeo de categorías a iconos
    const iconosCat = {
        'desbroce': { icon: 'bi-scissors', color: 'success' },
        'limpieza': { icon: 'bi-stars', color: 'info' },
        'herbicida': { icon: 'bi-droplet', color: 'danger' },
        'tala': { icon: 'bi-tree', color: 'warning' },
        'poda': { icon: 'bi-flower1', color: 'primary' },
        'otro': { icon: 'bi-box', color: 'secondary' }
    };

    const unidadesFormato = {
        'm2': 'm²',
        'unidades': 'uds',
        'hectareas': 'ha',
        'jornal': 'j'
    };

    const renderCategoria = (categoria, datos) => {
        const icono = iconosCat[categoria] || iconosCat['otro'];
        const unidad = unidadesFormato[datos.unidad] || datos.unidad;

        return `
            <div class="col-6">
                <div class="border rounded p-2 text-center">
                    <i class="bi ${icono.icon} text-${icono.color} fs-5"></i>
                    <h6 class="mb-0">${formatNumber(datos.cantidad)}</h6>
                    <small class="text-muted">${capitalize(categoria)} (${unidad})</small>
                </div>
            </div>
        `;
    };

    const renderTab = (id, estado, datos, active = false) => {
        if (!datos || !datos.categorias || Object.keys(datos.categorias).length === 0) {
            return `
                <div class="tab-pane fade ${active ? 'show active' : ''}" id="${id}">
                    <div class="text-center text-muted py-3">Sin producción ${estado.toLowerCase()}</div>
                </div>
            `;
        }

        let html = `
            <div class="tab-pane fade ${active ? 'show active' : ''}" id="${id}">
                <div class="row g-2">
        `;

        for (const [categoria, info] of Object.entries(datos.categorias)) {
            html += renderCategoria(categoria, info);
        }

        html += `
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <strong>${formatNumber(datos.importe_total)} €</strong>
                        en ${datos.num_partes} partes
                    </small>
                </div>
            </div>
        `;

        return html;
    };

    return `
        <div class="text-center mb-3">
            <small class="text-muted">${data.fecha_inicio || ''} - ${data.fecha_fin || ''}</small>
        </div>

        <ul class="nav nav-pills nav-fill mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-pendiente" type="button">
                    Pendiente <span class="badge bg-warning ms-1">${data.pendiente?.num_partes || 0}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-por-aprobar" type="button">
                    Por Aprobar <span class="badge bg-info ms-1">${data.por_aprobar?.num_partes || 0}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-aprobada" type="button">
                    Aprobado <span class="badge bg-success ms-1">${data.aprobada?.num_partes || 0}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            ${renderTab('tab-pendiente', 'Pendiente', data.pendiente, true)}
            ${renderTab('tab-por-aprobar', 'Por Aprobar', data.por_aprobar)}
            ${renderTab('tab-aprobada', 'Aprobado', data.aprobada)}
        </div>
    `;
}

// Utilidades
function formatNumber(value) {
    return new Intl.NumberFormat('es-ES').format(value || 0);
}

function truncate(str, length) {
    if (!str) return '';
    return str.length > length ? str.substring(0, length) + '...' : str;
}
</script>
@endpush
