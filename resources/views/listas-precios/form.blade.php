<x-app-layout>
  <x-slot name="header">
    {{ $listaPrecio->exists ? 'Editar Lista de Precios' : 'Nueva Lista de Precios' }}
  </x-slot>

  <div class="container py-4">
    <div class="card shadow">
      <div class="card-body">
        <form method="POST" action="{{ route('listas-precios.guardar') }}">
          @csrf
          <input type="hidden" name="id" value="{{ old('id', $listaPrecio->id) }}">

          <div class="row">
            {{-- Código --}}
            <div class="col-md-4 mb-3">
              <label class="form-label">Código <span class="text-danger">*</span></label>
              <input name="codigo" type="text"
                     class="form-control @error('codigo') is-invalid @enderror"
                     value="{{ old('codigo', $listaPrecio->codigo) }}"
                     placeholder="Ej: EXPORT1, LOCAL1">
              <small class="text-muted">Código único para identificar la lista</small>
              @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Nombre --}}
            <div class="col-md-5 mb-3">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <input name="nombre" type="text"
                     class="form-control @error('nombre') is-invalid @enderror"
                     value="{{ old('nombre', $listaPrecio->nombre) }}"
                     placeholder="Ej: Exportación Mayorista">
              @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Orden --}}
            <div class="col-md-3 mb-3">
              <label class="form-label">Orden <span class="text-danger">*</span></label>
              <input name="orden" type="number" min="0"
                     class="form-control @error('orden') is-invalid @enderror"
                     value="{{ old('orden', $listaPrecio->orden ?? 0) }}">
              <small class="text-muted">Posición en listados</small>
              @error('orden') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Descripción --}}
            <div class="col-md-12 mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="2"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Descripción opcional de la lista de precios">{{ old('descripcion', $listaPrecio->descripcion) }}</textarea>
              @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Estado Activo --}}
            <div class="col-md-12 mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                       name="activo" id="activo" value="1"
                       {{ old('activo', $listaPrecio->activo ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">
                  <strong>Lista activa</strong>
                </label>
                <small class="text-muted d-block">
                  Las listas inactivas no aparecerán en los formularios de clientes ni en el catálogo.
                </small>
              </div>
            </div>
          </div>

          {{-- Info de clientes asignados --}}
          @if($listaPrecio->exists)
            @php $clientesCount = $listaPrecio->clientes()->count(); @endphp
            @if($clientesCount > 0)
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Esta lista tiene <strong>{{ $clientesCount }}</strong> cliente(s) asignado(s).
                No se puede eliminar mientras tenga clientes.
              </div>
            @endif
          @endif

          <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Guardar
            </button>
            <a href="{{ route('listas-precios') }}" class="btn btn-outline-secondary">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
