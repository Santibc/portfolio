<x-app-layout>
    <x-slot name="header">{{ $meta->exists ? 'Editar Meta' : 'Nueva Meta' }}</x-slot>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <h4 class="text-2xl font-semibold mb-4">{{ $meta->exists ? 'Editar Meta' : 'Nueva Meta Mensual' }}</h4>

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
              </div>
            @endif

            <form method="POST" action="{{ route('metas.guardar') }}">
              @csrf
              @if ($meta->exists)
                <input type="hidden" name="id" value="{{ $meta->id }}">
              @endif

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Vendedor <span class="text-danger">*</span></label>
                  <select name="user_id" id="vendedorSelect" class="form-select" required>
                    <option value="">Seleccione un vendedor</option>
                    @if (!$meta->exists)
                      <option value="all" {{ old('user_id') === 'all' ? 'selected' : '' }}>— Todos los vendedores (aplicar a todo el equipo) —</option>
                    @endif
                    @foreach ($vendedores as $id => $nombre)
                      <option value="{{ $id }}" {{ old('user_id', $meta->user_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                  </select>
                  @if (!$meta->exists)
                    <small class="text-muted">Al elegir "Todos los vendedores", el mismo monto de meta se aplica a cada vendedor activo. Si ya existe una meta para alguno en ese periodo, se actualiza.</small>
                  @endif
                </div>
                <div class="col-md-3">
                  <label class="form-label">Año <span class="text-danger">*</span></label>
                  <input type="number" name="anio" class="form-control" required min="2020" max="2100"
                         value="{{ old('anio', $meta->anio ?? now()->year) }}">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Mes <span class="text-danger">*</span></label>
                  <select name="mes" class="form-select" required>
                    @foreach (['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'] as $num => $nombre)
                      <option value="{{ (int) $num }}" {{ old('mes', $meta->mes ?? now()->month) == (int) $num ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Monto meta (COP) <span class="text-danger">*</span></label>
                  <input type="number" name="monto" class="form-control" required min="0" step="0.01"
                         value="{{ old('monto', $meta->monto) }}">
                </div>
                <div class="col-12">
                  <label class="form-label">Observaciones</label>
                  <textarea name="observaciones" class="form-control" rows="2" maxlength="1000">{{ old('observaciones', $meta->observaciones) }}</textarea>
                </div>
              </div>

              <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save"></i> Guardar
                </button>
                <a href="{{ route('metas') }}" class="btn btn-outline-secondary">Cancelar</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      $('#vendedorSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione un vendedor',
        width: '100%'
      });
    });
    </script>
    @endpush
</x-app-layout>
