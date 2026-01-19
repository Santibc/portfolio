<x-app-layout>
  <x-slot name="header">{{ $ubicacion->id ? 'Editar Ubicación' : 'Nueva Ubicación' }}</x-slot>

  <div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <form action="{{ route('ubicaciones.guardar') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $ubicacion->id }}">

            <div class="mb-4">
              <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código *</label>
              <input type="text" name="codigo" id="codigo"
                class="w-full px-3 py-2 border rounded-md @error('codigo') border-red-500 @enderror"
                value="{{ old('codigo', $ubicacion->codigo) }}" required>
              @error('codigo')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4">
              <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
              <input type="text" name="nombre" id="nombre"
                class="w-full px-3 py-2 border rounded-md @error('nombre') border-red-500 @enderror"
                value="{{ old('nombre', $ubicacion->nombre) }}" required>
              @error('nombre')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4">
              <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
              <select name="tipo" id="tipo"
                class="w-full px-3 py-2 border rounded-md @error('tipo') border-red-500 @enderror" required>
                @foreach($tipos as $valor => $nombre)
                  <option value="{{ $valor }}" {{ old('tipo', $ubicacion->tipo) == $valor ? 'selected' : '' }}>
                    {{ $nombre }}
                  </option>
                @endforeach
              </select>
              @error('tipo')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="mb-4">
              <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
              <input type="text" name="direccion" id="direccion"
                class="w-full px-3 py-2 border rounded-md"
                value="{{ old('direccion', $ubicacion->direccion) }}">
            </div>

            <div class="mb-4">
              <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <input type="text" name="telefono" id="telefono"
                class="w-full px-3 py-2 border rounded-md"
                value="{{ old('telefono', $ubicacion->telefono) }}">
            </div>

            <div class="mb-4">
              <label for="responsable" class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
              <input type="text" name="responsable" id="responsable"
                class="w-full px-3 py-2 border rounded-md"
                value="{{ old('responsable', $ubicacion->responsable) }}">
            </div>

            <div class="mb-4">
              <label class="flex items-center">
                <input type="checkbox" name="activo" value="1"
                  class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  {{ old('activo', $ubicacion->activo ?? true) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Activo</span>
              </label>
            </div>

            @if(!$ubicacion->es_principal)
            <div class="mb-4">
              <label class="flex items-center">
                <input type="checkbox" name="es_principal" value="1"
                  class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  {{ old('es_principal', $ubicacion->es_principal) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-700">Marcar como ubicación principal de este tipo</span>
              </label>
            </div>
            @else
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
              <span class="text-yellow-800"><i class="bi bi-star-fill"></i> Esta es la ubicación principal</span>
            </div>
            @endif

            <div class="flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Guardar
              </button>
              <a href="{{ route('ubicaciones') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
