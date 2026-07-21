<x-app-layout>
    <x-slot name="header">Almacenes</x-slot>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <h4 class="text-2xl font-semibold mb-4">Almacenes</h4>

            @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table id="almacenes-table" class="table-responsive w-full text-sm text-left">
              <thead class="text-xs uppercase bg-gray-100">
                <tr>
                  <th>Acciones</th>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Dirección</th>
                  <th>Teléfono</th>
                  <th>Vendedores</th>
                  <th>Activo</th>
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
      $('#almacenes-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        scrollX: true,
        ajax: "{{ route('almacenes') }}",
        columns: [
          { data: 'action', orderable: false, searchable: false },
          { data: 'codigo', name: 'codigo' },
          { data: 'nombre', name: 'nombre' },
          { data: 'direccion', name: 'direccion' },
          { data: 'telefono', name: 'telefono' },
          { data: 'vendedores_count', name: 'vendedores_count', orderable: false, searchable: false },
          { data: 'activo_label', name: 'activo' },
        ],
        dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
        buttons: [
          { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas ' },
          {
            text: 'Nuevo', className: 'btn btn-outline-primary',
            action: () => window.location.href = "{{ route('almacenes.form') }}"
          }
        ],
        language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']]
      });
    });
    </script>
    @endpush
</x-app-layout>
