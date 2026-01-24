@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Header con filtros --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Panel de control administrativo</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group input-group-sm" style="width: 150px;">
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                <input type="date" id="fechaInicio" class="form-control" value="{{ $filtros['fecha_inicio'] }}">
            </div>
            <div class="input-group input-group-sm" style="width: 150px;">
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                <input type="date" id="fechaFin" class="form-control" value="{{ $filtros['fecha_fin'] }}">
            </div>
            <select id="obraId" class="form-select form-select-sm" style="width: 180px;">
                <option value="">Todas las obras</option>
                @foreach($opcionesFiltros['obras'] as $obra)
                    <option value="{{ $obra['id'] }}">{{ $obra['codigo_nombre'] }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-primary btn-sm" id="btnFiltrar">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRefresh">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    {{-- Fila 1: KPIs Principales --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-arrow-down-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-ingresos">{{ number_format($kpis['ingresos_periodo'], 2, ',', '.') }} €</h4>
                            <small class="text-muted">Ingresos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-arrow-up-circle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-gastos">{{ number_format($kpis['gastos_periodo'], 2, ',', '.') }} €</h4>
                            <small class="text-muted">Gastos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-graph-up-arrow text-primary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 {{ $kpis['beneficio_periodo'] >= 0 ? 'text-success' : 'text-danger' }}" id="kpi-beneficio">
                                {{ number_format($kpis['beneficio_periodo'], 2, ',', '.') }} €
                            </h4>
                            <small class="text-muted">Beneficio</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-cobros-pendientes">{{ number_format($kpis['cobros_pendientes'], 2, ',', '.') }} €</h4>
                            <small class="text-muted">Cobros Pend.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-building text-info fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-obras">{{ $kpis['obras_en_curso'] }}</h4>
                            <small class="text-muted">Obras en Curso</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-{{ ($kpis['alertas']['criticas'] ?? 0) > 0 ? 'danger' : 'secondary' }} bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-triangle text-{{ ($kpis['alertas']['criticas'] ?? 0) > 0 ? 'danger' : 'secondary' }} fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0" id="kpi-alertas">{{ $kpis['alertas']['no_leidas'] ?? 0 }}</h4>
                            <small class="text-muted">Alertas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 2: Rentabilidad Mensual + Alertas Críticas --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>Rentabilidad Mensual
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="chartRentabilidad"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-circle me-2 text-danger"></i>Alertas Críticas
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 340px; overflow-y: auto;">
                    <div id="widget-alertas-criticas">
                        @include('admin.dashboard.partials._widget-alertas-criticas', ['alertas' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 3: Flujo de Caja + Cobros Pendientes --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cash-stack me-2 text-success"></i>Flujo de Caja
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 280px;">
                        <canvas id="chartFlujoCaja"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-hourglass-split me-2 text-warning"></i>Cobros Pendientes por Antigüedad
                    </h5>
                </div>
                <div class="card-body">
                    <div id="widget-cobros-pendientes">
                        @include('admin.dashboard.partials._widget-cobros-pendientes', ['cobros' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 4: Rankings de Obras y Cuadrillas --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-trophy me-2 text-warning"></i>Rentabilidad por Obra
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="widget-rentabilidad-obras" style="max-height: 400px; overflow-y: auto;">
                        @include('admin.dashboard.partials._widget-rentabilidad-obras', ['obras' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2 text-info"></i>Ranking de Cuadrillas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="widget-rentabilidad-cuadrillas" style="max-height: 400px; overflow-y: auto;">
                        @include('admin.dashboard.partials._widget-rentabilidad-cuadrillas', ['cuadrillas' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 5: Obras en Riesgo + Producción --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-diamond me-2 text-danger"></i>Obras en Riesgo
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="widget-obras-riesgo" style="max-height: 350px; overflow-y: auto;">
                        @include('admin.dashboard.partials._widget-obras-riesgo', ['obras' => []])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-speedometer2 me-2 text-primary"></i>Producción del Mes
                    </h5>
                </div>
                <div class="card-body">
                    <div id="widget-produccion">
                        @include('admin.dashboard.partials._widget-produccion', ['produccion' => []])
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
.aging-bar {
    height: 30px;
    border-radius: 4px;
    position: relative;
}
.aging-segment {
    display: inline-block;
    height: 100%;
    position: relative;
}
.aging-segment span {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.7rem;
    font-weight: 600;
    color: white;
}
.variation-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}
</style>
@endpush

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Variables globales para los gráficos
let chartRentabilidad = null;
let chartFlujoCaja = null;

// Colores del tema
const COLORS = {
    primary: '#0d6efd',
    success: '#198754',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#0dcaf0',
    secondary: '#6c757d',
    light: '#f8f9fa',
};

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
    loadAllWidgets();
    setupEventListeners();
});

function setupEventListeners() {
    // Filtrar
    document.getElementById('btnFiltrar').addEventListener('click', () => {
        loadAllWidgets();
    });

    // Refresh
    document.getElementById('btnRefresh').addEventListener('click', () => {
        loadAllWidgets();
    });
}

function getFilters() {
    return {
        fecha_inicio: document.getElementById('fechaInicio').value,
        fecha_fin: document.getElementById('fechaFin').value,
        obra_id: document.getElementById('obraId').value,
    };
}

function initCharts() {
    // Gráfico de Rentabilidad Mensual (solo barras agrupadas)
    const ctxRentabilidad = document.getElementById('chartRentabilidad').getContext('2d');
    chartRentabilidad = new Chart(ctxRentabilidad, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Ingresos',
                    data: [],
                    backgroundColor: COLORS.success,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Gastos',
                    data: [],
                    backgroundColor: COLORS.danger,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Beneficio',
                    data: [],
                    backgroundColor: COLORS.primary,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatCurrencyShort(value);
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Flujo de Caja
    const ctxFlujoCaja = document.getElementById('chartFlujoCaja').getContext('2d');
    chartFlujoCaja = new Chart(ctxFlujoCaja, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Cobros',
                    data: [],
                    borderColor: COLORS.success,
                    backgroundColor: COLORS.success + '20',
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: 'Pagos',
                    data: [],
                    borderColor: COLORS.danger,
                    backgroundColor: COLORS.danger + '20',
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: 'Saldo Acumulado',
                    data: [],
                    borderColor: COLORS.primary,
                    backgroundColor: 'transparent',
                    borderWidth: 3,
                    tension: 0.3,
                    borderDash: [5, 5],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return formatCurrencyShort(value);
                        }
                    }
                }
            }
        }
    });
}

async function loadAllWidgets() {
    showLoadingAll();
    await Promise.all([
        loadKpis(),
        loadRentabilidadMensual(),
        loadFlujoCaja(),
        loadAlertasCriticas(),
        loadCobrosPendientes(),
        loadRentabilidadObras(),
        loadRentabilidadCuadrillas(),
        loadObrasRiesgo(),
        loadProduccion(),
    ]);
}

function showLoadingAll() {
    // Los widgets muestran su propio loading state
}

// Cargar KPIs
async function loadKpis() {
    try {
        const filters = getFilters();
        const params = new URLSearchParams(filters);
        const response = await fetch(`{{ route('admin.dashboard.api.kpis') }}?${params}`);
        const data = await response.json();

        document.getElementById('kpi-ingresos').textContent = formatCurrency(data.ingresos_periodo);
        document.getElementById('kpi-gastos').textContent = formatCurrency(data.gastos_periodo);

        const beneficioEl = document.getElementById('kpi-beneficio');
        beneficioEl.textContent = formatCurrency(data.beneficio_periodo);
        beneficioEl.className = `mb-0 ${data.beneficio_periodo >= 0 ? 'text-success' : 'text-danger'}`;

        document.getElementById('kpi-cobros-pendientes').textContent = formatCurrency(data.cobros_pendientes);
        document.getElementById('kpi-obras').textContent = data.obras_en_curso;
        document.getElementById('kpi-alertas').textContent = data.alertas?.no_leidas ?? 0;
    } catch (error) {
        console.error('Error loading KPIs:', error);
    }
}

// Cargar Rentabilidad Mensual (solo meses con datos)
async function loadRentabilidadMensual() {
    try {
        const response = await fetch(`{{ route('admin.dashboard.api.rentabilidad-mensual') }}`);
        const data = await response.json();

        chartRentabilidad.data.labels = data.labels;
        chartRentabilidad.data.datasets[0].data = data.ingresos;
        chartRentabilidad.data.datasets[1].data = data.gastos;
        chartRentabilidad.data.datasets[2].data = data.beneficio;
        chartRentabilidad.update();
    } catch (error) {
        console.error('Error loading rentabilidad:', error);
    }
}

// Cargar Flujo de Caja
async function loadFlujoCaja() {
    try {
        const response = await fetch(`{{ route('admin.dashboard.api.flujo-caja') }}`);
        const data = await response.json();

        chartFlujoCaja.data.labels = data.labels;
        chartFlujoCaja.data.datasets[0].data = data.cobros;
        chartFlujoCaja.data.datasets[1].data = data.pagos;
        chartFlujoCaja.data.datasets[2].data = data.saldoAcumulado;
        chartFlujoCaja.update();
    } catch (error) {
        console.error('Error loading flujo caja:', error);
    }
}

// Cargar Alertas Críticas
async function loadAlertasCriticas() {
    try {
        const response = await fetch(`{{ route('admin.dashboard.api.alertas-criticas') }}`);
        const data = await response.json();
        document.getElementById('widget-alertas-criticas').innerHTML = renderAlertasCriticas(data);
    } catch (error) {
        console.error('Error loading alertas:', error);
    }
}

// Cargar Cobros Pendientes
async function loadCobrosPendientes() {
    try {
        const response = await fetch(`{{ route('admin.dashboard.api.cobros-pendientes') }}`);
        const data = await response.json();
        document.getElementById('widget-cobros-pendientes').innerHTML = renderCobrosPendientes(data);
    } catch (error) {
        console.error('Error loading cobros:', error);
    }
}

// Cargar Rentabilidad por Obras
async function loadRentabilidadObras() {
    try {
        const filters = getFilters();
        const params = new URLSearchParams(filters);
        const response = await fetch(`{{ route('admin.dashboard.api.rentabilidad-obras') }}?${params}`);
        const data = await response.json();
        document.getElementById('widget-rentabilidad-obras').innerHTML = renderRentabilidadObras(data);
    } catch (error) {
        console.error('Error loading obras:', error);
    }
}

// Cargar Rentabilidad por Cuadrillas
async function loadRentabilidadCuadrillas() {
    try {
        const response = await fetch(`{{ route('admin.dashboard.api.rentabilidad-cuadrillas') }}`);
        const data = await response.json();
        document.getElementById('widget-rentabilidad-cuadrillas').innerHTML = renderRentabilidadCuadrillas(data);
    } catch (error) {
        console.error('Error loading cuadrillas:', error);
    }
}

// Cargar Obras en Riesgo
async function loadObrasRiesgo() {
    try {
        const response = await fetch(`{{ route('admin.dashboard.api.obras-riesgo') }}`);
        const data = await response.json();
        document.getElementById('widget-obras-riesgo').innerHTML = renderObrasRiesgo(data);
    } catch (error) {
        console.error('Error loading obras riesgo:', error);
    }
}

// Cargar Producción
async function loadProduccion() {
    try {
        const response = await fetch(`{{ route('admin.dashboard.api.produccion') }}`);
        const data = await response.json();
        document.getElementById('widget-produccion').innerHTML = renderProduccion(data);
    } catch (error) {
        console.error('Error loading produccion:', error);
    }
}

// Funciones de renderizado
function renderAlertasCriticas(alertas) {
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
            <li class="list-group-item border-start border-${prioridadClass} border-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-${prioridadClass} me-2">${alerta.prioridad?.toUpperCase()}</span>
                        <small class="text-muted">${alerta.tipo || 'Alerta'}</small>
                    </div>
                    <small class="text-muted">${alerta.fecha_vencimiento ? formatDate(alerta.fecha_vencimiento) : ''}</small>
                </div>
                <p class="mb-0 mt-1 small">${alerta.mensaje || alerta.descripcion || ''}</p>
            </li>
        `;
    });
    html += '</ul>';
    return html;
}

function renderCobrosPendientes(data) {
    if (!data || !data.resumen) {
        return '<div class="text-center text-muted py-4">Sin datos disponibles</div>';
    }

    const total = parseFloat(data.total_pendiente) || 0;
    const resumen = data.resumen;

    // Calcular porcentajes para la barra
    const tramos = [
        { key: 'al_dia', label: 'Al día', color: '#198754' },
        { key: '1_30', label: '1-30d', color: '#0dcaf0' },
        { key: '31_60', label: '31-60d', color: '#ffc107' },
        { key: '61_90', label: '61-90d', color: '#fd7e14' },
        { key: 'mas_90', label: '+90d', color: '#dc3545' },
    ];

    let barHtml = '<div class="aging-bar d-flex mb-3">';
    tramos.forEach(tramo => {
        const valor = resumen[tramo.key]?.total || 0;
        const pct = total > 0 ? (valor / total * 100) : 0;
        if (pct > 0) {
            barHtml += `<div class="aging-segment" style="width:${pct}%;background:${tramo.color}">
                ${pct >= 10 ? `<span>${Math.round(pct)}%</span>` : ''}
            </div>`;
        }
    });
    barHtml += '</div>';

    let tableHtml = `
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tramo</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Importe</th>
                </tr>
            </thead>
            <tbody>
    `;
    tramos.forEach(tramo => {
        const info = resumen[tramo.key] || { count: 0, total: 0 };
        tableHtml += `
            <tr>
                <td><span class="badge" style="background:${tramo.color}">${tramo.label}</span></td>
                <td class="text-end">${info.count}</td>
                <td class="text-end fw-semibold">${formatCurrency(info.total)}</td>
            </tr>
        `;
    });
    tableHtml += `
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th>Total</th>
                    <th class="text-end">${data.total_registros || 0}</th>
                    <th class="text-end">${formatCurrency(total)}</th>
                </tr>
            </tfoot>
        </table>
    `;

    return barHtml + tableHtml;
}

function renderRentabilidadObras(data) {
    if (!data || (!data.top?.length && !data.bottom?.length)) {
        return '<div class="text-center text-muted py-4">Sin datos de obras</div>';
    }

    let html = '';

    // Top obras
    if (data.top?.length) {
        html += `<div class="px-3 pt-3"><h6 class="text-success mb-2"><i class="bi bi-arrow-up me-1"></i>Mejores</h6></div>`;
        html += '<table class="table table-sm table-hover table-ranking mb-0">';
        html += '<thead><tr><th>Obra</th><th class="text-end">Ingresos</th><th class="text-end">Gastos</th><th class="text-end">Margen</th></tr></thead><tbody>';
        data.top.forEach(obra => {
            const margenClass = obra.margen_porcentaje >= 0 ? 'success' : 'danger';
            html += `
                <tr>
                    <td>
                        <a href="/obras/${obra.id}" class="text-decoration-none">${obra.codigo}</a>
                        <br><small class="text-muted">${truncate(obra.nombre, 25)}</small>
                    </td>
                    <td class="text-end text-success">${formatCurrencyShort(obra.total_ingresos)}</td>
                    <td class="text-end text-danger">${formatCurrencyShort(obra.total_gastos)}</td>
                    <td class="text-end"><span class="badge bg-${margenClass}">${obra.margen_porcentaje}%</span></td>
                </tr>
            `;
        });
        html += '</tbody></table>';
    }

    // Bottom obras
    if (data.bottom?.length) {
        html += `<div class="px-3 pt-3 border-top"><h6 class="text-danger mb-2"><i class="bi bi-arrow-down me-1"></i>Peores</h6></div>`;
        html += '<table class="table table-sm table-hover table-ranking mb-0">';
        html += '<thead><tr><th>Obra</th><th class="text-end">Ingresos</th><th class="text-end">Gastos</th><th class="text-end">Margen</th></tr></thead><tbody>';
        data.bottom.forEach(obra => {
            const margenClass = obra.margen_porcentaje >= 0 ? 'success' : 'danger';
            html += `
                <tr>
                    <td>
                        <a href="/obras/${obra.id}" class="text-decoration-none">${obra.codigo}</a>
                        <br><small class="text-muted">${truncate(obra.nombre, 25)}</small>
                    </td>
                    <td class="text-end text-success">${formatCurrencyShort(obra.total_ingresos)}</td>
                    <td class="text-end text-danger">${formatCurrencyShort(obra.total_gastos)}</td>
                    <td class="text-end"><span class="badge bg-${margenClass}">${obra.margen_porcentaje}%</span></td>
                </tr>
            `;
        });
        html += '</tbody></table>';
    }

    return html;
}

function renderRentabilidadCuadrillas(cuadrillas) {
    if (!cuadrillas || cuadrillas.length === 0) {
        return '<div class="text-center text-muted py-4">Sin datos de cuadrillas</div>';
    }

    let html = '<table class="table table-sm table-hover table-ranking mb-0">';
    html += '<thead><tr><th>Cuadrilla</th><th class="text-center">Trab.</th><th class="text-end">Producción</th><th class="text-end">Coste</th><th class="text-end">Margen</th></tr></thead><tbody>';

    cuadrillas.forEach((c, index) => {
        const margenClass = c.margen_porcentaje >= 20 ? 'success' : (c.margen_porcentaje >= 0 ? 'warning' : 'danger');
        const medalEmoji = index === 0 ? ' <i class="bi bi-trophy-fill text-warning"></i>' : (index === 1 ? ' <i class="bi bi-award-fill text-secondary"></i>' : '');

        html += `
            <tr>
                <td>
                    ${c.nombre}${medalEmoji}
                    <br><small class="text-muted">${c.capataz || 'Sin capataz'}</small>
                </td>
                <td class="text-center">${c.num_trabajadores}</td>
                <td class="text-end text-success">${formatCurrencyShort(c.produccion_total)}</td>
                <td class="text-end text-danger">${formatCurrencyShort(c.coste_estimado)}</td>
                <td class="text-end"><span class="badge bg-${margenClass}">${c.margen_porcentaje}%</span></td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    return html;
}

function renderObrasRiesgo(obras) {
    if (!obras || obras.length === 0) {
        return `<div class="text-center text-muted py-4">
            <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
            <p class="mb-0">Sin obras en riesgo</p>
        </div>`;
    }

    let html = '<table class="table table-sm table-hover mb-0">';
    html += '<thead class="table-light"><tr><th>Obra</th><th>Encargado</th><th class="text-end">Coste Est.</th><th class="text-end">Gasto Real</th><th class="text-end">Desviación</th></tr></thead><tbody>';

    obras.forEach(obra => {
        html += `
            <tr class="table-danger bg-opacity-10">
                <td>
                    <a href="/obras/${obra.id}" class="text-decoration-none fw-semibold">${obra.codigo}</a>
                    <br><small class="text-muted">${truncate(obra.nombre, 30)}</small>
                </td>
                <td><small>${obra.encargado?.name || 'Sin asignar'}</small></td>
                <td class="text-end">${formatCurrencyShort(obra.coste_estimado)}</td>
                <td class="text-end text-danger fw-semibold">${formatCurrencyShort(obra.gasto_real)}</td>
                <td class="text-end">
                    <span class="badge bg-danger">+${formatCurrencyShort(obra.desviacion)}</span>
                    <br><small class="text-danger">(+${obra.desviacion_porcentaje}%)</small>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    return html;
}

function renderProduccion(data) {
    if (!data || !data.actual) {
        return '<div class="text-center text-muted py-4">Sin datos de producción</div>';
    }

    const actual = data.actual;
    const variaciones = data.variaciones || {};

    const renderVariation = (v) => {
        if (!v) return '';
        const icon = v.tipo === 'positive' ? 'bi-arrow-up' : (v.tipo === 'negative' ? 'bi-arrow-down' : 'bi-dash');
        const color = v.tipo === 'positive' ? 'success' : (v.tipo === 'negative' ? 'danger' : 'secondary');
        return `<span class="badge bg-${color} variation-badge"><i class="bi ${icon}"></i> ${v.valor}%</span>`;
    };

    return `
        <div class="text-center mb-3">
            <span class="text-muted">${data.periodo || 'Mes actual'}</span>
        </div>
        <div class="row g-3">
            <div class="col-6">
                <div class="border rounded p-3 text-center">
                    <i class="bi bi-rulers text-primary fs-3 d-block mb-2"></i>
                    <h4 class="mb-0">${formatNumber(actual.desbroce_m2)}</h4>
                    <small class="text-muted">m² Desbroce</small>
                    <div class="mt-1">${renderVariation(variaciones.desbroce)}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-3 text-center">
                    <i class="bi bi-tree text-success fs-3 d-block mb-2"></i>
                    <h4 class="mb-0">${actual.talas}</h4>
                    <small class="text-muted">Talas</small>
                    <div class="mt-1">${renderVariation(variaciones.talas)}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-3 text-center">
                    <i class="bi bi-scissors text-info fs-3 d-block mb-2"></i>
                    <h4 class="mb-0">${actual.podas}</h4>
                    <small class="text-muted">Podas</small>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-3 text-center">
                    <i class="bi bi-currency-euro text-warning fs-3 d-block mb-2"></i>
                    <h4 class="mb-0">${formatCurrencyShort(actual.importe_total)}</h4>
                    <small class="text-muted">Producido</small>
                    <div class="mt-1">${renderVariation(variaciones.importe)}</div>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <small class="text-muted">${actual.num_partes || 0} partes diarios procesados</small>
        </div>
    `;
}

// Utilidades de formato
function formatCurrency(value) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2
    }).format(value || 0);
}

function formatCurrencyShort(value) {
    const num = parseFloat(value) || 0;
    if (Math.abs(num) >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M €';
    } else if (Math.abs(num) >= 1000) {
        return (num / 1000).toFixed(1) + 'K €';
    }
    return formatCurrency(num);
}

function formatNumber(value) {
    return new Intl.NumberFormat('es-ES').format(value || 0);
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-ES');
}

function truncate(str, length) {
    if (!str) return '';
    return str.length > length ? str.substring(0, length) + '...' : str;
}
</script>
@endpush
