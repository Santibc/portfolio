{{--
    Selector de Ubicación para Filtrar Productos por Zona

    Uso: @include('components.selector-ubicacion')
--}}

@php
    $ubicacionSeleccionada = session('ubicacion_seleccionada');
    $zonaSeleccionada = null;

    if ($ubicacionSeleccionada && isset($ubicacionSeleccionada['zona_id'])) {
        $zonaSeleccionada = \App\Models\ZonaCobertura::find($ubicacionSeleccionada['zona_id']);
    }
@endphp

<div class="selector-ubicacion-widget">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-start mb-3 mb-lg-0">
                        <div class="me-3">
                            <i class="bi bi-geo-alt-fill text-dark" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-semibold">¿Dónde quieres recibir tu pedido?</h5>
                            @if($zonaSeleccionada)
                                <p class="text-muted mb-0">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    Viendo productos disponibles en <strong>{{ $zonaSeleccionada->nombre }}</strong>
                                </p>
                            @else
                                <p class="text-muted mb-0 small">
                                    Selecciona tu ubicación para ver solo productos disponibles en tu zona
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end">
                    @if($zonaSeleccionada)
                        <button type="button" class="btn btn-outline-dark btn-sm me-2" data-bs-toggle="modal" data-bs-target="#selectorUbicacionModal">
                            <i class="bi bi-pencil"></i> Cambiar ubicación
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="limpiarUbicacion()">
                            <i class="bi bi-x-circle"></i> Ver todos
                        </button>
                    @else
                        <button type="button" class="btn btn-dark btn-selector-ubicacion" data-bs-toggle="modal" data-bs-target="#selectorUbicacionModal">
                            <i class="bi bi-pin-map"></i> Seleccionar ubicación
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Selector de Ubicación --}}
<div class="modal fade" id="selectorUbicacionModal" tabindex="-1" aria-labelledby="selectorUbicacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectorUbicacionModalLabel">
                    <i class="bi bi-geo-alt"></i> Selecciona tu ubicación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSelectorUbicacion">
                    <div class="mb-3">
                        <label for="selectRegion" class="form-label">Región</label>
                        <select class="form-select" id="selectRegion" name="region_id" required>
                            <option value="">Seleccione una región</option>
                            @php
                                // Obtener solo regiones de Chile (pais_id = 2)
                                $regiones = \Illuminate\Support\Facades\DB::table('departamentos')
                                    ->where('pais_id', 2)
                                    ->orderBy('nombre')
                                    ->get();
                            @endphp
                            @foreach($regiones as $region)
                                <option value="{{ $region->id }}">{{ $region->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="selectComuna" class="form-label">Comuna</label>
                        <select class="form-select" id="selectComuna" name="ciudad_id" required disabled>
                            <option value="">Primero seleccione una región</option>
                        </select>
                        <small class="text-muted">Selecciona la comuna donde quieres recibir tu pedido</small>
                    </div>

                    <div class="mb-3" id="zonaInfo" style="display: none;">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Zona de cobertura:</strong> <span id="zonaNombre"></span>
                        </div>
                    </div>

                    <div class="mb-3" id="sinCobertura" style="display: none;">
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Lo sentimos, aún no tenemos cobertura en esta ubicación.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-dark" id="btnAplicarUbicacion" disabled>
                    <i class="bi bi-check-circle"></i> Aplicar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.selector-ubicacion-widget .card {
    border-left: 4px solid #1a1a1a;
    background-color: #ffffff;
    transition: all 0.3s ease;
}

.selector-ubicacion-widget .card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}

.selector-ubicacion-widget .btn-dark {
    background-color: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
    font-weight: 500;
    padding: 0.625rem 1.5rem;
    transition: all 0.3s ease;
}

.selector-ubicacion-widget .btn-dark:hover {
    background-color: #000000;
    border-color: #000000;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.selector-ubicacion-widget .btn-outline-dark {
    border-color: #1a1a1a;
    color: #1a1a1a;
    font-weight: 500;
    transition: all 0.3s ease;
}

.selector-ubicacion-widget .btn-outline-dark:hover {
    background-color: #1a1a1a;
    border-color: #1a1a1a;
    color: #ffffff;
}

@media (max-width: 991px) {
    .selector-ubicacion-widget .col-lg-4 {
        text-align: center !important;
        margin-top: 1rem;
    }

    .selector-ubicacion-widget .btn {
        width: 100%;
        margin-bottom: 0.5rem;
        margin-right: 0 !important;
    }

    .selector-ubicacion-widget .btn:last-child {
        margin-bottom: 0;
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    let zonasCobertura = [];

    // Cargar zonas de cobertura (proyecto mono-empresa, todas las zonas)
    function cargarZonasCobertura() {
        $.ajax({
            url: '/api/zonas-cobertura',
            method: 'GET',
            success: function(data) {
                zonasCobertura = data;
                console.log('Zonas de cobertura cargadas:', zonasCobertura.length);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar zonas de cobertura:', error);
            }
        });
    }

    // Función para cargar comunas de una región
    function cargarComunasPorRegion(regionId) {
        const $selectComuna = $('#selectComuna');

        console.log('Cargando comunas para región:', regionId);

        $selectComuna.html('<option value="">Cargando...</option>').prop('disabled', true);
        $('#zonaInfo, #sinCobertura').hide();
        $('#btnAplicarUbicacion').prop('disabled', true);

        if (!regionId) {
            $selectComuna.html('<option value="">Primero seleccione una región</option>');
            return;
        }

        $.ajax({
            url: '/api/comunas/por-region/' + regionId,
            method: 'GET',
            success: function(comunas) {
                console.log('Comunas recibidas:', comunas.length);
                $selectComuna.html('<option value="">Seleccione una comuna</option>');

                comunas.forEach(function(comuna) {
                    $selectComuna.append(
                        $('<option></option>')
                            .attr('value', comuna.id)
                            .text(comuna.nombre)
                    );
                });

                $selectComuna.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar comunas:', xhr.responseText);
                $selectComuna.html('<option value="">Error al cargar comunas</option>');
            }
        });
    }

    // Cargar comunas cuando se selecciona región
    $('#selectRegion').on('change', function() {
        const regionId = $(this).val();
        cargarComunasPorRegion(regionId);
    });

    // Verificar cobertura cuando se selecciona comuna
    $('#selectComuna').on('change', function() {
        const comunaId = $(this).val();
        $('#zonaInfo, #sinCobertura').hide();
        $('#btnAplicarUbicacion').prop('disabled', true);

        if (!comunaId || zonasCobertura.length === 0) return;

        // Buscar zona que cubra esta comuna
        const zonaEncontrada = zonasCobertura.find(zona =>
            zona.ciudades_ids && zona.ciudades_ids.includes(parseInt(comunaId))
        );

        if (zonaEncontrada) {
            $('#zonaNombre').text(zonaEncontrada.nombre);
            $('#zonaInfo').show();
            $('#btnAplicarUbicacion')
                .prop('disabled', false)
                .data('zona-id', zonaEncontrada.id)
                .data('ciudad-id', comunaId);
        } else {
            $('#sinCobertura').show();
        }
    });

    // Aplicar ubicación seleccionada
    $('#btnAplicarUbicacion').on('click', function() {
        const zonaId = $(this).data('zona-id');
        const ciudadId = $(this).data('ciudad-id');

        if (!zonaId || !ciudadId) return;

        $.ajax({
            url: '/tienda/establecer-ubicacion',
            method: 'POST',
            data: {
                zona_id: zonaId,
                ciudad_id: ciudadId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Recargar página para aplicar filtro
                    window.location.reload();
                }
            },
            error: function() {
                alert('Error al establecer ubicación. Por favor intente nuevamente.');
            }
        });
    });

    // Cargar zonas al abrir modal
    $('#selectorUbicacionModal').on('show.bs.modal', function() {
        console.log('Modal abierto');

        if (zonasCobertura.length === 0) {
            cargarZonasCobertura();
        }

        // Si hay una región pre-seleccionada, cargar sus comunas directamente
        const regionSeleccionada = $('#selectRegion').val();
        console.log('Región actualmente seleccionada:', regionSeleccionada);

        if (regionSeleccionada && regionSeleccionada !== '') {
            console.log('Cargando comunas para región pre-seleccionada:', regionSeleccionada);
            cargarComunasPorRegion(regionSeleccionada);
        }
    });

    // Inicializar
    cargarZonasCobertura();
});

// Función global para limpiar ubicación
function limpiarUbicacion() {
    $.ajax({
        url: '/tienda/limpiar-ubicacion',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                window.location.reload();
            }
        }
    });
}
</script>
@endpush
