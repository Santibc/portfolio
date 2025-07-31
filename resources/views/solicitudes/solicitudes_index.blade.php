<x-app-layout>
  <x-slot name="header">Gestión de Solicitudes de Cotización</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      {{-- Mensajes de éxito/error --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Listado de Solicitudes</h4>

          <table id="solicitudes-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>Nº Solicitud</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Fecha</th>
                <th>Items</th>
                <th>Monto</th>
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
    const table = $('#solicitudes-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: "{{ route('solicitudes') }}",
      columns: [
        { data:'action', orderable:false, searchable:false },
        { data:'numero_solicitud', name:'numero_solicitud' },
        { data:'cliente_nombre', name:'cliente_nombre' },
        { data:'vendedor', name:'vendedor' },
        { data:'fecha', name:'created_at' },
        { data:'total_items', name:'total_items', searchable:false },
        { data:'monto_formateado', name:'monto_total' },
        { data:'estado_badge', name:'estado' }
      ],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend:'pageLength', className:'btn btn-outline-dark', text:'Filas ' },
        { extend:'colvis', className:'btn btn-outline-dark', text:'Columnas', columns:':not(.noVis)' },
        { extend:'excelHtml5', className:'btn btn-outline-success', text:'Excel' },
        {
          text:'<i class="bi bi-funnel"></i> Pendientes',
          className:'btn btn-outline-warning',
          action: function(e, dt, node, config) {
            if ($(node).hasClass('active')) {
              $(node).removeClass('active');
              dt.column(7).search('').draw();
            } else {
              $(node).addClass('active');
              dt.column(7).search('Pendiente').draw();
            }
          }
        },
        {
          text:'<i class="bi bi-funnel"></i> Aplicadas',
          className:'btn btn-outline-success',
          action: function(e, dt, node, config) {
            if ($(node).hasClass('active')) {
              $(node).removeClass('active');
              dt.column(7).search('').draw();
            } else {
              $(node).addClass('active');
              dt.column(7).search('Aplicada').draw();
            }
          }
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10,25,50,-1],[10,25,50,'Todos']],
      order: [[4, 'desc']] // Ordenar por fecha descendente
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

  // Funciones para los modales
  function verDetalle(solicitudId) {
    $('#modalDetalleContent').html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
    $('#modalDetalle').modal('show');
    
    $.get(`/solicitudes/${solicitudId}/detalle`, function(data) {
      $('#modalDetalleContent').html(data);
    }).fail(function(xhr) {
      $('#modalDetalleContent').html(
        '<div class="alert alert-danger">Error al cargar el detalle: ' + 
        (xhr.responseJSON?.error || 'Error desconocido') + '</div>'
      );
    });
  }

  function marcarAplicada(solicitudId) {
    // Cargar el detalle en el modal para poder agregar observaciones
    verDetalle(solicitudId);
  }

  function confirmarAplicar(solicitudId) {
    const observaciones = $('#observacionesAdmin').val();
    
    // Mostrar loading
    $('#modalDetalleContent').append(
      '<div class="loading-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:1000;">' +
      '<div class="spinner-border" role="status"></div></div>'
    );
    
    $.post(`/solicitudes/${solicitudId}/aplicar`, {
      _token: '{{ csrf_token() }}',
      observaciones: observaciones
    }, function(response) {
      if (response.success) {
        $('#modalDetalle').modal('hide');
        
        // Mostrar mensaje de éxito
        const alert = `
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${response.mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        `;
        $('.max-w-7xl').prepend(alert);
        
        // Recargar tabla
        $('#solicitudes-table').DataTable().ajax.reload();
      }
    }).fail(function(xhr) {
      $('.loading-overlay').remove();
      alert('Error: ' + (xhr.responseJSON?.mensaje || 'Error al aplicar la solicitud'));
    });
  }
  </script>
  @endpush

  <!-- Modal para ver detalle -->
  <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de Solicitud de Cotización</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="modalDetalleContent">
          <div class="text-center">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>