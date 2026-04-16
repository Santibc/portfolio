@extends('layouts.app')

@section('title', 'Entregas Pendientes')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <x-sinden.page-header title="Entregas Pendientes" description="Ordenes con piezas pendientes de entregar al cliente">
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-box-seam" :value="$totalPendientes" title="Ordenes Pendientes" color="primary" />
        <x-sinden.stat-card icon="bi bi-check2-square" :value="$piezasPendientes" title="Piezas Pendientes" color="warning" />
        <x-sinden.stat-card icon="bi bi-check-circle" :value="$entregasHoy" title="Entregas Hoy" color="success" />
        <x-sinden.stat-card icon="bi bi-exclamation-triangle" :value="$entregasVencidas" title="Entregas Vencidas" color="danger" />
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Ordenes con Entregas Pendientes
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="entregasTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Fecha Entrega</th>
                            <th>Porcentaje</th>
                            <th>Estado Trabajo</th>
                            <th>Estado Entrega</th>
                            <th class="text-end">Acciones</th>
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
var CSRF_TOKEN = '{{ csrf_token() }}';

$(function() {
    var table = $('#entregasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("recepcion.entregas-pendientes") }}',
        columns: [
            { data: 'numero_orden', name: 'numero_orden', width: '90px' },
            { data: 'cliente_nombre', name: 'cliente.nombre' },
            { data: 'fecha_entrega', name: 'fecha_entrega', width: '110px', className: 'text-center' },
            { data: 'porcentaje', name: 'porcentaje', className: 'text-center', orderable: false, searchable: false },
            { data: 'estado_trabajo_badge', name: 'estado_trabajo', className: 'text-center', orderable: true, searchable: false },
            { data: 'estado_entrega_badge', name: 'estado_entrega', className: 'text-center', orderable: true, searchable: false },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-end', width: '120px' }
        ],
        dom: '<"d-flex flex-wrap align-items-center justify-content-between mb-2"<"d-flex align-items-center gap-2"lB>f>rt<"d-flex justify-content-between"ip>',
        buttons: [
            { extend: 'colvis', text: '<i class="bi bi-layout-three-columns"></i> Columnas', className: 'btn btn-sm btn-outline-secondary' }
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

    // Entrega Rapida
    $(document).on('click', '.btn-entrega-rapida', function() {
        var ordenId = $(this).data('orden-id');

        Swal.fire({
            title: 'Entrega Rapida',
            text: 'Se entregaran TODAS las piezas pendientes de esta orden al cliente.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#475569',
            confirmButtonText: 'Si, entregar todas',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

                $.ajax({
                    url: '{{ url("recepcion/entregas-pendientes") }}/' + ordenId + '/entrega-rapida',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3000
                            });
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al procesar la entrega.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
