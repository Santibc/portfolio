<x-app-layout>
  <x-slot name="header">
    {{ $categoria->exists ? 'Editar Categoría' : 'Nueva Categoría' }}
  </x-slot>

  @push('styles')
    <!-- Select2 CSS (si lo necesitas) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  @endpush

  <div class="container py-4">
    <div class="card shadow">
      <div class="card-body">
        <form method="POST" action="{{ route('categorias.guardar') }}">
          @csrf
          <input type="hidden" name="id"   value="{{ old('id',$categoria->id) }}">
          
          <div class="row">
            {{-- Nombre --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input name="nombre" type="text"
                     class="form-control"
                     value="{{ old('nombre',$categoria->nombre) }}">
              @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Slug --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">Slug</label>
              <input name="slug" type="text"
                     class="form-control"
                     value="{{ old('slug',$categoria->slug) }}">
              @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Descripción --}}
            <div class="col-md-12 mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="3"
                        class="form-control">{{ old('descripcion',$categoria->descripcion) }}</textarea>
              @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Orden --}}
            <div class="col-md-3 mb-3">
              <label class="form-label">Orden <span class="text-danger">*</span></label>
              <input name="orden" type="number" min="0"
                     class="form-control"
                     value="{{ old('orden',$categoria->orden ?? 0) }}">
              @error('orden') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('categorias') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
    <!-- Opcional: Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
      // Ejemplo de auto-generar slug al cambiar el nombre
      document.querySelector('input[name=nombre]').addEventListener('change', function(){
        let slug = this.value.toLowerCase()
                      .replace(/\s+/g,'-')
                      .replace(/[^a-z0-9\-]/g,'');
        document.querySelector('input[name=slug]').value = slug;
      });
    </script>
  @endpush
</x-app-layout>
