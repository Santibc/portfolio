<x-app-layout>
  <x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
      <span>Editar Cotización: {{ $solicitud->numero_solicitud }}</span>
      <button type="button" class="btn btn-outline-warning btn-sm" onclick="verLogsSolicitud({{ $solicitud->id }})">
        <i class="bi bi-clock-history"></i> Ver Logs
      </button>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      {{-- Información del cliente y estado --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
        <div class="p-4">
          <div class="row">
            <div class="col-md-4">
              <h6 class="text-muted mb-2">Cliente</h6>
              <div class="d-flex align-items-center gap-2">
                <div>
                  <p class="mb-0 fw-bold" id="clienteNombreDisplay">{{ $solicitud->cliente->nombre_contacto }}</p>
                  <small class="text-muted" id="clienteEmailDisplay">{{ $solicitud->cliente->email }}</small>
                </div>
                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalCambiarCliente" title="Cambiar Cliente">
                  <i class="bi bi-person-gear"></i>
                </button>
              </div>
              <input type="hidden" name="cliente_id" id="clienteIdInput" form="formEditarSolicitud" value="{{ $solicitud->cliente_id }}">
            </div>
            <div class="col-md-4">
              <h6 class="text-muted mb-2">Lista de Precios</h6>
              <p class="mb-0" id="clienteListaPrecioDisplay">{{ $solicitud->cliente->listaPrecio?->nombre ?? 'Sin lista' }}</p>
            </div>
            <div class="col-md-4">
              <h6 class="text-muted mb-2">Estado de Reserva</h6>
              {!! $solicitud->badge_reserva !!}
              @if($solicitud->tieneReservaActiva())
                <br><small class="text-success">Expira: {{ $solicitud->reserva_expira_en->format('d/m/Y H:i') }}</small>
                <br>
                <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="renovarReserva()">
                  <i class="bi bi-arrow-clockwise"></i> Renovar 24h
                </button>
              @elseif($solicitud->reservaExpirada())
                <br><small class="text-warning">La reserva ha expirado. Al guardar se intentará reservar nuevamente.</small>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Formulario de edición --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <form id="formEditarSolicitud" method="POST">
            @csrf
            @method('PUT')

            {{-- Productos --}}
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Productos de la Cotización</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                  <i class="bi bi-plus-lg"></i> Agregar Producto
                </button>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered" id="tablaItems">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 80px;">Ref.</th>
                      <th>Producto</th>
                      <th>Variante</th>
                      <th style="width: 100px;">Cantidad</th>
                      <th style="width: 120px;">Precio Unit.</th>
                      <th style="width: 120px;">Subtotal</th>
                      <th>Observación</th>
                      <th style="width: 60px;">Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="itemsBody">
                    @foreach($solicitud->items as $index => $item)
                    <tr data-index="{{ $index }}">
                      <td>
                        <code>{{ $item->referencia_producto }}</code>
                        <input type="hidden" name="items[{{ $index }}][producto_id]" value="{{ $item->producto_id }}">
                        <input type="hidden" name="items[{{ $index }}][variante_id]" value="{{ $item->variante_producto_id }}">
                      </td>
                      <td>{{ $item->nombre_producto }}</td>
                      <td>{{ $item->info_variante ?: '-' }}</td>
                      <td>
                        <input type="number" name="items[{{ $index }}][cantidad]" class="form-control form-control-sm cantidad-input"
                               value="{{ $item->cantidad }}" min="1" onchange="actualizarSubtotal(this)">
                      </td>
                      <td>
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">$</span>
                          <input type="number" name="items[{{ $index }}][precio_manual]" class="form-control precio-input"
                                 value="{{ $item->precio_unitario }}" step="0.01" min="0" onchange="actualizarSubtotal(this)"
                                 @if(auth()->user()->hasRole('vendedor')) readonly @endif>
                        </div>
                        @if($item->precio_editado_manualmente)
                          <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Editado</small>
                        @endif
                      </td>
                      <td class="subtotal-cell">
                        ${{ number_format($item->precio_total, 2) }}
                      </td>
                      <td>
                        <input type="text" name="items[{{ $index }}][observacion]" class="form-control form-control-sm"
                               value="{{ $item->observacion }}" placeholder="Observación...">
                      </td>
                      <td>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarItem(this)">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="table-light">
                      <td colspan="6" class="text-end fw-bold">Subtotal:</td>
                      <td colspan="2" class="fw-bold" id="subtotalGeneral">
                        ${{ number_format($solicitud->subtotal, 2) }}
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6" class="text-end">Flete:</td>
                      <td colspan="2">
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">$</span>
                          <input type="number" name="valor_flete" class="form-control" id="valorFlete"
                                 value="{{ $solicitud->valor_flete ?? 0 }}" step="0.01" min="0" onchange="actualizarTotal()">
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6" class="text-end">Descuento:</td>
                      <td colspan="2">
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">$</span>
                          <input type="number" name="descuento_total" class="form-control" id="descuentoTotal"
                                 value="{{ $solicitud->descuento_total ?? 0 }}" step="0.01" min="0" onchange="actualizarTotal()"
                                 @if(auth()->user()->hasRole('vendedor')) readonly @endif>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6" class="text-end">IVA:</td>
                      <td colspan="2">
                        <select name="porcentaje_iva" class="form-select form-select-sm" id="porcentajeIva" onchange="actualizarTotal()">
                          <option value="">Sin IVA</option>
                          <option value="5" {{ ($solicitud->porcentaje_iva == 5) ? 'selected' : '' }}>5%</option>
                          <option value="19" {{ ($solicitud->porcentaje_iva == 19) ? 'selected' : '' }}>19%</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6" class="text-end">Valor IVA:</td>
                      <td colspan="2" id="valorIvaDisplay">
                        ${{ number_format($solicitud->valor_iva ?? 0, 2) }}
                      </td>
                    </tr>
                    <tr class="table-primary">
                      <td colspan="6" class="text-end fw-bold fs-5">TOTAL:</td>
                      <td colspan="2" class="fw-bold fs-5" id="totalGeneral">
                        @php
                          $totalConIva = ($solicitud->monto_total ?? 0) + ($solicitud->valor_iva ?? 0);
                        @endphp
                        ${{ number_format($totalConIva, 2) }}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            {{-- Notas y observaciones --}}
            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label">Notas del Cliente</label>
                <textarea name="notas_cliente" class="form-control" rows="3">{{ $solicitud->notas_cliente }}</textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Observaciones del Vendedor</label>
                <textarea name="observaciones_vendedor" class="form-control" rows="3">{{ $solicitud->observaciones_vendedor }}</textarea>
                <small class="text-muted">Este campo es obligatorio al aprobar la cotización</small>
              </div>
            </div>

            {{-- Botones de acción --}}
            <div class="d-flex justify-content-between">
              <a href="{{ route('solicitudes') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
              </a>
              <div>
                <button type="button" class="btn btn-success" onclick="guardarSolicitud()">
                  <i class="bi bi-check-lg"></i> Guardar Cambios
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal para agregar producto --}}
  <div class="modal fade" id="modalAgregarProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Buscar Producto</label>
            <input type="text" id="buscarProducto" class="form-control" placeholder="Escriba para buscar por nombre o referencia...">
          </div>

          <div id="resultadosProductos" class="list-group" style="max-height: 300px; overflow-y: auto;">
            <!-- Resultados de búsqueda -->
          </div>

          <div id="productoSeleccionado" class="mt-3" style="display: none;">
            <hr>
            <h6 id="nombreProductoSeleccionado"></h6>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Variante (opcional)</label>
                <select id="varianteProducto" class="form-select">
                  <option value="">Sin variante</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" id="cantidadProducto" class="form-control" value="1" min="1">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Precio Unitario</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" id="precioProducto" class="form-control" step="0.01" min="0">
                </div>
                <small class="text-muted">Precio de lista: $<span id="precioLista">0</span></small>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="agregarProducto()" id="btnAgregarProducto" disabled>
            <i class="bi bi-plus-lg"></i> Agregar
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal para cambiar cliente --}}
  <div class="modal fade" id="modalCambiarCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>Cambiar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning small">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Al cambiar el cliente, los precios de los productos se mantendrán como están. Puede ajustarlos manualmente después.
          </div>
          <div class="mb-3">
            <label class="form-label">Buscar Cliente</label>
            <input type="text" id="buscarCliente" class="form-control" placeholder="Escriba nombre, razón social, NIT o email...">
          </div>
          <div id="resultadosClientes" class="list-group" style="max-height: 400px; overflow-y: auto;">
            <!-- Resultados de búsqueda -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal para ver logs --}}
  <div class="modal fade" id="modalLogsSolicitud" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Historial de Cambios</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="logsContentSolicitud">
          <div class="text-center">
            <div class="spinner-border" role="status"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  let itemIndex = {{ $solicitud->items->count() }};
  let productoActual = null;

  // ===================== BÚSQUEDA DE PRODUCTOS =====================
  let searchTimeout;
  $('#buscarProducto').on('input', function() {
    clearTimeout(searchTimeout);
    const query = $(this).val();

    if (query.length < 2) {
      $('#resultadosProductos').html('');
      return;
    }

    searchTimeout = setTimeout(function() {
      $.get('{{ route("solicitudes.productos") }}', { search: query }, function(productos) {
        let html = '';
        productos.forEach(function(producto) {
          const imagen = producto.imagen_principal ?
            `<img src="/imagenes/productos/${producto.id}/${producto.imagen_principal.nombre_archivo}" class="rounded" style="width:40px;height:40px;object-fit:cover;">` :
            '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-image"></i></div>';

          html += `
            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center"
               onclick="seleccionarProducto(${producto.id}, '${producto.referencia}', '${producto.nombre.replace(/'/g, "\\'")}', ${JSON.stringify(producto.variantes || []).replace(/"/g, '&quot;')})">
              ${imagen}
              <div class="ms-3">
                <div class="fw-bold">${producto.nombre}</div>
                <small class="text-muted">Ref: ${producto.referencia}</small>
              </div>
            </a>
          `;
        });

        if (productos.length === 0) {
          html = '<div class="list-group-item text-muted">No se encontraron productos</div>';
        }

        $('#resultadosProductos').html(html);
      });
    }, 300);
  });

  function seleccionarProducto(id, referencia, nombre, variantes) {
    productoActual = { id, referencia, nombre, variantes };

    $('#nombreProductoSeleccionado').text(nombre + ' (' + referencia + ')');
    $('#productoSeleccionado').show();
    $('#btnAgregarProducto').prop('disabled', false);

    let options = '<option value="">Sin variante</option>';
    if (variantes && variantes.length > 0) {
      variantes.forEach(function(v) {
        const info = (v.referencia_variante || '') + ' ' + (v.color || '');
        options += `<option value="${v.id}" data-info="${info.trim()}">${info.trim()}</option>`;
      });
    }
    $('#varianteProducto').html(options);

    obtenerPrecio();
    return false;
  }

  function obtenerPrecio() {
    if (!productoActual) return;

    $.post('{{ route("solicitudes.precio") }}', {
      _token: '{{ csrf_token() }}',
      producto_id: productoActual.id,
      variante_id: $('#varianteProducto').val() || null,
      cliente_id: document.getElementById('clienteIdInput').value
    }, function(data) {
      const precio = parseFloat(data.precio) || 0;
      $('#precioLista').text(precio.toFixed(2));
      $('#precioProducto').val(precio.toFixed(2));
    });
  }

  $('#varianteProducto').on('change', obtenerPrecio);

  function agregarProducto() {
    if (!productoActual) return;

    const varianteId = $('#varianteProducto').val();
    const varianteInfo = $('#varianteProducto option:selected').data('info') || '-';
    const cantidad = parseInt($('#cantidadProducto').val()) || 1;
    const precio = parseFloat($('#precioProducto').val()) || 0;
    const subtotal = cantidad * precio;

    const row = `
      <tr data-index="${itemIndex}">
        <td>
          <code>${productoActual.referencia}</code>
          <input type="hidden" name="items[${itemIndex}][producto_id]" value="${productoActual.id}">
          <input type="hidden" name="items[${itemIndex}][variante_id]" value="${varianteId}">
        </td>
        <td>${productoActual.nombre}</td>
        <td>${varianteInfo}</td>
        <td>
          <input type="number" name="items[${itemIndex}][cantidad]" class="form-control form-control-sm cantidad-input"
                 value="${cantidad}" min="1" onchange="actualizarSubtotal(this)">
        </td>
        <td>
          <div class="input-group input-group-sm">
            <span class="input-group-text">$</span>
            <input type="number" name="items[${itemIndex}][precio_manual]" class="form-control precio-input"
                   value="${precio.toFixed(2)}" step="0.01" min="0" onchange="actualizarSubtotal(this)"
                   @if(auth()->user()->hasRole('vendedor')) readonly @endif>
          </div>
        </td>
        <td class="subtotal-cell">$${subtotal.toFixed(2)}</td>
        <td>
          <input type="text" name="items[${itemIndex}][observacion]" class="form-control form-control-sm" placeholder="Observación...">
        </td>
        <td>
          <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarItem(this)">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
    `;

    $('#itemsBody').append(row);
    itemIndex++;

    productoActual = null;
    $('#buscarProducto').val('');
    $('#resultadosProductos').html('');
    $('#productoSeleccionado').hide();
    $('#btnAgregarProducto').prop('disabled', true);
    $('#modalAgregarProducto').modal('hide');
    actualizarTotal();
  }

  function eliminarItem(btn) {
    $(btn).closest('tr').remove();
    actualizarTotal();
  }

  function actualizarSubtotal(input) {
    const row = $(input).closest('tr');
    const cantidad = parseFloat(row.find('.cantidad-input').val()) || 0;
    const precio = parseFloat(row.find('.precio-input').val()) || 0;
    const subtotal = cantidad * precio;

    row.find('.subtotal-cell').text('$' + subtotal.toFixed(2));
    actualizarTotal();
  }

  function actualizarTotal() {
    let subtotal = 0;

    $('#itemsBody tr').each(function() {
      const cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
      const precio = parseFloat($(this).find('.precio-input').val()) || 0;
      subtotal += cantidad * precio;
    });

    const flete = parseFloat($('#valorFlete').val()) || 0;
    const descuento = parseFloat($('#descuentoTotal').val()) || 0;
    const porcentajeIva = parseFloat($('#porcentajeIva').val()) || 0;

    const valorIva = subtotal * (porcentajeIva / 100);
    const totalSinIva = subtotal + flete - descuento;
    const total = totalSinIva + valorIva;

    $('#subtotalGeneral').text('$' + subtotal.toFixed(2));
    $('#valorIvaDisplay').text('$' + valorIva.toFixed(2));
    $('#totalGeneral').text('$' + total.toFixed(2));
  }

  function guardarSolicitud() {
    if ($('#itemsBody tr').length === 0) {
      Swal.fire('Error', 'Debe agregar al menos un producto a la cotización', 'error');
      return;
    }

    Swal.fire({
      title: 'Guardando...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    const formData = new FormData($('#formEditarSolicitud')[0]);

    $.ajax({
      url: '{{ route("solicitudes.update", $solicitud->id) }}',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.success) {
          Swal.fire({
            title: 'Guardado',
            text: response.mensaje,
            icon: 'success'
          }).then(() => {
            window.location.href = '{{ route("solicitudes") }}';
          });
        }
      },
      error: function(xhr) {
        Swal.fire('Error', xhr.responseJSON?.mensaje || 'Error al guardar la cotización', 'error');
      }
    });
  }

  function renovarReserva() {
    Swal.fire({
      title: 'Renovando reserva...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.post('{{ route("solicitudes.renovar-reserva", $solicitud->id) }}', {
      _token: '{{ csrf_token() }}'
    }, function(response) {
      if (response.success) {
        Swal.fire('Renovada', response.mensaje, 'success').then(() => {
          location.reload();
        });
      }
    }).fail(function(xhr) {
      Swal.fire('Error', xhr.responseJSON?.mensaje || 'Error al renovar la reserva', 'error');
    });
  }

  // ===================== BÚSQUEDA DE CLIENTES =====================
  let clienteSearchTimeout;
  $('#buscarCliente').on('input', function() {
    clearTimeout(clienteSearchTimeout);
    const query = $(this).val();

    if (query.length < 2) {
      $('#resultadosClientes').html('');
      return;
    }

    clienteSearchTimeout = setTimeout(function() {
      $.get('{{ route("solicitudes.buscar-clientes") }}', { search: query }, function(clientes) {
        let html = '';
        clientes.forEach(function(cliente) {
          html += `
            <a href="#" class="list-group-item list-group-item-action" onclick="seleccionarCliente(${cliente.id}, '${(cliente.nombre_contacto || '').replace(/'/g, "\\'")}', '${(cliente.email || '').replace(/'/g, "\\'")}', '${(cliente.lista_precio || '').replace(/'/g, "\\'")}')">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold">${cliente.nombre_contacto}</div>
                  <small class="text-muted">${cliente.razon_social || ''} ${cliente.nit ? '- NIT: ' + cliente.nit : ''}</small>
                </div>
                <span class="badge bg-info">${cliente.lista_precio}</span>
              </div>
              <small class="text-muted">${cliente.email || ''}</small>
            </a>
          `;
        });

        if (clientes.length === 0) {
          html = '<div class="list-group-item text-muted">No se encontraron clientes</div>';
        }

        $('#resultadosClientes').html(html);
      });
    }, 300);
  });

  function seleccionarCliente(id, nombre, email, listaPrecio) {
    const clienteAnterior = $('#clienteNombreDisplay').text();

    $('#clienteIdInput').val(id);
    $('#clienteNombreDisplay').text(nombre);
    $('#clienteEmailDisplay').text(email);
    $('#clienteListaPrecioDisplay').text(listaPrecio);

    // Limpiar modal
    $('#buscarCliente').val('');
    $('#resultadosClientes').html('');
    $('#modalCambiarCliente').modal('hide');

    // Notificar cambio
    Swal.fire({
      title: 'Cliente cambiado',
      html: `<p>Se cambió de <strong>${clienteAnterior}</strong> a <strong>${nombre}</strong>.</p>
             <small class="text-muted">Los precios se mantendrán. Puede ajustarlos manualmente.</small>`,
      icon: 'info',
      timer: 3000,
      showConfirmButton: true,
      confirmButtonText: 'Entendido'
    });

    return false;
  }

  // ===================== LOGS =====================
  function verLogsSolicitud(solicitudId) {
    $('#logsContentSolicitud').html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');
    $('#modalLogsSolicitud').modal('show');

    fetch(`/solicitudes/${solicitudId}/logs`)
      .then(res => res.json())
      .then(logs => {
        if (logs.length === 0) {
          $('#logsContentSolicitud').html('<div class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No hay registros de cambios aún.</div>');
          return;
        }

        let html = '<div class="timeline">';
        logs.forEach(function(log) {
          html += `
            <div class="d-flex align-items-start mb-3 border-start border-3 border-${log.accion_color} ps-3">
              <div class="w-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span>
                    <i class="bi ${log.accion_icon} text-${log.accion_color} me-1"></i>
                    <strong class="text-${log.accion_color}">${log.accion_label}</strong>
                    <span class="text-muted ms-2">por ${log.usuario}</span>
                  </span>
                  <small class="text-muted" title="${log.fecha}">${log.fecha_relativa}</small>
                </div>`;

          if (log.detalle) {
            // Cambios en campos
            if (log.detalle.cambios && log.detalle.cambios.length > 0) {
              html += '<div class="mt-2">';
              log.detalle.cambios.forEach(function(c) {
                html += `<div class="small mb-1">
                  <span class="badge bg-light text-dark">${c.campo}</span>
                  <span class="text-danger text-decoration-line-through">${c.anterior ?? '-'}</span>
                  <i class="bi bi-arrow-right mx-1"></i>
                  <span class="text-success">${c.nuevo ?? '-'}</span>
                </div>`;
              });
              html += '</div>';
            }

            // Items agregados
            if (log.detalle.items_agregados && log.detalle.items_agregados.length > 0) {
              html += '<div class="mt-2"><small class="text-success fw-bold"><i class="bi bi-plus-circle me-1"></i>Productos agregados:</small><ul class="list-unstyled ms-3 mb-0">';
              log.detalle.items_agregados.forEach(function(item) {
                html += `<li class="small text-success">+ ${item.referencia} - ${item.producto} ${item.variante !== '-' ? '(' + item.variante + ')' : ''} x${item.cantidad} @ $${parseFloat(item.precio).toFixed(2)}</li>`;
              });
              html += '</ul></div>';
            }

            // Items eliminados
            if (log.detalle.items_eliminados && log.detalle.items_eliminados.length > 0) {
              html += '<div class="mt-2"><small class="text-danger fw-bold"><i class="bi bi-dash-circle me-1"></i>Productos eliminados:</small><ul class="list-unstyled ms-3 mb-0">';
              log.detalle.items_eliminados.forEach(function(item) {
                html += `<li class="small text-danger">- ${item.referencia} - ${item.producto} ${item.variante !== '-' ? '(' + item.variante + ')' : ''} x${item.cantidad} @ $${parseFloat(item.precio).toFixed(2)}</li>`;
              });
              html += '</ul></div>';
            }

            // Items modificados
            if (log.detalle.items_modificados && log.detalle.items_modificados.length > 0) {
              html += '<div class="mt-2"><small class="text-warning fw-bold"><i class="bi bi-pencil me-1"></i>Productos modificados:</small>';
              log.detalle.items_modificados.forEach(function(item) {
                html += `<div class="ms-3 small"><strong>${item.referencia} - ${item.producto}</strong> ${item.variante !== '-' ? '(' + item.variante + ')' : ''}`;
                item.cambios.forEach(function(c) {
                  html += `<div class="ms-2">
                    <span class="badge bg-light text-dark">${c.campo}</span>
                    <span class="text-danger">${c.anterior}</span>
                    <i class="bi bi-arrow-right mx-1"></i>
                    <span class="text-success">${c.nuevo}</span>
                  </div>`;
                });
                html += '</div>';
              });
              html += '</div>';
            }
          }

          html += `
                <small class="text-muted d-block mt-1">${log.fecha}</small>
              </div>
            </div>`;
        });
        html += '</div>';

        $('#logsContentSolicitud').html(html);
      })
      .catch(() => {
        $('#logsContentSolicitud').html('<div class="alert alert-danger">Error al cargar los logs</div>');
      });
  }
  </script>
  @endpush
</x-app-layout>
