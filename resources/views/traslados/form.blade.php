<x-app-layout>
  <x-slot name="header">Nuevo Traslado</x-slot>

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

          <form action="{{ route('traslados.guardar') }}" method="POST" id="trasladoForm">
            @csrf

            <div class="mb-4">
              <label for="ubicacion_origen_id" class="block text-sm font-medium text-gray-700 mb-1">Ubicación Origen *</label>
              <select name="ubicacion_origen_id" id="ubicacion_origen_id"
                class="w-full px-3 py-2 border rounded-md @error('ubicacion_origen_id') border-red-500 @enderror" required>
                <option value="">Seleccione ubicación de origen</option>
                @foreach($ubicaciones as $ubicacion)
                  <option value="{{ $ubicacion->id }}" {{ old('ubicacion_origen_id') == $ubicacion->id ? 'selected' : '' }}>
                    {{ $ubicacion->nombre }} ({{ $ubicacion->tipo_nombre }})
                  </option>
                @endforeach
              </select>
              @error('ubicacion_origen_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4">
              <label for="ubicacion_destino_id" class="block text-sm font-medium text-gray-700 mb-1">Ubicación Destino *</label>
              <select name="ubicacion_destino_id" id="ubicacion_destino_id"
                class="w-full px-3 py-2 border rounded-md @error('ubicacion_destino_id') border-red-500 @enderror" required>
                <option value="">Seleccione ubicación de destino</option>
                @foreach($ubicaciones as $ubicacion)
                  <option value="{{ $ubicacion->id }}" {{ old('ubicacion_destino_id') == $ubicacion->id ? 'selected' : '' }}>
                    {{ $ubicacion->nombre }} ({{ $ubicacion->tipo_nombre }})
                  </option>
                @endforeach
              </select>
              @error('ubicacion_destino_id')
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
                <i class="bi bi-info-circle"></i> Stock disponible en origen: <strong id="stockDisponible">0</strong>
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
              <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
              <textarea name="notas" id="notas" rows="3"
                class="w-full px-3 py-2 border rounded-md"
                placeholder="Observaciones del traslado...">{{ old('notas') }}</textarea>
            </div>

            <div class="flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Crear Traslado
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
    const productoSelect = document.getElementById('producto_id');
    const varianteContainer = document.getElementById('varianteContainer');
    const varianteSelect = document.getElementById('variante_producto_id');
    const ubicacionOrigenSelect = document.getElementById('ubicacion_origen_id');
    const stockInfo = document.getElementById('stockInfo');
    const stockDisponible = document.getElementById('stockDisponible');

    productoSelect.addEventListener('change', async function() {
      const productoId = this.value;
      varianteSelect.innerHTML = '<option value="">Seleccione una variante</option>';
      varianteContainer.style.display = 'none';

      if (!productoId) {
        stockInfo.style.display = 'none';
        return;
      }

      try {
        const res = await fetch(`/traslados/variantes/${productoId}`);
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
    ubicacionOrigenSelect.addEventListener('change', actualizarStockDisponible);

    async function actualizarStockDisponible() {
      const productoId = productoSelect.value;
      const ubicacionId = ubicacionOrigenSelect.value;
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

        const res = await fetch(`/traslados/stock-disponible?${params}`);
        const data = await res.json();

        stockDisponible.textContent = data.stock_disponible;
        stockInfo.style.display = 'block';

        // Validar cantidad máxima
        document.getElementById('cantidad').max = data.stock_disponible;
      } catch (e) {
        console.error('Error al obtener stock:', e);
      }
    }
  });
  </script>
  @endpush
</x-app-layout>
