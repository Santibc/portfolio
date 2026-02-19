<x-app-layout>
  <x-slot name="header">{{ $traslado->id ? 'Editar Traslado #' . $traslado->numero_traslado : 'Nuevo Traslado' }}</x-slot>

  <div class="py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          @if($errors->any())
            <div class="alert alert-danger mb-4">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ $traslado->id ? route('traslados.actualizar', $traslado->id) : route('traslados.guardar') }}" method="POST" id="trasladoForm">
            @csrf

            <div class="row mb-4">
              <div class="col-md-6">
                <label for="ubicacion_origen_id" class="block text-sm font-medium text-gray-700 mb-1">Ubicaci&oacute;n Origen *</label>
                <select name="ubicacion_origen_id" id="ubicacion_origen_id"
                  class="w-full px-3 py-2 border rounded-md" required>
                  <option value="">Seleccione ubicaci&oacute;n de origen</option>
                  @foreach($ubicacionesOrigen as $ubicacion)
                    <option value="{{ $ubicacion->id }}"
                      {{ (old('ubicacion_origen_id', $traslado->ubicacion_origen_id)) == $ubicacion->id ? 'selected' : '' }}>
                      {{ $ubicacion->nombre }} ({{ $ubicacion->tipo_nombre }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="ubicacion_destino_id" class="block text-sm font-medium text-gray-700 mb-1">Ubicaci&oacute;n Destino *</label>
                <select name="ubicacion_destino_id" id="ubicacion_destino_id"
                  class="w-full px-3 py-2 border rounded-md" required>
                  <option value="">Seleccione ubicaci&oacute;n de destino</option>
                  @foreach($ubicacionesDestino as $ubicacion)
                    <option value="{{ $ubicacion->id }}"
                      {{ (old('ubicacion_destino_id', $traslado->ubicacion_destino_id)) == $ubicacion->id ? 'selected' : '' }}>
                      {{ $ubicacion->nombre }} ({{ $ubicacion->tipo_nombre }})
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <label for="tipo_operacion" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Operaci&oacute;n *</label>
                <select name="tipo_operacion" id="tipo_operacion"
                  class="w-full px-3 py-2 border rounded-md" required>
                  <option value="general" {{ old('tipo_operacion', $traslado->tipo_operacion ?? 'general') == 'general' ? 'selected' : '' }}>General</option>
                  <option value="credito" {{ old('tipo_operacion', $traslado->tipo_operacion) == 'credito' ? 'selected' : '' }}>Cr&eacute;dito</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <input type="text" name="notas" id="notas" class="w-full px-3 py-2 border rounded-md"
                  placeholder="Observaciones del traslado..." value="{{ old('notas', $traslado->notas) }}">
              </div>
            </div>

            <hr class="my-4">

            {{-- Sección para agregar productos --}}
            <h5 class="mb-3"><i class="bi bi-box-seam me-1"></i> Productos del Traslado</h5>

            <div class="row mb-3 align-items-end" id="addItemRow">
              <div class="col-md-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                <input type="text" id="sel_producto_buscar" class="w-full px-3 py-2 border rounded-md mb-1"
                  placeholder="Buscar producto..." style="display:none;">
                <select id="sel_producto" class="w-full px-3 py-2 border rounded-md" disabled>
                  <option value="">Primero seleccione ubicaci&oacute;n de origen</option>
                </select>
              </div>
              <div class="col-md-3" id="sel_variante_container" style="display:none;">
                <label class="block text-sm font-medium text-gray-700 mb-1">Variante</label>
                <select id="sel_variante" class="w-full px-3 py-2 border rounded-md">
                  <option value="">Seleccione variante</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                <input type="number" id="sel_cantidad" class="w-full px-3 py-2 border rounded-md" min="1" value="1">
              </div>
              <div class="col-md-1 text-center">
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <span id="sel_stock" class="badge bg-info fs-6">-</span>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-success w-100" id="btnAgregarItem" disabled>
                  <i class="bi bi-plus-lg me-1"></i> Agregar
                </button>
              </div>
            </div>

            {{-- Tabla de ítems agregados --}}
            <div class="table-responsive">
              <table class="table table-bordered table-sm" id="tablaItems">
                <thead class="table-light">
                  <tr>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th>Cantidad</th>
                    <th style="width:60px;"></th>
                  </tr>
                </thead>
                <tbody id="itemsBody">
                  <tr id="sinItems">
                    <td colspan="4" class="text-center text-muted py-3">No se han agregado productos al traslado</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex gap-2 mt-4">
              <button type="submit" class="btn btn-primary" id="btnCrear" {{ $traslado->id ? '' : 'disabled' }}>
                <i class="bi bi-save me-1"></i> {{ $traslado->id ? 'Guardar Cambios' : 'Crear Traslado' }}
              </button>
              <a href="{{ route('traslados') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const ubicacionOrigenSelect = document.getElementById('ubicacion_origen_id');
    const selProducto = document.getElementById('sel_producto');
    const selProductoBuscar = document.getElementById('sel_producto_buscar');
    const selVarianteContainer = document.getElementById('sel_variante_container');
    const selVariante = document.getElementById('sel_variante');
    const selCantidad = document.getElementById('sel_cantidad');
    const selStock = document.getElementById('sel_stock');
    const btnAgregar = document.getElementById('btnAgregarItem');
    const btnCrear = document.getElementById('btnCrear');
    const itemsBody = document.getElementById('itemsBody');

    let items = [];
    let itemIndex = 0;
    let productosData = [];

    // Cargar productos al cambiar ubicación de origen
    ubicacionOrigenSelect.addEventListener('change', async function() {
      const ubicacionId = this.value;
      selProducto.innerHTML = '<option value="">Cargando...</option>';
      selProducto.disabled = true;
      selProductoBuscar.style.display = 'none';
      selProductoBuscar.value = '';
      resetAddRow();

      if (!ubicacionId) {
        selProducto.innerHTML = '<option value="">Primero seleccione ubicaci&oacute;n de origen</option>';
        return;
      }

      try {
        const res = await fetch(`/traslados/productos-por-ubicacion/${ubicacionId}`);
        const data = await res.json();
        productosData = data.productos;

        renderProductos(productosData);
        if (data.productos.length > 0) {
          selProducto.disabled = false;
          selProductoBuscar.style.display = 'block';
        }
      } catch (e) {
        console.error('Error:', e);
        selProducto.innerHTML = '<option value="">Error al cargar</option>';
      }
    });

    function renderProductos(lista) {
      selProducto.innerHTML = '<option value="">Seleccione un producto</option>';
      if (lista.length === 0) {
        selProducto.innerHTML = '<option value="">No hay productos con stock</option>';
        return;
      }
      lista.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.referencia} - ${p.nombre} (Stock: ${p.stock_disponible})`;
        opt.dataset.tieneVariantes = p.tiene_variantes ? '1' : '0';
        opt.dataset.nombre = `${p.referencia} - ${p.nombre}`;
        selProducto.appendChild(opt);
      });
    }

    // Buscador de productos
    selProductoBuscar.addEventListener('input', function() {
      const buscar = this.value.toLowerCase().trim();
      if (!buscar) {
        renderProductos(productosData);
        return;
      }
      const filtrados = productosData.filter(p =>
        p.referencia.toLowerCase().includes(buscar) || p.nombre.toLowerCase().includes(buscar)
      );
      renderProductos(filtrados);
    });

    // Al seleccionar producto, cargar variantes si aplica
    selProducto.addEventListener('change', async function() {
      const productoId = this.value;
      const ubicacionId = ubicacionOrigenSelect.value;
      selVarianteContainer.style.display = 'none';
      selVariante.innerHTML = '<option value="">Seleccione variante</option>';
      selStock.textContent = '-';

      if (!productoId) {
        btnAgregar.disabled = true;
        return;
      }

      const opt = this.options[this.selectedIndex];
      const tieneVariantes = opt.dataset.tieneVariantes === '1';

      if (tieneVariantes) {
        try {
          const res = await fetch(`/traslados/variantes-por-ubicacion/${productoId}/${ubicacionId}`);
          const data = await res.json();
          if (data.tiene_variantes && data.variantes.length > 0) {
            data.variantes.forEach(v => {
              const o = document.createElement('option');
              o.value = v.id;
              o.textContent = `${v.nombre_variante} (Stock: ${v.stock_disponible})`;
              o.dataset.nombre = v.nombre_variante;
              selVariante.appendChild(o);
            });
            selVarianteContainer.style.display = 'block';
            btnAgregar.disabled = true;
            return;
          }
        } catch (e) {
          console.error('Error variantes:', e);
        }
      }

      await actualizarStock();
      btnAgregar.disabled = false;
    });

    selVariante.addEventListener('change', async function() {
      if (this.value) {
        await actualizarStock();
        btnAgregar.disabled = false;
      } else {
        selStock.textContent = '-';
        btnAgregar.disabled = true;
      }
    });

    async function actualizarStock() {
      const productoId = selProducto.value;
      const varianteId = selVariante.value;
      const ubicacionId = ubicacionOrigenSelect.value;
      if (!productoId || !ubicacionId) return;

      const params = new URLSearchParams({
        producto_id: productoId,
        ubicacion_id: ubicacionId
      });
      if (varianteId) params.append('variante_producto_id', varianteId);

      @if($traslado->id)
      params.append('traslado_id', '{{ $traslado->id }}');
      @endif

      try {
        const res = await fetch(`/traslados/stock-disponible?${params}`);
        const data = await res.json();

        const stockReal = data.stock_disponible;
        selStock.textContent = stockReal;
        selCantidad.max = stockReal;
        if (parseInt(selCantidad.value) > stockReal) selCantidad.value = stockReal;
      } catch (e) {
        console.error('Error stock:', e);
      }
    }

    btnAgregar.addEventListener('click', () => {
      const productoId = selProducto.value;
      const varianteId = selVariante.value || null;
      const cantidad = parseInt(selCantidad.value);
      const stockDisp = parseInt(selStock.textContent);

      if (!productoId || cantidad < 1) return;
      if (cantidad > stockDisp) {
        alert('La cantidad excede el stock disponible.');
        return;
      }

      const productoOpt = selProducto.options[selProducto.selectedIndex];
      const productoNombre = productoOpt.dataset.nombre;
      let varianteNombre = '';
      if (varianteId) {
        const varianteOpt = selVariante.options[selVariante.selectedIndex];
        varianteNombre = varianteOpt.dataset.nombre;
      }

      items.push({ idx: itemIndex++, producto_id: productoId, variante_producto_id: varianteId, cantidad, producto_nombre: productoNombre, variante_nombre: varianteNombre });

      renderItems();
      resetAddRow();
      selProducto.value = '';
      selProductoBuscar.value = '';
      renderProductos(productosData);
    });

    function renderItems() {
      itemsBody.innerHTML = '';

      if (items.length === 0) {
        itemsBody.innerHTML = '<tr id="sinItems"><td colspan="4" class="text-center text-muted py-3">No se han agregado productos al traslado</td></tr>';
        btnCrear.disabled = true;
        return;
      }

      items.forEach((item, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>
            ${item.producto_nombre}
            <input type="hidden" name="items[${i}][producto_id]" value="${item.producto_id}">
            <input type="hidden" name="items[${i}][variante_producto_id]" value="${item.variante_producto_id || ''}">
          </td>
          <td>${item.variante_nombre || '-'}</td>
          <td>
            ${item.cantidad}
            <input type="hidden" name="items[${i}][cantidad]" value="${item.cantidad}">
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarItem(${item.idx})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        `;
        itemsBody.appendChild(tr);
      });

      btnCrear.disabled = false;
    }

    window.eliminarItem = function(idx) {
      items = items.filter(i => i.idx !== idx);
      renderItems();
    };

    function resetAddRow() {
      selVarianteContainer.style.display = 'none';
      selVariante.innerHTML = '<option value="">Seleccione variante</option>';
      selCantidad.value = 1;
      selStock.textContent = '-';
      btnAgregar.disabled = true;
    }

    // Pre-cargar items existentes (modo edición)
    @if($traslado->id && $items->count())
    @php
      $itemsJson = $items->map(function($i) {
        return [
          'producto_id' => (string) $i->producto_id,
          'variante_producto_id' => $i->variante_producto_id ? (string) $i->variante_producto_id : null,
          'cantidad' => $i->cantidad,
          'producto_nombre' => ($i->producto->referencia ?? '') . ' - ' . ($i->producto->nombre ?? ''),
          'variante_nombre' => $i->varianteProducto ? $i->varianteProducto->nombre_variante : '',
        ];
      });
    @endphp
    const itemsIniciales = {!! json_encode($itemsJson) !!};
    itemsIniciales.forEach(item => { items.push({ idx: itemIndex++, ...item }); });
    renderItems();
    @endif

    // Si es edición y hay origen seleccionado, disparar carga de productos
    @if($traslado->id && $traslado->ubicacion_origen_id)
    ubicacionOrigenSelect.dispatchEvent(new Event('change'));
    @endif
  });
  </script>
  @endpush
</x-app-layout>
