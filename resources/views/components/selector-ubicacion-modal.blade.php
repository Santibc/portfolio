{{--
    Modal Selector de Ubicación (solo modal, sin widget)
    Uso: @include('components.selector-ubicacion-modal')
--}}

<div class="modal fade" id="selectorUbicacionModal" tabindex="-1" aria-labelledby="selectorUbicacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="selectorUbicacionModalLabel">
                    <i class="bi bi-geo-alt me-2"></i>Selecciona tu ubicación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted small mb-4">
                    Selecciona tu ubicación para ver solo productos disponibles en tu zona
                </p>
                <form id="formSelectorUbicacion">
                    <div class="mb-3">
                        <label for="selectRegion" class="form-label fw-semibold">Región</label>
                        <select class="form-select" id="selectRegion" name="region_id" required>
                            <option value="">Seleccione una región</option>
                            @php
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
                        <label for="selectComuna" class="form-label fw-semibold">Comuna</label>
                        <select class="form-select" id="selectComuna" name="ciudad_id" required disabled>
                            <option value="">Primero seleccione una región</option>
                        </select>
                    </div>

                    <div class="mb-3" id="zonaInfo" style="display: none;">
                        <div class="alert alert-success border-0 mb-0" style="background: #e8f5e9;">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Zona de cobertura:</strong> <span id="zonaNombre"></span>
                        </div>
                    </div>

                    <div class="mb-3" id="sinCobertura" style="display: none;">
                        <div class="alert alert-warning border-0 mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Lo sentimos, aún no tenemos cobertura en esta ubicación.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-dark px-4" id="btnAplicarUbicacion" disabled>
                    <i class="bi bi-check-circle me-2"></i>Aplicar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let zonasCobertura = [];

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

    function cargarComunasPorRegion(regionId) {
        const $selectComuna = $('#selectComuna');

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
            error: function(xhr) {
                console.error('Error al cargar comunas:', xhr.responseText);
                $selectComuna.html('<option value="">Error al cargar comunas</option>');
            }
        });
    }

    $('#selectRegion').on('change', function() {
        cargarComunasPorRegion($(this).val());
    });

    $('#selectComuna').on('change', function() {
        const comunaId = $(this).val();
        $('#zonaInfo, #sinCobertura').hide();
        $('#btnAplicarUbicacion').prop('disabled', true);

        if (!comunaId || zonasCobertura.length === 0) return;

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
                    // Redirigir al catálogo de productos
                    window.location.href = '{{ route("tienda.categorias") }}';
                }
            },
            error: function() {
                alert('Error al establecer ubicación. Por favor intente nuevamente.');
            }
        });
    });

    $('#selectorUbicacionModal').on('show.bs.modal', function() {
        if (zonasCobertura.length === 0) {
            cargarZonasCobertura();
        }

        const regionSeleccionada = $('#selectRegion').val();
        if (regionSeleccionada && regionSeleccionada !== '') {
            cargarComunasPorRegion(regionSeleccionada);
        }
    });

    cargarZonasCobertura();
});
</script>
@endpush
