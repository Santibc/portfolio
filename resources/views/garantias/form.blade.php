<x-app-layout>
  <x-slot name="header">Nueva Garantía</x-slot>

  @push('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  @endpush

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-2xl font-semibold mb-0">Registrar Garantía</h4>
            <a href="{{ route('garantias.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left"></i> Volver
            </a>
          </div>

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error:</strong> {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger">
              <strong>Por favor corrige los siguientes errores:</strong>
              <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('garantias.store') }}" method="POST" enctype="multipart/form-data" id="formGarantia">
            @csrf

            <div class="mb-3">
              <label for="cliente_id" class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
              <select name="cliente_id" id="cliente_id" class="form-select select2-cliente" required>
                <option value="">Seleccione un cliente...</option>
                @foreach($clientes as $c)
                  @php
                    $partesTexto = [];
                    if (!empty($c->razon_social)) $partesTexto[] = $c->razon_social;
                    if (!empty($c->nombre_contacto) && $c->nombre_contacto !== $c->razon_social) $partesTexto[] = $c->nombre_contacto;
                    $idDoc = $c->numero_identificacion ?? null;
                    $textoOpcion = implode(' — ', $partesTexto) . ($idDoc ? " ({$idDoc})" : '');
                  @endphp
                  <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                    {{ $textoOpcion }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mb-3 position-relative">
              <label for="buscarProducto" class="form-label fw-semibold">Producto <span class="text-danger">*</span></label>
              <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar por nombre, referencia, SKU o código de barras..." autocomplete="off">
              <input type="hidden" name="producto_id" id="producto_id" value="{{ old('producto_id') }}">
              <input type="hidden" name="variante_producto_id" id="variante_producto_id" value="{{ old('variante_producto_id') }}">
              <div id="resultadosBusqueda" class="position-absolute bg-white border rounded shadow-sm w-100" style="z-index: 1050; max-height: 320px; overflow-y: auto; display: none;"></div>
              <div id="productoSeleccionado" class="mt-2" style="display: none;">
                <div class="alert alert-success py-2 mb-0 d-flex justify-content-between align-items-center">
                  <span><i class="bi bi-check-circle"></i> <span id="productoSeleccionadoTexto"></span></span>
                  <button type="button" class="btn-close" onclick="limpiarProducto()"></button>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="tipo" class="form-label fw-semibold">Tipo de garantía <span class="text-danger">*</span></label>
              <select name="tipo" id="tipo" class="form-select" required onchange="toggleOtroTipo()">
                <option value="">Seleccione un tipo...</option>
                @foreach($tipos as $valor => $nombre)
                  <option value="{{ $valor }}" {{ old('tipo') == $valor ? 'selected' : '' }}>{{ $nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3" id="campoOtroTipo" style="display: none;">
              <label for="tipo_otro_descripcion" class="form-label fw-semibold">Especifica el tipo <span class="text-danger">*</span></label>
              <textarea name="tipo_otro_descripcion" id="tipo_otro_descripcion" class="form-control" rows="2" maxlength="500" placeholder="Describe el tipo de garantía que se le va a dar...">{{ old('tipo_otro_descripcion') }}</textarea>
            </div>

            <div class="mb-3">
              <label for="observacion_creacion" class="form-label fw-semibold">Observación <small class="text-muted fw-normal">(opcional)</small></label>
              <textarea name="observacion_creacion" id="observacion_creacion" class="form-control" rows="3" maxlength="1000" placeholder="Notas u observaciones sobre la garantía...">{{ old('observacion_creacion') }}</textarea>
              <small class="text-muted">Máximo 1000 caracteres.</small>
            </div>

            <div class="mb-3">
              <label for="documentos" class="form-label fw-semibold">Documentos adjuntos <span class="text-muted">(opcional)</span></label>
              <input type="file" name="documentos[]" id="documentos" class="form-control" multiple>
              <small class="text-muted">Puedes adjuntar uno o más archivos de cualquier tipo. Máximo 10MB cada uno.</small>
              <div id="listaArchivos" class="mt-2"></div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
              <a href="{{ route('garantias.index') }}" class="btn btn-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar Garantía</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
  $(function() {
    $('.select2-cliente').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'Buscar cliente por nombre o NIT...',
      allowClear: true
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    const inputBusqueda = document.getElementById('buscarProducto');
    const resultados = document.getElementById('resultadosBusqueda');
    const seleccionado = document.getElementById('productoSeleccionado');
    const seleccionadoTexto = document.getElementById('productoSeleccionadoTexto');
    let timeout = null;

    inputBusqueda.addEventListener('input', function() {
      clearTimeout(timeout);
      const q = this.value.trim();
      if (q.length < 2) {
        resultados.style.display = 'none';
        return;
      }
      timeout = setTimeout(() => buscar(q), 300);
    });

    inputBusqueda.addEventListener('focus', function() {
      if (this.value.trim().length >= 2 && resultados.innerHTML !== '') {
        resultados.style.display = 'block';
      }
    });

    document.addEventListener('click', function(e) {
      if (!inputBusqueda.contains(e.target) && !resultados.contains(e.target)) {
        resultados.style.display = 'none';
      }
    });

    function buscar(q) {
      fetch(`{{ route('garantias.buscar-productos') }}?q=${encodeURIComponent(q)}`, {
        headers: { 'Accept': 'application/json' }
      })
      .then(r => r.json())
      .then(payload => {
        const items = payload.data || [];
        if (items.length === 0) {
          resultados.innerHTML = '<div class="p-3 text-muted text-center">Sin resultados</div>';
        } else {
          resultados.innerHTML = items.map(item => `
            <div class="p-2 border-bottom resultado-item" style="cursor: pointer;"
                 data-producto-id="${item.producto_id}"
                 data-variante-id="${item.variante_producto_id ?? ''}"
                 data-nombre="${item.nombre_completo.replace(/"/g, '&quot;')}">
              <div class="fw-semibold">${item.nombre_completo}</div>
              <small class="text-muted">
                Ref: ${item.referencia ?? '—'}
                ${item.sku ? ' | SKU: ' + item.sku : ''}
                ${item.codigo_barras ? ' | CB: ' + item.codigo_barras : ''}
              </small>
            </div>
          `).join('');
          document.querySelectorAll('.resultado-item').forEach(el => {
            el.addEventListener('click', function() {
              seleccionarProducto(
                this.dataset.productoId,
                this.dataset.varianteId || null,
                this.dataset.nombre
              );
            });
            el.addEventListener('mouseenter', function() { this.style.backgroundColor = '#f8f9fa'; });
            el.addEventListener('mouseleave', function() { this.style.backgroundColor = ''; });
          });
        }
        resultados.style.display = 'block';
      })
      .catch(() => {
        resultados.innerHTML = '<div class="p-3 text-danger text-center">Error al buscar</div>';
        resultados.style.display = 'block';
      });
    }

    function seleccionarProducto(productoId, varianteId, nombre) {
      document.getElementById('producto_id').value = productoId;
      document.getElementById('variante_producto_id').value = varianteId || '';
      seleccionadoTexto.textContent = nombre;
      seleccionado.style.display = 'block';
      inputBusqueda.value = '';
      resultados.style.display = 'none';
    }

    window.limpiarProducto = function() {
      document.getElementById('producto_id').value = '';
      document.getElementById('variante_producto_id').value = '';
      seleccionado.style.display = 'none';
    };

    window.toggleOtroTipo = function() {
      const tipo = document.getElementById('tipo').value;
      document.getElementById('campoOtroTipo').style.display = tipo === 'otro' ? 'block' : 'none';
    };

    document.getElementById('documentos').addEventListener('change', function(e) {
      const lista = document.getElementById('listaArchivos');
      lista.innerHTML = '';
      if (this.files.length === 0) return;
      const ul = document.createElement('ul');
      ul.className = 'list-group';
      Array.from(this.files).forEach(f => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `<span><i class="bi bi-file-earmark"></i> ${f.name}</span><small class="text-muted">${(f.size/1024).toFixed(1)} KB</small>`;
        ul.appendChild(li);
      });
      lista.appendChild(ul);
    });

    document.getElementById('formGarantia').addEventListener('submit', function(e) {
      const productoId = document.getElementById('producto_id').value;
      if (!productoId) {
        e.preventDefault();
        Swal.fire('Producto requerido', 'Debes seleccionar un producto antes de continuar.', 'warning');
        return;
      }
      const tipo = document.getElementById('tipo').value;
      if (tipo === 'otro') {
        const desc = document.getElementById('tipo_otro_descripcion').value.trim();
        if (!desc) {
          e.preventDefault();
          Swal.fire('Descripción requerida', 'Debes especificar el tipo de garantía cuando seleccionas "Otro".', 'warning');
          return;
        }
      }
    });

    toggleOtroTipo();
  });
  </script>
  @endpush
</x-app-layout>
