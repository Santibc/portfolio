<x-app-layout>
    @section('title', 'Historial de Sesiones')

    @push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Sesiones</h4>
            <a href="{{ route('pdv.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select id="filtroCaja" class="form-select">
                            <option value="">Todas las cajas</option>
                            @foreach($cajas as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="tablaSesiones" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Caja</th>
                            <th>Cajero</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Ventas</th>
                            <th>Total</th>
                            <th>Diferencia</th>
                            <th>Estado</th>
                            <th width="100">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let tabla;
        $(function() {
            tabla = $('#tablaSesiones').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("pdv.sesiones.historial") }}',
                    data: function(d) {
                        d.caja_id = $('#filtroCaja').val();
                    }
                },
                columns: [
                    { data: 'caja_nombre' },
                    { data: 'usuario_nombre' },
                    { data: 'abierta_en', name: 'abierta_en' },
                    { data: 'cerrada_en', name: 'cerrada_en' },
                    { data: 'cantidad_ventas', name: 'cantidad_ventas' },
                    { data: 'total_ventas', name: 'total_ventas', render: v => '$' + parseFloat(v).toFixed(2) },
                    { data: 'diferencia_display', orderable: false },
                    { data: 'estado_badge', orderable: false },
                    { data: 'action', orderable: false, searchable: false },
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                order: [[2, 'desc']],
            });

            $('#filtroCaja').change(() => tabla.ajax.reload());
        });
    </script>
    @endpush
</x-app-layout>
