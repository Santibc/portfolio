@extends('layouts.app')

@section('title', 'Garantias Asignadas')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Garantias Asignadas" description="Piezas devueltas por garantia que debes reparar">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-excel"
                href="{{ route('operario.garantias.export-excel') }}">Excel</x-sinden.button>
            <a href="{{ route('operario.panel') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver al Panel
            </a>
        </x-slot>
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-shield-exclamation" :value="$pendientes" title="Pendientes de Completar" color="warning" />
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-shield-check me-2 text-primary"></i>Mis Garantias en Proceso
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="garantiasOpTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Orden #</th>
                            <th>Cliente</th>
                            <th>Pieza</th>
                            <th>Cant.</th>
                            <th>Motivo</th>
                            <th>Fecha</th>
                            <th class="text-end">Accion</th>
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
    var table = $('#garantiasOpTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("operario.garantias") }}',
        columns: [
            { data: 'orden_numero', name: 'orden_id', width: '90px' },
            { data: 'cliente_nombre', name: 'cliente_nombre', orderable: false, searchable: false },
            { data: 'pieza_nombre', name: 'pieza_nombre', orderable: false },
            { data: 'cantidad_devuelta', name: 'cantidad_devuelta', width: '60px', className: 'text-center' },
            { data: 'motivo_corto', name: 'motivo', orderable: false },
            { data: 'created_at', name: 'created_at', width: '130px' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-end', width: '120px' }
        ],
        dom: '<"d-flex flex-wrap align-items-center justify-content-between mb-2"lf>rt<"d-flex justify-content-between"ip>',
        order: [[5, 'asc']],
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

    // Completar garantia
    $(document).on('click', '.btn-completar-garantia', function() {
        var garantiaId = $(this).data('id');
        Swal.fire({
            title: 'Completar Garantia?',
            text: 'Confirma que el trabajo de reparacion esta terminado.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4A7C59',
            confirmButtonText: 'Si, Completar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: '{{ url("operario/garantias") }}/' + garantiaId + '/completar',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Completada',
                            text: response.message,
                            confirmButtonColor: '#4A7C59'
                        });
                        table.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            });
        });
    });
});
</script>
@endpush
