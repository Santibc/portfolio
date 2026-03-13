<x-app-layout>
  <x-slot name="header">Gestión de Cotizaciones</x-slot>

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

      {{-- Alerta de cotizaciones antiguas --}}
      @if(isset($totalAntiguas) && $totalAntiguas > 0)
        <div class="alert alert-warning alert-dismissible fade show mb-4 d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
          <div>
            <strong>Atención:</strong> Hay <strong>{{ $totalAntiguas }}</strong> {{ $totalAntiguas === 1 ? 'cotización pendiente' : 'cotizaciones pendientes' }} con más de 3 días sin procesar.
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-2xl font-semibold mb-0">Listado de Cotizaciones</h4>
            @if(isset($totalAntiguas) && $totalAntiguas > 0)
              <span class="badge bg-danger fs-6">
                <i class="bi bi-clock-history"></i> {{ $totalAntiguas }} pendientes >3 días
              </span>
            @endif
          </div>

          <div class="row mb-3">
            {{-- Filtro de Vendedor (para admin, auxiliar_administrativo e inventarios) --}}
            @if(auth()->user()->hasAnyRole(['admin', 'auxiliar_administrativo', 'inventarios']))
            <div class="col-md-4">
              <label class="form-label">Filtrar por Vendedor:</label>
              <select id="filtroVendedor" class="form-select">
                <option value="">Todos los vendedores</option>
                @foreach($vendedores as $vendedor)
                  <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

            {{-- Filtro de Estado de Pago --}}
            <div class="col-md-3">
              <label class="form-label">Filtrar por Pago:</label>
              <select id="filtroEstadoPago" class="form-select">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="parcial">Parcial</option>
                <option value="pagado">Pagado</option>
                <option value="credito">Crédito</option>
              </select>
            </div>
          </div>

          <table id="solicitudes-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                @if(auth()->user()->hasRole('inventarios'))
                <th>Marca</th>
                @endif
                <th>Acciones</th>
                <th>Nº Cotización</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Fecha</th>
                <th>Items</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Pago</th>
                <th>Descargue</th>
                <th>Envío</th>
                <th>Reserva</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  @push('styles')
  <style>
    /* Estilo para solicitudes antiguas (más de 3 días pendientes) */
    tr.solicitud-antigua {
      background-color: #ffebee !important;
    }

    tr.solicitud-antigua:hover {
      background-color: #ffcdd2 !important;
    }

    /* Asegurar que el color se aplique a todas las celdas */
    tr.solicitud-antigua td {
      background-color: inherit !important;
    }
  </style>
  @endpush

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const table = $('#solicitudes-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: {
        url: "{{ route('solicitudes') }}",
        data: function(d) {
          if ($('#filtroVendedor').length) {
            d.vendedor_id = $('#filtroVendedor').val();
          }
          d.estado_pago = $('#filtroEstadoPago').val();
        }
      },
      columns: [
        @if(auth()->user()->hasRole('inventarios'))
        { data:'marcada_badge', name:'marcada_badge', orderable:false, searchable:false },
        @endif
        { data:'action', orderable:false, searchable:false },
        { data:'numero_solicitud', name:'numero_solicitud' },
        { data:'cliente_nombre', name:'cliente_nombre' },
        { data:'vendedor', name:'vendedor' },
        { data:'fecha', name:'created_at' },
        { data:'total_items', name:'total_items', searchable:false },
        { data:'monto_formateado', name:'monto_total' },
        { data:'estado_badge', name:'estado' },
        { data:'estado_pago_badge', name:'estado_pago', orderable:false, searchable:false },
        { data:'descargue_badge', name:'descargue_badge', orderable:false, searchable:false },
        { data:'estado_envio_badge', name:'estado_envio', orderable:false, searchable:false },
        { data:'reserva_badge', name:'reserva_badge', orderable:false, searchable:false }
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
        },
        {
          text:'<i class="bi bi-funnel"></i> Rechazadas',
          className:'btn btn-outline-danger',
          action: function(e, dt, node, config) {
            if ($(node).hasClass('active')) {
              $(node).removeClass('active');
              dt.column(7).search('').draw();
            } else {
              $(node).addClass('active');
              dt.column(7).search('Rechazada').draw();
            }
          }
        },
        {
          text:'<i class="bi bi-file-earmark-excel"></i> Exportar Todo',
          className:'btn btn-outline-info',
          action: function() {
            $('#modalExportarExcel').modal('show');
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

    // Filtro de vendedor (solo si existe en la página, es decir, si es admin)
    if ($('#filtroVendedor').length) {
      $('#filtroVendedor').on('change', function() {
        table.ajax.reload();
      });
    }

    // Filtro de estado de pago
    $('#filtroEstadoPago').on('change', function() {
      table.ajax.reload();
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
      alert('Error: ' + (xhr.responseJSON?.mensaje || 'Error al aplicar la cotización'));
    });
  }

  function confirmarRechazo(solicitudId) {
    const motivoRechazo = $('#observacionesAdmin').val();

    if (!motivoRechazo || motivoRechazo.trim() === '') {
      alert('Por favor ingrese el motivo del rechazo en el campo de observaciones');
      return;
    }

    if (!confirm('¿Está seguro de rechazar esta cotización?')) {
      return;
    }

    // Mostrar loading
    $('#modalDetalleContent').append(
      '<div class="loading-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:1000;">' +
      '<div class="spinner-border text-danger" role="status"></div></div>'
    );

    $.post(`/solicitudes/${solicitudId}/rechazar`, {
      _token: '{{ csrf_token() }}',
      motivo_rechazo: motivoRechazo
    }, function(response) {
      if (response.success) {
        $('#modalDetalle').modal('hide');

        // Mostrar mensaje de éxito
        const alert = `
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
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
      alert('Error: ' + (xhr.responseJSON?.mensaje || 'Error al rechazar la cotización'));
    });
  }
  
  function confirmarDescontarStock(solicitudId) {
    if (!confirm('¿Está seguro de descontar el stock del inventario para esta cotización?')) {
      return;
    }

    // Mostrar loading
    $('#modalDetalleContent').append(
      '<div class="loading-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:1000;">' +
      '<div class="spinner-border text-warning" role="status"></div></div>'
    );

    $.post(`/solicitudes/${solicitudId}/descontar-stock`, {
      _token: '{{ csrf_token() }}'
    }, function(response) {
      if (response.success) {
        // Recargar el detalle del modal para mostrar el log
        verDetalle(solicitudId);

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
      alert('Error: ' + (xhr.responseJSON?.mensaje || 'Error al descontar stock'));
    });
  }

  // Aprobar un pago pendiente
  function aprobarPago(solicitudId, pagoId) {
    Swal.fire({
      title: '¿Aprobar este pago?',
      text: 'El monto se sumará al total pagado de la cotización',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#198754',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, aprobar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Aprobando...',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); }
        });

        $.post(`/solicitudes/${solicitudId}/pagos/${pagoId}/aprobar`, {
          _token: '{{ csrf_token() }}'
        }, function(response) {
          if (response.success) {
            Swal.fire('Aprobado', response.mensaje, 'success');
            verDetalle(solicitudId);
            $('#solicitudes-table').DataTable().ajax.reload(null, false);
          }
        }).fail(function(xhr) {
          Swal.fire('Error', xhr.responseJSON?.mensaje || 'Error al aprobar el pago', 'error');
        });
      }
    });
  }

  // Rechazar un pago pendiente
  function rechazarPago(solicitudId, pagoId) {
    Swal.fire({
      title: '¿Rechazar este pago?',
      text: 'El pago será marcado como rechazado y no contará hacia el total pagado',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, rechazar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Rechazando...',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); }
        });

        $.post(`/solicitudes/${solicitudId}/pagos/${pagoId}/rechazar`, {
          _token: '{{ csrf_token() }}'
        }, function(response) {
          if (response.success) {
            Swal.fire('Rechazado', response.mensaje, 'success');
            verDetalle(solicitudId);
            $('#solicitudes-table').DataTable().ajax.reload(null, false);
          }
        }).fail(function(xhr) {
          Swal.fire('Error', xhr.responseJSON?.mensaje || 'Error al rechazar el pago', 'error');
        });
      }
    });
  }

  // Función para exportar Excel con filtros
  function exportarExcel() {
    const form = $('#formExportarExcel');
    form.submit();
    $('#modalExportarExcel').modal('hide');
  }

  // Función para clonar cotización
  function clonarSolicitud(solicitudId) {
    Swal.fire({
      title: '¿Clonar cotización?',
      text: 'Se creará una copia de esta cotización con precios y stock actualizados',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, clonar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Clonando...',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); }
        });

        $.post(`/solicitudes/${solicitudId}/clonar`, {
          _token: '{{ csrf_token() }}'
        }, function(response) {
          if (response.success) {
            Swal.fire({
              title: '¡Clonada!',
              text: response.mensaje,
              icon: 'success',
              confirmButtonText: 'Ir a editar',
              showCancelButton: true,
              cancelButtonText: 'Cerrar'
            }).then((result) => {
              if (result.isConfirmed && response.redirect_url) {
                window.location.href = response.redirect_url;
              } else {
                $('#solicitudes-table').DataTable().ajax.reload();
              }
            });
          }
        }).fail(function(xhr) {
          Swal.fire('Error', xhr.responseJSON?.mensaje || 'Error al clonar la cotización', 'error');
        });
      }
    });
  }

  // Función para eliminar cotización
  function eliminarSolicitud(solicitudId) {
    Swal.fire({
      title: '¿Eliminar cotización?',
      text: 'Esta acción no se puede deshacer. Si hay reservas de stock, serán liberadas.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Eliminando...',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
          url: `/solicitudes/${solicitudId}`,
          type: 'DELETE',
          data: { _token: '{{ csrf_token() }}' },
          success: function(response) {
            if (response.success) {
              Swal.fire('Eliminada', response.mensaje, 'success');
              $('#solicitudes-table').DataTable().ajax.reload();
            }
          },
          error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.mensaje || 'Error al eliminar la cotización', 'error');
          }
        });
      }
    });
  }

  // Toggle marcada (corazón) para inventarios
  function toggleMarcada(solicitudId, btn) {
    $.post(`/solicitudes/${solicitudId}/toggle-marcada`, {
      _token: '{{ csrf_token() }}'
    }, function(response) {
      if (response.success) {
        const icon = btn.querySelector('i');
        if (response.marcada) {
          icon.className = 'bi bi-heart-fill text-danger fs-5';
        } else {
          icon.className = 'bi bi-heart text-muted fs-5';
        }
      }
    });
  }

  // Función para gestionar envío
  function gestionarEnvio(solicitudId, estadoActual, numeroGuia, transportadora) {
    const estadosEnvio = {
      'pendiente': 'Pendiente',
      'preparando': 'Preparando',
      'despachado': 'Despachado',
      'en_transito': 'En Tránsito',
      'entregado': 'Entregado'
    };

    let estadoOptions = '';
    for (const [value, label] of Object.entries(estadosEnvio)) {
      const selected = value === estadoActual ? 'selected' : '';
      estadoOptions += `<option value="${value}" ${selected}>${label}</option>`;
    }

    Swal.fire({
      title: 'Gestionar Envío',
      html: `
        <div class="text-start">
          <div class="mb-3">
            <label class="form-label">Estado de Envío</label>
            <select id="swal-estado-envio" class="form-select">
              ${estadoOptions}
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Transportadora</label>
            <input type="text" id="swal-transportadora" class="form-control"
                   value="${transportadora || ''}" placeholder="Ej: Servientrega, Coordinadora...">
          </div>
          <div class="mb-3">
            <label class="form-label">Número de Guía</label>
            <input type="text" id="swal-numero-guia" class="form-control"
                   value="${numeroGuia || ''}" placeholder="Número de seguimiento">
          </div>
          <div class="mb-3">
            <label class="form-label">Archivo de Guía (PDF)</label>
            <input type="file" id="swal-archivo-guia" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
            <small class="text-muted">Opcional: Subir PDF o imagen de la guía</small>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonColor: '#0d6efd',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="bi bi-check"></i> Actualizar Envío',
      cancelButtonText: 'Cancelar',
      preConfirm: () => {
        return {
          estado_envio: document.getElementById('swal-estado-envio').value,
          transportadora: document.getElementById('swal-transportadora').value,
          numero_guia: document.getElementById('swal-numero-guia').value,
          archivo_guia: document.getElementById('swal-archivo-guia').files[0] || null
        };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Actualizando envío...',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); }
        });

        // Crear FormData para enviar archivo
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('estado_envio', result.value.estado_envio);
        formData.append('transportadora', result.value.transportadora);
        formData.append('numero_guia', result.value.numero_guia);
        if (result.value.archivo_guia) {
          formData.append('archivo_guia', result.value.archivo_guia);
        }

        $.ajax({
          url: `/solicitudes/${solicitudId}/envio`,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response.success) {
              Swal.fire({
                title: '¡Actualizado!',
                text: response.mensaje,
                icon: 'success'
              });
              $('#solicitudes-table').DataTable().ajax.reload();
            }
          },
          error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.mensaje || 'Error al actualizar el envío', 'error');
          }
        });
      }
    });
  }

  </script>
  @endpush

  <!-- Modal para ver detalle -->
  <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de Cotización</h5>
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

  <!-- Modal para Exportar Excel -->
  <div class="modal fade" id="modalExportarExcel" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Exportar Cotizaciones a Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="formExportarExcel" action="{{ route('solicitudes.exportar-excel') }}" method="GET">
          <div class="modal-body">
            <p class="text-muted mb-4">
              Este reporte incluirá tres hojas:
              <ul class="small text-muted">
                <li><strong>Resumen:</strong> Información general de cada cotización</li>
                <li><strong>Detalle:</strong> Todos los items de todas las cotizaciones</li>
                <li><strong>Productos:</strong> Resumen de productos más cotizados</li>
              </ul>
            </p>
            
            <div class="mb-3">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                <option value="">Todos los estados</option>
                <option value="pendiente">Solo Pendientes</option>
                <option value="aplicada">Solo Aplicadas</option>
                <option value="rechazada">Solo Rechazadas</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Fecha Desde</label>
              <input type="date" name="fecha_desde" class="form-control">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Fecha Hasta</label>
              <input type="date" name="fecha_hasta" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-download"></i> Descargar Excel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>