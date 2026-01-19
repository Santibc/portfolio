<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history fs-4"></i>
            <span>Mis Pedidos</span>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            {{-- Alertas de sesión --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if(session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif

            {{-- Card con tabla --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Historial de Pedidos
                    </h5>
                    <a href="{{ route('portal.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Portal
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla-historial" class="table table-hover" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº Solicitud</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Pago</th>
                                    <th>Envío</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Leyenda de estados --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Leyenda de Estados
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="small mb-2"><strong>Estado de Cotización:</strong></p>
                            <span class="badge bg-primary me-1">Pendiente</span>
                            <span class="badge bg-success me-1">Aplicada</span>
                            <span class="badge bg-danger">Rechazada</span>
                        </div>
                        <div class="col-md-4">
                            <p class="small mb-2"><strong>Estado de Pago:</strong></p>
                            <span class="badge bg-warning me-1">Pendiente</span>
                            <span class="badge bg-info me-1">Parcial</span>
                            <span class="badge bg-success">Pagado</span>
                        </div>
                        <div class="col-md-4">
                            <p class="small mb-2"><strong>Estado de Envío:</strong></p>
                            <span class="badge bg-secondary me-1">Pendiente</span>
                            <span class="badge bg-info me-1">Preparando</span>
                            <span class="badge bg-primary me-1">Despachado</span>
                            <span class="badge bg-warning me-1">En Tránsito</span>
                            <span class="badge bg-success">Entregado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#tabla-historial').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route("portal.historial") }}',
                    columns: [
                        { data: 'numero_solicitud', name: 'numero_solicitud' },
                        { data: 'fecha', name: 'created_at' },
                        { data: 'monto_formateado', name: 'monto_total' },
                        { data: 'estado_badge', name: 'estado' },
                        { data: 'pago_badge', name: 'estado_pago' },
                        { data: 'envio_badge', name: 'estado_envio' },
                        { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
                    ],
                    order: [[1, 'desc']],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    responsive: true
                });
            });
        </script>
    @endpush
</x-app-layout>
