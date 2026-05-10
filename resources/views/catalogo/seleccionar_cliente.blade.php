<x-app-layout>
  <x-slot name="header">Catálogo - Seleccionar Cliente</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if($incompleto = session('cliente_incompleto'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
          <i class="bi bi-exclamation-triangle"></i>
          El cliente <strong>{{ $incompleto['nombre'] }}</strong> no se puede cotizar porque le falta
          configurar <strong>{{ $incompleto['falta'] }}</strong>.
          <a href="{{ $incompleto['edit_url'] }}" class="alert-link ms-2">
            <i class="bi bi-pencil-square"></i> Editar cliente
          </a>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Seleccionar Cliente para Cotizar</h4>
          
          <p class="text-muted mb-4">
            Seleccione el cliente para el cual desea generar una cotización. 
            Se mostrarán los precios correspondientes a la lista de precios asignada al cliente.
          </p>

          {{-- Selector rápido con buscador (AJAX) --}}
          <div class="card mb-4 border-primary">
            <div class="card-body">
              <label class="form-label fw-semibold">
                <i class="bi bi-lightning-charge"></i> Selección rápida
              </label>
              <form id="formSeleccionRapida" action="{{ route('catalogo.cliente') }}" method="POST">
                @csrf
                <div class="d-flex gap-2">
                  <select id="selectorRapidoCliente" name="cliente_id"
                          class="form-select cliente-select2-ajax flex-grow-1"
                          data-placeholder="Buscar cliente por nombre, documento o email...">
                    <option value=""></option>
                  </select>
                  <button type="submit" class="btn btn-primary" id="btnSeleccionRapida" disabled>
                    <i class="bi bi-cart"></i> Cotizar
                  </button>
                </div>
                <small class="text-muted d-block mt-1">
                  Escribe el nombre, identificación o email del cliente para encontrarlo rápido.
                </small>
              </form>
            </div>
          </div>

          {{-- Filtros de búsqueda --}}
          <div class="card mb-4">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Buscar por nombre</label>
                  <input type="text" class="form-control" id="buscarNombre"
                         placeholder="Nombre del cliente...">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Ciudad</label>
                  <select class="form-select" id="filtroCiudad">
                    <option value="">Todas las ciudades</option>
                    @foreach($clientes->pluck('ciudad')->unique()->filter() as $ciudad)
                      <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Lista de Precios</label>
                  <select class="form-select" id="filtroLista">
                    <option value="">Todas las listas</option>
                    @foreach($clientes->pluck('listaPrecio')->unique()->filter() as $lista)
                      <option value="{{ $lista->id }}">{{ $lista->nombre }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2 mb-3">
                  <label class="form-label">&nbsp;</label>
                  <button class="btn btn-secondary w-100" id="btnLimpiarFiltros">
                    <i class="bi bi-x-circle"></i> Limpiar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="row" id="clientesContainer">
            @forelse($clientes as $cliente)
              @php
                // Cliente "solo de Servicio Técnico" o sin configuración para cotizar:
                // requiere al menos lista_precio_id; el resto se considera deseable.
                $faltantes = [];
                if (!$cliente->lista_precio_id) $faltantes[] = 'lista de precios';
                if (!$cliente->vendedor_id)     $faltantes[] = 'vendedor';
                if (!$cliente->ciudad_id)       $faltantes[] = 'ciudad';
                $bloqueante = !$cliente->lista_precio_id; // sin lista no se puede cotizar
                $tieneAdvertencia = count($faltantes) > 0;
              @endphp

              <div class="col-md-4 mb-4 cliente-card"
                   data-nombre="{{ strtolower($cliente->nombre_contacto) }}"
                   data-ciudad="{{ $cliente->ciudad?->id }}"
                   data-lista="{{ $cliente->lista_precio_id }}">
                <div class="card h-100 {{ $bloqueante ? 'border-warning' : '' }}">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <h5 class="card-title mb-2">{{ $cliente->nombre_contacto }}</h5>
                      @if($cliente->tipo_documento)
                        <span class="badge bg-light text-dark" title="Cliente registrado en Servicio Técnico">
                          <i class="bi bi-tools"></i> ST
                        </span>
                      @endif
                    </div>
                    <p class="card-text">
                      <small class="text-muted">
                        <i class="bi bi-geo-alt"></i> {{ $cliente->ciudad?->nombre ?? $cliente->ciudad_texto ?? 'Sin ciudad' }}<br>
                        <i class="bi bi-telephone"></i> {{ $cliente->telefono ?? $cliente->celular ?? '—' }}<br>
                        <i class="bi bi-envelope"></i> {{ $cliente->email ?? '—' }}<br>
                        <i class="bi bi-tag"></i> Lista: {{ $cliente->listaPrecio?->nombre ?? 'Sin lista' }}
                        @if(auth()->user()->hasRole('admin'))
                          <br><i class="bi bi-person"></i> Vendedor: {{ $cliente->vendedor?->name ?? 'Sin vendedor' }}
                        @endif
                      </small>
                    </p>

                    @if($tieneAdvertencia)
                      <div class="alert {{ $bloqueante ? 'alert-warning' : 'alert-info' }} py-2 px-2 mb-2 small">
                        <i class="bi bi-exclamation-triangle"></i>
                        @if($bloqueante)
                          <strong>No se puede cotizar.</strong>
                        @endif
                        Falta configurar: <strong>{{ implode(', ', $faltantes) }}</strong>.
                        <a href="{{ route('clientes.form', $cliente->id) }}" class="alert-link d-block mt-1">
                          <i class="bi bi-pencil-square"></i> Editar cliente para completar
                        </a>
                      </div>
                    @endif

                    @if($bloqueante)
                      <a href="{{ route('clientes.form', $cliente->id) }}" class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-pencil-square"></i> Completar datos del cliente
                      </a>
                    @else
                      <form action="{{ route('catalogo.cliente') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                          <i class="bi bi-cart"></i> Seleccionar
                        </button>
                      </form>
                    @endif
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i> No tiene clientes asignados activos.
                </div>
              </div>
            @endforelse
          </div>

          {{-- Mensaje cuando no hay resultados --}}
          <div class="col-12" id="noResultados" style="display:none;">
            <div class="alert alert-warning">
              <i class="bi bi-search"></i> No se encontraron clientes con los filtros aplicados.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    $(document).ready(function() {
      // ===== Selector rápido con buscador AJAX =====
      var $rapido = $('#selectorRapidoCliente');
      $rapido.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: $rapido.data('placeholder'),
        ajax: {
          url: "{{ route('clientes.buscar-ajax') }}",
          dataType: 'json',
          delay: 250,
          data: function (params) { return { q: params.term, page: params.page || 1 }; },
          processResults: function (data, params) {
            params.page = params.page || 1;
            // Marcar visualmente clientes sin lista_precio (no se pueden cotizar)
            (data.results || []).forEach(function (r) {
              if (r.has_lista_precio === false) {
                r.disabled = true;
                r.text = r.text + ' (sin lista de precios — editar primero)';
              }
            });
            return data;
          },
          cache: true
        }
      });

      // Habilitar botón cuando haya selección
      $rapido.on('change', function () {
        $('#btnSeleccionRapida').prop('disabled', !$(this).val());
      });

      function filtrarClientes() {
        const busqueda = $('#buscarNombre').val().toLowerCase();
        const ciudadId = $('#filtroCiudad').val();
        const listaId = $('#filtroLista').val();
        let visibles = 0;

        $('.cliente-card').each(function() {
          const $card = $(this);
          const nombre = $card.data('nombre');
          const ciudad = $card.data('ciudad');
          const lista = $card.data('lista');

          let mostrar = true;

          // Filtro por nombre
          if (busqueda && !nombre.includes(busqueda)) {
            mostrar = false;
          }

          // Filtro por ciudad
          if (ciudadId && ciudad != ciudadId) {
            mostrar = false;
          }

          // Filtro por lista
          if (listaId && lista != listaId) {
            mostrar = false;
          }

          if (mostrar) {
            $card.show();
            visibles++;
          } else {
            $card.hide();
          }
        });

        // Mostrar mensaje si no hay resultados
        if (visibles === 0) {
          $('#noResultados').show();
        } else {
          $('#noResultados').hide();
        }
      }

      // Eventos de filtros
      $('#buscarNombre').on('keyup', function() {
        filtrarClientes();
      });

      $('#filtroCiudad, #filtroLista').on('change', function() {
        filtrarClientes();
      });

      $('#btnLimpiarFiltros').click(function() {
        $('#buscarNombre').val('');
        $('#filtroCiudad').val('');
        $('#filtroLista').val('');
        filtrarClientes();
      });
    });
  </script>
  @endpush
</x-app-layout>