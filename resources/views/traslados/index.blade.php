<x-app-layout>
  <x-slot name="header">Traslados de Stock</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Gestión de Traslados</h4>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          {{-- Filtros --}}
          <div class="card mb-4">
            <div class="card-body">
              <div class="row g-3 align-items-end">
                <div class="col-md-3">
                  <label class="form-label">Estado</label>
                  <select id="filtro-estado" class="form-select">
                    <option value="">-- Todos --</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_transito">En Tránsito</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Tipo de Operación</label>
                  <select id="filtro-tipo-operacion" class="form-select">
                    <option value="">-- Todos --</option>
                    <option value="general">General</option>
                    <option value="credito">Crédito</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <button type="button" id="btn-filtrar" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                  </button>
                  <button type="button" id="btn-limpiar" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <table id="traslados-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>Número</th>
                <th>Producto</th>
                <th>Ruta</th>
                <th>Cantidad</th>
                <th>Tipo Op.</th>
                <th>Estado</th>
                <th>Creado por</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detalle -->
  <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle del Traslado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="detalleContent">
          <!-- Contenido cargado dinámicamente -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const table = $('#traslados-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: {
        url: "{{ route('traslados') }}",
        data: function(d) {
          d.estado = $('#filtro-estado').val();
          d.tipo_operacion = $('#filtro-tipo-operacion').val();
        }
      },
      columns: [
        { data: 'action', orderable: false, searchable: false },
        { data: 'numero_traslado', name: 'numero_traslado' },
        { data: 'producto_nombre', name: 'producto.nombre' },
        { data: 'ruta', name: 'ubicacion_origen_id', orderable: false },
        { data: 'cantidad', name: 'cantidad' },
        { data: 'tipo_operacion_badge', name: 'tipo_operacion' },
        { data: 'estado_badge', name: 'estado' },
        { data: 'creador', name: 'usuarioCreador.name', orderable: false },
        { data: 'created_at', name: 'created_at' },
      ],
      order: [[8, 'desc']],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas' },
        { extend: 'colvis', className: 'btn btn-outline-dark', text: 'Columnas', columns: ':not(.noVis)' },
        { extend: 'excelHtml5', className: 'btn btn-outline-success', text: 'Excel' },
        @if(!auth()->user()->hasRole('centro_experiencia'))
        {
          text: 'Nuevo Traslado',
          className: 'btn btn-outline-primary',
          action: () => window.location.href = "{{ route('traslados.form') }}"
        }
        @endif
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

    // Filtros
    $('#btn-filtrar').on('click', function() {
      table.ajax.reload();
    });

    $('#btn-limpiar').on('click', function() {
      $('#filtro-estado').val('');
      $('#filtro-tipo-operacion').val('');
      table.ajax.reload();
    });

    window.enviarTraslado = function(id) {
      Swal.fire({
        title: '¿Enviar traslado?',
        text: 'Se descontará el stock de la ubicación de origen',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/traslados/${id}/enviar`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Enviado', data.message, 'success');
              table.ajax.reload();
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'No se pudo enviar el traslado', 'error'));
        }
      });
    };

    window.recibirTraslado = function(id) {
      Swal.fire({
        title: '¿Recibir traslado?',
        text: 'Se agregará el stock a la ubicación de destino',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, recibir',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/traslados/${id}/recibir`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Recibido', data.message, 'success');
              table.ajax.reload();
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'No se pudo recibir el traslado', 'error'));
        }
      });
    };

    window.cancelarTraslado = function(id) {
      Swal.fire({
        title: '¿Cancelar traslado?',
        text: 'Si estaba en tránsito, se devolverá el stock al origen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/traslados/${id}/cancelar`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Cancelado', data.message, 'success');
              table.ajax.reload();
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'No se pudo cancelar el traslado', 'error'));
        }
      });
    };

    window.verDetalleTraslado = function(id) {
      fetch(`/traslados/${id}/detalle`)
        .then(res => res.json())
        .then(data => {
          const tipoOpBadge = data.tipo_operacion === 'credito'
            ? '<span class="badge bg-info">Crédito</span>'
            : '<span class="badge bg-secondary">General</span>';
          let html = `
            <div class="space-y-3">
              <p><strong>Número:</strong> ${data.numero_traslado}</p>
              <p><strong>Producto:</strong> ${data.producto?.nombre || 'N/A'}${data.variante_producto ? ' - ' + data.variante_producto.nombre_variante : ''}</p>
              <p><strong>Origen:</strong> ${data.ubicacion_origen?.nombre || 'N/A'}</p>
              <p><strong>Destino:</strong> ${data.ubicacion_destino?.nombre || 'N/A'}</p>
              <p><strong>Cantidad:</strong> ${data.cantidad}</p>
              <p><strong>Tipo de Operación:</strong> ${tipoOpBadge}</p>
              <p><strong>Estado:</strong> ${data.estado}</p>
              <p><strong>Creado por:</strong> ${data.usuario_creador?.name || 'N/A'}</p>
              ${data.usuario_receptor ? `<p><strong>Recibido por:</strong> ${data.usuario_receptor.name}</p>` : ''}
              ${data.enviado_en ? `<p><strong>Enviado:</strong> ${data.enviado_en}</p>` : ''}
              ${data.recibido_en ? `<p><strong>Recibido:</strong> ${data.recibido_en}</p>` : ''}
              ${data.notas ? `<p><strong>Notas:</strong> ${data.notas}</p>` : ''}
            </div>
          `;
          document.getElementById('detalleContent').innerHTML = html;
          new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        })
        .catch(() => Swal.fire('Error', 'No se pudo cargar el detalle', 'error'));
    };
  });
  </script>
  @endpush
</x-app-layout>
