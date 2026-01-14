@extends('tienda.layout')

@section('title', 'Paso 3: Elige tu Envoltura - Arma tu Ramo')

@section('content')
<div class="arma-ramo-wizard">
    <div class="container py-4">
        {{-- Header del wizard --}}
        <div class="wizard-header text-center mb-4">
            <h1 class="wizard-title">Arma tu ramo</h1>
            <p class="wizard-subtitle">Crea un arreglo unico y personalizado en 3 simples pasos</p>
        </div>

        {{-- Indicador de pasos --}}
        @include('tienda.arma-tu-ramo.partials.steps-indicator', ['pasoActual' => 3])

        {{-- Contenido del paso --}}
        <div class="wizard-content">
            <div class="paso-header text-center mb-4">
                <h2 class="paso-titulo">Paso 3: Elige tu envoltura</h2>
            </div>

            {{-- Grid de envolturas --}}
            <div class="envolturas-grid mb-5">
                @foreach($envolturas as $envoltura)
                <div class="envoltura-card {{ $ramo->envoltura_ramo_id == $envoltura->id ? 'selected' : '' }}"
                     data-envoltura-id="{{ $envoltura->id }}"
                     data-precio="{{ $envoltura->precio_adicional }}">
                    @if($envoltura->imagen)
                        <div class="envoltura-imagen">
                            <img src="{{ asset($envoltura->imagen) }}" alt="{{ $envoltura->nombre }}">
                        </div>
                    @else
                        <div class="envoltura-imagen envoltura-imagen-placeholder">
                            <i class="bi {{ $envoltura->icono ?? 'bi-box' }}"></i>
                        </div>
                    @endif
                    <div class="envoltura-info">
                        <h3 class="envoltura-nombre">{{ $envoltura->nombre }}</h3>
                        <p class="envoltura-precio">${{ number_format($envoltura->precio_adicional, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Resumen del ramo --}}
            <div class="resumen-ramo">
                <h3 class="resumen-titulo">Resumen de tu ramo</h3>
                <div class="resumen-content">
                    <div class="resumen-linea">
                        <span class="resumen-label">Estilo:</span>
                        <span class="resumen-valor" id="resumen-estilo">{{ $ramo->estilo->nombre ?? '-' }}</span>
                    </div>
                    @foreach($ramo->resumen_flores as $flor)
                    <div class="resumen-linea">
                        <span class="resumen-label">{{ $flor['nombre'] }} x {{ $flor['cantidad'] }}</span>
                        <span class="resumen-valor">${{ number_format($flor['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="resumen-linea resumen-linea-envoltura" style="{{ !$ramo->envoltura_ramo_id ? 'display: none;' : '' }}">
                        <span class="resumen-label">Envoltura:</span>
                        <span class="resumen-valor" id="resumen-envoltura">${{ number_format($ramo->precio_envoltura ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="resumen-total">
                        <span class="total-label">Total</span>
                        <span class="total-valor" id="resumen-total">${{ number_format($ramo->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Botones de accion --}}
            <div class="wizard-actions mt-4">
                <a href="{{ route('arma-tu-ramo.paso', ['paso' => 2]) }}" class="btn btn-outline-secondary btn-lg">
                    Atras
                </a>
                <button type="button" class="btn btn-primary btn-lg btn-agregar-carrito" {{ !$ramo->envoltura_ramo_id ? 'disabled' : '' }}>
                    Agregar al carrito
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
@include('tienda.arma-tu-ramo.partials.wizard-styles')
<style>
    .wizard-title {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .wizard-subtitle {
        color: var(--flores-text-light);
        font-size: 1rem;
    }

    .paso-titulo {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
    }

    .envolturas-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 900px;
        margin: 0 auto;
    }

    .envoltura-card {
        background: #fff;
        border: 2px solid var(--flores-border);
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .envoltura-card:hover {
        border-color: var(--flores-primary);
    }

    .envoltura-card.selected {
        border-color: var(--flores-primary);
        box-shadow: 0 0 0 2px var(--flores-primary);
    }

    .envoltura-imagen {
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f8f8;
    }

    .envoltura-imagen img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .envoltura-imagen-placeholder i {
        font-size: 4rem;
        color: var(--flores-text-light);
        opacity: 0.4;
    }

    .envoltura-info {
        padding: 16px;
    }

    .envoltura-nombre {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .envoltura-precio {
        font-size: 0.95rem;
        color: var(--flores-text-light);
        margin: 0;
    }

    /* Resumen del ramo */
    .resumen-ramo {
        max-width: 900px;
        margin: 0 auto;
        background: #f9f9f9;
        border-radius: 12px;
        padding: 24px;
    }

    .resumen-titulo {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .resumen-content {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .resumen-linea {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .resumen-label {
        color: var(--flores-text-light);
    }

    .resumen-valor {
        font-weight: 500;
    }

    .resumen-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        margin-top: 8px;
        border-top: 2px solid var(--flores-text);
    }

    .total-label {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .total-valor {
        font-size: 1.3rem;
        font-weight: 700;
    }

    /* Wizard actions */
    .wizard-actions {
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        gap: 16px;
    }

    .wizard-actions .btn {
        min-width: 150px;
    }

    @media (max-width: 768px) {
        .envolturas-grid {
            grid-template-columns: 1fr;
        }

        .wizard-actions {
            flex-direction: column;
        }

        .wizard-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const cards = document.querySelectorAll('.envoltura-card');
    const btnAgregar = document.querySelector('.btn-agregar-carrito');
    let envolturaSeleccionada = {{ $ramo->envoltura_ramo_id ?? 'null' }};

    // Seleccionar envoltura
    cards.forEach(card => {
        card.addEventListener('click', async function() {
            const envolturaId = this.dataset.envolturaId;
            const precio = this.dataset.precio;

            cards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            envolturaSeleccionada = envolturaId;
            btnAgregar.disabled = false;

            try {
                const res = await fetch('{{ route("arma-tu-ramo.envoltura") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ envoltura_id: envolturaId })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('resumen-total').textContent = '$' + new Intl.NumberFormat('es-CO').format(data.total);
                    document.getElementById('resumen-envoltura').textContent = '$' + new Intl.NumberFormat('es-CO').format(precio);
                    document.querySelector('.resumen-linea-envoltura').style.display = 'flex';
                }
            } catch (e) {
                console.error('Error:', e);
            }
        });
    });

    // Agregar al carrito
    btnAgregar.addEventListener('click', async function() {
        if (!envolturaSeleccionada) return;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Agregando...';

        try {
            const res = await fetch('{{ route("arma-tu-ramo.agregar-carrito") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado al carrito!',
                    text: 'Tu ramo personalizado ha sido agregado al carrito.',
                    confirmButtonColor: 'var(--flores-primary)',
                    confirmButtonText: 'Ver Carrito',
                    showCancelButton: true,
                    cancelButtonText: 'Seguir comprando'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("tienda.carrito") }}';
                    } else {
                        window.location.href = '{{ route("tienda.empresa") }}';
                    }
                });
            } else {
                throw new Error(data.message || 'Error al agregar');
            }
        } catch (e) {
            console.error('Error:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo agregar al carrito. Intenta nuevamente.',
                confirmButtonColor: 'var(--flores-primary)'
            });
            this.disabled = false;
            this.innerHTML = 'Agregar al carrito';
        }
    });
});
</script>
@endpush
@endsection
