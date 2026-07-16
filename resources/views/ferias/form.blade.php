<x-app-layout>
  <x-slot name="header">Nueva Feria</x-slot>

  <div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-2xl font-semibold mb-0">Crear Feria</h4>
            <a href="{{ route('ferias.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
          </div>

          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
          @endif

          <form action="{{ route('ferias.store') }}" method="POST" id="formFeria">
            @csrf

            <div class="mb-3">
              <label class="form-label fw-semibold">Nombre de la feria <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" maxlength="150" placeholder="Ej: Nail Fest Bogotá 2026" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Lista de precios base <span class="text-danger">*</span></label>
              <select name="lista_precio_base_id" class="form-select" required>
                <option value="">Selecciona la lista desde la que se copiarán los precios...</option>
                @foreach($listas as $l)
                  <option value="{{ $l->id }}" {{ old('lista_precio_base_id') == $l->id ? 'selected' : '' }}>{{ $l->nombre }}</option>
                @endforeach
              </select>
              <small class="text-muted">Se creará una lista propia de la feria copiando estos precios. Las listas regulares NO se modifican.</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Ubicación de la feria <span class="text-danger">*</span></label>
              <div class="d-flex gap-3 mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="ubicacion_modo" id="modoNueva" value="nueva" {{ old('ubicacion_modo', 'nueva') === 'nueva' ? 'checked' : '' }}>
                  <label class="form-check-label" for="modoNueva">Crear ubicación nueva</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="ubicacion_modo" id="modoExistente" value="existente" {{ old('ubicacion_modo') === 'existente' ? 'checked' : '' }}>
                  <label class="form-check-label" for="modoExistente">Reusar una existente</label>
                </div>
              </div>
              <div id="campoNueva">
                <input type="text" name="ubicacion_nombre" class="form-control" value="{{ old('ubicacion_nombre') }}" maxlength="150" placeholder="Nombre de la ubicación nueva (ej: Stand Nail Fest)">
              </div>
              <div id="campoExistente" style="display:none;">
                <select name="ubicacion_id" class="form-select">
                  <option value="">Selecciona una ubicación existente...</option>
                  @foreach($ubicaciones as $u)
                    <option value="{{ $u->id }}" {{ old('ubicacion_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre }} ({{ $u->tipo }})</option>
                  @endforeach
                </select>
                <small class="text-muted">Su inventario actual se usará como el de la feria (puedes reiniciarlo desde Ubicaciones).</small>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Notas <small class="text-muted fw-normal">(opcional)</small></label>
              <textarea name="notas" class="form-control" rows="2" maxlength="1000">{{ old('notas') }}</textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
              <a href="{{ route('ferias.index') }}" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Crear feria</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  (function() {
    function toggleUbicacion() {
      const modo = document.querySelector('input[name="ubicacion_modo"]:checked').value;
      document.getElementById('campoNueva').style.display = modo === 'nueva' ? 'block' : 'none';
      document.getElementById('campoExistente').style.display = modo === 'existente' ? 'block' : 'none';
    }
    document.querySelectorAll('input[name="ubicacion_modo"]').forEach(r => r.addEventListener('change', toggleUbicacion));
    toggleUbicacion();
  })();
  </script>
  @endpush
</x-app-layout>
