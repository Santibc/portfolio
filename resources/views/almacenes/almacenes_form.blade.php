<x-app-layout>
    <x-slot name="header">{{ $almacen->exists ? 'Editar Almacén' : 'Nuevo Almacén' }}</x-slot>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <h4 class="text-2xl font-semibold mb-4">{{ $almacen->exists ? 'Editar Almacén' : 'Nuevo Almacén' }}</h4>

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="POST" action="{{ route('almacenes.guardar') }}">
              @csrf
              @if ($almacen->exists)
                <input type="hidden" name="id" value="{{ $almacen->id }}">
              @endif

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Código <span class="text-danger">*</span></label>
                  <input type="text" name="codigo" class="form-control" required
                         value="{{ old('codigo', $almacen->codigo) }}" maxlength="50">
                </div>
                <div class="col-md-8">
                  <label class="form-label">Nombre <span class="text-danger">*</span></label>
                  <input type="text" name="nombre" class="form-control" required
                         value="{{ old('nombre', $almacen->nombre) }}" maxlength="255">
                </div>
                <div class="col-md-8">
                  <label class="form-label">Dirección</label>
                  <input type="text" name="direccion" class="form-control"
                         value="{{ old('direccion', $almacen->direccion) }}" maxlength="255">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Teléfono</label>
                  <input type="text" name="telefono" class="form-control"
                         value="{{ old('telefono', $almacen->telefono) }}" maxlength="50">
                </div>
                <div class="col-md-4">
                  <label class="form-label d-block">Estado</label>
                  <div class="form-check form-switch">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" class="form-check-input" id="activoSwitch"
                           {{ old('activo', $almacen->exists ? $almacen->activo : true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activoSwitch">Activo</label>
                  </div>
                </div>

                <div class="col-12">
                  <label class="form-label">Vendedores asignados</label>
                  <select name="vendedores[]" id="vendedoresSelect" class="form-select" multiple>
                    @foreach ($vendedoresDisponibles as $id => $nombre)
                      <option value="{{ $id }}"
                        {{ collect(old('vendedores', $vendedoresAsignados->pluck('id')->all()))->contains($id) ? 'selected' : '' }}>
                        {{ $nombre }}
                      </option>
                    @endforeach
                  </select>
                  <small class="text-muted">Solo se muestran vendedores sin almacén asignado y los ya asignados a este almacén.</small>
                </div>
              </div>

              <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save"></i> Guardar
                </button>
                <a href="{{ route('almacenes') }}" class="btn btn-outline-secondary">Cancelar</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      $('#vendedoresSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccionar vendedores',
        width: '100%'
      });
    });
    </script>
    @endpush
</x-app-layout>
