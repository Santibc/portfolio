<x-app-layout>
  <x-slot name="header">Dashboard de Métricas de Cotizaciones</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      {{-- Filtros de Fecha y Exportación --}}
      <div class="bg-white shadow-sm rounded-lg p-4 mb-4">
        <form method="GET" action="{{ route('dashboard.metricas') }}" class="row g-3">
          <div class="col-md-3">
            <label for="fecha_desde" class="form-label">Fecha Desde</label>
            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ $fechaDesde }}">
          </div>
          <div class="col-md-3">
            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ $fechaHasta }}">
          </div>
          <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-funnel"></i> Filtrar
            </button>
            <a href="{{ route('dashboard.metricas') }}" class="btn btn-secondary">
              <i class="bi bi-x-circle"></i> Limpiar
            </a>
          </div>
          <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
            <a href="{{ route('reportes.ventas.excel', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}" class="btn btn-success">
              <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('reportes.metricas.pdf', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}" class="btn btn-danger">
              <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
          </div>
        </form>
      </div>

      {{-- Métricas Principales --}}
      <div class="row g-3 mb-4">
        {{-- Total Cotizado --}}
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Total Cotizado</p>
                  <h4 class="mb-0">${{ number_format($valorCotizadoTotal, 0) }}</h4>
                  <small class="text-muted">{{ $totalSolicitudes }} solicitudes</small>
                </div>
                <div class="bg-secondary bg-opacity-10 p-2 rounded">
                  <i class="bi bi-currency-dollar text-secondary fs-5"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Aplicadas --}}
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Aplicadas</p>
                  <h4 class="mb-0 text-success">${{ number_format($valorAplicadas, 0) }}</h4>
                  <small class="text-muted">{{ $totalAplicadas }} ({{ number_format($porcentajeAplicadas, 1) }}%)</small>
                </div>
                <div class="bg-success bg-opacity-10 p-2 rounded">
                  <i class="bi bi-check-circle text-success fs-5"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentajeAplicadas }}%"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Contado --}}
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Contado</p>
                  <h4 class="mb-0 text-success">${{ number_format($valorContado, 0) }}</h4>
                  <small class="text-muted">{{ $totalContado }} ({{ number_format($porcentajeContado, 1) }}%)</small>
                </div>
                <div class="bg-success bg-opacity-10 p-2 rounded">
                  <i class="bi bi-cash-coin text-success fs-5"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentajeContado }}%"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Crédito --}}
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Crédito</p>
                  <h4 class="mb-0 text-primary">${{ number_format($valorCredito, 0) }}</h4>
                  <small class="text-muted">{{ $totalCredito }} ({{ number_format($porcentajeCredito, 1) }}%)</small>
                </div>
                <div class="bg-primary bg-opacity-10 p-2 rounded">
                  <i class="bi bi-credit-card text-primary fs-5"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $porcentajeCredito }}%"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Descontadas --}}
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Descontadas</p>
                  <h4 class="mb-0 text-info">${{ number_format($valorDescontadas, 0) }}</h4>
                  <small class="text-muted">{{ $totalDescontadas }} ({{ number_format($porcentajeDescontadas, 1) }}%)</small>
                </div>
                <div class="bg-info bg-opacity-10 p-2 rounded">
                  <i class="bi bi-box-arrow-down text-info fs-5"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $porcentajeDescontadas }}%"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Rechazadas --}}
        <div class="col-6 col-lg">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Rechazadas</p>
                  <h4 class="mb-0 text-danger">${{ number_format($valorRechazadas, 0) }}</h4>
                  <small class="text-muted">{{ $totalRechazadas }} ({{ number_format($porcentajeRechazadas, 1) }}%)</small>
                </div>
                <div class="bg-danger bg-opacity-10 p-2 rounded">
                  <i class="bi bi-x-circle text-danger fs-5"></i>
                </div>
              </div>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $porcentajeRechazadas }}%"></div>
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
                    <th class="text-center">Total</th>
                    <th class="text-end">Valor Total</th>
                    <th class="text-center">Aplicadas</th>
                    <th class="text-center">Contado</th>
                    <th class="text-center">Crédito</th>
                    <th class="text-center">Descontadas</th>
                    <th class="text-center">Rechazadas</th>
                    <th class="text-end">Promedio</th>
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
                        <strong>${{ number_format($asesor['valor_total'], 0) }}</strong>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-success mb-1">{{ $asesor['total_aplicadas'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_aplicadas'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-success mb-1">{{ $asesor['total_contado'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_contado'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-primary mb-1">{{ $asesor['total_credito'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_credito'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-info mb-1">{{ $asesor['total_descontadas'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_descontadas'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                          <span class="badge bg-danger mb-1">{{ $asesor['total_rechazadas'] }}</span>
                          <small class="text-muted">${{ number_format($asesor['valor_rechazadas'], 0) }}</small>
                        </div>
                      </td>
                      <td class="text-end text-muted">
                        ${{ number_format($asesor['valor_total'] / max($asesor['total_solicitudes'], 1), 0) }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot class="table-light">
                  <tr>
                    <th>Total General</th>
                    <th class="text-center">{{ $valorPorAsesor->sum('total_solicitudes') }}</th>
                    <th class="text-end">${{ number_format($valorPorAsesor->sum('valor_total'), 0) }}</th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-success mb-1">{{ $valorPorAsesor->sum('total_aplicadas') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_aplicadas'), 0) }}</small>
                      </div>
                    </th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-success mb-1">{{ $valorPorAsesor->sum('total_contado') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_contado'), 0) }}</small>
                      </div>
                    </th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-primary mb-1">{{ $valorPorAsesor->sum('total_credito') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_credito'), 0) }}</small>
                      </div>
                    </th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-info mb-1">{{ $valorPorAsesor->sum('total_descontadas') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_descontadas'), 0) }}</small>
                      </div>
                    </th>
                    <th class="text-center">
                      <div class="d-flex flex-column align-items-center">
                        <span class="badge bg-danger mb-1">{{ $valorPorAsesor->sum('total_rechazadas') }}</span>
                        <small class="text-muted">${{ number_format($valorPorAsesor->sum('valor_rechazadas'), 0) }}</small>
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
                    <span>Aplicadas</span>
                  </div>
                  <strong class="text-success">{{ $totalAplicadas }} ({{ number_format($porcentajeAplicadas, 1) }}%)</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-light rounded">
                  <div>
                    <i class="bi bi-cash-coin text-success me-2"></i>
                    <span>Contado</span>
                  </div>
                  <strong class="text-success">{{ $totalContado }} ({{ number_format($porcentajeContado, 1) }}%)</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-light rounded">
                  <div>
                    <i class="bi bi-credit-card text-primary me-2"></i>
                    <span>Crédito</span>
                  </div>
                  <strong class="text-primary">{{ $totalCredito }} ({{ number_format($porcentajeCredito, 1) }}%)</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-light rounded">
                  <div>
                    <i class="bi bi-box-arrow-down text-info me-2"></i>
                    <span>Descontadas</span>
                  </div>
                  <strong class="text-info">{{ $totalDescontadas }} ({{ number_format($porcentajeDescontadas, 1) }}%)</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                  <div>
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    <span>Rechazadas</span>
                  </div>
                  <strong class="text-danger">{{ $totalRechazadas }} ({{ number_format($porcentajeRechazadas, 1) }}%)</strong>
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
      const ctx = document.getElementById('estadoCotizacionesChart');

      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Aplicadas', 'Contado', 'Crédito', 'Descontadas', 'Rechazadas'],
          datasets: [{
            data: [{{ $totalAplicadas }}, {{ $totalContado }}, {{ $totalCredito }}, {{ $totalDescontadas }}, {{ $totalRechazadas }}],
            backgroundColor: [
              'rgba(40, 167, 69, 0.8)',   // Verde - Aplicadas
              'rgba(25, 135, 84, 0.8)',   // Verde oscuro - Contado
              'rgba(13, 110, 253, 0.8)',  // Azul - Crédito
              'rgba(13, 202, 240, 0.8)',  // Cyan - Descontadas
              'rgba(220, 53, 69, 0.8)'   // Rojo - Rechazadas
            ],
            borderColor: [
              'rgba(40, 167, 69, 1)',
              'rgba(25, 135, 84, 1)',
              'rgba(13, 110, 253, 1)',
              'rgba(13, 202, 240, 1)',
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
