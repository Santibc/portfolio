<x-app-layout>
  <x-slot name="header">Métricas de Productos</x-slot>

  <div class="py-4">
    <div class="container-fluid px-3 px-lg-4">

      {{-- Filtros --}}
      <form method="GET" action="{{ route('metricas.productos.index') }}" class="card mb-4">
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
              <label class="form-label small fw-semibold mb-1" title="Solo afecta ventas PdV; cotizaciones no tienen ubicación directa">
                Ubicación <i class="bi bi-info-circle text-muted"></i>
              </label>
              <select name="ubicacion_id" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach($ubicaciones as $u)
                  <option value="{{ $u->id }}" @selected((int)$filtros['ubicacion_id']===$u->id)>{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold mb-1">Tipo</label>
              <select name="tipo" class="form-select form-select-sm">
                <option value="todos"       @selected($filtros['tipo']==='todos')>Todos</option>
                <option value="con_ventas"  @selected($filtros['tipo']==='con_ventas')>Con ventas</option>
                <option value="sin_ventas"  @selected($filtros['tipo']==='sin_ventas')>Sin movimiento</option>
                <option value="stock_bajo"  @selected($filtros['tipo']==='stock_bajo')>Stock bajo</option>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <div class="form-check">
                <input type="hidden" name="solo_con_stock" value="0">
                <input type="checkbox" name="solo_con_stock" value="1" id="solo_con_stock"
                       class="form-check-input" @checked(!empty($filtros['solo_con_stock']))>
                <label for="solo_con_stock" class="form-check-label small">
                  Solo con stock <span class="text-muted">(≠ 0)</span>
                </label>
              </div>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
              <a href="{{ route('metricas.productos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-eraser"></i> Limpiar
              </a>
              <a href="{{ route('metricas.productos.graficas', request()->query()) }}" class="btn btn-info btn-sm">
                <i class="bi bi-pie-chart"></i> Gráficas
              </a>
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-funnel"></i> Filtrar
              </button>
            </div>
          </div>
        </div>
      </form>

      {{-- KPIs --}}
      <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
          <div class="card h-100">
            <div class="card-body py-3">
              <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-cash-coin"></i> Ingresos</div>
              <div class="fs-5 fw-bold" style="color: var(--miracle-pink);">${{ number_format($kpis['ingresos'], 0, ',', '.') }}</div>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <div class="card h-100">
            <div class="card-body py-3">
              <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-box-seam"></i> Unidades</div>
              <div class="fs-5 fw-bold" style="color: var(--miracle-dark);">{{ number_format($kpis['unidades']) }}</div>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <div class="card h-100">
            <div class="card-body py-3">
              <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-bag-check"></i> Productos vendidos</div>
              <div class="fs-5 fw-bold" style="color: var(--miracle-dark);">{{ number_format($kpis['productos_vendidos']) }} / {{ number_format($kpis['total_skus']) }}</div>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <div class="card h-100" style="border-color: #dc3545 !important;">
            <div class="card-body py-3">
              <div class="text-danger small fw-semibold text-uppercase mb-1"><i class="bi bi-exclamation-triangle"></i> Sin movimiento</div>
              <div class="fs-5 fw-bold text-danger">{{ number_format($kpis['sin_movimiento']) }}</div>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <div class="card h-100">
            <div class="card-body py-3">
              <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-receipt"></i> Ticket prom. línea</div>
              <div class="fs-5 fw-bold" style="color: var(--miracle-dark);">${{ number_format($kpis['ticket_promedio_linea'], 0, ',', '.') }}</div>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <div class="card h-100">
            <div class="card-body py-3">
              <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-graph-up"></i> % SKUs con ventas</div>
              <div class="fs-5 fw-bold" style="color: var(--miracle-dark);">{{ $kpis['pct_con_ventas'] }}%</div>
              <div class="progress mt-1" style="height: 5px;">
                <div class="progress-bar" role="progressbar"
                     style="width: {{ $kpis['pct_con_ventas'] }}%; background-color: var(--miracle-pink);"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Tabla --}}
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table id="metricas-table" class="table table-hover w-100" style="font-size: 0.9rem;">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Categoría</th>
                  <th class="text-center">Stock</th>
                  <th class="text-end">Unidades</th>
                  <th class="text-end">Ingresos</th>
                  <th class="text-end"># Trans</th>
                  <th class="text-end">Precio prom</th>
                  <th>Última venta</th>
                  <th class="text-center">Δ vs anterior</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const filtros = {
      fecha_inicio:    @json($filtros['fecha_inicio']),
      fecha_fin:       @json($filtros['fecha_fin']),
      fuente:          @json($filtros['fuente']),
      categoria_id:    @json($filtros['categoria_id']),
      ubicacion_id:    @json($filtros['ubicacion_id']),
      tipo:            @json($filtros['tipo']),
      solo_con_stock:  @json(!empty($filtros['solo_con_stock']) ? 1 : 0),
    };

    const table = $('#metricas-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: {
        url: "{{ route('metricas.productos.index') }}",
        data: function (d) {
          Object.keys(filtros).forEach(k => {
            if (filtros[k] !== null && filtros[k] !== '') d[k] = filtros[k];
          });
        }
      },
      order: [[4, 'desc']],
      columns: [
        { data: 'producto_info',    name: 'producto_info' },
        { data: 'categoria_nombre', name: 'pv.categoria_nombre' },
        { data: 'stock_badge',      name: 'stock_badge', className: 'text-center', searchable: false },
        { data: 'unidades',         name: 'unidades', className: 'text-end', searchable: false },
        { data: 'ingresos_fmt',     name: 'ingresos_fmt', className: 'text-end', searchable: false },
        { data: 'transacciones',    name: 'transacciones', className: 'text-end', searchable: false },
        { data: 'precio_prom_fmt',  name: 'precio_prom_fmt', className: 'text-end', searchable: false },
        { data: 'ultima_venta_fmt', name: 'ultima_venta_fmt', searchable: false },
        { data: 'delta_badge',      name: 'delta_badge', className: 'text-center', searchable: false },
      ],
      dom: "<'d-flex justify-content-between mb-3'<'d-flex gap-2'B><'flex-grow-1 ms-3'f>>tip",
      buttons: [
        { extend: 'pageLength', className: 'btn btn-outline-secondary btn-sm', text: '<i class="bi bi-list"></i> Filas' },
        { extend: 'colvis', className: 'btn btn-outline-secondary btn-sm', text: '<i class="bi bi-columns"></i> Columnas' },
        {
          extend: 'excelHtml5',
          className: 'btn btn-outline-success btn-sm',
          text: '<i class="bi bi-file-earmark-excel"></i> Excel',
          title: 'Métricas Productos ' + filtros.fecha_inicio + ' al ' + filtros.fecha_fin,
          exportOptions: { columns: ':visible' }
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Todos']],
      pageLength: 25,
    });
  });
  </script>
  @endpush
</x-app-layout>
