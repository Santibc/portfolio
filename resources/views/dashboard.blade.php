<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="container-fluid py-4">
        @if(Auth::user()->hasRole('admin') && isset($metricas))
            {{-- Filtros de período --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-2">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="bi bi-graph-up me-2"></i>
                                        Métricas - {{ $metricas['mes_actual'] ?? now()->isoFormat('MMMM YYYY') }}
                                    </h5>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dashboard', ['periodo' => 'hoy']) }}"
                                           class="btn btn-sm {{ $periodo === 'hoy' ? 'btn-primary' : 'btn-outline-primary' }}">
                                            Hoy
                                        </a>
                                        <a href="{{ route('dashboard', ['periodo' => 'semana']) }}"
                                           class="btn btn-sm {{ $periodo === 'semana' ? 'btn-primary' : 'btn-outline-primary' }}">
                                            Semana
                                        </a>
                                        <a href="{{ route('dashboard', ['periodo' => 'mes']) }}"
                                           class="btn btn-sm {{ $periodo === 'mes' ? 'btn-primary' : 'btn-outline-primary' }}">
                                            Mes
                                        </a>
                                        <a href="{{ route('dashboard', ['periodo' => 'año']) }}"
                                           class="btn btn-sm {{ $periodo === 'año' ? 'btn-primary' : 'btn-outline-primary' }}">
                                            Año
                                        </a>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('reportes.ventas.excel') }}" class="btn btn-sm btn-success" title="Exportar Excel">
                                            <i class="bi bi-file-earmark-excel"></i> Excel
                                        </a>
                                        <a href="{{ route('reportes.metricas.pdf') }}" class="btn btn-sm btn-danger" title="Exportar PDF">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cards de métricas principales --}}
            <div class="row g-3 mb-4">
                {{-- Total Ventas --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Total Ventas"
                        :value="'$' . number_format($metricas['resumen']['total_ventas'] ?? 0, 0, ',', '.')"
                        icon="bi-currency-dollar"
                        color="success"
                        :subtitle="($metricas['resumen']['total_transacciones'] ?? 0) . ' transacciones'"
                        :trend="$metricas['comparativa']['variacion']['ventas']['tendencia'] ?? null"
                        :trendValue="($metricas['comparativa']['variacion']['ventas']['valor'] ?? 0) . '%'"
                    />
                </div>

                {{-- Aplicadas --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Aplicadas"
                        :value="($metricas['cotizaciones']['aplicadas']['cantidad'] ?? 0)"
                        icon="bi-check-circle"
                        color="success"
                        :subtitle="'$' . number_format($metricas['cotizaciones']['aplicadas']['monto'] ?? 0, 0, ',', '.')"
                    />
                </div>

                {{-- Contado --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Contado"
                        :value="($metricas['cotizaciones']['contado']['cantidad'] ?? 0)"
                        icon="bi-cash-stack"
                        color="success"
                        :subtitle="'$' . number_format($metricas['cotizaciones']['contado']['monto'] ?? 0, 0, ',', '.')"
                    />
                </div>

                {{-- Crédito --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Crédito"
                        :value="($metricas['cotizaciones']['credito']['cantidad'] ?? 0)"
                        icon="bi-credit-card"
                        color="primary"
                        :subtitle="'$' . number_format($metricas['cotizaciones']['credito']['monto'] ?? 0, 0, ',', '.')"
                    />
                </div>
            </div>

            <div class="row g-3 mb-4">
                {{-- Total Pagadas --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Total Pagadas"
                        :value="($metricas['cotizaciones']['pagadas']['cantidad'] ?? 0)"
                        icon="bi-wallet2"
                        color="purple"
                        :subtitle="'$' . number_format($metricas['cotizaciones']['pagadas']['monto'] ?? 0, 0, ',', '.')"
                    />
                </div>

                {{-- Despachadas --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Despachadas"
                        :value="($metricas['cotizaciones']['despachadas']['cantidad'] ?? 0)"
                        icon="bi-truck"
                        color="info"
                        :subtitle="'$' . number_format($metricas['cotizaciones']['despachadas']['monto'] ?? 0, 0, ',', '.')"
                    />
                </div>

                {{-- Rechazadas --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Rechazadas"
                        :value="($metricas['cotizaciones']['rechazadas']['cantidad'] ?? 0)"
                        icon="bi-x-circle"
                        color="danger"
                        :subtitle="'$' . number_format($metricas['cotizaciones']['rechazadas']['monto'] ?? 0, 0, ',', '.')"
                    />
                </div>

                {{-- Tasa de Conversión --}}
                <div class="col-md-6 col-lg-3">
                    <x-card-metric
                        title="Tasa de Conversión"
                        :value="($metricas['cotizaciones']['tasa_conversion'] ?? 0) . '%'"
                        icon="bi-graph-up-arrow"
                        color="lilac"
                        subtitle="Aplicadas / Total"
                    />
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="row g-3 mb-4">
                {{-- Gráfico de tendencia diaria --}}
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Ventas Últimos 30 Días</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="chartTendencia" height="100"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Gráfico de distribución por estado --}}
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribución por Estado</h6>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="chartEstados" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tablas --}}
            <div class="row g-3 mb-4">
                {{-- Top vendedores --}}
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top 5 Vendedores</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Vendedor</th>
                                            <th class="text-center">Cotiz.</th>
                                            <th class="text-center">Aplicadas</th>
                                            <th class="text-center">Pagadas</th>
                                            <th class="text-center">Descontadas</th>
                                            <th class="text-center">Conv.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($metricas['top_vendedores'] ?? [] as $index => $vendedor)
                                            <tr>
                                                <td>
                                                    @if($index === 0)
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-trophy-fill"></i>
                                                        </span>
                                                    @else
                                                        {{ $index + 1 }}
                                                    @endif
                                                </td>
                                                <td>{{ $vendedor['vendedor'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $vendedor['total_cotizaciones'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success">{{ $vendedor['aplicadas'] }}</span>
                                                    <br><small class="text-muted">${{ number_format($vendedor['monto_aplicadas'], 0, ',', '.') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $vendedor['pagadas'] }}</span>
                                                    <br><small class="text-muted">${{ number_format($vendedor['monto_pagadas'], 0, ',', '.') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge" style="background-color: #FF84D5;">{{ $vendedor['descontadas'] }}</span>
                                                    <br><small class="text-muted">${{ number_format($vendedor['monto_descontadas'], 0, ',', '.') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $vendedor['tasa_conversion'] >= 50 ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ $vendedor['tasa_conversion'] }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">
                                                    Sin datos para este periodo
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top productos --}}
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Top 5 Productos</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th class="text-center">Cant.</th>
                                            <th class="text-end">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($metricas['top_productos'] ?? [] as $index => $producto)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $producto['referencia'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($producto['nombre'], 30) }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">{{ $producto['cantidad_vendida'] }}</span>
                                                </td>
                                                <td class="text-end">${{ number_format($producto['monto_total'], 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">
                                                    Sin datos para este período
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cotizaciones del período --}}
            @php
                $periodoLabels = [
                    'hoy' => 'Hoy',
                    'semana' => 'Esta Semana',
                    'mes' => 'Este Mes',
                    'año' => 'Este Año',
                ];
                $periodoLabel = $periodoLabels[$periodo] ?? 'Este Mes';
            @endphp
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-file-earmark-text me-2"></i>Cotizaciones - {{ $periodoLabel }}
                                <span class="badge bg-secondary ms-2">{{ $cotizacionesPaginadas->total() }}</span>
                            </h6>
                            <a href="{{ route('solicitudes') }}" class="btn btn-sm btn-outline-primary">
                                Ver todas <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nº</th>
                                            <th>Cliente</th>
                                            <th>Vendedor</th>
                                            <th class="text-end">Monto</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Pago</th>
                                            <th class="text-center">Descontada</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cotizacionesPaginadas as $cotizacion)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('solicitudes.detalle', $cotizacion->id) }}" class="text-decoration-none">
                                                        {{ $cotizacion->numero_solicitud }}
                                                    </a>
                                                </td>
                                                <td>{{ $cotizacion->cliente->nombre_contacto ?? 'N/A' }}</td>
                                                <td>{{ $cotizacion->createdBy->name ?? 'N/A' }}</td>
                                                <td class="text-end">{{ $cotizacion->monto_total_formateado }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $cotizacion->color_estado }}">
                                                        {{ ucfirst($cotizacion->estado) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($cotizacion->estado === 'aplicada' && $cotizacion->estado_pago === 'pagado')
                                                        @if($cotizacion->forma_pago_factura && str_contains($cotizacion->forma_pago_factura, 'Crédito'))
                                                            <span class="badge bg-primary">Crédito</span>
                                                        @else
                                                            <span class="badge bg-success">Contado</span>
                                                        @endif
                                                    @elseif($cotizacion->estado_pago === 'parcial')
                                                        <span class="badge bg-info">Parcial</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($cotizacion->stock_descontado)
                                                        <span class="badge" style="background-color:#FF84D5;color:#fff;">
                                                            <i class="bi bi-check-circle me-1"></i>Sí
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted" title="{{ $cotizacion->created_at->format('d/m/Y H:i') }}">
                                                        {{ $cotizacion->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-3">
                                                    No hay cotizaciones en este período
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($cotizacionesPaginadas->hasPages())
                            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Mostrando {{ $cotizacionesPaginadas->firstItem() }} - {{ $cotizacionesPaginadas->lastItem() }}
                                    de {{ $cotizacionesPaginadas->total() }} cotizaciones
                                </small>
                                <div>
                                    {{ $cotizacionesPaginadas->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @else
            {{-- Vista para vendedores (no admin) --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-person-circle display-1 text-muted mb-3"></i>
                            <h4>Bienvenido, {{ Auth::user()->name }}</h4>
                            <p class="text-muted">Accede a las diferentes secciones desde el menú lateral</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(Auth::user()->hasRole('admin') && isset($metricas))
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos de tendencia
            const tendenciaData = @json($metricas['tendencia'] ?? []);

            // Gráfico de tendencia diaria
            const ctxTendencia = document.getElementById('chartTendencia');
            if (ctxTendencia && tendenciaData.length > 0) {
                new Chart(ctxTendencia, {
                    type: 'bar',
                    data: {
                        labels: tendenciaData.map(d => d.fecha_corta),
                        datasets: [{
                            label: 'Ventas',
                            data: tendenciaData.map(d => d.monto),
                            backgroundColor: 'rgba(255, 132, 213, 0.6)',
                            borderColor: '#FF84D5',
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '$ ' + context.raw.toLocaleString('es-CO');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$ ' + value.toLocaleString('es-CO');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Datos de estados
            const estadosData = {
                contado: {{ $metricas['cotizaciones']['contado']['cantidad'] ?? 0 }},
                credito: {{ $metricas['cotizaciones']['credito']['cantidad'] ?? 0 }},
                despachadas: {{ $metricas['cotizaciones']['despachadas']['cantidad'] ?? 0 }},
                rechazadas: {{ $metricas['cotizaciones']['rechazadas']['cantidad'] ?? 0 }}
            };

            // Gráfico de distribución por estado
            const ctxEstados = document.getElementById('chartEstados');
            if (ctxEstados) {
                new Chart(ctxEstados, {
                    type: 'doughnut',
                    data: {
                        labels: ['Contado', 'Crédito', 'Despachadas', 'Rechazadas'],
                        datasets: [{
                            data: [estadosData.contado, estadosData.credito, estadosData.despachadas, estadosData.rechazadas],
                            backgroundColor: [
                                '#28a745', // success - contado
                                '#0d6efd', // primary - crédito
                                '#0dcaf0', // info - despachadas
                                '#dc3545'  // danger - rechazadas
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        });
    </script>
    @endpush
    @endif
</x-app-layout>
