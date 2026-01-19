@props(['solicitud'])

@php
    use App\Models\SolicitudCotizacion;

    $estados = [
        SolicitudCotizacion::ENVIO_PENDIENTE => [
            'icono' => 'bi-clock',
            'label' => 'Pendiente',
            'descripcion' => 'Esperando preparación del pedido'
        ],
        SolicitudCotizacion::ENVIO_PREPARANDO => [
            'icono' => 'bi-box-seam',
            'label' => 'Preparando',
            'descripcion' => 'Tu pedido está siendo preparado'
        ],
        SolicitudCotizacion::ENVIO_DESPACHADO => [
            'icono' => 'bi-truck',
            'label' => 'Despachado',
            'descripcion' => 'Tu pedido ha sido enviado'
        ],
        SolicitudCotizacion::ENVIO_EN_TRANSITO => [
            'icono' => 'bi-geo-alt',
            'label' => 'En Tránsito',
            'descripcion' => 'Tu pedido está en camino'
        ],
        SolicitudCotizacion::ENVIO_ENTREGADO => [
            'icono' => 'bi-check-circle',
            'label' => 'Entregado',
            'descripcion' => 'Tu pedido ha sido entregado'
        ],
    ];

    $estadoActual = $solicitud->estado_envio;
    $estadosKeys = array_keys($estados);
    $pasoActivo = array_search($estadoActual, $estadosKeys);
@endphp

<div class="timeline-envio">
    @foreach($estados as $key => $estado)
        @php
            $indice = array_search($key, $estadosKeys);
            $esActivo = $indice <= $pasoActivo;
            $esActual = $key === $estadoActual;

            // Determinar clases CSS
            if ($esActual) {
                $circuloClass = 'bg-primary text-white';
                $lineaClass = 'bg-primary';
            } elseif ($esActivo) {
                $circuloClass = 'bg-success text-white';
                $lineaClass = 'bg-success';
            } else {
                $circuloClass = 'bg-light text-muted';
                $lineaClass = 'bg-light';
            }
        @endphp

        <div class="timeline-step d-flex mb-4 {{ $loop->last ? '' : 'position-relative' }}">
            {{-- Círculo con icono --}}
            <div class="timeline-circle rounded-circle d-flex align-items-center justify-content-center {{ $circuloClass }}"
                 style="width: 50px; height: 50px; min-width: 50px; z-index: 1;">
                <i class="bi {{ $estado['icono'] }} fs-5"></i>
            </div>

            {{-- Contenido --}}
            <div class="timeline-content ms-3 flex-grow-1">
                <h6 class="mb-1 {{ $esActivo ? '' : 'text-muted' }}">
                    {{ $estado['label'] }}
                    @if($esActual)
                        <span class="badge bg-primary ms-2">Actual</span>
                    @elseif($esActivo)
                        <i class="bi bi-check-circle-fill text-success ms-1"></i>
                    @endif
                </h6>
                <p class="mb-0 small {{ $esActivo ? 'text-muted' : 'text-muted opacity-50' }}">
                    {{ $estado['descripcion'] }}
                </p>

                {{-- Mostrar información adicional según el estado --}}
                @if($esActivo)
                    @if($key === SolicitudCotizacion::ENVIO_DESPACHADO && $solicitud->despachado_en)
                        <small class="text-info">
                            <i class="bi bi-calendar me-1"></i>
                            {{ $solicitud->despachado_en->format('d/m/Y H:i') }}
                        </small>
                    @endif
                    @if($key === SolicitudCotizacion::ENVIO_ENTREGADO && $solicitud->entregado_en)
                        <small class="text-success">
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ $solicitud->entregado_en->format('d/m/Y H:i') }}
                        </small>
                    @endif
                @endif
            </div>

            {{-- Línea conectora --}}
            @if(!$loop->last)
                <div class="timeline-line position-absolute {{ $esActivo && $indice < $pasoActivo ? 'bg-success' : 'bg-light' }}"
                     style="left: 24px; top: 50px; width: 2px; height: calc(100% - 10px);"></div>
            @endif
        </div>
    @endforeach
</div>

<style>
    .timeline-envio {
        padding: 1rem 0;
    }

    .timeline-step {
        transition: all 0.3s ease;
    }

    .timeline-circle {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timeline-line {
        transition: background-color 0.3s ease;
    }
</style>
