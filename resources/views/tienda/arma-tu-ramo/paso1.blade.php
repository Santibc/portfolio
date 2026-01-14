@extends('tienda.layout')

@section('title', 'Paso 1: Elige tu Estilo - Arma tu Ramo')

@section('content')
<div class="arma-ramo-wizard">
    <div class="container py-4">
        {{-- Header del wizard --}}
        <div class="wizard-header text-center mb-5">
            <h1 class="wizard-title">Arma tu Ramo</h1>
            <p class="wizard-subtitle">Crea un arreglo unico y personalizado</p>
        </div>

        {{-- Indicador de pasos --}}
        @include('tienda.arma-tu-ramo.partials.steps-indicator', ['pasoActual' => 1])

        {{-- Contenido del paso --}}
        <div class="wizard-content">
            <div class="paso-header text-center mb-4">
                <span class="paso-numero">Paso 1</span>
                <h2 class="paso-titulo">Elige tu estilo</h2>
                <p class="paso-descripcion">Selecciona el estilo que mejor represente tu mensaje</p>
            </div>

            <div class="estilos-grid">
                @foreach($estilos as $estilo)
                <div class="estilo-card {{ $ramo->estilo_ramo_id == $estilo->id ? 'selected' : '' }}"
                     data-estilo-id="{{ $estilo->id }}">
                    <div class="estilo-card-inner">
                        @if($estilo->imagen)
                            <div class="estilo-imagen">
                                <img src="{{ asset($estilo->imagen) }}" alt="{{ $estilo->nombre }}">
                            </div>
                        @else
                            <div class="estilo-imagen estilo-imagen-placeholder">
                                <i class="{{ $estilo->icono ?? 'bi-flower2' }}" style="color: {{ $estilo->color_principal ?? '#e91e63' }}"></i>
                            </div>
                        @endif

                        <div class="estilo-info">
                            <h3 class="estilo-nombre">{{ $estilo->nombre }}</h3>
                            <p class="estilo-descripcion">{{ $estilo->descripcion }}</p>
                            <div class="estilo-detalles">
                                <span class="estilo-flores">
                                    <i class="bi bi-flower1"></i> {{ $estilo->flores_minimo }} - {{ $estilo->flores_maximo }} flores
                                </span>
                                <span class="estilo-precio">
                                    Desde ${{ number_format($estilo->precio_base, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="estilo-check">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Boton continuar --}}
            <div class="wizard-actions mt-5">
                <a href="{{ route('tienda.empresa') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <button type="button" class="btn btn-primary btn-lg btn-continuar" disabled>
                    Continuar <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@include('tienda.arma-tu-ramo.partials.resumen-flotante')

@push('styles')
@include('tienda.arma-tu-ramo.partials.wizard-styles')
<style>
    .estilos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .estilo-card {
        background: #fff;
        border: 2px solid var(--flores-border);
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .estilo-card:hover {
        border-color: var(--flores-primary);
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .estilo-card.selected {
        border-color: var(--flores-primary);
        background: #fafafa;
    }

    .estilo-card.selected .estilo-check {
        opacity: 1;
        transform: scale(1);
    }

    .estilo-card-inner {
        padding: 0;
    }

    .estilo-imagen {
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f8f8 0%, #f0f0f0 100%);
    }

    .estilo-imagen img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .estilo-imagen-placeholder i {
        font-size: 4rem;
        opacity: 0.8;
    }

    .estilo-info {
        padding: 20px;
    }

    .estilo-nombre {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--flores-text);
    }

    .estilo-descripcion {
        font-size: 0.9rem;
        color: var(--flores-text-light);
        margin-bottom: 16px;
        line-height: 1.5;
    }

    .estilo-detalles {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid var(--flores-border);
    }

    .estilo-flores {
        font-size: 0.85rem;
        color: var(--flores-text-light);
    }

    .estilo-flores i {
        color: #e91e63;
    }

    .estilo-precio {
        font-weight: 600;
        color: var(--flores-primary);
    }

    .estilo-check {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        background: var(--flores-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        .estilos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const cards = document.querySelectorAll('.estilo-card');
    const btnContinuar = document.querySelector('.btn-continuar');
    let estiloSeleccionado = {{ $ramo->estilo_ramo_id ?? 'null' }};

    // Habilitar boton si ya hay estilo seleccionado
    if (estiloSeleccionado) {
        btnContinuar.disabled = false;
    }

    // Click en cards
    cards.forEach(card => {
        card.addEventListener('click', async function() {
            const estiloId = this.dataset.estiloId;

            // Quitar seleccion anterior
            cards.forEach(c => c.classList.remove('selected'));

            // Seleccionar esta
            this.classList.add('selected');
            estiloSeleccionado = estiloId;
            btnContinuar.disabled = false;

            // Guardar en servidor
            try {
                const res = await fetch('{{ route("arma-tu-ramo.estilo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ estilo_id: estiloId })
                });

                const data = await res.json();
                if (data.success) {
                    // Actualizar resumen flotante si existe
                    actualizarResumen(data);
                }
            } catch (e) {
                console.error('Error:', e);
            }
        });
    });

    // Boton continuar
    btnContinuar.addEventListener('click', function() {
        if (estiloSeleccionado) {
            window.location.href = '{{ route("arma-tu-ramo.paso", ["paso" => 2]) }}';
        }
    });

    function actualizarResumen(data) {
        const totalElement = document.getElementById('resumen-total');
        if (totalElement && data.ramo) {
            totalElement.textContent = '$' + new Intl.NumberFormat('es-CO').format(data.ramo.total || 0);
        }
    }
});
</script>
@endpush
@endsection
