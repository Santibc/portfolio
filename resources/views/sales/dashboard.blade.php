<x-app-layout>
    <x-agromarket.page-header
        title="Panel Vendedor"
        subtitle="Gestiona tus prospectos y comisiones"
    >
        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Prospecto
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
        {{-- Conversión Mensual --}}
        <div class="dashboard-col-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Conversión Mensual</h3>
                    <span class="text-muted">Prospectos nuevos vs convertidos</span>
                </div>
                <div class="card-body">
                    <x-agromarket.chart-container
                        chartId="conversionMensual"
                        :height="300"
                    />
                </div>
            </div>
        </div>

        {{-- Prospectos por Estado --}}
        <div class="dashboard-col-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Por Estado</h3>
                </div>
                <div class="card-body">
                    <x-agromarket.chart-container
                        chartId="prospectosPorEstado"
                        :height="300"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Mis Prospectos --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users"></i> Mis Prospectos Recientes</h3>
                </div>
                <div class="card-body">
                    @if(count($prospectos) > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th>Nivel de Interés</th>
                                        <th>Última Actividad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prospectos as $prospecto)
                                        @php
                                            $estadoColors = [
                                                'nuevo' => 'secondary',
                                                'contactado' => 'info',
                                                'en_seguimiento' => 'primary',
                                                'interesado' => 'success',
                                                'negociacion' => 'warning',
                                                'convertido' => 'success',
                                                'perdido' => 'danger',
                                                'inactivo' => 'secondary',
                                            ];
                                            $color = $estadoColors[$prospecto['estado']] ?? 'secondary';
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $prospecto['nombre'] }}</strong></td>
                                            <td>{{ $prospecto['email'] }}</td>
                                            <td>{{ $prospecto['telefono'] }}</td>
                                            <td>
                                                <x-agromarket.badge
                                                    :color="$color"
                                                    :text="ucfirst(str_replace('_', ' ', $prospecto['estado']))"
                                                />
                                            </td>
                                            <td>
                                                @if($prospecto['interes'] === 'alto')
                                                    <span class="interest-badge high">
                                                        <i class="fas fa-fire"></i> Alto
                                                    </span>
                                                @elseif($prospecto['interes'] === 'medio')
                                                    <span class="interest-badge medium">
                                                        <i class="fas fa-star"></i> Medio
                                                    </span>
                                                @else
                                                    <span class="interest-badge low">
                                                        <i class="fas fa-circle"></i> Bajo
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($prospecto['dias_sin_contacto'] > 7)
                                                    <span class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        {{ $prospecto['dias_sin_contacto'] }} días
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        {{ $prospecto['dias_sin_contacto'] }} días
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="#" class="btn-icon" title="Ver detalle">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="#" class="btn-icon" title="Registrar actividad">
                                                        <i class="fas fa-plus-circle"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="#" class="btn btn-outline-primary">Ver Todos los Prospectos</a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <h4>No tienes prospectos aún</h4>
                            <p class="text-muted">Comienza a agregar prospectos para hacer seguimiento</p>
                            <a href="#" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> Agregar Prospecto
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
            // Gráfico de Conversión Mensual
            const conversionCtx = document.getElementById('conversionMensual');
            if (conversionCtx) {
                new Chart(conversionCtx, {
                    type: 'line',
                    data: @json($conversion_mensual),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }

            // Gráfico de Prospectos por Estado
            const prospectosPorEstadoCtx = document.getElementById('prospectosPorEstado');
            if (prospectosPorEstadoCtx) {
                new Chart(prospectosPorEstadoCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($prospectos_por_estado['labels']),
                        datasets: [{
                            data: @json($prospectos_por_estado['data']),
                            backgroundColor: @json($prospectos_por_estado['backgroundColor']),
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

        .interest-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .interest-badge.high {
            background: #fef3c7;
            color: #d97706;
        }

        .interest-badge.medium {
            background: #dbeafe;
            color: #2563eb;
        }

        .interest-badge.low {
            background: #f3f4f6;
            color: #6b7280;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: #6b7280;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-icon:hover {
            background: #f3f4f6;
            color: var(--color-primary);
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

        .btn-outline-primary {
            background: transparent;
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
        }

        .btn-outline-primary:hover {
            background: var(--color-primary);
            color: white;
        }

        .table-responsive {
            overflow-x: auto;
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
        }
    </style>
    @endpush

    @php
    function getEstadoBadgeColor($estado) {
        return match($estado) {
            'nuevo' => 'secondary',
            'contactado' => 'info',
            'en_seguimiento' => 'warning',
            'interesado' => 'success',
            'negociacion' => 'primary',
            'convertido' => 'success',
            'perdido' => 'danger',
            'inactivo' => 'secondary',
            default => 'secondary',
        };
    }
    @endphp
</x-app-layout>
