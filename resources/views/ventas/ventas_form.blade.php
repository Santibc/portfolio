<x-app-layout>
    <x-slot name="header">
      @if ($venta->exists)
        Editar Venta
      @else
        {{ $esAdmin ? 'Nueva Venta' : 'Registrar Venta en Tienda' }}
      @endif
    </x-slot>

    @php
        $itemsExistentes = $venta->exists ? $venta->items->toArray() : [];
    @endphp

    <div class="py-6">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('ventas.guardar') }}" id="ventaForm">
          @csrf
          @if ($venta->exists)
            <input type="hidden" name="id" value="{{ $venta->id }}">
          @endif

          {{-- ============= HEADER — Card del vendedor ============= --}}
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <div class="row g-3 align-items-center">
                <div class="col-md-auto">
                  <div id="vendedorAvatar"
                       class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold"
                       style="width:64px; height:64px; font-size:1.5rem;">
                    <i class="bi bi-person"></i>
                  </div>
                </div>
                <div class="col-md">
                  <div class="text-muted small text-uppercase mb-1">
                    Vendedor <span class="text-danger">*</span>
                    @if (!$esAdmin)
                      <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1">Tú</span>
                    @endif
                  </div>
                  <select name="user_id" id="vendedorSelect" class="form-select form-select-lg" required
                          {{ $esAdmin ? '' : 'disabled' }}>
                    @if ($esAdmin)
                      <option value="">Seleccione un vendedor</option>
                    @endif
                    @foreach ($vendedores as $v)
                      <option value="{{ $v->id }}"
                              data-nombre="{{ $v->name }}"
                              data-almacen-id="{{ $v->almacen_id }}"
                              data-almacen-nombre="{{ $v->almacen?->nombre }}"
                              {{ old('user_id', $venta->user_id) == $v->id ? 'selected' : '' }}>
                        {{ $v->name }}{{ $v->almacen ? ' — '.$v->almacen->nombre : '' }}
                      </option>
                    @endforeach
                  </select>
                  @if (!$esAdmin)
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                  @endif
                  <div class="mt-2 small">
                    <span class="text-muted">Almacén:</span>
                    <span id="vendedorAlmacenLabel" class="fw-semibold">—</span>
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="form-label small text-uppercase text-muted mb-1">Fecha <span class="text-danger">*</span></label>
                  <input type="date" name="fecha" class="form-control" required
                         max="{{ now()->format('Y-m-d') }}"
                         value="{{ old('fecha', optional($venta->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                </div>
              </div>

              <div class="row g-3 mt-1">
                <div class="col-md-{{ $esAdmin ? 6 : 12 }}">
                  <label class="form-label small text-uppercase text-muted mb-1">Cliente <span class="text-muted">(opcional)</span></label>
                  <select name="cliente_id" id="clienteSelect" class="form-select">
                    <option value="">Sin cliente asociado</option>
                    @if ($venta->exists && $venta->cliente)
                      <option value="{{ $venta->cliente->id }}" selected>{{ $venta->cliente->nombre_contacto }}</option>
                    @endif
                  </select>
                  <small class="text-muted">Si eliges cliente, se sugiere precio según su lista.</small>
                </div>
                @if ($esAdmin)
                <div class="col-md-6">
                  <label class="form-label small text-uppercase text-muted mb-1">Almacén <span class="text-muted">(sobrescribe el del vendedor)</span></label>
                  <select name="almacen_id" id="almacenSelect" class="form-select">
                    <option value="">Heredar del vendedor</option>
                    @foreach ($almacenes as $id => $nombre)
                      <option value="{{ $id }}" {{ old('almacen_id', $venta->almacen_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                  </select>
                </div>
                @endif
              </div>
            </div>
          </div>

          {{-- ============= PRODUCTOS ============= --}}
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Productos vendidos</h5>
                <span class="text-muted small">Busca por referencia o nombre y agrega líneas.</span>
              </div>

              {{-- Buscador --}}
              <div class="row g-2 align-items-end mb-3">
                <div class="col-md-7 position-relative">
                  <label class="form-label small mb-1">Buscar producto</label>
                  <input type="text" id="buscarProductoInput" class="form-control"
                         placeholder="Escribe al menos 2 caracteres (referencia o nombre)..."
                         autocomplete="off">
                  <div id="buscarProductoResultados" class="list-group position-absolute w-100"
                       style="z-index:1080; max-height:280px; overflow-y:auto;"></div>
                </div>
                <div class="col-md-2">
                  <label class="form-label small mb-1">Cantidad</label>
                  <input type="number" id="nuevoCantidad" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-2">
                  <label class="form-label small mb-1">Precio unit.</label>
                  <input type="number" id="nuevoPrecio" class="form-control" step="0.01" min="0" placeholder="—">
                </div>
                <div class="col-md-1">
                  <button type="button" class="btn btn-success w-100" id="btnAgregarItem" disabled>
                    <i class="bi bi-plus"></i>
                  </button>
                </div>
              </div>
              <input type="hidden" id="nuevoProductoId">
              <input type="hidden" id="nuevoProductoRef">
              <input type="hidden" id="nuevoProductoNombre">

              {{-- Tabla de items --}}
              <div class="table-responsive">
                <table class="table align-middle mb-0" id="tablaItems">
                  <thead class="table-light">
                    <tr>
                      <th style="width:120px">Referencia</th>
                      <th>Producto</th>
                      <th style="width:110px" class="text-center">Cantidad</th>
                      <th style="width:140px" class="text-end">Precio unit.</th>
                      <th style="width:150px" class="text-end">Subtotal</th>
                      <th style="width:50px"></th>
                    </tr>
                  </thead>
                  <tbody id="itemsBody">
                    <tr id="itemsVacio">
                      <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-basket3 fs-1 d-block mb-2"></i>
                        Aún no has agregado productos.<br>
                        <small>Puedes dejarlo vacío y usar el "monto manual" abajo.</small>
                      </td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr class="table-light">
                      <th colspan="4" class="text-end">Total:</th>
                      <th class="text-end fs-5"><span id="totalVenta">$ 0</span></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          {{-- ============= EXTRAS ============= --}}
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label small text-uppercase text-muted mb-1">
                    Monto manual <span class="text-muted">(si no agregas productos)</span>
                  </label>
                  <input type="number" name="monto_manual" id="montoManual" class="form-control"
                         step="0.01" min="0"
                         value="{{ old('monto_manual', $venta->exists && $itemsExistentes === [] ? $venta->monto : '') }}"
                         placeholder="0">
                  <small class="text-muted">Solo se usa si la tabla de productos está vacía.</small>
                </div>
                <div class="col-md-8">
                  <label class="form-label small text-uppercase text-muted mb-1">Descripción / Notas</label>
                  <input type="text" name="descripcion" class="form-control" maxlength="255"
                         value="{{ old('descripcion', $venta->descripcion) }}"
                         placeholder="Ej. Venta cámaras IP para proyecto XYZ">
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('ventas') }}" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left"></i> Volver
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-save"></i> Guardar venta
            </button>
          </div>
        </form>
      </div>
    </div>

    @push('scripts')
    <script>
    (function () {
      const fmtCOP = v => '$ ' + new Intl.NumberFormat('es-CO').format(Math.round(Number(v) || 0));

      // ============= Vendedor: iniciales + almacén =============
      function pintarVendedor() {
        const opt = $('#vendedorSelect').find(':selected');
        const nombre = opt.data('nombre') || '';
        const almacen = opt.data('almacen-nombre') || '';
        const iniciales = nombre.split(' ').filter(Boolean).slice(0,2).map(w => w[0]).join('').toUpperCase();
        const avatar = document.getElementById('vendedorAvatar');
        if (iniciales) {
          avatar.innerHTML = iniciales;
        } else {
          avatar.innerHTML = '<i class="bi bi-person"></i>';
        }
        document.getElementById('vendedorAlmacenLabel').textContent = almacen || 'Sin almacén asignado';
      }
      $('#vendedorSelect').on('change', pintarVendedor);
      pintarVendedor();

      // ============= Select2 =============
      $('#vendedorSelect').select2({ theme: 'bootstrap-5', width: '100%' });
      if ($('#almacenSelect').length) {
        $('#almacenSelect').select2({ theme: 'bootstrap-5', width: '100%' });
      }
      $('#vendedorSelect').on('select2:select', pintarVendedor);

      // Cliente Select2 con AJAX
      $('#clienteSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'Sin cliente asociado',
        allowClear: true,
        width: '100%',
        ajax: {
          url: "{{ route('clientes.buscar-ajax') }}",
          dataType: 'json',
          delay: 250,
          data: params => ({ q: params.term, page: params.page || 1 }),
          processResults: (data, params) => ({
            results: data.results,
            pagination: data.pagination
          })
        }
      });

      // ============= Buscador de productos =============
      let searchTimer = null;
      $('#buscarProductoInput').on('input', function () {
        const q = this.value.trim();
        clearTimeout(searchTimer);
        if (q.length < 2) {
          $('#buscarProductoResultados').empty();
          return;
        }
        searchTimer = setTimeout(() => buscarProductos(q), 250);
      });

      function buscarProductos(q) {
        const clienteId = $('#clienteSelect').val();
        $.get("{{ route('ventas.buscar-productos') }}", { q, cliente_id: clienteId }, function (rows) {
          const c = $('#buscarProductoResultados').empty();
          if (!rows.length) {
            c.append('<div class="list-group-item text-muted">Sin resultados</div>');
            return;
          }
          rows.forEach(p => {
            const item = $(`
              <button type="button" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between">
                  <div>
                    <code class="text-primary">${p.referencia || ''}</code>
                    <span class="ms-2">${p.nombre}</span>
                    ${p.marca ? `<small class="text-muted ms-2">${p.marca}</small>` : ''}
                  </div>
                  <div class="fw-semibold text-success">${fmtCOP(p.precio_sugerido)}</div>
                </div>
              </button>
            `);
            item.on('click', () => seleccionarProducto(p));
            c.append(item);
          });
        });
      }

      function seleccionarProducto(p) {
        $('#nuevoProductoId').val(p.id);
        $('#nuevoProductoRef').val(p.referencia);
        $('#nuevoProductoNombre').val(p.nombre);
        $('#buscarProductoInput').val(`[${p.referencia}] ${p.nombre}`);
        $('#nuevoPrecio').val(p.precio_sugerido || '');
        $('#nuevoCantidad').val(1).focus().select();
        $('#buscarProductoResultados').empty();
        $('#btnAgregarItem').prop('disabled', false);
      }

      // Cerrar dropdown al hacer click fuera
      $(document).on('click', e => {
        if (!$(e.target).closest('#buscarProductoInput, #buscarProductoResultados').length) {
          $('#buscarProductoResultados').empty();
        }
      });

      // ============= Tabla de items =============
      let itemIdx = 0;

      function agregarItem({ producto_id, referencia, nombre, cantidad, precio_unitario }) {
        $('#itemsVacio').remove();
        const idx = itemIdx++;
        const subtotal = cantidad * precio_unitario;
        const row = $(`
          <tr data-idx="${idx}">
            <td><code>${referencia || ''}</code></td>
            <td>
              ${nombre}
              <input type="hidden" name="items[${idx}][producto_id]" value="${producto_id}">
            </td>
            <td class="text-center">
              <input type="number" min="1" value="${cantidad}"
                     name="items[${idx}][cantidad]"
                     class="form-control form-control-sm text-center item-cantidad">
            </td>
            <td class="text-end">
              <input type="number" step="0.01" min="0" value="${precio_unitario}"
                     name="items[${idx}][precio_unitario]"
                     class="form-control form-control-sm text-end item-precio">
            </td>
            <td class="text-end fw-semibold item-subtotal">${fmtCOP(subtotal)}</td>
            <td class="text-center">
              <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        `);
        $('#itemsBody').append(row);
        actualizarTotal();
      }

      $('#itemsBody').on('input', '.item-cantidad, .item-precio', function () {
        const row = $(this).closest('tr');
        const cant = parseFloat(row.find('.item-cantidad').val()) || 0;
        const precio = parseFloat(row.find('.item-precio').val()) || 0;
        row.find('.item-subtotal').text(fmtCOP(cant * precio));
        actualizarTotal();
      });

      $('#itemsBody').on('click', '.btn-eliminar', function () {
        $(this).closest('tr').remove();
        if (!$('#itemsBody tr').length) {
          $('#itemsBody').append(`
            <tr id="itemsVacio">
              <td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-basket3 fs-1 d-block mb-2"></i>
                Aún no has agregado productos.<br>
                <small>Puedes dejarlo vacío y usar el "monto manual" abajo.</small>
              </td>
            </tr>
          `);
        }
        actualizarTotal();
      });

      function actualizarTotal() {
        let total = 0;
        $('#itemsBody tr').each(function () {
          const cant = parseFloat($(this).find('.item-cantidad').val()) || 0;
          const precio = parseFloat($(this).find('.item-precio').val()) || 0;
          total += cant * precio;
        });
        $('#totalVenta').text(fmtCOP(total));

        // Si hay items, deshabilitar monto manual visualmente
        if (total > 0) {
          $('#montoManual').prop('disabled', true).val('').attr('placeholder', 'Se usa el total de productos');
        } else {
          $('#montoManual').prop('disabled', false).attr('placeholder', '0');
        }
      }

      $('#btnAgregarItem').on('click', () => {
        const productoId = $('#nuevoProductoId').val();
        const referencia = $('#nuevoProductoRef').val();
        const nombre = $('#nuevoProductoNombre').val();
        const cantidad = parseInt($('#nuevoCantidad').val(), 10) || 1;
        const precio = parseFloat($('#nuevoPrecio').val()) || 0;

        if (!productoId) return;
        if (cantidad < 1) return alert('La cantidad debe ser al menos 1');

        agregarItem({
          producto_id: productoId,
          referencia,
          nombre,
          cantidad,
          precio_unitario: precio,
        });

        // Reset del buscador
        $('#nuevoProductoId, #nuevoProductoRef, #nuevoProductoNombre').val('');
        $('#buscarProductoInput').val('').focus();
        $('#nuevoCantidad').val(1);
        $('#nuevoPrecio').val('');
        $('#btnAgregarItem').prop('disabled', true);
      });

      // ============= Precargar items existentes (edición) =============
      @foreach ($itemsExistentes as $it)
        agregarItem({
          producto_id: {{ $it['producto_id'] }},
          referencia: @json($it['referencia_producto']),
          nombre: @json($it['nombre_producto']),
          cantidad: {{ $it['cantidad'] }},
          precio_unitario: {{ $it['precio_unitario'] }},
        });
      @endforeach

      actualizarTotal();
    })();
    </script>
    @endpush
</x-app-layout>
