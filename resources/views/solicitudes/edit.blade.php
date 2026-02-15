<x-app-layout>
  <x-slot name="header">
    Editar Cotización: {{ $solicitud->numero_solicitud }}
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      {{-- Información del cliente y estado --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
        <div class="p-4">
          <div class="row">
            <div class="col-md-4">
              <h6 class="text-muted mb-2">Cliente</h6>
              <p class="mb-0 fw-bold">{{ $solicitud->cliente->nombre_contacto }}</p>
              <small class="text-muted">{{ $solicitud->cliente->email }}</small>
            </div>
            <div class="col-md-4">
              <h6 class="text-muted mb-2">Lista de Precios</h6>
              <p class="mb-0">{{ $solicitud->cliente->listaPrecio?->nombre ?? 'Sin lista' }}</p>
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
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarItem(this)">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="table-light">
                      <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                      <td colspan="2" class="fw-bold" id="subtotalGeneral">
                        ${{ number_format($solicitud->subtotal, 2) }}
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5" class="text-end">Flete:</td>
                      <td colspan="2">
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">$</span>
                          <input type="number" name="valor_flete" class="form-control" id="valorFlete"
                                 value="{{ $solicitud->valor_flete ?? 0 }}" step="0.01" min="0" onchange="actualizarTotal()">
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5" class="text-end">Descuento:</td>
                      <td colspan="2">
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">$</span>
                          <input type="number" name="descuento_total" class="form-control" id="descuentoTotal"
                                 value="{{ $solicitud->descuento_total ?? 0 }}" step="0.01" min="0" onchange="actualizarTotal()">
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5" class="text-end">IVA:</td>
                      <td colspan="2">
                        <select name="porcentaje_iva" class="form-select form-select-sm" id="porcentajeIva" onchange="actualizarTotal()">
                          <option value="">Sin IVA</option>
                          <option value="5" {{ ($solicitud->porcentaje_iva == 5) ? 'selected' : '' }}>5%</option>
                          <option value="19" {{ ($solicitud->porcentaje_iva == 19) ? 'selected' : '' }}>19%</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5" class="text-end">Valor IVA:</td>
                      <td colspan="2" id="valorIvaDisplay">
                        ${{ number_format($solicitud->valor_iva ?? 0, 2) }}
                      </td>
                    </tr>
                    <tr class="table-primary">
                      <td colspan="5" class="text-end fw-bold fs-5">TOTAL:</td>
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

  @push('scripts')
  <script>
  let itemIndex = {{ $solicitud->items->count() }};
  let productoActual = null;

  // Buscar productos
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

  // Seleccionar producto
  function seleccionarProducto(id, referencia, nombre, variantes) {
    productoActual = { id, referencia, nombre, variantes };

    $('#nombreProductoSeleccionado').text(nombre + ' (' + referencia + ')');
    $('#productoSeleccionado').show();
    $('#btnAgregarProducto').prop('disabled', false);

    // Cargar variantes
    let options = '<option value="">Sin variante</option>';
    if (variantes && variantes.length > 0) {
      variantes.forEach(function(v) {
        const info = (v.referencia_variante || '') + ' ' + (v.color || '');
        options += `<option value="${v.id}" data-info="${info.trim()}">${info.trim()}</option>`;
      });
    }
    $('#varianteProducto').html(options);

    // Obtener precio
    obtenerPrecio();

    return false;
  }

  // Obtener precio según cliente
  function obtenerPrecio() {
    if (!productoActual) return;

    $.post('{{ route("solicitudes.precio") }}', {
      _token: '{{ csrf_token() }}',
      producto_id: productoActual.id,
      variante_id: $('#varianteProducto').val() || null,
      cliente_id: {{ $solicitud->cliente_id }}
    }, function(data) {
      const precio = parseFloat(data.precio) || 0;
      $('#precioLista').text(precio.toFixed(2));
      $('#precioProducto').val(precio.toFixed(2));
    });
  }

  $('#varianteProducto').on('change', obtenerPrecio);

  // Agregar producto a la tabla
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
          <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarItem(this)">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
    `;

    $('#itemsBody').append(row);
    itemIndex++;

    // Limpiar modal
    productoActual = null;
    $('#buscarProducto').val('');
    $('#resultadosProductos').html('');
    $('#productoSeleccionado').hide();
    $('#btnAgregarProducto').prop('disabled', true);

    $('#modalAgregarProducto').modal('hide');

    actualizarTotal();
  }

  // Eliminar item
  function eliminarItem(btn) {
    $(btn).closest('tr').remove();
    actualizarTotal();
  }

  // Actualizar subtotal de una fila
  function actualizarSubtotal(input) {
    const row = $(input).closest('tr');
    const cantidad = parseFloat(row.find('.cantidad-input').val()) || 0;
    const precio = parseFloat(row.find('.precio-input').val()) || 0;
    const subtotal = cantidad * precio;

    row.find('.subtotal-cell').text('$' + subtotal.toFixed(2));
    actualizarTotal();
  }

  // Actualizar total general
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

    // El IVA se calcula sobre el subtotal de productos (sin flete ni descuento)
    const valorIva = subtotal * (porcentajeIva / 100);
    const totalSinIva = subtotal + flete - descuento;
    const total = totalSinIva + valorIva;

    $('#subtotalGeneral').text('$' + subtotal.toFixed(2));
    $('#valorIvaDisplay').text('$' + valorIva.toFixed(2));
    $('#totalGeneral').text('$' + total.toFixed(2));
  }

  // Guardar solicitud
  function guardarSolicitud() {
    // Verificar que haya al menos un item
    if ($('#itemsBody tr').length === 0) {
      Swal.fire('Error', 'Debe agregar al menos un producto a la cotización', 'error');
      return;
    }

    Swal.fire({
      title: 'Guardando...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    // Recolectar datos del formulario
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

  // Renovar reserva
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
  </script>
  @endpush
</x-app-layout>
