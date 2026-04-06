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
    <div class="modal-dialog modal-lg">
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

  {{-- Modal Logs --}}
  <div class="modal fade" id="modalLogs" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i>Historial de Actividad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Cargando historial...</p>
                </div>
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
        { data: 'producto_nombre', name: 'producto_nombre', orderable: false, searchable: false },
        { data: 'ruta', name: 'ubicacion_origen_id', orderable: false },
        { data: 'cantidad_total', name: 'cantidad_total', orderable: false, searchable: false },
        { data: 'tipo_operacion_badge', name: 'tipo_operacion' },
        { data: 'estado_badge', name: 'estado' },
        { data: 'creador', name: 'usuarioCreador.name', orderable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'tiene_observacion', name: 'tiene_observacion', visible: false },
      ],
      createdRow: function(row, data) {
        if (data.tiene_observacion === '1') {
          $(row).css('background-color', '#f8d7da');
        }
      },
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
        input: 'textarea',
        inputLabel: 'Observación (opcional)',
        inputPlaceholder: 'Escribe una observación si es necesario...',
        inputAttributes: {
          'aria-label': 'Observación'
        },
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, recibir',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          const observacion = result.value || '';
          fetch(`/traslados/${id}/recibir`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ observacion_recepcion: observacion })
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

          let itemsHtml = '';
          if (data.items && data.items.length > 0) {
            itemsHtml = `
              <table class="table table-sm table-bordered mt-3">
                <thead class="table-light">
                  <tr><th>Producto</th><th>Variante</th><th>Cantidad</th></tr>
                </thead>
                <tbody>
                  ${data.items.map(item => `
                    <tr>
                      <td>${item.producto?.referencia || ''} - ${item.producto?.nombre || 'N/A'}</td>
                      <td>${item.variante_producto?.nombre_variante || '-'}</td>
                      <td>${item.cantidad}</td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            `;
          }

          let html = `
            <div class="space-y-3">
              <p><strong>Número:</strong> ${data.numero_traslado}</p>
              <p><strong>Origen:</strong> ${data.ubicacion_origen?.nombre || 'N/A'}</p>
              <p><strong>Destino:</strong> ${data.ubicacion_destino?.nombre || 'N/A'}</p>
              <p><strong>Tipo de Operación:</strong> ${tipoOpBadge}</p>
              <p><strong>Estado:</strong> ${data.estado}</p>
              <p><strong>Creado por:</strong> ${data.usuario_creador?.name || 'N/A'}</p>
              ${data.usuario_receptor ? `<p><strong>Recibido por:</strong> ${data.usuario_receptor.name}</p>` : ''}
              ${data.enviado_en ? `<p><strong>Enviado:</strong> ${data.enviado_en}</p>` : ''}
              ${data.recibido_en ? `<p><strong>Recibido:</strong> ${data.recibido_en}</p>` : ''}
              ${data.notas ? `<p><strong>Notas:</strong> ${data.notas}</p>` : ''}
              ${data.observacion_recepcion ? `<p><strong>Observación de recepción:</strong> <span class="text-danger">${data.observacion_recepcion}</span></p>` : ''}
              <h6 class="mt-3 mb-0"><strong>Productos:</strong></h6>
              ${itemsHtml}
            </div>
          `;
          document.getElementById('detalleContent').innerHTML = html;
          new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        })
        .catch(() => Swal.fire('Error', 'No se pudo cargar el detalle', 'error'));
    };

    window.verLogsTraslado = function(id) {
      const modal = new bootstrap.Modal(document.getElementById('modalLogs'));
      const content = document.getElementById('logsContent');
      content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Cargando historial...</p></div>';
      modal.show();

      fetch('/traslados/' + id + '/logs', {
          headers: { 'Accept': 'application/json' }
      })
      .then(r => r.json())
      .then(logs => {
          if (logs.length === 0) {
              content.innerHTML = '<div class="text-center py-4 text-muted"><i class="bi bi-inbox" style="font-size: 2rem;"></i><p class="mt-2">No hay registros de actividad</p></div>';
              return;
          }

          let html = '<div class="timeline">';
          logs.forEach(log => {
              html += `
                  <div class="d-flex mb-3 align-items-start">
                      <div class="me-3 text-center" style="min-width: 40px;">
                          <span class="badge bg-${log.accion_color} rounded-circle p-2">
                              <i class="bi ${log.accion_icon}"></i>
                          </span>
                      </div>
                      <div class="flex-grow-1">
                          <div class="d-flex justify-content-between align-items-center">
                              <strong class="text-${log.accion_color}">${log.accion_label}</strong>
                              <small class="text-muted" title="${log.fecha}">${log.fecha_relativa}</small>
                          </div>
                          <div class="text-muted small">Por: <strong>${log.usuario}</strong></div>`;

              if (log.detalle) {
                  html += '<div class="mt-2 p-2 bg-light rounded small">';
                  if (log.accion === 'creacion') {
                      html += '<div><strong>Origen:</strong> ' + (log.detalle.origen || '-') + '</div>';
                      html += '<div><strong>Destino:</strong> ' + (log.detalle.destino || '-') + '</div>';
                      html += '<div><strong>Tipo:</strong> ' + (log.detalle.tipo_operacion || '-') + '</div>';
                      if (log.detalle.items) {
                          html += '<div class="mt-1"><strong>Items:</strong></div>';
                          html += '<ul class="mb-0 ps-3">';
                          log.detalle.items.forEach(item => {
                              html += '<li>' + item.producto + ' (x' + item.cantidad + ')</li>';
                          });
                          html += '</ul>';
                      }
                  } else if (log.accion === 'edicion') {
                      html += '<div><strong>Editado por:</strong> ' + (log.detalle.editado_por || '-') + '</div>';
                      if (log.detalle.estado_nuevo && log.detalle.estado_nuevo.items) {
                          html += '<div class="mt-1"><strong>Items actualizados:</strong></div>';
                          html += '<ul class="mb-0 ps-3">';
                          log.detalle.estado_nuevo.items.forEach(item => {
                              html += '<li>' + item.producto + ' (x' + item.cantidad + ')</li>';
                          });
                          html += '</ul>';
                      }
                  } else if (log.accion === 'envio') {
                      html += '<div><strong>Enviado por:</strong> ' + (log.detalle.enviado_por || '-') + '</div>';
                      html += '<div><strong>Ruta:</strong> ' + (log.detalle.origen || '') + ' → ' + (log.detalle.destino || '') + '</div>';
                  } else if (log.accion === 'recepcion') {
                      html += '<div><strong>Recibido por:</strong> ' + (log.detalle.recibido_por || '-') + '</div>';
                  } else if (log.accion === 'cancelacion') {
                      html += '<div><strong>Cancelado por:</strong> ' + (log.detalle.cancelado_por || '-') + '</div>';
                      if (log.detalle.stock_devuelto) {
                          html += '<div class="text-warning"><i class="bi bi-arrow-return-left me-1"></i>Stock devuelto al origen</div>';
                      }
                  } else {
                      // Generic: show all keys
                      Object.keys(log.detalle).forEach(key => {
                          html += '<div><strong>' + key + ':</strong> ' + JSON.stringify(log.detalle[key]) + '</div>';
                      });
                  }
                  html += '</div>';
              }

              html += '</div></div>';
          });
          html += '</div>';
          content.innerHTML = html;
      })
      .catch(err => {
          content.innerHTML = '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i><p class="mt-2">Error al cargar el historial</p></div>';
      });
    };
  });
  </script>
  @endpush
</x-app-layout>
