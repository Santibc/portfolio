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

          {{-- Filtros --}}
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label small mb-1">Cliente</label>
              <select id="filtroCliente" class="form-select cliente-select2-ajax"
                      data-placeholder="Todos los clientes">
                <option value=""></option>
              </select>
            </div>
            @if(auth()->user()->hasRole('admin'))
            <div class="col-md-3">
              <label class="form-label small mb-1">Vendedor</label>
              <select id="filtroVendedor" class="form-select select2-search"
                      data-placeholder="Todos los vendedores" data-allow-clear="1">
                <option value=""></option>
                @foreach($vendedores as $vendedor)
                  <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="col-md-{{ auth()->user()->hasRole('admin') ? '2' : '3' }}">
              <label class="form-label small mb-1">Fecha desde</label>
              <input type="date" id="filtroFechaDesde" class="form-control">
            </div>
            <div class="col-md-{{ auth()->user()->hasRole('admin') ? '2' : '3' }}">
              <label class="form-label small mb-1">Fecha hasta</label>
              <input type="date" id="filtroFechaHasta" class="form-control">
            </div>
            <div class="col-md-1 d-flex align-items-end">
              <button type="button" id="btnLimpiarFiltros" class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                <i class="bi bi-x-circle"></i>
              </button>
            </div>
          </div>

          <table id="solicitudes-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>Nº Cotización</th>
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
          d.cliente_id    = $('#filtroCliente').val();
          d.fecha_desde   = $('#filtroFechaDesde').val();
          d.fecha_hasta   = $('#filtroFechaHasta').val();
          // Solo enviar filtro de vendedor si existe (es decir, si es admin)
          if ($('#filtroVendedor').length) {
            d.vendedor_id = $('#filtroVendedor').val();
          }
        }
      },
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

    // Cliente con buscador AJAX
    var $filtroCliente = $('#filtroCliente');
    $filtroCliente.select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: $filtroCliente.data('placeholder'),
      allowClear: true,
      ajax: {
        url: "{{ route('clientes.buscar-ajax') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) { return { q: params.term, page: params.page || 1 }; },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return data;
        },
        cache: true
      }
    });

    // Auto-aplicar todos los filtros al cambiar
    $('#filtroCliente, #filtroVendedor, #filtroFechaDesde, #filtroFechaHasta').on('change', function() {
      table.ajax.reload();
    });

    // Limpiar filtros
    $('#btnLimpiarFiltros').on('click', function() {
      $('#filtroCliente').val(null).trigger('change');
      $('#filtroVendedor').val(null).trigger('change');
      $('#filtroFechaDesde').val('');
      $('#filtroFechaHasta').val('');
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
  
  // Función para exportar Excel con filtros
  function exportarExcel() {
    const form = $('#formExportarExcel');
    form.submit();
    $('#modalExportarExcel').modal('hide');
  }

  // ===== Edición de cotización =====
  function editarCotizacion(solicitudId) {
    $('#modalEditarContent').html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
    $('#modalEditar').modal('show');

    $.get(`/solicitudes/${solicitudId}/editar`, function(html) {
      $('#modalEditarContent').html(html);
      bindEdicionEventos(solicitudId);
    }).fail(function(xhr) {
      $('#modalEditarContent').html(
        '<div class="alert alert-danger">' +
        (xhr.responseJSON?.error || 'Error al cargar el formulario.') + '</div>'
      );
    });
  }

  function recalcularSubtotalesEdicion() {
    let total = 0;
    $('#tablaItemsEditar tbody tr').each(function() {
      const $tr = $(this);
      const cantidad = parseFloat($tr.find('.item-cantidad').val()) || 0;
      const precio = parseFloat($tr.find('.item-precio').val()) || 0;
      const sub = cantidad * precio;
      $tr.find('.item-subtotal').text('$' + sub.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
      total += sub;
    });
    $('#editarMontoTotal').text('$' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
  }

  function bindEdicionEventos(solicitudId) {
    // Recalcular subtotales en vivo
    $('#tablaItemsEditar').on('input', '.item-cantidad, .item-precio', recalcularSubtotalesEdicion);

    // Eliminar item
    $('#tablaItemsEditar').on('click', '.btn-eliminar-item', function() {
      const itemId = $(this).data('item-id');
      const $tr = $(this).closest('tr');
      if (!confirm('¿Eliminar este producto de la cotización?')) return;

      $.ajax({
        url: `/solicitudes/${solicitudId}/items/${itemId}`,
        method: 'DELETE',
        data: { _token: '{{ csrf_token() }}' }
      }).done(function(resp) {
        if (resp.success) {
          $tr.remove();
          recalcularSubtotalesEdicion();
        } else {
          alert(resp.mensaje || 'No se pudo eliminar.');
        }
      }).fail(function(xhr) {
        alert(xhr.responseJSON?.mensaje || 'Error al eliminar.');
      });
    });

    // Buscador de productos (autocomplete)
    let buscarTimer = null;
    $('#buscarProductoInput').on('input', function() {
      const q = $(this).val();
      $('#nuevoProductoId').val('');
      $('#nuevoVarianteId').val('');
      $('#btnAgregarItem').prop('disabled', true);

      if (q.length < 2) {
        $('#buscarProductoResultados').empty();
        return;
      }
      clearTimeout(buscarTimer);
      buscarTimer = setTimeout(() => {
        $.get('/solicitudes/buscar-productos', { q: q, solicitud_id: solicitudId }, function(items) {
          const $list = $('#buscarProductoResultados').empty();
          if (!items.length) {
            $list.append('<div class="list-group-item text-muted small">Sin resultados</div>');
            return;
          }
          items.forEach(it => {
            const $a = $('<a href="#" class="list-group-item list-group-item-action small"></a>')
              .text(it.label)
              .on('click', function(ev) {
                ev.preventDefault();
                $('#buscarProductoInput').val(it.label);
                $('#nuevoProductoId').val(it.id);
                $('#nuevoVarianteId').val(it.variante_producto_id || '');
                $('#btnAgregarItem').prop('disabled', false);
                $('#buscarProductoResultados').empty();

                // Autocompletar precio si admin (campo existe) y la búsqueda lo trajo
                if (it.precio !== null && it.precio !== undefined) {
                  $('#nuevoPrecio').val(parseFloat(it.precio).toFixed(2));
                  $('#nuevoPrecioHint').text('Precio según lista del cliente: $' +
                    parseFloat(it.precio).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                } else {
                  $('#nuevoPrecio').val('');
                  $('#nuevoPrecioHint').text('Sin precio en la lista del cliente — ingresa uno manualmente.');
                }
              });
            $list.append($a);
          });
        });
      }, 250);
    });

    // Cerrar lista al hacer click fuera
    $(document).on('click.editarBuscar', function(e) {
      if (!$(e.target).closest('#buscarProductoInput, #buscarProductoResultados').length) {
        $('#buscarProductoResultados').empty();
      }
    });

    // Agregar item
    $('#btnAgregarItem').on('click', function() {
      const productoId = $('#nuevoProductoId').val();
      const varianteId = $('#nuevoVarianteId').val();
      const cantidad = parseInt($('#nuevoCantidad').val()) || 0;
      const precio = $('#nuevoPrecio').val();

      if (!productoId || cantidad < 1) {
        alert('Selecciona un producto y una cantidad válida.');
        return;
      }

      const data = {
        _token: '{{ csrf_token() }}',
        producto_id: productoId,
        variante_producto_id: varianteId || null,
        cantidad: cantidad,
      };
      if (precio) data.precio_unitario = precio;

      $.post(`/solicitudes/${solicitudId}/items`, data, function(resp) {
        if (resp.success) {
          // Recargar el modal entero para reflejar el nuevo item
          editarCotizacion(solicitudId);
        } else {
          alert(resp.mensaje || 'No se pudo agregar.');
        }
      }).fail(function(xhr) {
        alert(xhr.responseJSON?.mensaje || 'Error al agregar producto.');
      });
    });

    // Guardar cambios (notas + items existentes)
    $('#btnGuardarEdicion').on('click', function() {
      const $btn = $(this);
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');

      const formData = $('#formEditarCotizacion').serialize();

      $.ajax({
        url: `/solicitudes/${solicitudId}`,
        method: 'POST',
        data: formData + '&_method=PUT',
      }).done(function(resp) {
        if (resp.success) {
          $('#modalEditar').modal('hide');
          $('.max-w-7xl').prepend(
            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            resp.mensaje +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
          );
          $('#solicitudes-table').DataTable().ajax.reload(null, false);
        } else {
          alert(resp.mensaje || 'No se pudo guardar.');
        }
      }).fail(function(xhr) {
        alert(xhr.responseJSON?.mensaje || 'Error al guardar.');
      }).always(function() {
        $btn.prop('disabled', false).html('<i class="bi bi-save"></i> Guardar cambios');
      });
    });
  }
  </script>
  @endpush

  <!-- Modal para editar cotización -->
  <div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Cotización</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="modalEditarContent">
          <div class="text-center">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

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