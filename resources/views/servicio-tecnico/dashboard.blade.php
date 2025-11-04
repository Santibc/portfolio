<x-app-layout>
    <x-slot name="header">Servicio Técnico - Dashboard</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <p class="text-muted">Monitoreo y gestión de órdenes de servicio</p>
            </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Órdenes Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ordenes_pendientes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Recibidas Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ordenes_hoy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Urgentes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ordenes_urgentes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Retrasadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ordenes_retrasadas'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted">Clientes Activos</h6>
                    <h3>{{ $stats['clientes_total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted">Equipos Registrados</h6>
                    <h3>{{ $stats['equipos_total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted">Técnicos Activos</h6>
                    <h3>{{ $stats['tecnicos_activos'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted">Repuestos Bajo Stock</h6>
                    <h3 class="text-danger">{{ $stats['repuestos_bajo_stock'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Gráfico de órdenes por estado --}}
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Órdenes por Estado</h6>
                </div>
                <div class="card-body" style="height: 320px;">
                    <canvas id="ordenesEstadoChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Carga de trabajo por técnico --}}
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Carga de Trabajo - Técnicos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Técnico</th>
                                    <th>Especialidad</th>
                                    <th class="text-center">Órdenes Activas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tecnicos as $tecnico)
                                    <tr>
                                        <td>{{ $tecnico->nombre_completo }}</td>
                                        <td><span class="badge bg-secondary">{{ $tecnico->especialidad }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $tecnico->ordenes_activas > 5 ? 'danger' : 'success' }}">
                                                {{ $tecnico->ordenes_activas }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay técnicos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimas órdenes --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Últimas Órdenes de Servicio</h6>
                    <a href="{{ route('st.ordenes.index') }}" class="btn btn-sm btn-light">Ver Todas</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>N° Orden</th>
                                    <th>Cliente</th>
                                    <th>Equipo</th>
                                    <th>Técnico</th>
                                    <th>Tipo</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimasOrdenes as $orden)
                                    <tr>
                                        <td><strong>{{ $orden->numero_orden }}</strong></td>
                                        <td>{{ $orden->cliente->nombre_completo }}</td>
                                        <td>{{ $orden->equipo ? $orden->equipo->marca . ' ' . $orden->equipo->modelo : '-' }}</td>
                                        <td>{{ $orden->tecnico ? $orden->tecnico->nombre_completo : 'Sin asignar' }}</td>
                                        <td>{{ $orden->tipo_servicio }}</td>
                                        <td>
                                            @php
                                                $prioridadBadge = [
                                                    'baja' => 'secondary',
                                                    'media' => 'info',
                                                    'alta' => 'warning',
                                                    'urgente' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $prioridadBadge[$orden->prioridad] }}">
                                                {{ ucfirst($orden->prioridad) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $estadoBadge = [
                                                    'recibida' => 'secondary',
                                                    'asignada' => 'info',
                                                    'en_proceso' => 'primary',
                                                    'pendiente_repuestos' => 'warning',
                                                    'completada' => 'success',
                                                    'entregada' => 'success',
                                                    'cancelada' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $estadoBadge[$orden->estado] }}">
                                                {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                            </span>
                                        </td>
                                        <td>{{ $orden->fecha_recepcion->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('st.ordenes.show', $orden) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No hay órdenes registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

@push('styles')
<style>
.border-left-primary { border-left: 4px solid #4e73df; }
.border-left-success { border-left: 4px solid #1cc88a; }
.border-left-danger { border-left: 4px solid #e74a3b; }
.border-left-warning { border-left: 4px solid #f6c23e; }
.hover-card:hover { transform: translateY(-5px); transition: all 0.3s; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de órdenes por estado
    const ctx = document.getElementById('ordenesEstadoChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($ordenesPorEstado->keys()),
                datasets: [{
                    data: @json($ordenesPorEstado->values()),
                    backgroundColor: [
                        '#6c757d',
                        '#17a2b8',
                        '#007bff',
                        '#ffc107',
                        '#28a745'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush
</x-app-layout>
