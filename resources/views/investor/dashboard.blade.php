<x-app-layout>
    <x-agromarket.page-header
        title="Mi Portafolio"
        subtitle="Resumen de tus inversiones y retornos"
    />

    {{-- Estadísticas Principales --}}
    <div class="stats-grid">
        @foreach($estadisticas as $stat)
            <x-agromarket.stat-card
                :title="$stat['titulo']"
                :value="$stat['valor']"
                :icon="$stat['icono']"
                :color="$stat['color']"
                :description="$stat['descripcion']"
            />
        @endforeach
    </div>

    {{-- Gráficos --}}
    <div class="dashboard-row mt-4">
        {{-- Rendimiento del Portafolio --}}
        <div class="dashboard-col-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-area"></i> Rendimiento del Portafolio</h3>
                    <span class="text-muted">Dividendos recibidos en los últimos 12 meses</span>
                </div>
                <div class="card-body">
                    <x-agromarket.chart-container
                        chartId="rendimientoPortafolio"
                        :height="300"
                    />
                </div>
            </div>
        </div>

        {{-- Distribución de Inversiones --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Distribución</h3>
                </div>
                <div class="card-body">
                    <x-agromarket.chart-container
                        chartId="distribucionInversiones"
                        :height="300"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Próximos Dividendos --}}
    @if(count($proximos_dividendos) > 0)
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Próximos Dividendos</h3>
                </div>
                <div class="card-body">
                    <div class="dividends-timeline">
                        @foreach($proximos_dividendos as $dividendo)
                            <div class="dividend-item">
                                <div class="dividend-date">
                                    <div class="date-day">{{ \Carbon\Carbon::parse($dividendo['fecha_pago'])->format('d') }}</div>
                                    <div class="date-month">{{ \Carbon\Carbon::parse($dividendo['fecha_pago'])->locale('es')->isoFormat('MMM') }}</div>
                                </div>
                                <div class="dividend-info">
                                    <div class="dividend-project">{{ $dividendo['proyecto'] }}</div>
                                    <div class="dividend-amount">${{ number_format($dividendo['monto'], 0, ',', '.') }}</div>
                                </div>
                                <div class="dividend-countdown">
                                    @if($dividendo['dias_restantes'] >= 0)
                                        <x-agromarket.badge
                                            color="success"
                                            text="En {{ $dividendo['dias_restantes'] }} días"
                                        />
                                    @else
                                        <x-agromarket.badge
                                            color="warning"
                                            text="Pendiente de pago"
                                        />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Inversiones Activas --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-briefcase"></i> Mis Inversiones Activas</h3>
                </div>
                <div class="card-body">
                    @if(count($inversiones_activas) > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Categoría</th>
                                        <th>Monto Invertido</th>
                                        <th>Fecha</th>
                                        <th>Retorno Esperado</th>
                                        <th>Progreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inversiones_activas as $inv)
                                        <tr>
                                            <td><strong>{{ $inv['proyecto'] }}</strong></td>
                                            <td>
                                                <x-agromarket.badge
                                                    color="primary"
                                                    text="{{ $inv['categoria'] }}"
                                                />
                                            </td>
                                            <td>${{ number_format($inv['monto'], 0, ',', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($inv['fecha'])->format('d/m/Y') }}</td>
                                            <td class="text-success">
                                                <strong>${{ number_format($inv['retorno_esperado'], 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                <x-agromarket.progress-bar
                                                    :percentage="$inv['progreso']"
                                                    color="success"
                                                />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                            <h4>No tienes inversiones activas</h4>
                            <p class="text-muted">Comienza a invertir en proyectos agrícolas</p>
                            <a href="#" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> Explorar Proyectos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para gráficos --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Rendimiento del Portafolio
            const rendimientoCtx = document.getElementById('rendimientoPortafolio');
            if (rendimientoCtx) {
                new Chart(rendimientoCtx, {
                    type: 'line',
                    data: @json($rendimiento_portafolio),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString('es-CO');
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Dividendos: $' + context.parsed.y.toLocaleString('es-CO');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de Distribución de Inversiones
            const distribucionCtx = document.getElementById('distribucionInversiones');
            if (distribucionCtx) {
                new Chart(distribucionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($distribucion_inversiones['labels']),
                        datasets: [{
                            data: @json($distribucion_inversiones['data']),
                            backgroundColor: @json($distribucion_inversiones['backgroundColor']),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += '$' + context.parsed.toLocaleString('es-CO');
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .dashboard-col-4 {
            flex: 1;
            min-width: 300px;
            max-width: 33.333%;
        }

        .dashboard-col-8 {
            flex: 2;
            min-width: 500px;
        }

        .dashboard-col-12 {
            flex: 1;
            width: 100%;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
            height: 100%;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .dividends-timeline {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .dividend-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .dividend-item:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }

        .dividend-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: var(--color-primary);
            color: white;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .date-day {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .date-month {
            font-size: 0.75rem;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        .dividend-info {
            flex: 1;
        }

        .dividend-project {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .dividend-amount {
            color: #28a745;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .dividend-countdown {
            flex-shrink: 0;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.85rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table tr:hover {
            background: #f9fafb;
        }

        @media (max-width: 1024px) {
            .dashboard-col-4,
            .dashboard-col-8 {
                max-width: 100%;
                min-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .dashboard-row {
                flex-direction: column;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
    @endpush
</x-app-layout>
