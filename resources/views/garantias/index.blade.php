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
    <div class="modal-dialog">
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
  <script>
  document.addEventListener('DOMContentLoaded', () => {
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
        { data: 'fecha_creacion', name: 'created_at' },
        { data: 'cliente_nombre', name: 'cliente.nombre_contacto', orderable: false },
        { data: 'producto_nombre', name: 'producto.nombre', orderable: false },
        { data: 'tipo_badge', name: 'tipo' },
        { data: 'estado_badge', name: 'estado' },
        { data: 'cotizacion_vinculada', name: 'solicitud.numero_solicitud', orderable: false },
      ],
      order: [[1, 'desc']],
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
      new bootstrap.Modal(document.getElementById('modalLiberar')).show();
    };

    window.confirmarLiberar = function() {
      const id = document.getElementById('liberar-garantia-id').value;
      const observacion = document.getElementById('liberar-observacion').value.trim();
      if (observacion.length < 5) {
        Swal.fire('Observación requerida', 'Debes ingresar una observación de al menos 5 caracteres.', 'warning');
        return;
      }
      fetch(`/garantias/${id}/liberar`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ observacion_liberacion: observacion })
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

          let liberacion = '';
          if (g.estado === 'liberado') {
            liberacion = `
              <div class="alert alert-success mt-3">
                <h6 class="mb-2"><i class="bi bi-check-circle"></i> Liberada</h6>
                <p class="mb-1"><strong>Observación:</strong> ${g.observacion_liberacion ?? ''}</p>
                <p class="mb-1"><strong>Liberada por:</strong> ${g.usuario_liberador ?? '—'}</p>
                <p class="mb-0"><strong>Fecha:</strong> ${g.liberado_en ?? '—'}</p>
                ${g.cotizacion ? `<p class="mb-0 mt-2"><strong>Cotización vinculada:</strong> <span class="badge bg-info">${g.cotizacion.numero}</span></p>` : ''}
              </div>`;
          } else if (g.cotizacion) {
            liberacion = `<div class="mt-3"><strong>Cotización vinculada:</strong> <span class="badge bg-info">${g.cotizacion.numero}</span></div>`;
          }

          document.getElementById('verContent').innerHTML = `
            <div class="row g-3">
              <div class="col-md-6"><strong>Cliente:</strong><br>${g.cliente ?? '—'}</div>
              <div class="col-md-6"><strong>Producto:</strong><br>${g.producto ?? '—'}${g.variante ? ' — ' + g.variante : ''}</div>
              <div class="col-md-6"><strong>Tipo:</strong><br>${g.tipo_legible}</div>
              <div class="col-md-6"><strong>Estado:</strong><br><span class="badge bg-${g.estado === 'pendiente' ? 'warning text-dark' : 'success'}">${g.estado}</span></div>
              <div class="col-md-6"><strong>Creada por:</strong><br>${g.usuario_creador ?? '—'}</div>
              <div class="col-md-6"><strong>Fecha de creación:</strong><br>${g.created_at ?? '—'}</div>
            </div>
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
