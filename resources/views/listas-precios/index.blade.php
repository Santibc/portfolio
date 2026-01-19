<x-app-layout>
  <x-slot name="header">Listas de Precios</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Gestión de Listas de Precios</h4>

          <table id="listas-precios-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Orden</th>
                <th>Clientes</th>
                <th>Estado</th>
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
    const table = $('#listas-precios-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: "{{ route('listas-precios') }}",
      columns: [
        { data:'action', orderable:false, searchable:false },
        { data:'codigo', name:'codigo' },
        { data:'nombre', name:'nombre' },
        {
          data:'descripcion',
          name:'descripcion',
          render: data => data ? data.substr(0,50)+'...' : '-'
        },
        { data:'orden', name:'orden' },
        { data:'clientes_count', name:'clientes_count', orderable:false, searchable:false },
        { data:'activo', name:'activo' },
      ],
      order: [[4, 'asc']],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend:'pageLength', className:'btn btn-outline-dark', text:'Filas ' },
        { extend:'colvis', className:'btn btn-outline-dark', text:'Columnas', columns:':not(.noVis)' },
        { extend:'excelHtml5', className:'btn btn-outline-success', text:'Excel' },
        {
          text:'Nueva Lista', className:'btn btn-outline-primary',
          action: () => window.location.href = "{{ route('listas-precios.form') }}"
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10,25,50,-1],[10,25,50,'Todos']]
    });

    // Toggle estado
    $(document).on('click', '.btn-toggle-estado', function() {
      const id = $(this).data('id');
      const activo = $(this).data('activo');
      const accion = activo == 1 ? 'desactivar' : 'activar';

      Swal.fire({
        title: `¿${activo == 1 ? 'Desactivar' : 'Activar'} lista de precios?`,
        text: activo == 1
          ? 'Si tiene clientes asignados, no se podrá desactivar.'
          : 'La lista estará disponible para asignar a clientes.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: activo == 1 ? '#d33' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: activo == 1 ? 'Sí, desactivar' : 'Sí, activar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/listas-precios/${id}/toggle-estado`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Actualizado', data.message, 'success');
              table.ajax.reload(null, false);
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(err => {
            Swal.fire('Error', 'Ocurrió un error al procesar la solicitud.', 'error');
          });
        }
      });
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
