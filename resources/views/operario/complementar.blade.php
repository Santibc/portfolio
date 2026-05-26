@extends('layouts.app')

@section('title', 'Complementar Ordenes')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Complementar Otras Ordenes" description="Piezas disponibles en cola general que puedes tomar para trabajar">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-excel"
                href="{{ route('operario.complementar.export-excel') }}">Excel</x-sinden.button>
            <a href="{{ route('operario.panel') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver al Panel
            </a>
        </x-slot>
    </x-sinden.page-header>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Piezas Disponibles
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="complementarTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Orden #</th>
                            <th>Pieza</th>
                            <th>Progreso</th>
                            <th>Ultimo Operario</th>
                            <th>Cliente</th>
                            <th>Fecha Entrega</th>
                            <th>Accion</th>
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
    var CSRF_TOKEN = '{{ csrf_token() }}';

    var table = $('#complementarTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("operario.complementar") }}',
        columns: [
            { data: 'orden_numero', name: 'orden_numero', className: 'fw-semibold', width: '90px' },
            { data: 'pieza_info', name: 'nombre' },
            { data: 'progreso', name: 'porcentaje_avance', width: '140px', orderable: true, searchable: false },
            { data: 'ultimo_operario', name: 'ultimo_operario', orderable: false, searchable: false },
            { data: 'cliente_nombre', name: 'cliente_nombre', orderable: false, searchable: false },
            { data: 'fecha_entrega', name: 'fecha_entrega', orderable: false, searchable: false, className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center', width: '100px' }
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
            $('#totalRegistros').text(total + ' pieza' + (total !== 1 ? 's' : '') + ' disponible' + (total !== 1 ? 's' : ''));
        }
    });

    // Tomar pieza
    $(document).on('click', '.btn-tomar-pieza', function() {
        var btn = $(this);
        var piezaId = btn.data('pieza-id');
        var piezaNombre = btn.data('pieza-nombre');

        Swal.fire({
            title: 'Tomar pieza?',
            html: 'Vas a tomar la pieza <b>' + piezaNombre + '</b> para trabajar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si, tomar pieza',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#475569'
        }).then(function(result) {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

                $.ajax({
                    url: '/operario/piezas/' + piezaId + '/tomar',
                    method: 'POST',
                    data: { _token: CSRF_TOKEN },
                    success: function(data) {
                        if (data.success) {
                            window.location.href = '/operario/ordenes/' + data.orden_id;
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'No se pudo tomar la pieza.' });
                            btn.prop('disabled', false).html('<i class="bi bi-hand-index me-1"></i>Tomar');
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la operacion.' });
                        btn.prop('disabled', false).html('<i class="bi bi-hand-index me-1"></i>Tomar');
                    }
                });
            }
        });
    });
});
</script>
@endpush
