<x-app-layout>
  <x-slot name="header">Novedades de Stock</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Gestión de Novedades (Garantías, Saldos, Pérdidas)</h4>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <!-- Filtros -->
          <div class="row mb-4">
            <div class="col-md-3">
              <label class="form-label">Tipo</label>
              <select id="filtro-tipo" class="form-select">
                <option value="">Todos</option>
                @foreach($tipos as $valor => $nombre)
                  <option value="{{ $valor }}">{{ $nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select id="filtro-estado" class="form-select">
                <option value="">Todos</option>
                @foreach($estados as $valor => $nombre)
                  <option value="{{ $valor }}">{{ $nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <table id="novedades-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Acciones</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Valor</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Cerrar Novedad -->
  <div class="modal fade" id="modalCerrar" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cerrar Novedad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="cerrar-novedad-id">
          <div class="mb-3">
            <label class="form-label">Nuevo Estado *</label>
            <select id="cerrar-estado" class="form-select" required>
              <option value="">Seleccione...</option>
              <option value="procesado">Procesado</option>
              <option value="recuperado">Recuperado</option>
              <option value="dado_de_baja">Dado de Baja</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Notas de Cierre</label>
            <textarea id="cerrar-notas" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="recuperar-stock">
            <label class="form-check-label" for="recuperar-stock">
              Recuperar stock al inventario (aplica para garantías procesadas exitosamente)
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="confirmarCierre()">Cerrar Novedad</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Detalle -->
  <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de Novedad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="detalleContent">
          <!-- Contenido cargado dinámicamente -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const table = $('#novedades-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: {
        url: "{{ route('novedades-stock') }}",
        data: function(d) {
          d.tipo = $('#filtro-tipo').val();
          d.estado = $('#filtro-estado').val();
        }
      },
      columns: [
        { data: 'action', orderable: false, searchable: false },
        { data: 'producto_nombre', name: 'producto.nombre' },
        { data: 'tipo_badge', name: 'tipo' },
        { data: 'cantidad', name: 'cantidad' },
        { data: 'valor_formateado', name: 'valor_original' },
        { data: 'ubicacion_nombre', name: 'ubicacion.nombre', orderable: false },
        { data: 'estado_badge', name: 'estado' },
        { data: 'created_at', name: 'created_at' },
      ],
      order: [[7, 'desc']],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas' },
        { extend: 'colvis', className: 'btn btn-outline-dark', text: 'Columnas', columns: ':not(.noVis)' },
        { extend: 'excelHtml5', className: 'btn btn-outline-success', text: 'Excel' },
        {
          text: 'Nueva Novedad',
          className: 'btn btn-outline-primary',
          action: () => window.location.href = "{{ route('novedades-stock.form') }}"
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']]
    });

    // Recargar tabla cuando cambian los filtros
    $('#filtro-tipo, #filtro-estado').on('change', function() {
      table.ajax.reload();
    });

    table.on('buttons-action', () => {
      setTimeout(() => {
        $('.dt-button-collection')
          .addClass('bg-white border rounded shadow-md mt-2 p-2')
          .css({ position: 'absolute', 'z-index': 999, top: 'calc(100% + .5rem)', left: 0 });
        $('.dt-button-collection button')
          .removeClass()
          .addClass('block w-full text-left px-4 py-2 rounded hover:bg-gray-100');
      }, 50);
    });

    window.cerrarNovedad = function(id) {
      document.getElementById('cerrar-novedad-id').value = id;
      document.getElementById('cerrar-estado').value = '';
      document.getElementById('cerrar-notas').value = '';
      document.getElementById('recuperar-stock').checked = false;
      new bootstrap.Modal(document.getElementById('modalCerrar')).show();
    };

    window.confirmarCierre = function() {
      const id = document.getElementById('cerrar-novedad-id').value;
      const estado = document.getElementById('cerrar-estado').value;
      const notas = document.getElementById('cerrar-notas').value;
      const recuperarStock = document.getElementById('recuperar-stock').checked;

      if (!estado) {
        Swal.fire('Error', 'Debe seleccionar un estado', 'error');
        return;
      }

      fetch(`/novedades-stock/${id}/cerrar`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          estado: estado,
          notas_cierre: notas,
          recuperar_stock: recuperarStock
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire('Cerrado', data.message, 'success');
          bootstrap.Modal.getInstance(document.getElementById('modalCerrar')).hide();
          table.ajax.reload();
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'No se pudo cerrar la novedad', 'error'));
    };

    window.verDetalleNovedad = function(id) {
      fetch(`/novedades-stock/${id}/detalle`)
        .then(res => res.json())
        .then(data => {
          let html = `
            <div class="row">
              <div class="col-md-6">
                <p><strong>Producto:</strong> ${data.producto?.nombre || 'N/A'}${data.variante_producto ? ' - ' + data.variante_producto.nombre_variante : ''}</p>
                <p><strong>Tipo:</strong> ${data.tipo}</p>
                <p><strong>Cantidad:</strong> ${data.cantidad}</p>
                <p><strong>Valor Original:</strong> $${parseFloat(data.valor_original).toFixed(2)}</p>
                ${data.valor_saldo ? `<p><strong>Valor Saldo:</strong> $${parseFloat(data.valor_saldo).toFixed(2)}</p>` : ''}
              </div>
              <div class="col-md-6">
                <p><strong>Ubicación:</strong> ${data.ubicacion?.nombre || 'N/A'}</p>
                <p><strong>Estado:</strong> ${data.estado}</p>
                <p><strong>Registrado por:</strong> ${data.usuario?.name || 'N/A'}</p>
                <p><strong>Fecha:</strong> ${data.created_at}</p>
              </div>
            </div>
            <hr>
            <p><strong>Descripción:</strong> ${data.descripcion}</p>
            ${data.numero_garantia ? `<p><strong>Número de Garantía:</strong> ${data.numero_garantia}</p>` : ''}
            ${data.fecha_vencimiento_garantia ? `<p><strong>Vence:</strong> ${data.fecha_vencimiento_garantia}</p>` : ''}
            ${data.cerrado_en ? `
              <hr>
              <p><strong>Cerrado por:</strong> ${data.usuario_cierre?.name || 'N/A'}</p>
              <p><strong>Fecha de cierre:</strong> ${data.cerrado_en}</p>
              ${data.notas_cierre ? `<p><strong>Notas de cierre:</strong> ${data.notas_cierre}</p>` : ''}
            ` : ''}
          `;
          document.getElementById('detalleContent').innerHTML = html;
          new bootstrap.Modal(document.getElementById('modalDetalle')).show();
        })
        .catch(() => Swal.fire('Error', 'No se pudo cargar el detalle', 'error'));
    };
  });
  </script>
  @endpush
</x-app-layout>
