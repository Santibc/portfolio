<x-app-layout>
  <x-slot name="header">Ubicaciones</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Gestión de Ubicaciones</h4>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <table id="ubicaciones-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Dirección</th>
                <th>Responsable</th>
                <th>Principal</th>
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
    const table = $('#ubicaciones-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: "{{ route('ubicaciones') }}",
      columns: [
        { data: 'action', orderable: false, searchable: false },
        { data: 'codigo', name: 'codigo' },
        { data: 'nombre', name: 'nombre' },
        { data: 'tipo_nombre', name: 'tipo' },
        { data: 'direccion', name: 'direccion', defaultContent: '-' },
        { data: 'responsable', name: 'responsable', defaultContent: '-' },
        { data: 'es_principal', name: 'es_principal', orderable: false, searchable: false },
        { data: 'activo', name: 'activo' },
      ],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas' },
        { extend: 'colvis', className: 'btn btn-outline-dark', text: 'Columnas', columns: ':not(.noVis)' },
        { extend: 'excelHtml5', className: 'btn btn-outline-success', text: 'Excel' },
        {
          text: 'Nueva Ubicación',
          className: 'btn btn-outline-primary',
          action: () => window.location.href = "{{ route('ubicaciones.form') }}"
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']]
    });

    table.on('buttons-action', () => {
      setTimeout(() => {
        $('.dt-button-collection')
          .addClass('bg-white border rounded shadow-md mt-2 p-2')
          .css({ position: 'absolute', 'z-index': 999, top: 'calc(100% + .5rem)', left: 0 });
        $('.dt-button-collection button')
          .removeClass()
          .addClass('block w-full text-left px-4 py-2 rounded hover:bg-gray-100');
      }, 50);
    });

    window.eliminarUbicacion = function(id) {
      Swal.fire({
        title: '¿Eliminar ubicación?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/ubicaciones/${id}/eliminar`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Eliminado', data.message, 'success');
              table.ajax.reload();
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'No se pudo eliminar la ubicación', 'error'));
        }
      });
    };

    window.reiniciarInventario = function(id, nombre) {
      Swal.fire({
        title: '¿Reiniciar inventario a 0?',
        html: `Vas a poner en <strong>0</strong> todo el stock disponible de <strong>${nombre}</strong> para un nuevo conteo.<br><br>` +
              `<small>Las reservas activas de cotizaciones se conservan. La acción queda registrada en el log y en el historial de movimientos.</small><br><br>` +
              `Escribe <code>REINICIAR</code> para confirmar:`,
        input: 'text',
        inputPlaceholder: 'REINICIAR',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, reiniciar a 0',
        cancelButtonText: 'Cancelar',
        preConfirm: (val) => {
          if ((val || '').trim().toUpperCase() !== 'REINICIAR') {
            Swal.showValidationMessage('Debes escribir REINICIAR para confirmar');
          }
        }
      }).then((result) => {
        if (!result.isConfirmed) return;
        $.ajax({
          url: `/ubicaciones/${id}/reiniciar-inventario`,
          method: 'POST',
          data: { _token: '{{ csrf_token() }}' },
          success: function(resp) {
            Swal.fire('Inventario reiniciado', resp.message, 'success');
            table.ajax.reload(null, false);
          },
          error: function(xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo reiniciar el inventario.';
            Swal.fire('Error', msg, 'error');
          }
        });
      });
    };
  });
  </script>
  @endpush
</x-app-layout>
