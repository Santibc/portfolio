<x-app-layout>
    <x-slot name="header">Ventas</x-slot>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <h4 class="text-2xl font-semibold mb-4">Ventas</h4>

            @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row g-3 mb-3">
              @if ($esAdmin)
              <div class="col-md-3">
                <label class="form-label">Vendedor</label>
                <select id="filtroVendedor" class="form-select">
                  <option value="">Todos</option>
                  @foreach ($vendedores as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                  @endforeach
                </select>
              </div>
              @endif
              <div class="col-md-3">
                <label class="form-label">Almacén</label>
                <select id="filtroAlmacen" class="form-select">
                  <option value="">Todos</option>
                  @foreach ($almacenes as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" id="filtroDesde" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
              </div>
              <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" id="filtroHasta" class="form-control" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button id="btnLimpiar" type="button" class="btn btn-outline-secondary w-100">Limpiar</button>
              </div>
            </div>

            <table id="ventas-table" class="table-responsive w-full text-sm text-left">
              <thead class="text-xs uppercase bg-gray-100">
                <tr>
                  <th>Acciones</th>
                  <th>Fecha</th>
                  <th>Vendedor</th>
                  <th>Almacén</th>
                  <th>Cliente</th>
                  <th class="text-center">Items</th>
                  <th>Monto</th>
                  <th>Descripción</th>
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
      const table = $('#ventas-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        order: [[1, 'desc']],
        ajax: {
          url: "{{ route('ventas') }}",
          data: (d) => {
            d.user_id = $('#filtroVendedor').val();
            d.almacen_id = $('#filtroAlmacen').val();
            d.desde = $('#filtroDesde').val();
            d.hasta = $('#filtroHasta').val();
          }
        },
        columns: [
          { data: 'action', orderable: false, searchable: false },
          { data: 'fecha_fmt', name: 'fecha' },
          { data: 'vendedor', name: 'vendedor' },
          { data: 'almacen', name: 'almacen' },
          { data: 'cliente', orderable: false, searchable: false },
          { data: 'items_count', orderable: false, searchable: false, className: 'text-center' },
          { data: 'monto_fmt', name: 'monto', searchable: false },
          { data: 'descripcion', name: 'descripcion' },
        ],
        dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
        buttons: [
          { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas ' },
          {
            text: 'Nueva Venta', className: 'btn btn-outline-primary',
            action: () => window.location.href = "{{ route('ventas.form') }}"
          },
          @if ($esAdmin)
          {
            text: 'Carga masiva Excel', className: 'btn btn-outline-success',
            action: () => window.location.href = "{{ route('ventas.importar') }}"
          }
          @endif
        ],
        language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']]
      });

      $('#filtroAlmacen, #filtroDesde, #filtroHasta').on('change', () => table.ajax.reload());
      @if ($esAdmin)
      $('#filtroVendedor').on('change', () => table.ajax.reload());
      @endif
      $('#btnLimpiar').on('click', () => {
        $('#filtroAlmacen, #filtroDesde, #filtroHasta').val('');
        @if ($esAdmin) $('#filtroVendedor').val(''); @endif
        table.ajax.reload();
      });
    });
    </script>
    @endpush
</x-app-layout>
