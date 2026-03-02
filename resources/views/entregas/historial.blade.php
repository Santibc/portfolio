@extends('layouts.app')

@section('title', 'Historial de Entregas')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <x-sinden.page-header title="Historial de Entregas" description="Registro de todas las piezas entregadas al cliente">
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-box-seam" :value="$totalEntregadas" title="Total Entregadas" color="primary" />
        <x-sinden.stat-card icon="bi bi-check-circle" :value="$entregadasHoy" title="Entregadas Hoy" color="success" />
        <x-sinden.stat-card icon="bi bi-calendar-week" :value="$entregadasSemana" title="Ultimos 7 Dias" color="info" />
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Piezas Entregadas
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="historialTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Fecha Entrega</th>
                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Pieza</th>
                            <th class="text-center">Cant.</th>
                            <th>Material</th>
                            <th>Calibre</th>
                            <th>Entregado Por</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#historialTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("recepcion.entregas.historial") }}',
        columns: [
            { data: 'fecha_entrega_formatted', name: 'fecha_entrega_formatted', width: '140px' },
            { data: 'numero_orden', name: 'numero_orden', width: '100px' },
            { data: 'cliente_nombre', name: 'cliente_nombre' },
            { data: 'nombre', name: 'nombre' },
            { data: 'cantidad', name: 'cantidad', className: 'text-center', width: '60px' },
            { data: 'material', name: 'material' },
            { data: 'calibre', name: 'calibre', width: '80px' },
            { data: 'entregado_por_nombre', name: 'entregado_por_nombre', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        drawCallback: function(settings) {
            var total = settings._iRecordsTotal || 0;
            $('#totalRegistros').text(total + ' registro' + (total !== 1 ? 's' : ''));
        }
    });
});
</script>
@endpush
