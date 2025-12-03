<x-app-layout>
    <x-agromarket.page-header
        title="Panel Agricultor"
        subtitle="Gestiona tus proyectos agrícolas"
    >
        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crear Proyecto
        </a>
    </x-agromarket.page-header>

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
        {{-- Recaudación Mensual --}}
        <div class="dashboard-col-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-area"></i> Recaudación Mensual</h3>
                    <span class="text-muted">Inversiones recibidas en tus proyectos</span>
                </div>
                <div class="card-body">
                    <x-agromarket.chart-container
                        chartId="recaudacionMensual"
                        :height="300"
                    />
                </div>
            </div>
        </div>

        {{-- Proyectos por Estado --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tasks"></i> Estado de Proyectos</h3>
                </div>
                <div class="card-body">
                    <div class="status-list">
                        @foreach($proyectos_por_estado as $estado)
                            <div class="status-item-inline">
                                <span class="status-label">{{ $estado['estado'] }}</span>
                                <x-agromarket.badge
                                    color="primary"
                                    :text="$estado['total']"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mis Proyectos --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-seedling"></i> Mis Proyectos</h3>
                </div>
                <div class="card-body">
                    @if(count($proyectos) > 0)
                        <div class="projects-grid">
                            @foreach($proyectos as $proyecto)
                                <div class="project-card">
                                    <div class="project-header">
                                        <h4 class="project-title">{{ $proyecto['nombre'] }}</h4>
                                        @php
                                            $estadoColors = [
                                                'borrador' => 'secondary',
                                                'en_revision' => 'warning',
                                                'aprobado' => 'info',
                                                'en_recaudacion' => 'primary',
                                                'fondeado' => 'success',
                                                'en_ejecucion' => 'success',
                                                'finalizado' => 'dark',
                                                'cancelado' => 'danger',
                                                'rechazado' => 'danger',
                                            ];
                                            $color = $estadoColors[$proyecto['estado']] ?? 'secondary';
                                        @endphp
                                        <x-agromarket.badge
                                            :color="$color"
                                            :text="ucfirst(str_replace('_', ' ', $proyecto['estado']))"
                                        />
                                    </div>

                                    <div class="project-category">
                                        <i class="fas fa-tag"></i> {{ $proyecto['categoria'] }}
                                    </div>

                                    <div class="project-stats">
                                        <div class="project-stat">
                                            <span class="stat-label">Objetivo</span>
                                            <span class="stat-value">${{ number_format($proyecto['monto_objetivo'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="project-stat">
                                            <span class="stat-label">Recaudado</span>
                                            <span class="stat-value text-success">${{ number_format($proyecto['monto_recaudado'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <div class="project-progress">
                                        <x-agromarket.progress-bar
                                            :percentage="$proyecto['progreso']"
                                            color="success"
                                        />
                                    </div>

                                    <div class="project-dates">
                                        @if($proyecto['fecha_inicio'] && $proyecto['fecha_fin'])
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i>
                                                {{ \Carbon\Carbon::parse($proyecto['fecha_inicio'])->format('d/m/Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($proyecto['fecha_fin'])->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </div>

                                    <div class="project-actions">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        @if(in_array($proyecto['estado'], ['borrador', 'rechazado']))
                                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        @endif
                                        @if($proyecto['estado'] === 'aprobado')
                                            <a href="#" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-rocket"></i> Publicar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                            <h4>No tienes proyectos aún</h4>
                            <p class="text-muted">Crea tu primer proyecto agrícola</p>
                            <a href="#" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> Crear Proyecto
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
            // Gráfico de Recaudación Mensual
            const recaudacionCtx = document.getElementById('recaudacionMensual');
            if (recaudacionCtx) {
                new Chart(recaudacionCtx, {
                    type: 'line',
                    data: @json($recaudacion_mensual),
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
                                        return 'Recaudación: $' + context.parsed.y.toLocaleString('es-CO');
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

        .status-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .status-item-inline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 6px;
        }

        .status-label {
            font-weight: 500;
            color: #1f2937;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .project-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.2s;
        }

        .project-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .project-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            flex: 1;
        }

        .project-category {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .project-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .project-stat {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .project-progress {
            margin-bottom: 1rem;
        }

        .project-dates {
            margin-bottom: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .project-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--color-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--color-primary-dark);
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
        }

        .btn-outline-primary {
            background: transparent;
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
        }

        .btn-outline-primary:hover {
            background: var(--color-primary);
            color: white;
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1px solid #6b7280;
            color: #6b7280;
        }

        .btn-outline-secondary:hover {
            background: #6b7280;
            color: white;
        }

        .btn-outline-success {
            background: transparent;
            border: 1px solid #28a745;
            color: #28a745;
        }

        .btn-outline-success:hover {
            background: #28a745;
            color: white;
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

            .projects-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush
</x-app-layout>
