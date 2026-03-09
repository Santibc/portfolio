@extends('layouts.app')

@section('title', 'Orden ' . ($orden->numero_orden ?? 'Borrador #' . $orden->id))

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <x-sinden.page-header :title="'Orden ' . ($orden->numero_orden ?? 'Borrador #' . $orden->id)" description="Detalle completo de la orden">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-arrow-left"
                href="{{ route('recepcion.ordenes.index') }}">Volver</x-sinden.button>
            @if($orden->estado_trabajo !== 'anulada')
                <x-sinden.button variant="primary" icon="bi bi-pencil"
                    href="{{ route('recepcion.ordenes.edit', $orden) }}">Editar</x-sinden.button>
                <button type="button" class="btn btn-outline-secondary" onclick="copiarOrden()">
                    <i class="bi bi-copy me-1"></i>Copiar
                </button>
            @endif
            @if(!in_array($orden->estado_trabajo, ['anulada', 'borrador']))
                <button type="button" class="btn btn-outline-danger" onclick="$('#modalAnularOrden').modal('show')">
                    <i class="bi bi-x-circle me-1"></i>Anular
                </button>
            @endif
        </x-slot>
    </x-sinden.page-header>

    {{-- Seccion 1: Encabezado + Estados --}}
    @include('ordenes.show._seccion-encabezado')

    <div class="row mt-4">
        <div class="col-lg-8">
            {{-- Seccion 2: Cliente --}}
            @include('ordenes.show._seccion-cliente')

            {{-- Seccion 3: Fechas --}}
            @include('ordenes.show._seccion-fechas')

            {{-- Seccion 4: Items + Totales --}}
            @include('ordenes.show._seccion-items')

            {{-- Seccion 5: Piezas (con bosquejos integrados) --}}
            @include('ordenes.show._seccion-piezas')
        </div>

        <div class="col-lg-4">
            {{-- Seccion 7: Pagos --}}
            @include('ordenes.show._seccion-pagos')

            {{-- Seccion 8: Firma --}}
            @include('ordenes.show._seccion-firma')

            {{-- Seccion 9: Fotos --}}
            @include('ordenes.show._seccion-fotos')

            {{-- Seccion 10: Comentarios --}}
            @include('ordenes.show._seccion-comentarios')

            {{-- Seccion 11: Garantias --}}
            @include('ordenes.show._seccion-garantias')
        </div>
    </div>
</div>

{{-- Modal Agregar Pago --}}
<div class="modal fade" id="modalAgregarPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-medium">Monto <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="pagoMonto" min="1" step="1">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Metodo de Pago</label>
                    <select class="form-select" id="pagoMetodo">
                        <option value="efectivo">Efectivo</option>
                        <option value="nequi">Nequi</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Referencia</label>
                    <input type="text" class="form-control" id="pagoReferencia" placeholder="No. de referencia (opcional)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnRegistrarPago" onclick="registrarPago()">
                    <i class="bi bi-check-lg me-1"></i>Registrar
                </button>
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
                    Esta accion anulara la orden y liberara todas las asignaciones de piezas.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Motivo de Anulacion <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="motivoAnulacion" rows="3" placeholder="Ingrese el motivo..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarAnular" onclick="confirmarAnulacion()">
                    <i class="bi bi-x-circle me-1"></i>Anular Orden
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-white" id="lightboxTitle"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="lightboxImage" src="" class="img-fluid" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/orden-detalle.js') }}"></script>
<script>
var ORDEN_ID = {{ $orden->id }};
var CSRF_TOKEN = '{{ csrf_token() }}';
@php
    $esContabilidad = request()->is('contabilidad/*');
@endphp
var ROUTES_DETALLE = {
    copiar: '{{ route("recepcion.ordenes.copiar", $orden) }}',
    anular: '{{ route("recepcion.ordenes.anular", $orden) }}',
    comentarios: '{{ route("recepcion.ordenes.comentarios.store", $orden) }}',
    pagos: '{{ $esContabilidad ? route("contabilidad.ordenes.pagos.store", $orden) : route("recepcion.ordenes.pagos.store", $orden) }}',
    index: '{{ route("recepcion.ordenes.index") }}',
    edit: '{{ route("recepcion.ordenes.edit", $orden) }}',
};

// Historial de entregas por pieza
$(document).on('click', '.btn-historial-entregas', function(e) {
    e.preventDefault();
    var piezaId = $(this).data('pieza-id');
    var piezaNombre = $(this).data('pieza-nombre');
    $('#modalEntregaPiezaNombre').text(piezaNombre);
    $('#modalEntregaLoading').removeClass('d-none');
    $('#modalEntregaContenido').addClass('d-none');
    $('#modalEntregaVacio').addClass('d-none');
    $('#modalEntregaResumen').text('');
    $('#modalEntregaTabla').empty();
    $('#modalHistorialEntregas').modal('show');

    $.ajax({
        url: '{{ url("recepcion/entregas-pendientes/pieza") }}/' + piezaId + '/historial',
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        success: function(data) {
            $('#modalEntregaLoading').addClass('d-none');
            $('#modalEntregaResumen').text(data.pieza.cantidad_entregada + ' de ' + data.pieza.cantidad + ' entregadas');

            if (data.entregas.length === 0) {
                $('#modalEntregaVacio').removeClass('d-none');
                return;
            }

            var html = '';
            data.entregas.forEach(function(ent) {
                html += '<tr>';
                html += '<td>' + ent.fecha + '</td>';
                html += '<td class="text-center"><span class="badge bg-primary">' + ent.cantidad + '</span></td>';
                html += '<td>' + ent.entregado_por + '</td>';
                html += '<td>';
                if (ent.fotos && ent.fotos.length > 0) {
                    ent.fotos.forEach(function(f) {
                        html += '<img src="' + f.url + '" class="border rounded me-1" style="width:40px;height:40px;object-fit:cover;cursor:pointer;" onclick="abrirLightbox(\'' + f.url.replace('{{ url("/") }}/', '') + '\', \'Foto Entrega\')" title="Ver foto">';
                    });
                } else {
                    html += '<span class="text-muted small">-</span>';
                }
                html += '</td>';
                html += '</tr>';
            });
            $('#modalEntregaTabla').html(html);
            $('#modalEntregaContenido').removeClass('d-none');
        },
        error: function() {
            $('#modalEntregaLoading').addClass('d-none');
            $('#modalEntregaVacio').removeClass('d-none').text('Error al cargar el historial.');
        }
    });
});
</script>
@endpush
