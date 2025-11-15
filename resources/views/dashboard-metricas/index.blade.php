<x-app-layout>
  <x-slot name="header">Dashboard de Métricas de Cotizaciones</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      {{-- Filtros de Fecha --}}
      <div class="bg-white shadow-sm rounded-lg p-4 mb-4">
        <form method="GET" action="{{ route('dashboard.metricas') }}" class="row g-3">
          <div class="col-md-4">
            <label for="fecha_desde" class="form-label">Fecha Desde</label>
            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ $fechaDesde }}">
          </div>
          <div class="col-md-4">
            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ $fechaHasta }}">
          </div>
          <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-funnel"></i> Filtrar
            </button>
            <a href="{{ route('dashboard.metricas') }}" class="btn btn-secondary">
              <i class="bi bi-x-circle"></i> Limpiar
            </a>
          </div>
        </form>
      </div>

      {{-- Métricas Principales --}}
      <div class="row g-4 mb-4">
        {{-- Total Cotizado --}}
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Total Cotizado</p>
                  <h3 class="mb-0">${{ number_format($valorCotizadoTotal, 2) }}</h3>
                  <small class="text-muted">{{ $totalSolicitudes }} solicitudes</small>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                  <i class="bi bi-currency-dollar text-primary fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Cotizaciones Aprobadas --}}
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Aprobadas</p>
                  <h3 class="mb-0 text-success">${{ number_format($valorAprobado, 2) }}</h3>
                  <small class="text-muted">
                    {{ $totalAprobadas }} solicitudes ({{ number_format($porcentajeAprobadas, 1) }}%)
                  </small>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                  <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 5px;">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: {{ $porcentajeAprobadas }}%"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Cotizaciones Pendientes --}}
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Pendientes</p>
                  <h3 class="mb-0 text-warning">${{ number_format($valorPendiente, 2) }}</h3>
                  <small class="text-muted">
                    {{ $totalPendientes }} solicitudes ({{ number_format($porcentajePendientes, 1) }}%)
                  </small>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                  <i class="bi bi-clock-history text-warning fs-4"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 5px;">
                <div class="progress-bar bg-warning" role="progressbar"
                     style="width: {{ $porcentajePendientes }}%"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Cotizaciones Perdidas --}}
        <div class="col-md-6 col-lg-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Perdidas</p>
                  <h3 class="mb-0 text-danger">${{ number_format($valorPerdido, 2) }}</h3>
                  <small class="text-muted">
                    {{ $totalPerdidas }} solicitudes ({{ number_format($porcentajePerdidas, 1) }}%)
                  </small>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                  <i class="bi bi-x-circle text-danger fs-4"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 5px;">
                <div class="progress-bar bg-danger" role="progressbar"
                     style="width: {{ $porcentajePerdidas }}%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Tabla de Valor por Asesor --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-4">
          <h5 class="mb-3">
            <i class="bi bi-person-badge"></i> Valor Cotizado por Asesor Comercial
          </h5>

          @if($valorPorAsesor->isEmpty())
            <div class="alert alert-info">
              <i class="bi bi-info-circle"></i> No hay datos de cotizaciones con asesores asignados en el período seleccionado.
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Asesor Comercial</th>
                    <th class="text-center">Total Solicitudes</th>
                    <th class="text-end">Valor Total Cotizado</th>
                    <th class="text-center">Pendientes</th>
                    <th class="text-center">Rechazadas</th>
                    <th class="text-center">Aprobadas</th>
                    <th class="text-end">Promedio por Solicitud</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($valorPorAsesor as $asesor)
                    <tr>
                      <td>
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        <strong>{{ $asesor['asesor'] }}</strong>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-secondary">{{ $asesor['total_solicitudes'] }}</span>
                      </td>
                      <td class="text-end">
                        <strong>${{ number_format($asesor['valor_total'], 2) }}</strong>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-warning text-dark mb-1">{{ $asesor['total_pendientes'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_pendientes'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-danger mb-1">{{ $asesor['total_rechazadas'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_rechazadas'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-success mb-1">{{ $asesor['total_aprobadas'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_aprobadas'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-end text-muted">
                        ${{ number_format($asesor['valor_total'] / $asesor['total_solicitudes'], 2) }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot class="table-light">
                  <tr>
                    <th>Total General</th>
                    <th class="text-center">{{ $valorPorAsesor->sum('total_solicitudes') }}</th>
                    <th class="text-end">${{ number_format($valorPorAsesor->sum('valor_total'), 2) }}</th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-warning text-dark mb-1">{{ $valorPorAsesor->sum('total_pendientes') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_pendientes'), 0) }}</small>
                      </div>
                    </th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-danger mb-1">{{ $valorPorAsesor->sum('total_rechazadas') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_rechazadas'), 0) }}</small>
                      </div>
                    </th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-success mb-1">{{ $valorPorAsesor->sum('total_aprobadas') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_aprobadas'), 0) }}</small>
                      </div>
                    </th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Gráfico de Estado de Cotizaciones --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden mt-4">
        <div class="p-4">
          <h5 class="mb-3">
            <i class="bi bi-pie-chart"></i> Distribución de Cotizaciones por Estado
          </h5>
          <div class="row">
            <div class="col-md-6">
              <canvas id="estadoCotizacionesChart" style="max-height: 300px;"></canvas>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="w-100">
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-light rounded">
                  <div>
                    <i class="bi bi-check-circle text-success me-2"></i>
                    <span>Aprobadas</span>
                  </div>
                  <strong class="text-success">{{ $totalAprobadas }} ({{ number_format($porcentajeAprobadas, 1) }}%)</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-light rounded">
                  <div>
                    <i class="bi bi-clock-history text-warning me-2"></i>
                    <span>Pendientes</span>
                  </div>
                  <strong class="text-warning">{{ $totalPendientes }} ({{ number_format($porcentajePendientes, 1) }}%)</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                  <div>
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    <span>Perdidas</span>
                  </div>
                  <strong class="text-danger">{{ $totalPerdidas }} ({{ number_format($porcentajePerdidas, 1) }}%)</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Gráfico de pastel
      const ctx = document.getElementById('estadoCotizacionesChart');

      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Aprobadas', 'Pendientes', 'Perdidas'],
          datasets: [{
            data: [{{ $totalAprobadas }}, {{ $totalPendientes }}, {{ $totalPerdidas }}],
            backgroundColor: [
              'rgba(40, 167, 69, 0.8)',   // Verde
              'rgba(255, 193, 7, 0.8)',   // Amarillo
              'rgba(220, 53, 69, 0.8)'    // Rojo
            ],
            borderColor: [
              'rgba(40, 167, 69, 1)',
              'rgba(255, 193, 7, 1)',
              'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 20,
                font: {
                  size: 12
                }
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.parsed || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                  return label + ': ' + value + ' (' + percentage + '%)';
                }
              }
            }
          }
        }
      });
    });
  </script>
  @endpush
</x-app-layout>
