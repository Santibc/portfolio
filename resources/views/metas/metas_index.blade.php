<x-app-layout>
    <x-slot name="header">Metas</x-slot>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <h4 class="text-2xl font-semibold mb-4">Metas de Ventas</h4>

            @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row g-3 mb-3">
              <div class="col-md-2">
                <label class="form-label">Año</label>
                <select id="filtroAnio" class="form-select">
                  <option value="">Todos</option>
                  @for ($y = $anioActual + 1; $y >= $anioActual - 3; $y--)
                    <option value="{{ $y }}" {{ $y == $anioActual ? 'selected' : '' }}>{{ $y }}</option>
                  @endfor
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Mes</label>
                <select id="filtroMes" class="form-select">
                  <option value="">Todos</option>
                  @foreach (['01' => 'Enero','02' => 'Febrero','03' => 'Marzo','04' => 'Abril','05' => 'Mayo','06' => 'Junio','07' => 'Julio','08' => 'Agosto','09' => 'Septiembre','10' => 'Octubre','11' => 'Noviembre','12' => 'Diciembre'] as $num => $nombre)
                    <option value="{{ (int) $num }}" {{ (int) $num == $mesActual ? 'selected' : '' }}>{{ $nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Vendedor</label>
                <select id="filtroVendedor" class="form-select">
                  <option value="">Todos</option>
                  @foreach ($vendedores as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <button id="btnLimpiar" type="button" class="btn btn-outline-secondary">Limpiar filtros</button>
              </div>
            </div>

            <table id="metas-table" class="table-responsive w-full text-sm text-left">
              <thead class="text-xs uppercase bg-gray-100">
                <tr>
                  <th>Acciones</th>
                  <th>Vendedor</th>
                  <th>Periodo</th>
                  <th>Meta</th>
                  <th>Vendido</th>
                  <th>Cumplimiento</th>
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
      const table = $('#metas-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: {
          url: "{{ route('metas') }}",
          data: (d) => {
            d.anio = $('#filtroAnio').val();
            d.mes = $('#filtroMes').val();
            d.user_id = $('#filtroVendedor').val();
          }
        },
        columns: [
          { data: 'action', orderable: false, searchable: false },
          { data: 'vendedor', name: 'vendedor' },
          { data: 'periodo', name: 'periodo', orderable: false, searchable: false },
          { data: 'monto_fmt', name: 'monto', searchable: false },
          { data: 'vendido', orderable: false, searchable: false, render: v => '$ ' + new Intl.NumberFormat('es-CO').format(v) },
          { data: 'cumplimiento', orderable: false, searchable: false },
        ],
        dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
        buttons: [
          { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas ' },
          {
            text: 'Nueva Meta', className: 'btn btn-outline-primary',
            action: () => window.location.href = "{{ route('metas.form') }}"
          }
        ],
        language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']]
      });

      $('#filtroAnio, #filtroMes, #filtroVendedor').on('change', () => table.ajax.reload());
      $('#btnLimpiar').on('click', () => {
        $('#filtroAnio, #filtroMes, #filtroVendedor').val('');
        table.ajax.reload();
      });
    });
    </script>
    @endpush
</x-app-layout>
