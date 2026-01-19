<x-app-layout>
    <x-slot name="header">
        Historial de Ventas PdV
        @if($ubicacionSeleccionada)
            <span class="badge bg-primary ms-2">{{ $ubicacionSeleccionada->nombre }}</span>
        @endif
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alertas --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            {{-- Filtros --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Ubicación</label>
                            <select id="filtroUbicacion" class="form-select">
                                <option value="">-- Todas --</option>
                                @foreach($ubicaciones as $ubi)
                                    <option value="{{ $ubi->id }}" {{ $ubicacionId == $ubi->id ? 'selected' : '' }}>
                                        {{ $ubi->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select id="filtroEstado" class="form-select">
                                <option value="">-- Todos --</option>
                                <option value="completada">Completadas</option>
                                <option value="anulada">Anuladas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Método Pago</label>
                            <select id="filtroMetodoPago" class="form-select">
                                <option value="">-- Todos --</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="mixto">Mixto</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" id="filtroFechaDesde" class="form-control"
                                   value="{{ now()->startOfMonth()->toDateString() }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" id="filtroFechaHasta" class="form-control"
                                   value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" onclick="filtrar()">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <a href="{{ route('punto-venta.nueva-venta') }}" class="btn btn-success">
                        <i class="bi bi-cart-plus"></i> Nueva Venta
                    </a>
                    <a href="{{ route('punto-venta.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </div>
                <div>
                    <a href="{{ route('punto-venta.reporte') }}" class="btn btn-info">
                        <i class="bi bi-bar-chart"></i> Reportes
                    </a>
                </div>
            </div>

            {{-- Tabla de ventas --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaVentas">
                            <thead class="table-dark">
                                <tr>
                                    <th>N° Venta</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Ubicación</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-center">Método</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Estado</th>
                                    <th>Vendedor</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detalle venta --}}
    <div class="modal fade" id="modalDetalleVenta" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalleVenta">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal anular venta --}}
    <div class="modal fade" id="modalAnularVenta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle"></i> Anular Venta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Esta acción no se puede deshacer. El stock será restaurado.
                    </div>
                    <input type="hidden" id="ventaIdAnular">
                    <div class="mb-3">
                        <label class="form-label">Motivo de anulación</label>
                        <textarea id="motivoAnulacion" class="form-control" rows="3"
                                  placeholder="Describa el motivo de la anulación (mínimo 10 caracteres)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="confirmarAnulacion()">
                        <i class="bi bi-x-circle"></i> Anular Venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let tablaVentas;

        $(document).ready(function() {
            tablaVentas = $('#tablaVentas').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("punto-venta.index") }}',
                    data: function(d) {
                        d.ubicacion_id = $('#filtroUbicacion').val();
                        d.estado = $('#filtroEstado').val();
                        d.metodo_pago = $('#filtroMetodoPago').val();
                        d.fecha_desde = $('#filtroFechaDesde').val();
                        d.fecha_hasta = $('#filtroFechaHasta').val();
                    }
                },
                columns: [
                    { data: 'numero_venta', name: 'numero_venta' },
                    { data: 'fecha', name: 'created_at' },
                    { data: 'cliente_nombre', name: 'cliente_nombre', orderable: false },
                    { data: 'ubicacion_nombre', name: 'ubicacion_nombre', orderable: false },
                    { data: 'items_count', name: 'items_count', className: 'text-center', orderable: false },
                    { data: 'metodo_pago_badge', name: 'metodo_pago', className: 'text-center' },
                    { data: 'total_formateado', name: 'total', className: 'text-end' },
                    { data: 'estado_badge', name: 'estado', className: 'text-center' },
                    { data: 'vendedor', name: 'vendedor', orderable: false },
                    { data: 'action', name: 'action', orderable: false, className: 'text-center' }
                ],
                order: [[1, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                responsive: true
            });
        });

        function filtrar() {
            tablaVentas.ajax.reload();
        }

        function verDetalle(ventaId) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleVenta'));
            document.getElementById('contenidoDetalleVenta').innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>';
            modal.show();

            fetch(`{{ url('punto-venta') }}/${ventaId}/detalle`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('contenidoDetalleVenta').innerHTML = data.html;
                })
                .catch(error => {
                    document.getElementById('contenidoDetalleVenta').innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
                });
        }

        function anularVenta(ventaId) {
            document.getElementById('ventaIdAnular').value = ventaId;
            document.getElementById('motivoAnulacion').value = '';
            new bootstrap.Modal(document.getElementById('modalAnularVenta')).show();
        }

        function confirmarAnulacion() {
            const ventaId = document.getElementById('ventaIdAnular').value;
            const motivo = document.getElementById('motivoAnulacion').value;

            if (motivo.length < 10) {
                Swal.fire('Error', 'El motivo debe tener al menos 10 caracteres', 'warning');
                return;
            }

            fetch(`{{ url('punto-venta') }}/${ventaId}/anular`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ motivo: motivo })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalAnularVenta')).hide();
                    Swal.fire('Éxito', data.message, 'success');
                    tablaVentas.ajax.reload();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error al anular la venta', 'error');
            });
        }
    </script>
    @endpush
</x-app-layout>
