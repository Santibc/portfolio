<x-app-layout>
  <x-slot name="header">Garantías</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Gestión de Garantías</h4>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <div class="row mb-4 g-2">
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Estado</label>
              <select id="filtro-estado" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="liberado">Liberado</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Cliente</label>
              <select id="filtro-cliente" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Tipo</label>
              <select id="filtro-tipo" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach($tipos as $valor => $nombre)
                  <option value="{{ $valor }}">{{ $nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Desde</label>
              <input type="date" id="filtro-fecha-desde" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Hasta</label>
              <input type="date" id="filtro-fecha-hasta" class="form-control form-control-sm">
            </div>
            <div class="col-md-1 d-flex align-items-end">
              <button class="btn btn-sm btn-outline-secondary w-100" onclick="limpiarFiltros()" title="Limpiar filtros">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>

          <table id="garantias-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Cotización</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalLiberar" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Liberar Garantía</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="liberar-garantia-id">
          <div class="mb-3">
            <label class="form-label">Observación de liberación <span class="text-danger">*</span></label>
            <textarea id="liberar-observacion" class="form-control" rows="4" placeholder="Describe el motivo o resultado de la liberación..." required></textarea>
            <small class="text-muted">Mínimo 5 caracteres.</small>
          </div>

          @include('garantias._productos_cambio', ['prefix' => 'index', 'ubicaciones' => $ubicaciones ?? collect()])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" onclick="confirmarLiberar()"><i class="bi bi-unlock"></i> Liberar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalVer" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de Garantía</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="verContent"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="{{ asset('js/garantias/productos-cambio.js') }}"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const garProdIndex = window.GarantiaProductosCambio('index');
    garProdIndex.init();

    const table = $('#garantias-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: {
        url: "{{ route('garantias.index') }}",
        data: function(d) {
          d.estado = $('#filtro-estado').val();
          d.cliente_id = $('#filtro-cliente').val();
          d.tipo = $('#filtro-tipo').val();
          d.fecha_desde = $('#filtro-fecha-desde').val();
          d.fecha_hasta = $('#filtro-fecha-hasta').val();
        }
      },
      columns: [
        { data: 'action', orderable: false, searchable: false },
        { data: 'id_badge', name: 'id' },
        { data: 'fecha_creacion', name: 'created_at' },
        { data: 'cliente_nombre', name: 'cliente.nombre_contacto', orderable: false },
        { data: 'producto_nombre', name: 'producto.nombre', orderable: false },
        { data: 'tipo_badge', name: 'tipo' },
        { data: 'estado_badge', name: 'estado' },
        { data: 'cotizacion_vinculada', name: 'solicitud.numero_solicitud', orderable: false },
      ],
      order: [[2, 'desc']],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas' },
        { extend: 'excelHtml5', className: 'btn btn-outline-success', text: 'Excel' },
        {
          text: '<i class="bi bi-plus-lg"></i> Nueva Garantía',
          className: 'btn btn-primary',
          action: () => window.location.href = "{{ route('garantias.crear') }}"
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']]
    });

    $('#filtro-estado, #filtro-cliente, #filtro-tipo, #filtro-fecha-desde, #filtro-fecha-hasta').on('change', () => table.ajax.reload());

    window.limpiarFiltros = function() {
      ['filtro-estado','filtro-cliente','filtro-tipo','filtro-fecha-desde','filtro-fecha-hasta'].forEach(id => document.getElementById(id).value = '');
      table.ajax.reload();
    };

    window.liberarGarantia = function(id) {
      document.getElementById('liberar-garantia-id').value = id;
      document.getElementById('liberar-observacion').value = '';
      garProdIndex.reset();
      new bootstrap.Modal(document.getElementById('modalLiberar')).show();
    };

    window.confirmarLiberar = function() {
      const id = document.getElementById('liberar-garantia-id').value;
      const observacion = document.getElementById('liberar-observacion').value.trim();
      if (observacion.length < 5) {
        Swal.fire('Observación requerida', 'Debes ingresar una observación de al menos 5 caracteres.', 'warning');
        return;
      }
      const val = garProdIndex.validate();
      if (!val.ok) {
        Swal.fire('Datos incompletos', val.error, 'warning');
        return;
      }
      const body = Object.assign({ observacion_liberacion: observacion }, garProdIndex.getPayload());

      fetch(`/garantias/${id}/liberar`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(body)
      })
      .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || data.message || 'Error al liberar');
        return data;
      })
      .then(data => {
        Swal.fire('Liberada', data.mensaje, 'success');
        bootstrap.Modal.getInstance(document.getElementById('modalLiberar')).hide();
        table.ajax.reload();
      })
      .catch(err => Swal.fire('Error', err.message, 'error'));
    };

    window.verGarantia = function(id) {
      fetch(`/garantias/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(g => {
          let docs = '';
          if (g.documentos.length === 0) {
            docs = '<p class="text-muted">Sin documentos.</p>';
          } else {
            docs = '<ul class="list-group">' + g.documentos.map(d => `
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark"></i> ${d.nombre_original}</span>
                <a href="${d.url_descarga}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bi bi-download"></i> Descargar</a>
              </li>`).join('') + '</ul>';
          }

          const escapeHtml = (str) => {
            if (str === null || str === undefined) return '';
            return String(str)
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#39;')
              .replace(/\n/g, '<br>');
          };

          let observacionCreacion = '';
          if (g.observacion_creacion) {
            observacionCreacion = `
              <div class="alert alert-info mt-3 mb-0">
                <h6 class="mb-2"><i class="bi bi-chat-left-text"></i> Observación de creación</h6>
                <p class="mb-0">${escapeHtml(g.observacion_creacion)}</p>
              </div>`;
          }

          let productosLiberacionHtml = '';
          if (Array.isArray(g.productos_liberacion) && g.productos_liberacion.length > 0) {
            productosLiberacionHtml = `
              <div class="mt-3">
                <h6 class="mb-2"><i class="bi bi-box-seam"></i> Productos descontados de stock</h6>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                      <tr><th>Producto</th><th>Variante</th><th>Ubicación</th><th class="text-center">Cantidad</th></tr>
                    </thead>
                    <tbody>
                      ${g.productos_liberacion.map(p => `
                        <tr>
                          <td>${escapeHtml(p.producto) || '—'}</td>
                          <td>${escapeHtml(p.variante) || '—'}</td>
                          <td>${escapeHtml(p.ubicacion) || '—'}</td>
                          <td class="text-center">${p.cantidad}</td>
                        </tr>`).join('')}
                    </tbody>
                  </table>
                </div>
              </div>`;
          }

          let liberacion = '';
          if (g.estado === 'liberado') {
            liberacion = `
              <div class="alert alert-success mt-3 mb-0">
                <h6 class="mb-2"><i class="bi bi-check-circle"></i> Liberada</h6>
                <p class="mb-1"><strong>Observación de liberación:</strong><br>${escapeHtml(g.observacion_liberacion)}</p>
                <p class="mb-1"><strong>Liberada por:</strong> ${escapeHtml(g.usuario_liberador) || '—'}</p>
                <p class="mb-0"><strong>Fecha:</strong> ${escapeHtml(g.liberado_en) || '—'}</p>
                ${g.cotizacion ? `<p class="mb-0 mt-2"><strong>Cotización vinculada:</strong> <span class="badge bg-info">${escapeHtml(g.cotizacion.numero)}</span></p>` : ''}
                ${productosLiberacionHtml}
              </div>`;
          } else if (g.cotizacion) {
            liberacion = `<div class="mt-3"><strong>Cotización vinculada:</strong> <span class="badge bg-info">${escapeHtml(g.cotizacion.numero)}</span></div>`;
          }

          let productosHtml = '';
          if (Array.isArray(g.items) && g.items.length > 0) {
            productosHtml = '<ul class="mb-0 ps-3">' + g.items.map(it => {
              let n = escapeHtml(it.producto) || 'Sin producto';
              if (it.variante) n += ' — ' + escapeHtml(it.variante);
              return `<li>${n} <span class="badge bg-secondary">x${it.cantidad}</span></li>`;
            }).join('') + '</ul>';
          } else {
            productosHtml = '<span class="text-muted">Sin productos asociados</span>';
          }

          document.getElementById('verContent').innerHTML = `
            <div class="row g-3">
              <div class="col-md-6"><strong>Cliente:</strong><br>${escapeHtml(g.cliente) || '—'}</div>
              <div class="col-md-6"><strong>Productos:</strong><br>${productosHtml}</div>
              <div class="col-md-6"><strong>Tipo:</strong><br>${escapeHtml(g.tipo_legible)}</div>
              <div class="col-md-6"><strong>Estado:</strong><br><span class="badge bg-${g.estado === 'pendiente' ? 'warning text-dark' : 'success'}">${escapeHtml(g.estado)}</span></div>
              <div class="col-md-6"><strong>Creada por:</strong><br>${escapeHtml(g.usuario_creador) || '—'}</div>
              <div class="col-md-6"><strong>Fecha de creación:</strong><br>${escapeHtml(g.created_at) || '—'}</div>
            </div>
            ${observacionCreacion}
            <hr>
            <h6>Documentos adjuntos</h6>
            ${docs}
            ${liberacion}
          `;
          new bootstrap.Modal(document.getElementById('modalVer')).show();
        })
        .catch(() => Swal.fire('Error', 'No se pudo cargar la garantía', 'error'));
    };
  });
  </script>
  @endpush
</x-app-layout>
