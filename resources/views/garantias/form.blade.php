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

            <div class="mb-2 position-relative">
              <label for="buscarProducto" class="form-label fw-semibold">Productos <small class="text-muted fw-normal">(opcional — puedes agregar varios)</small></label>
              <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar por nombre, referencia, SKU o código de barras..." autocomplete="off">
              <div id="resultadosBusqueda" class="position-absolute bg-white border rounded shadow-sm w-100" style="z-index: 1050; max-height: 320px; overflow-y: auto; display: none;"></div>
              <small class="text-muted d-block mt-1">Busca y selecciona un producto para agregarlo a la lista. La garantía se puede registrar sin productos.</small>
            </div>

            <div class="mb-3">
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" id="tablaProductos">
                  <thead class="table-light">
                    <tr>
                      <th>Producto</th>
                      <th style="width: 140px;">Cantidad</th>
                      <th style="width: 50px;"></th>
                    </tr>
                  </thead>
                  <tbody id="cuerpoProductos">
                    <tr id="filaVaciaProductos"><td colspan="3" class="text-center text-muted py-3">Sin productos agregados</td></tr>
                  </tbody>
                </table>
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
    const cuerpoProductos = document.getElementById('cuerpoProductos');
    let timeout = null;
    let filaIndex = 0;

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
              agregarProducto(
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

    function toggleFilaVacia() {
      const vacia = document.getElementById('filaVaciaProductos');
      const hayFilas = cuerpoProductos.querySelectorAll('tr.fila-producto').length > 0;
      if (vacia) vacia.style.display = hayFilas ? 'none' : '';
    }

    const escapeAttr = (str) => String(str ?? '').replace(/"/g, '&quot;');

    function agregarProducto(productoId, varianteId, nombre, cantidad = 1) {
      varianteId = varianteId || '';
      // Si ya está el mismo producto+variante, solo sube la cantidad.
      const existente = cuerpoProductos.querySelector(
        `tr.fila-producto[data-producto-id="${productoId}"][data-variante-id="${varianteId}"]`
      );
      if (existente) {
        const inp = existente.querySelector('input.cantidad-producto');
        inp.value = (parseInt(inp.value || '1', 10) + 1);
        inputBusqueda.value = '';
        resultados.style.display = 'none';
        inp.focus();
        return;
      }

      const idx = filaIndex++;
      const tr = document.createElement('tr');
      tr.className = 'fila-producto';
      tr.dataset.productoId = productoId;
      tr.dataset.varianteId = varianteId;
      tr.innerHTML = `
        <td>
          <i class="bi bi-box-seam text-primary"></i> ${escapeAttr(nombre)}
          <input type="hidden" name="items[${idx}][producto_id]" value="${escapeAttr(productoId)}">
          <input type="hidden" name="items[${idx}][variante_producto_id]" value="${escapeAttr(varianteId)}">
        </td>
        <td>
          <input type="number" name="items[${idx}][cantidad]" class="form-control form-control-sm cantidad-producto" value="${parseInt(cantidad, 10) || 1}" min="1" step="1" required>
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-producto" title="Quitar"><i class="bi bi-trash"></i></button>
        </td>`;
      cuerpoProductos.appendChild(tr);
      tr.querySelector('.btn-quitar-producto').addEventListener('click', function() {
        tr.remove();
        toggleFilaVacia();
      });
      toggleFilaVacia();
      inputBusqueda.value = '';
      resultados.style.display = 'none';
    }

    // Repoblar si hubo error de validación.
    @if(old('items'))
      @foreach(old('items') as $it)
        @php $prod = \App\Models\Producto::find($it['producto_id'] ?? null); @endphp
        @if($prod)
          agregarProducto(
            @json($it['producto_id']),
            @json($it['variante_producto_id'] ?? ''),
            @json($prod->nombre),
            @json((int)($it['cantidad'] ?? 1))
          );
        @endif
      @endforeach
    @endif

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
      // El producto es opcional: la garantía puede registrarse sin productos.
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
