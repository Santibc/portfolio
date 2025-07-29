<x-app-layout>
  <x-slot name="header">Categorías</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Listado de Categorías</h4>

          <table id="categorias-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Descripción</th>
                <th>Orden</th>
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
    const table = $('#categorias-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: "{{ route('categorias') }}",
      columns: [
        { data:'action',    orderable:false, searchable:false },
        { data:'nombre',    name:'nombre' },
        { data:'slug',      name:'slug' },
        { 
          data:'descripcion', 
          name:'descripcion',
          render: data => data ? data.substr(0,50)+'…' : ''
        },
        { data:'orden',     name:'orden' },
        { data:'activo',    name:'activo' },
      ],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend:'pageLength', className:'btn btn-outline-dark', text:'Filas ' },
        { extend:'colvis',     className:'btn btn-outline-dark', text:'Columnas', columns:':not(.noVis)' },
        { extend:'excelHtml5', className:'btn btn-outline-success', text:'Excel' },
        {
          text:'Nuevo', className:'btn btn-outline-primary',
          action: () => window.location.href = "{{ route('categorias.form') }}"
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10,25,50,-1],[10,25,50,'Todos']]
    });

    table.on('buttons-action', () => {
      setTimeout(() => {
        $('.dt-button-collection')
          .addClass('bg-white border rounded shadow-md mt-2 p-2')
          .css({ position:'absolute','z-index':999,top:'calc(100% + .5rem)',left:0 });
        $('.dt-button-collection button')
          .removeClass()
          .addClass('block w-full text-left px-4 py-2 rounded hover:bg-gray-100');
      }, 50);
    });
  });
  </script>
  @endpush
</x-app-layout>
