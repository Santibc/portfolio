@extends('layouts.app')

@section('title', 'Buscar Orden')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Buscar Orden" description="Busca una orden por su numero para ver su estado">
        <x-slot name="actions">
            <a href="{{ route('operario.panel') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver al Panel
            </a>
        </x-slot>
    </x-sinden.page-header>

    {{-- Busqueda --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Numero de Orden</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">#</span>
                        <input type="text" class="form-control" id="inputBuscarOrden"
                               placeholder="0001" autofocus
                               inputmode="numeric">
                        <button class="btn btn-primary" type="button" id="btnBuscar">
                            <i class="bi bi-search me-1"></i>Buscar
                        </button>
                    </div>
                    <small class="text-muted">Ingresa el numero de la orden (sin el #)</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Resultado --}}
    <div id="resultadoBusqueda" class="mt-4" style="display:none;">
        {{-- Se llena via JS --}}
    </div>

    {{-- Orden no encontrada --}}
    <div id="ordenNoEncontrada" class="mt-4" style="display:none;">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                <h5 class="mt-3 text-muted">Orden no encontrada</h5>
                <p class="text-muted mb-0">Verifica el numero e intenta nuevamente.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    var BUSCAR_URL = '{{ route("operario.buscar-orden") }}';

    function buscarOrden() {
        var numero = $('#inputBuscarOrden').val().trim();
        if (!numero) {
            Swal.fire({ icon: 'info', title: 'Ingresa un numero', text: 'Escribe el numero de la orden a buscar.', timer: 2000, showConfirmButton: false });
            return;
        }

        $('#resultadoBusqueda').hide();
        $('#ordenNoEncontrada').hide();

        $.ajax({
            url: BUSCAR_URL,
            data: { q: numero },
            success: function(data) {
                if (data.success) {
                    renderizarOrden(data.orden);
                    $('#resultadoBusqueda').fadeIn();
                } else {
                    $('#ordenNoEncontrada').fadeIn();
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo realizar la busqueda.' });
            }
        });
    }

    $('#btnBuscar').on('click', buscarOrden);
    $('#inputBuscarOrden').on('keypress', function(e) {
        if (e.which === 13) buscarOrden();
    });

    function renderizarOrden(orden) {
        var estadoBadge = getBadgeEstado(orden.estado_trabajo);
        var entregaBadge = orden.estado_entrega ? getBadgeEntrega(orden.estado_entrega) : '<span class="text-muted">-</span>';

        var html = '<div class="card border-0 shadow-sm">';
        html += '<div class="card-header bg-white border-0 px-4 pt-4 pb-2">';
        html += '<div class="d-flex align-items-center justify-content-between">';
        html += '<h5 class="mb-0 fw-bold">Orden ' + escapeHtml(orden.numero_orden) + '</h5>';
        html += '<div class="d-flex gap-2">' + estadoBadge + entregaBadge + '</div>';
        html += '</div>';
        html += '<p class="text-muted mt-1 mb-0"><i class="bi bi-person me-1"></i>' + escapeHtml(orden.cliente);
        if (orden.fecha_entrega) {
            html += ' | <i class="bi bi-calendar me-1"></i>Entrega: ' + escapeHtml(orden.fecha_entrega);
        }
        html += '</p>';
        html += '</div>';
        html += '<div class="card-body px-4 pb-4">';

        if (orden.notas) {
            html += '<div class="alert alert-light border mb-3"><i class="bi bi-sticky me-1"></i>' + escapeHtml(orden.notas) + '</div>';
        }

        // Piezas
        if (orden.piezas && orden.piezas.length > 0) {
            html += '<h6 class="fw-semibold mb-3"><i class="bi bi-puzzle me-1 text-primary"></i>Piezas (' + orden.piezas.length + ')</h6>';

            orden.piezas.forEach(function(pieza) {
                var pct = pieza.porcentaje_avance;
                var pctColor = pct >= 100 ? 'success' : (pct >= 50 ? 'warning' : (pct > 0 ? 'info' : 'secondary'));

                html += '<div class="pieza-card mb-3">';
                html += '<div class="d-flex align-items-center justify-content-between mb-2">';
                html += '<div>';
                html += '<span class="fw-semibold">' + escapeHtml(pieza.nombre) + '</span>';
                html += ' <small class="text-muted">(x' + pieza.cantidad + ')</small>';
                if (pieza.entregada) {
                    html += ' <span class="badge bg-success ms-1">ENTREGADA</span>';
                }
                html += '</div>';
                html += '<span class="fw-bold text-' + pctColor + '">' + Math.round(pct) + '%</span>';
                html += '</div>';

                if (pieza.especificacion) {
                    html += '<small class="text-muted d-block mb-2">' + escapeHtml(pieza.especificacion) + '</small>';
                }

                html += '<div class="progress mb-2" style="height:8px;">';
                html += '<div class="progress-bar bg-' + pctColor + '" style="width:' + Math.min(100, Math.max(0, pct)) + '%"></div>';
                html += '</div>';

                html += '<div class="d-flex justify-content-between">';
                if (pieza.material || pieza.calibre) {
                    html += '<small class="text-muted">' + escapeHtml([pieza.material, pieza.calibre].filter(Boolean).join(' - ')) + '</small>';
                } else {
                    html += '<span></span>';
                }
                html += '<small class="text-muted"><i class="bi bi-person me-1"></i>' + escapeHtml(pieza.operario) + '</small>';
                html += '</div>';

                // Historial
                if (pieza.historial && pieza.historial.length > 0) {
                    html += '<div class="mt-2">';
                    html += '<a class="text-primary text-decoration-none small" data-bs-toggle="collapse" href="#histBuscar' + pieza.id + '">';
                    html += '<i class="bi bi-clock-history me-1"></i>Ver historial (' + pieza.historial.length + ')</a>';
                    html += '<div class="collapse mt-2" id="histBuscar' + pieza.id + '">';
                    html += '<div class="historial-timeline">';
                    pieza.historial.forEach(function(h) {
                        html += '<div class="timeline-item">';
                        html += '<strong>' + escapeHtml(h.operario) + '</strong> ';
                        html += '<small class="text-muted">(' + escapeHtml(h.fecha) + ')</small><br>';
                        html += '<small>' + h.desde + '% &rarr; ' + h.hasta + '% ';
                        if (h.contribucion != 0) {
                            var sign = h.contribucion > 0 ? '+' : '';
                            html += '<span class="fw-semibold">' + sign + h.contribucion + '%</span>';
                        }
                        html += '</small>';
                        html += '</div>';
                    });
                    html += '</div></div></div>';
                }

                html += '</div>';
            });
        } else {
            html += '<p class="text-muted text-center mb-0">Esta orden no tiene piezas.</p>';
        }

        html += '</div></div>';

        $('#resultadoBusqueda').html(html);
    }

    function getBadgeEstado(estado) {
        var map = {
            'generada': '<span class="badge bg-info">GENERADA</span>',
            'en_ejecucion': '<span class="badge bg-warning">EN EJECUCION</span>',
            'ejecutada_parcialmente': '<span class="badge bg-warning">EJECUTADA PARC.</span>',
            'ejecutada': '<span class="badge bg-success">EJECUTADA</span>',
            'anulada': '<span class="badge bg-danger">ANULADA</span>'
        };
        return map[estado] || '<span class="badge bg-secondary">' + estado.toUpperCase() + '</span>';
    }

    function getBadgeEntrega(estado) {
        var map = {
            'entregada_parcialmente': '<span class="badge bg-info">ENTREGADA PARC.</span>',
            'entregada': '<span class="badge bg-success">ENTREGADA</span>'
        };
        return map[estado] || '';
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
});
</script>
@endpush
