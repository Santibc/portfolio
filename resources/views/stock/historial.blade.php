<x-app-layout>
  <x-slot name="header">Historial de movimientos de stock</x-slot>

  @push('styles')
  <style>
    /* Estiliza los controles propios de DataTables 2.x (.dt-*) y legacy (.dataTables_*) */
    div.dt-length select,
    .dataTables_length select {
      display: inline-block !important;
      width: 80px !important;
      padding: .25rem 2rem .25rem .5rem;
      margin: 0 .5rem;
      font-size: .875rem;
      line-height: 1.5;
      color: #212529;
      background-color: #fff;
      border: 1px solid #ced4da;
      border-radius: .25rem;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right .5rem center;
      background-size: 12px 12px;
      -webkit-appearance: none;
      appearance: none;
    }
    div.dt-search input,
    .dataTables_filter input {
      display: inline-block !important;
      width: 220px !important;
      padding: .25rem .5rem;
      margin-left: .5rem;
      font-size: .875rem;
      line-height: 1.5;
      color: #212529;
      background-color: #fff;
      border: 1px solid #ced4da;
      border-radius: .25rem;
    }
  </style>
  @endpush

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h4 class="text-2xl font-semibold mb-0">Movimientos registrados</h4>
            <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left"></i> Volver a gestión de stock
            </a>
          </div>

          {{-- Filtros --}}
          <div class="row g-2 mb-3">
            <div class="col-md-4">
              <label for="f-producto" class="form-label small mb-1">Producto</label>
              <select id="f-producto" class="form-select form-select-sm">
                <option value="">— Todos —</option>
                @foreach($productos as $p)
                  <option value="{{ $p->id }}">{{ $p->referencia }} — {{ $p->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label for="f-tipo" class="form-label small mb-1">Tipo</label>
              <select id="f-tipo" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
                <option value="ajuste">Ajuste</option>
                <option value="reserva">Reserva</option>
                <option value="liberacion">Liberación</option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="f-origen" class="form-label small mb-1">Origen</label>
              <select id="f-origen" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="compra">Compra</option>
                <option value="venta">Venta</option>
                <option value="devolucion">Devolución</option>
                <option value="ajuste_inventario">Ajuste de inventario</option>
                <option value="cotizacion">Cotización</option>
                <option value="otro">Otro</option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="f-desde" class="form-label small mb-1">Desde</label>
              <input type="date" id="f-desde" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
              <label for="f-hasta" class="form-label small mb-1">Hasta</label>
              <input type="date" id="f-hasta" class="form-control form-control-sm">
            </div>
            <div class="col-12 d-flex gap-2">
              <button id="btn-filtrar" class="btn btn-primary btn-sm">
                <i class="bi bi-funnel"></i> Aplicar filtros
              </button>
              <button id="btn-limpiar" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-circle"></i> Limpiar
              </button>
            </div>
          </div>

          <table id="movimientos-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Fecha/Hora</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Stock ant. → nuevo</th>
                <th>Origen</th>
                <th>Motivo</th>
                <th>Usuario</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const table = $('#movimientos-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      order: [[0, 'desc']],
      ajax: {
        url: "{{ route('stock.historial') }}",
        data: (d) => {
          d.producto_id = $('#f-producto').val();
          d.tipo        = $('#f-tipo').val();
          d.origen      = $('#f-origen').val();
          d.desde       = $('#f-desde').val();
          d.hasta       = $('#f-hasta').val();
        }
      },
      columns: [
        { data: 'fecha',       name: 'created_at' },
        { data: 'producto',    name: 'producto',   orderable: false },
        { data: 'tipo',        name: 'tipo_movimiento' },
        { data: 'cantidad_fmt',name: 'cantidad',   orderable: false },
        { data: 'stocks',      name: 'stock_nuevo', orderable: false, searchable: false },
        { data: 'origen_fmt',  name: 'origen' },
        { data: 'motivo_fmt',  name: 'motivo',     orderable: false },
        { data: 'usuario_fmt', name: 'usuario_id', orderable: false }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      pageLength: 25
    });

    $('#btn-filtrar').on('click', () => table.ajax.reload());
    $('#btn-limpiar').on('click', () => {
      $('#f-producto, #f-tipo, #f-origen').val('');
      $('#f-desde, #f-hasta').val('');
      table.ajax.reload();
    });

    // Aplicar filtro también al pulsar Enter en los inputs de fecha
    $('#f-desde, #f-hasta').on('change', () => table.ajax.reload());
    $('#f-producto, #f-tipo, #f-origen').on('change', () => table.ajax.reload());
  });
  </script>
  @endpush
</x-app-layout>
