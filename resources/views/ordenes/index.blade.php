@extends('layouts.app')

@section('title', 'Ordenes de Trabajo')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <x-sinden.page-header title="Ordenes de Trabajo" description="Buscar y gestionar ordenes">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-excel"
                href="{{ route('recepcion.ordenes.export-excel') }}">Excel</x-sinden.button>
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-pdf"
                href="{{ route('recepcion.ordenes.export-pdf') }}">PDF</x-sinden.button>
            <x-sinden.button variant="primary" icon="bi bi-plus-lg"
                href="{{ route('recepcion.ordenes.crear') }}">Nueva Orden</x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-file-earmark-text" :value="$totalOrdenes" title="Total Ordenes" color="primary" />
        <x-sinden.stat-card icon="bi bi-file-earmark" :value="$borradores" title="Borradores" color="secondary" />
        <x-sinden.stat-card icon="bi bi-gear" :value="$enProceso" title="En Proceso" color="warning" />
        <x-sinden.stat-card icon="bi bi-check-circle" :value="$ejecutadas" title="Ejecutadas" color="success" />
        <x-sinden.stat-card icon="bi bi-currency-dollar" :value="'$' . number_format($saldoPendienteTotal, 0, ',', '.')" title="Saldo Pendiente" color="danger" />
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body px-4 py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-medium mb-1">N° Orden</label>
                    <input type="text" class="form-control form-control-sm" id="filtroNumeroOrden" placeholder="Ej: OT-00001">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium mb-1">Cliente</label>
                    <input type="text" class="form-control form-control-sm" id="filtroCliente" placeholder="Nombre del cliente">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium mb-1">Fecha Desde</label>
                    <input type="date" class="form-control form-control-sm" id="filtroFechaDesde">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium mb-1">Fecha Hasta</label>
                    <input type="date" class="form-control form-control-sm" id="filtroFechaHasta">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium mb-1">Estado Trabajo</label>
                    <select class="form-select form-select-sm" id="filtroEstadoTrabajo">
                        <option value="">Todos</option>
                        <option value="borrador">Borrador</option>
                        <option value="generada">Generada</option>
                        <option value="en_ejecucion">En Ejecucion</option>
                        <option value="ejecutada_parcialmente">Ejec. Parcial</option>
                        <option value="ejecutada">Ejecutada</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium mb-1">Estado Entrega</label>
                    <select class="form-select form-select-sm" id="filtroEstadoEntrega">
                        <option value="">Todos</option>
                        <option value="entregada_parcialmente">Entrega Parcial</option>
                        <option value="entregada">Entregada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium mb-1">Estado Pago</label>
                    <select class="form-select form-select-sm" id="filtroEstadoPago">
                        <option value="">Todos</option>
                        <option value="saldo_pendiente">Saldo Pendiente</option>
                        <option value="pagado">Pagado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary" id="btnFiltrar">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnLimpiar">
                        <i class="bi bi-x-lg me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>Listado de Ordenes
                </h6>
                <span class="badge bg-light text-muted border" id="totalRegistros"></span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sinden-datatable" id="ordenesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Creacion</th>
                            <th>Entrega</th>
                            <th>Trabajo</th>
                            <th>Entrega</th>
                            <th>Pago</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Saldo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Anular --}}
<div class="modal fade" id="modalAnularOrden" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Anular Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Esta accion anulara la orden <strong id="anularOrdenNumero"></strong> y liberara todas las asignaciones.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Motivo de Anulacion <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="motivoAnulacion" rows="3" placeholder="Ingrese el motivo..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarAnular">
                    <i class="bi bi-x-circle me-1"></i>Anular Orden
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var CSRF_TOKEN = '{{ csrf_token() }}';
var anularOrdenId = null;

$(function() {
    var table = $('#ordenesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("recepcion.ordenes.index") }}',
            data: function(d) {
                d.numero_orden = $('#filtroNumeroOrden').val();
                d.cliente = $('#filtroCliente').val();
                d.estado_trabajo = $('#filtroEstadoTrabajo').val();
                d.estado_entrega = $('#filtroEstadoEntrega').val();
                d.estado_pago = $('#filtroEstadoPago').val();
                d.fecha_desde = $('#filtroFechaDesde').val();
                d.fecha_hasta = $('#filtroFechaHasta').val();
            }
        },
        columns: [
            { data: 'numero_orden', name: 'numero_orden', width: '90px', className: 'fw-semibold' },
            { data: 'cliente_nombre', name: 'cliente.nombre' },
            { data: 'created_at', name: 'created_at', width: '95px', className: 'text-center' },
            { data: 'fecha_entrega', name: 'fecha_entrega', width: '95px', className: 'text-center' },
            { data: 'estado_trabajo_badge', name: 'estado_trabajo', className: 'text-center', orderable: true, searchable: false },
            { data: 'estado_entrega_badge', name: 'estado_entrega', className: 'text-center', orderable: true, searchable: false },
            { data: 'estado_pago_badge', name: 'estado_pago', className: 'text-center', orderable: true, searchable: false },
            { data: 'total_formatted', name: 'total', className: 'text-end', orderable: true, searchable: false },
            { data: 'saldo_formatted', name: 'saldo', className: 'text-end', orderable: true, searchable: false },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-end', width: '140px' }
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

    $('#btnFiltrar').on('click', function() { table.ajax.reload(); });
    $('#btnLimpiar').on('click', function() {
        $('#filtroNumeroOrden, #filtroCliente, #filtroFechaDesde, #filtroFechaHasta').val('');
        $('#filtroEstadoTrabajo, #filtroEstadoEntrega, #filtroEstadoPago').val('');
        table.ajax.reload();
    });

    $('#btnConfirmarAnular').on('click', function() {
        var motivo = $('#motivoAnulacion').val().trim();
        if (!motivo) {
            Swal.fire('Error', 'Debe ingresar un motivo de anulacion.', 'error');
            return;
        }
        var btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: '{{ url("recepcion/ordenes") }}/' + anularOrdenId + '/anular',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            data: { motivo: motivo },
            success: function(data) {
                $('#modalAnularOrden').modal('hide');
                if (data.success) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                    table.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al anular la orden.';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });
});

function copiarOrden(ordenId) {
    Swal.fire({
        title: 'Copiar orden?',
        text: 'Se creara un nuevo borrador con los mismos items, bosquejos y piezas.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4A7C59',
        confirmButtonText: 'Si, copiar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Copiando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            $.ajax({
                url: '{{ url("recepcion/ordenes") }}/' + ordenId + '/copiar',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Orden copiada', text: data.message, confirmButtonColor: '#4A7C59' }).then(function() {
                            window.location.href = data.redirect;
                        });
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al copiar.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        }
    });
}

function anularOrden(ordenId, numeroOrden) {
    anularOrdenId = ordenId;
    $('#anularOrdenNumero').text(numeroOrden || '#' + ordenId);
    $('#motivoAnulacion').val('');
    $('#modalAnularOrden').modal('show');
}
</script>
@endpush
