<x-app-layout>
  <x-slot name="header">Nueva Novedad de Stock</x-slot>

  <div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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

          <form action="{{ route('novedades-stock.guardar') }}" method="POST" id="novedadForm">
            @csrf

            <div class="mb-4">
              <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Novedad *</label>
              <select name="tipo" id="tipo"
                class="w-full px-3 py-2 border rounded-md @error('tipo') border-red-500 @enderror" required>
                <option value="">Seleccione tipo</option>
                @foreach($tipos as $valor => $nombre)
                  <option value="{{ $valor }}" {{ old('tipo') == $valor ? 'selected' : '' }}>{{ $nombre }}</option>
                @endforeach
              </select>
              @error('tipo')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4">
              <label for="ubicacion_id" class="block text-sm font-medium text-gray-700 mb-1">Ubicación *</label>
              <select name="ubicacion_id" id="ubicacion_id"
                class="w-full px-3 py-2 border rounded-md @error('ubicacion_id') border-red-500 @enderror"
                required {{ isset($ubicacionCajeroId) && $ubicacionCajeroId ? 'disabled' : '' }}>
                <option value="">Seleccione ubicación</option>
                @foreach($ubicaciones as $ubicacion)
                  <option value="{{ $ubicacion->id }}"
                    {{ (isset($ubicacionCajeroId) && $ubicacionCajeroId == $ubicacion->id) || old('ubicacion_id') == $ubicacion->id ? 'selected' : '' }}>
                    {{ $ubicacion->nombre }} ({{ $ubicacion->tipo_nombre }})
                  </option>
                @endforeach
              </select>
              @if(isset($ubicacionCajeroId) && $ubicacionCajeroId)
                <input type="hidden" name="ubicacion_id" value="{{ $ubicacionCajeroId }}">
              @endif
              @error('ubicacion_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4">
              <label for="producto_id" class="block text-sm font-medium text-gray-700 mb-1">Producto *</label>
              <select name="producto_id" id="producto_id"
                class="w-full px-3 py-2 border rounded-md @error('producto_id') border-red-500 @enderror" required>
                <option value="">Seleccione un producto</option>
                @foreach($productos as $producto)
                  <option value="{{ $producto->id }}"
                    data-tiene-variantes="{{ $producto->tiene_variantes ? '1' : '0' }}"
                    {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                    {{ $producto->referencia }} - {{ $producto->nombre }}
                  </option>
                @endforeach
              </select>
              @error('producto_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4" id="varianteContainer" style="display: none;">
              <label for="variante_producto_id" class="block text-sm font-medium text-gray-700 mb-1">Variante</label>
              <select name="variante_producto_id" id="variante_producto_id"
                class="w-full px-3 py-2 border rounded-md">
                <option value="">Seleccione una variante</option>
              </select>
            </div>

            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md" id="stockInfo" style="display: none;">
              <span class="text-blue-800">
                <i class="bi bi-info-circle"></i> Stock disponible: <strong id="stockDisponible">0</strong>
              </span>
            </div>

            <div class="mb-4">
              <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
              <input type="number" name="cantidad" id="cantidad"
                class="w-full px-3 py-2 border rounded-md @error('cantidad') border-red-500 @enderror"
                value="{{ old('cantidad', 1) }}" min="1" required>
              @error('cantidad')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4">
              <label for="valor_original" class="block text-sm font-medium text-gray-700 mb-1">Valor Original *</label>
              <input type="number" name="valor_original" id="valor_original" step="0.01"
                class="w-full px-3 py-2 border rounded-md @error('valor_original') border-red-500 @enderror"
                value="{{ old('valor_original') }}" min="0" required>
              @error('valor_original')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4" id="valorSaldoContainer" style="display: none;">
              <label for="valor_saldo" class="block text-sm font-medium text-gray-700 mb-1">Valor de Saldo</label>
              <input type="number" name="valor_saldo" id="valor_saldo" step="0.01"
                class="w-full px-3 py-2 border rounded-md"
                value="{{ old('valor_saldo') }}" min="0">
              <small class="text-gray-500">Precio reducido para venta como saldo</small>
            </div>

            <div class="mb-4">
              <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
              <textarea name="descripcion" id="descripcion" rows="3"
                class="w-full px-3 py-2 border rounded-md @error('descripcion') border-red-500 @enderror"
                required placeholder="Describa el motivo de la novedad...">{{ old('descripcion') }}</textarea>
              @error('descripcion')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <!-- Campos específicos para garantía -->
            <div id="garantiaFields" style="display: none;">
              <div class="mb-4">
                <label for="numero_garantia" class="block text-sm font-medium text-gray-700 mb-1">Número de Garantía</label>
                <input type="text" name="numero_garantia" id="numero_garantia"
                  class="w-full px-3 py-2 border rounded-md"
                  value="{{ old('numero_garantia') }}">
              </div>

              <div class="mb-4">
                <label for="fecha_vencimiento_garantia" class="block text-sm font-medium text-gray-700 mb-1">Fecha Vencimiento Garantía</label>
                <input type="date" name="fecha_vencimiento_garantia" id="fecha_vencimiento_garantia"
                  class="w-full px-3 py-2 border rounded-md"
                  value="{{ old('fecha_vencimiento_garantia') }}">
              </div>
            </div>

            <div class="flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Registrar Novedad
              </button>
              <a href="{{ route('novedades-stock') }}" class="btn btn-outline-secondary">
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
    const tipoSelect = document.getElementById('tipo');
    const productoSelect = document.getElementById('producto_id');
    const varianteContainer = document.getElementById('varianteContainer');
    const varianteSelect = document.getElementById('variante_producto_id');
    const ubicacionSelect = document.getElementById('ubicacion_id');
    const stockInfo = document.getElementById('stockInfo');
    const stockDisponible = document.getElementById('stockDisponible');
    const garantiaFields = document.getElementById('garantiaFields');
    const valorSaldoContainer = document.getElementById('valorSaldoContainer');

    // Mostrar/ocultar campos según tipo
    tipoSelect.addEventListener('change', function() {
      if (this.value === 'garantia') {
        garantiaFields.style.display = 'block';
        valorSaldoContainer.style.display = 'none';
      } else if (this.value === 'saldo') {
        garantiaFields.style.display = 'none';
        valorSaldoContainer.style.display = 'block';
      } else {
        garantiaFields.style.display = 'none';
        valorSaldoContainer.style.display = 'none';
      }
    });

    productoSelect.addEventListener('change', async function() {
      const productoId = this.value;
      varianteSelect.innerHTML = '<option value="">Seleccione una variante</option>';
      varianteContainer.style.display = 'none';

      if (!productoId) {
        stockInfo.style.display = 'none';
        return;
      }

      try {
        const res = await fetch(`/novedades-stock/variantes/${productoId}`);
        const data = await res.json();

        if (data.tiene_variantes && data.variantes.length > 0) {
          data.variantes.forEach(v => {
            const option = document.createElement('option');
            option.value = v.id;
            option.textContent = v.nombre_variante;
            varianteSelect.appendChild(option);
          });
          varianteContainer.style.display = 'block';
        }

        actualizarStockDisponible();
      } catch (e) {
        console.error('Error al cargar variantes:', e);
      }
    });

    varianteSelect.addEventListener('change', actualizarStockDisponible);
    ubicacionSelect.addEventListener('change', actualizarStockDisponible);

    async function actualizarStockDisponible() {
      const productoId = productoSelect.value;
      const ubicacionId = ubicacionSelect.value;
      const varianteId = varianteSelect.value;

      if (!productoId || !ubicacionId) {
        stockInfo.style.display = 'none';
        return;
      }

      try {
        const params = new URLSearchParams({
          producto_id: productoId,
          ubicacion_id: ubicacionId
        });
        if (varianteId) {
          params.append('variante_producto_id', varianteId);
        }

        const res = await fetch(`/novedades-stock/stock-disponible?${params}`);
        const data = await res.json();

        stockDisponible.textContent = data.stock_disponible;
        stockInfo.style.display = 'block';

        document.getElementById('cantidad').max = data.stock_disponible;
      } catch (e) {
        console.error('Error al obtener stock:', e);
      }
    }

    // Trigger inicial si hay valores
    if (tipoSelect.value) {
      tipoSelect.dispatchEvent(new Event('change'));
    }
  });
  </script>
  @endpush
</x-app-layout>
