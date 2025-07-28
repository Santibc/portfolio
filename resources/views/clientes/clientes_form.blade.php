<x-app-layout>
    <x-slot name="header">
        {{ $cliente->exists ? 'Editar Cliente' : 'Nuevo Cliente' }}
    </x-slot>
  @push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  @endpush
    <div class="container py-4">
      <div class="card shadow">
        <div class="card-body">
          <form method="POST" action="{{ route('clientes.guardar') }}">
            @csrf
            <input type="hidden" name="id" value="{{ old('id',$cliente->id) }}">
<input type="hidden" name="pais_id" value="{{ $pais_id }}">
            <div class="row">
              {{-- Identificación --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Identificación <span class="text-danger">*</span></label>
                <input name="numero_identificacion" type="text"
                       class="form-control"
                       value="{{ old('numero_identificacion',$cliente->numero_identificacion) }}">
                @error('numero_identificacion') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Contacto --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Contacto <span class="text-danger">*</span></label>
                <input name="nombre_contacto" type="text"
                       class="form-control"
                       value="{{ old('nombre_contacto',$cliente->nombre_contacto) }}">
                @error('nombre_contacto') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Email --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input name="email" type="email"
                       class="form-control"
                       value="{{ old('email',$cliente->email) }}">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Teléfono --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono</label>
                <input name="telefono" type="text"
                       class="form-control"
                       value="{{ old('telefono',$cliente->telefono) }}">
                @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- País --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Departamento <span class="text-danger">*</span></label>
            <select id="departamento-select" name="departamento_id" class="form-select select2">
              <option value="">-- Seleccionar --</option>
              @foreach($departamentos as $id => $nombre)
                <option value="{{ $id }}"
                  {{ old('departamento_id',$cliente->ciudad->departamento_id ?? '') == $id ? 'selected':'' }}>
                  {{ $nombre }}
                </option>
              @endforeach
            </select>
            @error('departamento_id')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Ciudad --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Ciudad <span class="text-danger">*</span></label>
            <select id="ciudad-select" name="ciudad_id" class="form-select select2">
              <option value="">-- Seleccionar --</option>
              {{-- Si editamos, pre-cargamos --}}
              @if($cliente->exists)
                @foreach(\App\Models\Ciudad::where('departamento_id',$cliente->ciudad->departamento_id)->pluck('nombre','id') as $id=>$ciudad)
                  <option value="{{ $id }}"
                    {{ old('ciudad_id',$cliente->ciudad_id)==$id ? 'selected':'' }}>
                    {{ $ciudad }}
                  </option>
                @endforeach
              @endif
            </select>
            @error('ciudad_id')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

              {{-- Vendedor --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Vendedor <span class="text-danger">*</span></label>
                <select name="vendedor_id" class="form-select">
                  <option value="">-- Seleccionar --</option>
                  @foreach($vendedores as $id=>$name)
                    <option value="{{ $id }}"
                      {{ old('vendedor_id',$cliente->vendedor_id)==$id ? 'selected' : '' }}>
                      {{ $name }}
                    </option>
                  @endforeach
                </select>
                @error('vendedor_id') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Lista de Precio --}}
              <div class="col-md-6 mb-3">
                <label class="form-label">Lista de Precio <span class="text-danger">*</span></label>
                <select name="lista_precio_id" class="form-select">
                  <option value="">-- Seleccionar --</option>
                  @foreach($listas as $id=>$nombre)
                    <option value="{{ $id }}"
                      {{ old('lista_precio_id',$cliente->lista_precio_id)==$id ? 'selected' : '' }}>
                      {{ $nombre }}
                    </option>
                  @endforeach
                </select>
                @error('lista_precio_id') <small class="text-danger">{{ $message }}</small> @enderror
              </div>


            </div>

            <div class="d-flex justify-content-between mt-4">
              <button type="submit" class="btn btn-primary">Guardar</button>
              <a href="{{ route('clientes') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
          </form>
        </div>
      </div>
    </div>
      @push('scripts')
    <!-- jQuery + Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function(){
      // Inicializamos Select2
      $('.select2').select2({ width: '100%' });

      // Al cambiar departamento, recargamos ciudades
      $('#departamento-select').on('change', function(){
        let depId = $(this).val();
        $('#ciudad-select').empty().append('<option>Buscando…</option>');
        if (!depId) {
          $('#ciudad-select').empty().append('<option value="">-- Seleccionar --</option>');
          return;
        }
        $.getJSON("{{ route('ajax.ciudades') }}", { departamento_id: depId })
         .done(function(data){
           let $ciudad = $('#ciudad-select').empty().append('<option value="">-- Seleccionar --</option>');
           data.forEach(c => {
             $ciudad.append(`<option value="${c.id}">${c.nombre}</option>`);
           });
           $ciudad.trigger('change');
         });
      });
    });
    </script>
  @endpush
</x-app-layout>
