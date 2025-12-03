<x-app-layout>
    <x-agromarket.page-header
        title="Dashboard Administrador"
        subtitle="Panel de control y estadísticas generales"
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
        {{-- Fondos por Categoría --}}
        <div class="dashboard-col-6">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Fondos por Categoría</h3>
                </div>
                <div class="card-body">
                    <x-agromarket.chart-container
                        chartId="fondosPorCategoria"
                        :height="300"
                    />
                </div>
            </div>
        </div>

        {{-- Inversiones por Mes --}}
        <div class="dashboard-col-6">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Inversiones Mensuales</h3>
                </div>
                <div class="card-body">
                    <x-agromarket.chart-container
                        chartId="inversionesPorMes"
                        :height="300"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Solicitudes Pendientes --}}
    <div class="dashboard-row mt-4">
        {{-- Retiros Pendientes --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave"></i> Retiros Pendientes
                        <x-agromarket.badge color="danger" :text="$solicitudes_pendientes['retiros']['cantidad']" />
                    </h3>
                </div>
                <div class="card-body">
                    @if($solicitudes_pendientes['retiros']['cantidad'] > 0)
                        <div class="pending-list">
                            @foreach($solicitudes_pendientes['retiros']['items'] as $retiro)
                                <div class="pending-item">
                                    <div class="pending-info">
                                        <strong>{{ $retiro->user->name }}</strong>
                                        <span class="text-muted">${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="pending-date">
                                        {{ $retiro->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($solicitudes_pendientes['retiros']['cantidad'] > 5)
                            <a href="#" class="btn-view-all">Ver todos ({{ $solicitudes_pendientes['retiros']['cantidad'] }})</a>
                        @endif
                    @else
                        <p class="text-muted text-center">No hay retiros pendientes</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- KYC Pendientes --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card"></i> KYC Pendientes
                        <x-agromarket.badge color="warning" :text="$solicitudes_pendientes['kyc']['cantidad']" />
                    </h3>
                </div>
                <div class="card-body">
                    @if($solicitudes_pendientes['kyc']['cantidad'] > 0)
                        <div class="pending-list">
                            @foreach($solicitudes_pendientes['kyc']['items'] as $kyc)
                                <div class="pending-item">
                                    <div class="pending-info">
                                        <strong>{{ $kyc->user->name }}</strong>
                                        <span class="text-muted">{{ ucfirst($kyc->tipo_documento) }}</span>
                                    </div>
                                    <div class="pending-date">
                                        {{ $kyc->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($solicitudes_pendientes['kyc']['cantidad'] > 5)
                            <a href="#" class="btn-view-all">Ver todos ({{ $solicitudes_pendientes['kyc']['cantidad'] }})</a>
                        @endif
                    @else
                        <p class="text-muted text-center">No hay documentos KYC pendientes</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Proyectos Pendientes --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-seedling"></i> Proyectos en Revisión
                        <x-agromarket.badge color="info" :text="$solicitudes_pendientes['proyectos']['cantidad']" />
                    </h3>
                </div>
                <div class="card-body">
                    @if($solicitudes_pendientes['proyectos']['cantidad'] > 0)
                        <div class="pending-list">
                            @foreach($solicitudes_pendientes['proyectos']['items'] as $proyecto)
                                <div class="pending-item">
                                    <div class="pending-info">
                                        <strong>{{ Str::limit($proyecto->nombre, 30) }}</strong>
                                        <span class="text-muted">{{ $proyecto->categoria->nombre }}</span>
                                    </div>
                                    <div class="pending-date">
                                        {{ $proyecto->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($solicitudes_pendientes['proyectos']['cantidad'] > 5)
                            <a href="#" class="btn-view-all">Ver todos ({{ $solicitudes_pendientes['proyectos']['cantidad'] }})</a>
                        @endif
                    @else
                        <p class="text-muted text-center">No hay proyectos pendientes</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Proyectos por Estado --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tasks"></i> Proyectos por Estado</h3>
                </div>
                <div class="card-body">
                    <div class="projects-status-grid">
                        @foreach($proyectos_por_estado as $estado)
                            <div class="status-item">
                                <div class="status-label">{{ $estado['estado'] }}</div>
                                <div class="status-value">{{ $estado['total'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para gráficos --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Fondos por Categoría (Pie Chart)
            const fondosCtx = document.getElementById('fondosPorCategoria');
            if (fondosCtx) {
                new Chart(fondosCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($fondos_por_categoria['labels']),
                        datasets: [{
                            data: @json($fondos_por_categoria['data']),
                            backgroundColor: @json($fondos_por_categoria['backgroundColor']),
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

            // Gráfico de Inversiones por Mes (Line Chart)
            const inversionesCtx = document.getElementById('inversionesPorMes');
            if (inversionesCtx) {
                new Chart(inversionesCtx, {
                    type: 'line',
                    data: @json($inversiones_por_mes),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString('es-CO');
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false,
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 0) {
                                            label += '$' + context.parsed.y.toLocaleString('es-CO');
                                        } else {
                                            label += context.parsed.y;
                                        }
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
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .dashboard-col-4 {
            flex: 1;
            min-width: 300px;
        }

        .dashboard-col-6 {
            flex: 1;
            min-width: 400px;
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

        .pending-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .pending-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .pending-item:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }

        .pending-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .pending-info strong {
            color: #1f2937;
            font-size: 0.9rem;
        }

        .pending-info .text-muted {
            color: #6b7280;
            font-size: 0.85rem;
        }

        .pending-date {
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .btn-view-all {
            display: block;
            text-align: center;
            padding: 0.75rem;
            margin-top: 1rem;
            color: var(--color-primary);
            font-weight: 500;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .btn-view-all:hover {
            background: #f3f4f6;
        }

        .projects-status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .status-item {
            text-align: center;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .status-item:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
        }

        .status-label {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .status-value {
            color: #1f2937;
            font-size: 1.75rem;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .dashboard-row {
                flex-direction: column;
            }

            .dashboard-col-4,
            .dashboard-col-6 {
                min-width: 100%;
            }
        }
    </style>
    @endpush
</x-app-layout>
