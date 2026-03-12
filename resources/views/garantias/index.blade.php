@extends('layouts.app')

@section('title', 'Garantias')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Garantias" description="Devoluciones por garantia de piezas entregadas">
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-shield-exclamation" :value="$abiertas" title="Abiertas" color="warning" />
        <x-sinden.stat-card icon="bi bi-gear" :value="$enProceso" title="En Proceso" color="info" />
        <x-sinden.stat-card icon="bi bi-check-circle" :value="$completadas" title="Listas Re-entrega" color="success" />
        <x-sinden.stat-card icon="bi bi-cash" :value="'$' . number_format($totalCobrables, 0, ',', '.')" title="Total Cobrable" color="danger" />
    </div>

    {{-- Filtros --}}
    <div class="filters-row mt-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div>
                <label class="form-label small fw-medium mb-1">Estado</label>
                <select class="form-select form-select-sm" id="filtroEstado" style="min-width: 150px;">
                    <option value="">Todos</option>
                    <option value="abierta">Abierta</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completada">Completada</option>
                    <option value="reentregada">Reentregada</option>
                </select>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-shield-check me-2 text-primary"></i>Listado de Garantias
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="garantiasTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Pieza</th>
                            <th>Cant.</th>
                            <th>Motivo</th>
                            <th>Cobrable</th>
                            <th>Estado</th>
                            <th>Operario</th>
                            <th>Fecha</th>
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
    var table = $('#garantiasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("recepcion.garantias.index") }}',
            data: function(d) {
                d.estado = $('#filtroEstado').val();
            }
        },
        columns: [
            { data: 'orden_numero', name: 'orden_id', width: '90px' },
            { data: 'cliente_nombre', name: 'cliente_nombre', orderable: false, searchable: false },
            { data: 'pieza_nombre', name: 'pieza_nombre', orderable: false },
            { data: 'cantidad_devuelta', name: 'cantidad_devuelta', width: '60px', className: 'text-center' },
            { data: 'motivo_corto', name: 'motivo', orderable: false },
            { data: 'cobrable_display', name: 'cobrable', className: 'text-center', orderable: false, searchable: false },
            { data: 'estado_badge', name: 'estado', className: 'text-center' },
            { data: 'operario_nombre', name: 'operario_nombre', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at', width: '130px' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-end', width: '100px' }
        ],
        dom: '<"d-flex flex-wrap align-items-center justify-content-between mb-2"<"d-flex align-items-center gap-2"lB>f>rt<"d-flex justify-content-between"ip>',
        buttons: [
            { extend: 'colvis', text: '<i class="bi bi-layout-three-columns"></i> Columnas', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[8, 'desc']],
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

    // Filtro por estado
    $('#filtroEstado').on('change', function() {
        table.ajax.reload();
    });

    // Asignar operario desde lista
    $(document).on('click', '.btn-asignar-operario-garantia', function() {
        var garantiaId = $(this).data('id');
        $.ajax({
            url: '{{ route("recepcion.ordenes.operarios") }}',
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            success: function(response) {
                var operarios = response.operarios || response;
                var options = {};
                if (Array.isArray(operarios)) {
                    operarios.forEach(function(op) { options[op.id] = op.name; });
                }
                Swal.fire({
                    title: 'Asignar Operario',
                    input: 'select',
                    inputOptions: options,
                    inputPlaceholder: 'Seleccione...',
                    showCancelButton: true,
                    confirmButtonColor: '#4A7C59',
                    confirmButtonText: 'Asignar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: function(value) { if (!value) return 'Seleccione un operario.'; }
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '{{ url("recepcion/garantias") }}/' + garantiaId + '/asignar-operario',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                        contentType: 'application/json',
                        data: JSON.stringify({ operario_asignado_id: result.value }),
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.message, showConfirmButton: false, timer: 2000 });
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error';
                            Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        }
                    });
                });
            }
        });
    });

    // Marcar reentregada desde lista
    $(document).on('click', '.btn-reentrega-garantia', function() {
        var garantiaId = $(this).data('id');
        Swal.fire({
            title: 'Marcar como Reentregada?',
            text: 'Confirma que la pieza fue re-entregada al cliente.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4A7C59',
            confirmButtonText: 'Si, Reentregada',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '{{ url("recepcion/garantias") }}/' + garantiaId + '/estado',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                contentType: 'application/json',
                data: JSON.stringify({ estado: 'reentregada' }),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.message, showConfirmButton: false, timer: 2000 });
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
