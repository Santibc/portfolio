<x-app-layout>
  <x-slot name="header">Gráficas — Métricas de Productos</x-slot>

  <div class="py-4">
    <div class="container-fluid px-3 px-lg-4">

      {{-- Filtros --}}
      <form method="GET" action="{{ route('metricas.productos.graficas') }}" class="card mb-4">
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-md-2">
              <label class="form-label small fw-semibold mb-1">Desde</label>
              <input type="date" name="fecha_inicio" value="{{ $filtros['fecha_inicio'] }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold mb-1">Hasta</label>
              <input type="date" name="fecha_fin" value="{{ $filtros['fecha_fin'] }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold mb-1">Fuente</label>
              <select name="fuente" class="form-select form-select-sm">
                <option value="ambas"        @selected($filtros['fuente']==='ambas')>PdV + Cotizaciones</option>
                <option value="pdv"          @selected($filtros['fuente']==='pdv')>Solo PdV</option>
                <option value="cotizaciones" @selected($filtros['fuente']==='cotizaciones')>Solo Cotizaciones</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold mb-1">Categoría</label>
              <select name="categoria_id" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id }}" @selected((int)$filtros['categoria_id']===$cat->id)>{{ $cat->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold mb-1">Ubicación</label>
              <select name="ubicacion_id" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach($ubicaciones as $u)
                  <option value="{{ $u->id }}" @selected((int)$filtros['ubicacion_id']===$u->id)>{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
              <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                <i class="bi bi-funnel"></i> Filtrar
              </button>
              <a href="{{ route('metricas.productos.index', request()->query()) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-table"></i>
              </a>
            </div>
          </div>
        </div>
      </form>

      {{-- Indicador de carga --}}
      <div id="cargando" class="text-center py-5">
        <div class="spinner-border" role="status"></div>
        <p class="mt-2 text-muted">Cargando métricas…</p>
      </div>

      <div id="contenedor-graficas" style="display:none;">
        <div class="row g-3">
          <div class="col-lg-6">
            <div class="card h-100">
              <div class="card-header py-2"><strong><i class="bi bi-trophy text-warning"></i> Top 15 por Ingresos</strong></div>
              <div class="card-body" style="height: 420px;"><canvas id="chart-top-ingresos"></canvas></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card h-100">
              <div class="card-header py-2"><strong><i class="bi bi-box-seam text-info"></i> Top 15 por Unidades</strong></div>
              <div class="card-body" style="height: 420px;"><canvas id="chart-top-unidades"></canvas></div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card h-100">
              <div class="card-header py-2"><strong><i class="bi bi-pie-chart"></i> Ingresos por Categoría</strong></div>
              <div class="card-body" style="height: 420px;"><canvas id="chart-categorias-dona"></canvas></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card h-100">
              <div class="card-header py-2"><strong><i class="bi bi-bar-chart-line"></i> Ticket promedio por Categoría</strong></div>
              <div class="card-body" style="height: 420px;"><canvas id="chart-ranking-ticket"></canvas></div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-graph-up"></i> Evolución diaria de ingresos (período actual vs anterior)</strong>
              </div>
              <div class="card-body" style="height: 380px;"><canvas id="chart-evolucion-diaria"></canvas></div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header py-2">
                <strong><i class="bi bi-bar-chart-steps"></i> Pareto 80/20</strong>
                <span id="pareto-resumen" class="text-muted small ms-2"></span>
              </div>
              <div class="card-body" style="height: 420px;"><canvas id="chart-pareto"></canvas></div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card h-100">
              <div class="card-header py-2"><strong><i class="bi bi-exclamation-triangle text-danger"></i> Productos sin movimiento (por categoría)</strong></div>
              <div class="card-body" style="height: 420px;"><canvas id="chart-sin-movimiento"></canvas></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card h-100">
              <div class="card-header py-2"><strong><i class="bi bi-bullseye"></i> Stock vs Ventas (scatter)</strong></div>
              <div class="card-body" style="height: 420px;"><canvas id="chart-stock-vs-ventas"></canvas></div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header py-2"><strong><i class="bi bi-grid-3x3"></i> Heatmap día de semana × categoría</strong></div>
              <div class="card-body" style="overflow-x:auto;">
                <div id="heatmap-container"></div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card">
              <div class="card-header py-2"><strong><i class="bi bi-stars"></i> Productos nuevos del período</strong></div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-sm mb-0" id="tabla-nuevos">
                    <thead>
                      <tr>
                        <th>Referencia</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Creado</th>
                        <th class="text-end">Unidades</th>
                        <th class="text-end">Ingresos</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
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
  document.addEventListener('DOMContentLoaded', () => {
    const PALETA = {
      pink:   '#FF84D5',
      lilac:  '#BCA9F5',
      gold:   '#D4AF37',
      aqua:   '#B9DFDE',
      dark:   '#382E65',
      cream:  '#FFF1DD',
      pinkLight: '#FFE4F3',
      lilacLight: '#E8E1FA',
    };
    const ROTACION = [PALETA.pink, PALETA.lilac, PALETA.gold, PALETA.aqua, PALETA.dark, '#FF6BC9', '#A896E8', '#9ED1CF', '#C9A227', '#5B4B8A'];

    const fmtMoney = (v) => '$' + new Intl.NumberFormat('es-CO').format(Math.round(v));
    const fmtInt   = (v) => new Intl.NumberFormat('es-CO').format(v);

    function truncar(label, max = 35) {
      return label.length > max ? label.substring(0, max - 1) + '…' : label;
    }

    const params = new URLSearchParams(window.location.search);
    const url = "{{ route('metricas.productos.graficas.data') }}" + (params.toString() ? ('?' + params.toString()) : '');

    fetch(url)
      .then(r => r.json())
      .then(data => {
        document.getElementById('cargando').style.display = 'none';
        document.getElementById('contenedor-graficas').style.display = '';

        renderTopBar('chart-top-ingresos', data.top_ingresos, 'ingresos', PALETA.pink, fmtMoney);
        renderTopBar('chart-top-unidades', data.top_unidades, 'unidades', PALETA.lilac, fmtInt);
        renderDona('chart-categorias-dona', data.categorias_dona);
        renderTicketCategoria('chart-ranking-ticket', data.ranking_ticket_categoria);
        renderEvolucion('chart-evolucion-diaria', data.evolucion);
        renderPareto('chart-pareto', data.pareto);
        renderSinMovimiento('chart-sin-movimiento', data.sin_movimiento_por_cat);
        renderStockVsVentas('chart-stock-vs-ventas', data.stock_vs_ventas);
        renderHeatmap(data.heatmap);
        renderProductosNuevos(data.productos_nuevos);
      })
      .catch(err => {
        document.getElementById('cargando').innerHTML = '<div class="alert alert-danger">Error al cargar gráficas: ' + err.message + '</div>';
      });

    function renderTopBar(id, items, campo, color, fmt) {
      const ctx = document.getElementById(id);
      if (!items.length) { ctx.parentElement.innerHTML = '<div class="text-center text-muted py-5">Sin datos en el período.</div>'; return; }
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: items.map(x => truncar(x.label, 50)),
          datasets: [{
            label: campo === 'ingresos' ? 'Ingresos' : 'Unidades',
            data: items.map(x => x[campo]),
            backgroundColor: color,
            borderRadius: 4,
          }]
        },
        options: {
          indexAxis: 'y', responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => fmt(c.raw) } } },
          scales: { x: { ticks: { callback: v => fmt(v) } }, y: { ticks: { font: { size: 10 } } } }
        }
      });
    }

    function renderDona(id, items) {
      const ctx = document.getElementById(id);
      if (!items.length) { ctx.parentElement.innerHTML = '<div class="text-center text-muted py-5">Sin datos.</div>'; return; }
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: items.map(x => x.categoria),
          datasets: [{ data: items.map(x => x.ingresos), backgroundColor: items.map((_, i) => ROTACION[i % ROTACION.length]) }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right', labels: { font: { size: 10 } } },
            tooltip: { callbacks: { label: c => c.label + ': ' + fmtMoney(c.raw) } }
          }
        }
      });
    }

    function renderTicketCategoria(id, items) {
      const ctx = document.getElementById(id);
      if (!items.length) { ctx.parentElement.innerHTML = '<div class="text-center text-muted py-5">Sin datos.</div>'; return; }
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: items.map(x => x.categoria),
          datasets: [{
            label: 'Ticket promedio',
            data: items.map(x => x.ticket_prom),
            backgroundColor: PALETA.gold,
            borderRadius: 4,
          }]
        },
        options: {
          indexAxis: 'y', responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => fmtMoney(c.raw) + ' (' + items[c.dataIndex].transacciones + ' trans.)' } } },
          scales: { x: { ticks: { callback: v => fmtMoney(v) } } }
        }
      });
    }

    function renderEvolucion(id, evo) {
      const ctx = document.getElementById(id);
      if (!evo.actual.length && !evo.previo.length) { ctx.parentElement.innerHTML = '<div class="text-center text-muted py-5">Sin datos.</div>'; return; }
      // Usar índice posicional para alinear ambas series superpuestas
      const len = Math.max(evo.actual.length, evo.previo.length);
      const labels = evo.actual.map(p => p.dia);
      while (labels.length < len) labels.push('');

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            { label: 'Período actual', data: evo.actual.map(p => p.ingresos), borderColor: PALETA.pink, backgroundColor: PALETA.pinkLight, tension: 0.3, fill: true },
            { label: 'Período anterior', data: evo.previo.map(p => p.ingresos), borderColor: PALETA.lilac, borderDash: [5, 5], tension: 0.3, fill: false }
          ]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { tooltip: { callbacks: { label: c => c.dataset.label + ': ' + fmtMoney(c.raw) } } },
          scales: { y: { ticks: { callback: v => fmtMoney(v) } } }
        }
      });
    }

    function renderPareto(id, pareto) {
      const ctx = document.getElementById(id);
      if (!pareto.items || !pareto.items.length) { ctx.parentElement.innerHTML = '<div class="text-center text-muted py-5">Sin datos.</div>'; return; }

      document.getElementById('pareto-resumen').textContent = pareto.productos_80
        ? '— ' + pareto.productos_80 + ' productos generan el 80% de los ingresos'
        : '';

      new Chart(ctx, {
        data: {
          labels: pareto.items.map(x => truncar(x.label, 25)),
          datasets: [
            { type: 'bar', label: 'Ingresos', data: pareto.items.map(x => x.ingresos), backgroundColor: PALETA.pink, yAxisID: 'y' },
            { type: 'line', label: '% acumulado', data: pareto.items.map(x => x.pct_acum), borderColor: PALETA.dark, backgroundColor: PALETA.dark, yAxisID: 'y1', tension: 0 }
          ]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { tooltip: { callbacks: { label: c => c.dataset.label + ': ' + (c.dataset.yAxisID === 'y1' ? c.raw.toFixed(1) + '%' : fmtMoney(c.raw)) } } },
          scales: {
            y:  { type: 'linear', position: 'left',  ticks: { callback: v => fmtMoney(v) } },
            y1: { type: 'linear', position: 'right', min: 0, max: 100, grid: { drawOnChartArea: false }, ticks: { callback: v => v + '%' } },
            x:  { ticks: { font: { size: 9 }, maxRotation: 90, minRotation: 45 } }
          }
        }
      });
    }

    function renderSinMovimiento(id, items) {
      const ctx = document.getElementById(id);
      if (!items.length) { ctx.parentElement.innerHTML = '<div class="text-success text-center py-5"><i class="bi bi-check-circle"></i> Todos los SKUs tuvieron movimiento.</div>'; return; }
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: items.map(x => x.categoria),
          datasets: [{ label: 'SKUs sin venta', data: items.map(x => x.sin_movimiento), backgroundColor: '#dc3545', borderRadius: 4 }]
        },
        options: {
          indexAxis: 'y', responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false } }
        }
      });
    }

    function renderStockVsVentas(id, items) {
      const ctx = document.getElementById(id);
      if (!items.length) { ctx.parentElement.innerHTML = '<div class="text-center text-muted py-5">Sin datos.</div>'; return; }
      new Chart(ctx, {
        type: 'scatter',
        data: {
          datasets: [{
            label: 'SKU',
            data: items.map(x => ({ x: x.stock, y: x.unidades, label: x.label, nombre: x.nombre })),
            backgroundColor: PALETA.pink + '99',
            borderColor: PALETA.pink,
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: c => c.raw.label + ' — Stock: ' + c.raw.x + ', Vendido: ' + c.raw.y } }
          },
          scales: {
            x: { title: { display: true, text: 'Stock actual' } },
            y: { title: { display: true, text: 'Unidades vendidas' } }
          }
        }
      });
    }

    function renderHeatmap(hm) {
      const cont = document.getElementById('heatmap-container');
      if (!hm.categorias || !hm.categorias.length) { cont.innerHTML = '<p class="text-muted text-center py-3">Sin datos.</p>'; return; }
      const max = Math.max(1, ...hm.matriz.flat());
      let html = '<table class="table table-sm table-bordered mb-0" style="min-width:600px;"><thead><tr><th>Categoría</th>';
      hm.dias.forEach(d => html += '<th class="text-center">' + d + '</th>');
      html += '</tr></thead><tbody>';
      hm.categorias.forEach((cat, ci) => {
        html += '<tr><td class="fw-semibold">' + cat + '</td>';
        hm.matriz[ci].forEach(v => {
          const intensidad = v / max;
          const bg = 'rgba(255,132,213,' + (intensidad * 0.85).toFixed(2) + ')';
          const colorTxt = intensidad > 0.5 ? 'white' : 'var(--miracle-dark)';
          html += '<td class="text-center" style="background-color:' + bg + ';color:' + colorTxt + ';font-size:0.8rem;">' + (v > 0 ? fmtMoney(v) : '—') + '</td>';
        });
        html += '</tr>';
      });
      html += '</tbody></table>';
      cont.innerHTML = html;
    }

    function renderProductosNuevos(items) {
      const tbody = document.querySelector('#tabla-nuevos tbody');
      if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Sin productos nuevos en este período.</td></tr>';
        return;
      }
      tbody.innerHTML = items.map(x => `
        <tr>
          <td><strong>${x.referencia}</strong></td>
          <td>${x.nombre}</td>
          <td>${x.categoria}</td>
          <td>${x.creado ? new Date(x.creado).toLocaleDateString('es-CO') : '—'}</td>
          <td class="text-end">${fmtInt(x.unidades)}</td>
          <td class="text-end">${fmtMoney(x.ingresos)}</td>
        </tr>
      `).join('');
    }
  });
  </script>
  @endpush
</x-app-layout>
