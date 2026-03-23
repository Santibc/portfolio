<x-app-layout>
    @section('title', 'Historial de Ventas')

    @push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Historial de Ventas</h4>
            <div>
                <a href="{{ route('pdv.ventas.crear') }}" class="btn text-white" style="background: var(--miracle-pink);">
                    <i class="bi bi-cart-plus me-1"></i>Nueva Venta
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-2">
                        <select id="filtroEstado" class="form-select form-select-sm">
                            <option value="">Todos los estados</option>
                            <option value="completada">Completada</option>
                            <option value="anulada">Anulada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="filtroCaja" class="form-select form-select-sm">
                            <option value="">Todas las cajas</option>
                            @foreach($cajas as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="filtroMetodo" class="form-select form-select-sm">
                            <option value="">Todos los métodos</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="mixto">Mixto</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="filtroDesde" class="form-control form-control-sm" placeholder="Desde">
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="filtroHasta" class="form-control form-control-sm" placeholder="Hasta">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="limpiarFiltros()">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="tablaVentas" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Nro. Venta</th>
                            <th>Caja</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Método</th>
                            <th>Cajero</th>
                            <th>Estado</th>
                            <th>Factura</th>
                            <th>Fecha</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow" id="modalDetalleContent"></div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let tabla;
        $(function() {
            tabla = $('#tablaVentas').DataTable({
                processing: true, serverSide: true,
                ajax: {
                    url: '{{ route("pdv.ventas.index") }}',
                    data: function(d) {
                        d.estado = $('#filtroEstado').val();
                        d.caja_id = $('#filtroCaja').val();
                        d.metodo_pago = $('#filtroMetodo').val();
                        d.fecha_desde = $('#filtroDesde').val();
                        d.fecha_hasta = $('#filtroHasta').val();
                    }
                },
                columns: [
                    { data: 'numero_venta', name: 'numero_venta' },
                    { data: 'caja_nombre', name: 'caja_nombre', orderable: false },
                    { data: 'cliente_display', name: 'cliente_display', orderable: false },
                    { data: 'total', name: 'total', render: v => '$' + parseFloat(v).toLocaleString('es-CO', {minimumFractionDigits: 2}) },
                    { data: 'metodo_badge', name: 'metodo_pago', orderable: false },
                    { data: 'usuario_nombre', name: 'usuario_nombre', orderable: false },
                    { data: 'estado_badge', name: 'estado', orderable: false },
                    { data: 'factura_badge', name: 'factura_badge', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', orderable: false, searchable: false },
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                order: [[8, 'desc']],
            });

            $('#filtroEstado, #filtroCaja, #filtroMetodo, #filtroDesde, #filtroHasta').change(() => tabla.ajax.reload());
        });

        function limpiarFiltros() {
            $('#filtroEstado, #filtroCaja, #filtroMetodo').val('');
            $('#filtroDesde, #filtroHasta').val('');
            tabla.ajax.reload();
        }

        function verDetalle(id) {
            fetch(`/pdv/ventas/${id}/detalle`).then(r => r.text()).then(html => {
                document.getElementById('modalDetalleContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalDetalle')).show();
            });
        }

        function anularVenta(id) {
            Swal.fire({
                title: 'Anular Venta',
                input: 'textarea',
                inputLabel: 'Motivo de anulación (mínimo 10 caracteres)',
                inputValidator: (v) => { if (!v || v.length < 10) return 'Ingrese un motivo de al menos 10 caracteres'; },
                showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Anular',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/pdv/ventas/${id}/anular`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ motivo_anulacion: result.value }),
                    }).then(r => r.json()).then(data => {
                        Swal.fire(data.exito ? 'Anulada' : 'Error', data.mensaje, data.exito ? 'success' : 'error');
                        tabla.ajax.reload();
                    });
                }
            });
        }

        // SIIGO invoice functions (called from detalle partial)
        function reenviarEmailFactura(ventaId) {
            fetch(`/pdv/ventas/${ventaId}/factura/reenviar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                Swal.fire(data.exito ? 'Enviado' : 'Error', data.mensaje, data.exito ? 'success' : 'error');
                if (data.exito) verDetalle(ventaId);
            }).catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
        }

        function consultarEstadoFactura(ventaId) {
            fetch(`/pdv/ventas/${ventaId}/factura/estado`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                Swal.fire('Estado Actualizado', data.mensaje, 'info');
                verDetalle(ventaId);
                tabla.ajax.reload();
            }).catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
        }

        function reintentarFactura(ventaId) {
            fetch(`/pdv/ventas/${ventaId}/factura/reintentar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                Swal.fire(data.exito ? 'Reintento exitoso' : 'Error', data.mensaje, data.exito ? 'success' : 'error');
                verDetalle(ventaId);
                tabla.ajax.reload();
            }).catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
        }

        function reintentarNotaCredito(ventaId, ncId) {
            fetch(`/pdv/ventas/${ventaId}/nota-credito/${ncId}/reintentar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                Swal.fire(data.exito ? 'Reintento NC' : 'Error', data.mensaje, data.exito ? 'success' : 'error');
                verDetalle(ventaId);
            }).catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
        }
    </script>
    @endpush
</x-app-layout>
