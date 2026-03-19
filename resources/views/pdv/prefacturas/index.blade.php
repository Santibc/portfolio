<x-app-layout>
    @section('title', 'Prefacturas')
    @push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Prefacturas</h4>
            <div>
                @if(auth()->user()->hasRole(['auxiliar_venta', 'vendedor', 'admin']))
                    <a href="{{ route('pdv.prefacturas.crear') }}" class="btn text-white" style="background: var(--miracle-pink);">
                        <i class="bi bi-plus-lg me-1"></i>Nueva Prefactura
                    </a>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select id="filtroEstado" class="form-select form-select-sm">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aceptada">Aceptada</option>
                            <option value="anulada">Anulada</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="tablaPrefacturas" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Creador</th>
                            <th>Cajero</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

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
            tabla = $('#tablaPrefacturas').DataTable({
                processing: true, serverSide: true,
                ajax: {
                    url: '{{ route("pdv.prefacturas.index") }}',
                    data: d => { d.estado = $('#filtroEstado').val(); }
                },
                columns: [
                    { data: 'numero_prefactura' },
                    { data: 'cliente_display', orderable: false },
                    { data: 'total', render: v => '$' + parseFloat(v).toFixed(2) },
                    { data: 'creador_nombre', orderable: false },
                    { data: 'cajero_nombre', orderable: false },
                    { data: 'estado_badge', orderable: false },
                    { data: 'created_at' },
                    { data: 'action', orderable: false, searchable: false },
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                order: [[6, 'desc']],
            });
            $('#filtroEstado').change(() => tabla.ajax.reload());
        });

        function verDetalle(id) {
            fetch(`/pdv/prefacturas/${id}/detalle`).then(r => r.text()).then(html => {
                document.getElementById('modalDetalleContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalDetalle')).show();
            });
        }

        function aceptarPrefactura(id) {
            Swal.fire({
                title: 'Aceptar Prefactura',
                html: `<select id="swalMetodoPago" class="form-select mb-2">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="mixto">Mixto</option>
                </select>`,
                showCancelButton: true, confirmButtonText: 'Aceptar y crear venta',
                confirmButtonColor: '#28a745',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/pdv/prefacturas/${id}/aceptar`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ metodo_pago: document.getElementById('swalMetodoPago').value }),
                    }).then(r => r.json()).then(data => {
                        Swal.fire(data.exito ? 'Aceptada' : 'Error', data.mensaje, data.exito ? 'success' : 'error');
                        tabla.ajax.reload();
                    });
                }
            });
        }

        function anularPrefactura(id) {
            Swal.fire({
                title: 'Anular Prefactura',
                input: 'textarea', inputLabel: 'Motivo (mínimo 5 caracteres)',
                inputValidator: v => { if (!v || v.length < 5) return 'Ingrese un motivo válido'; },
                showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Anular',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/pdv/prefacturas/${id}/anular`, {
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
    </script>
    @endpush
</x-app-layout>
