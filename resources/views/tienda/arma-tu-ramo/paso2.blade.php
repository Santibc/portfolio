@extends('tienda.layout')

@section('title', 'Paso 2: Selecciona tus Flores - Arma tu Ramo')

@section('content')
<div class="arma-ramo-wizard">
    <div class="container py-4">
        {{-- Header del wizard --}}
        <div class="wizard-header text-center mb-5">
            <h1 class="wizard-title">Arma tu Ramo</h1>
            <p class="wizard-subtitle">Crea un arreglo unico y personalizado</p>
        </div>

        {{-- Indicador de pasos --}}
        @include('tienda.arma-tu-ramo.partials.steps-indicator', ['pasoActual' => 2])

        {{-- Contenido del paso --}}
        <div class="wizard-content">
            <div class="paso-header text-center mb-4">
                <span class="paso-numero">Paso 2</span>
                <h2 class="paso-titulo">Selecciona tus flores</h2>
                <p class="paso-descripcion">
                    Estilo <strong>{{ $estilo->nombre }}</strong>:
                    selecciona entre {{ $estilo->flores_minimo }} y {{ $estilo->flores_maximo }} flores
                </p>
            </div>

            {{-- Contador de progreso --}}
            <div class="flores-progreso mb-4">
                <div class="progreso-info">
                    <span class="progreso-texto">
                        <strong id="total-flores">{{ $ramo->total_flores }}</strong> de {{ $estilo->flores_minimo }}-{{ $estilo->flores_maximo }} flores
                    </span>
                    <span class="progreso-estado" id="progreso-estado">
                        @if($ramo->total_flores < $estilo->flores_minimo)
                            <span class="text-warning"><i class="bi bi-exclamation-circle"></i> Faltan {{ $estilo->flores_minimo - $ramo->total_flores }} flores</span>
                        @elseif($ramo->cumpleRequisitoFlores())
                            <span class="text-success"><i class="bi bi-check-circle"></i> Listo</span>
                        @else
                            <span class="text-danger"><i class="bi bi-x-circle"></i> Maximo alcanzado</span>
                        @endif
                    </span>
                </div>
                <div class="progreso-barra">
                    <div class="progreso-fill" id="progreso-fill"
                         style="width: {{ min(100, ($ramo->total_flores / $estilo->flores_minimo) * 100) }}%">
                    </div>
                </div>
            </div>

            {{-- Grid de flores --}}
            <div class="flores-grid">
                @foreach($flores as $flor)
                <div class="flor-card {{ $ramo->flores_seleccionadas && collect($ramo->flores_seleccionadas)->firstWhere('flor_id', $flor->id) ? 'has-selection' : '' }}"
                     data-flor-id="{{ $flor->id }}"
                     data-precio="{{ $flor->precio_unidad }}"
                     data-stock="{{ $flor->stock }}">

                    <div class="flor-imagen">
                        @if($flor->imagen)
                            <img src="{{ asset($flor->imagen) }}" alt="{{ $flor->nombre }}">
                        @else
                            <div class="flor-placeholder">
                                <i class="bi bi-flower1" style="color: {{ $flor->color ?? '#e91e63' }}"></i>
                            </div>
                        @endif
                        @if($flor->color)
                            <span class="flor-color-badge" style="background-color: {{ $flor->color }}"></span>
                        @endif
                    </div>

                    <div class="flor-info">
                        <h4 class="flor-nombre">{{ $flor->nombre }}</h4>
                        <!-- Se comento el precio para que no se muestre en el paso 2 -->
                        <!-- <p class="flor-precio">${{ number_format($flor->precio_unidad, 0, ',', '.') }}/u</p> -->
                    </div>

                    <div class="flor-cantidad-control">
                        @php
                            $cantidadActual = 0;
                            if ($ramo->flores_seleccionadas) {
                                $florEnRamo = collect($ramo->flores_seleccionadas)->firstWhere('flor_id', $flor->id);
                                $cantidadActual = $florEnRamo['cantidad'] ?? 0;
                            }
                        @endphp
                        <button type="button" class="btn-cantidad btn-minus" {{ $cantidadActual == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="cantidad-display">
                            {{ $cantidadActual }}
                        </span>
                        <button type="button" class="btn-cantidad btn-plus">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>

                    <div class="flor-stock">
                        <small class="text-muted">Stock: {{ $flor->stock }}</small>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Boton continuar --}}
            <div class="wizard-actions mt-5">
                <a href="{{ route('arma-tu-ramo.paso', ['paso' => 1]) }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Anterior
                </a>
                <button type="button" class="btn btn-primary btn-lg btn-continuar" {{ !$ramo->cumpleRequisitoFlores() ? 'disabled' : '' }}>
                    Continuar <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Resumen flotante en la parte inferior comentado por que el cliente no quiere reflejar precios -->
<!-- @include('tienda.arma-tu-ramo.partials.resumen-flotante') -->

@push('styles')
@include('tienda.arma-tu-ramo.partials.wizard-styles')
<style>
    .flores-progreso {
        max-width: 500px;
        margin: 0 auto;
        background: #f8f8f8;
        padding: 16px 20px;
        border-radius: 12px;
    }

    .progreso-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .progreso-texto {
        font-size: 0.95rem;
    }

    .progreso-barra {
        height: 8px;
        background: var(--flores-border);
        border-radius: 4px;
        overflow: hidden;
    }

    .progreso-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--flores-primary), #333);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .flores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .flor-card {
        background: #fff;
        border: 2px solid var(--flores-border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        text-align: center;
    }

    .flor-card:hover {
        border-color: var(--flores-primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .flor-card.has-selection {
        border-color: var(--flores-primary);
        background: #fafafa;
    }

    .flor-imagen {
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f8f8 0%, #f0f0f0 100%);
        position: relative;
    }

    .flor-imagen img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .flor-placeholder i {
        font-size: 3rem;
        opacity: 0.7;
    }

    .flor-color-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .flor-info {
        padding: 12px 12px 8px;
    }

    .flor-nombre {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 4px;
        color: var(--flores-text);
    }

    .flor-precio {
        font-size: 0.85rem;
        color: var(--flores-primary);
        font-weight: 600;
        margin: 0;
    }

    .flor-cantidad-control {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 12px;
        border-top: 1px solid var(--flores-border);
    }

    .btn-cantidad {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid var(--flores-border);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-cantidad:hover:not(:disabled) {
        background: var(--flores-primary);
        border-color: var(--flores-primary);
        color: #fff;
    }

    .btn-cantidad:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .cantidad-display {
        font-size: 1.1rem;
        font-weight: 700;
        min-width: 30px;
        text-align: center;
    }

    .flor-stock {
        padding: 4px 12px 12px;
    }

    @media (max-width: 576px) {
        .flores-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .flor-imagen {
            height: 100px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const btnContinuar = document.querySelector('.btn-continuar');
    const totalFloresEl = document.getElementById('total-flores');
    const progresoEstadoEl = document.getElementById('progreso-estado');
    const progresoFillEl = document.getElementById('progreso-fill');

    const minFlores = {{ $estilo->flores_minimo }};
    const maxFlores = {{ $estilo->flores_maximo }};

    // Plus/Minus buttons
    document.querySelectorAll('.btn-plus').forEach(btn => {
        btn.addEventListener('click', async function() {
            const card = this.closest('.flor-card');
            const florId = card.dataset.florId;
            const stock = parseInt(card.dataset.stock);
            const displayEl = card.querySelector('.cantidad-display');
            const actual = parseInt(displayEl.textContent);

            if (actual < stock) {
                await actualizarFlor(florId, actual + 1, card);
            }
        });
    });

    document.querySelectorAll('.btn-minus').forEach(btn => {
        btn.addEventListener('click', async function() {
            const card = this.closest('.flor-card');
            const florId = card.dataset.florId;
            const displayEl = card.querySelector('.cantidad-display');
            const actual = parseInt(displayEl.textContent);

            if (actual > 0) {
                await actualizarFlor(florId, actual - 1, card);
            }
        });
    });

    async function actualizarFlor(florId, cantidad, card) {
        try {
            const res = await fetch('{{ route("arma-tu-ramo.flor.actualizar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ flor_id: florId, cantidad: cantidad })
            });

            const data = await res.json();

            if (data.success) {
                // Actualizar UI
                const displayEl = card.querySelector('.cantidad-display');
                displayEl.textContent = cantidad;

                // Actualizar estado de la card
                if (cantidad > 0) {
                    card.classList.add('has-selection');
                } else {
                    card.classList.remove('has-selection');
                }

                // Actualizar botones
                const minusBtn = card.querySelector('.btn-minus');
                minusBtn.disabled = cantidad === 0;

                // Actualizar progreso
                actualizarProgreso(data.total_flores, data.cumple_requisito);

                // Actualizar resumen flotante
                document.getElementById('resumen-flores').textContent = data.total_flores + ' seleccionadas';
                document.getElementById('resumen-total').textContent = '$' + new Intl.NumberFormat('es-CO').format(data.total);

            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atencion',
                    text: data.message,
                    confirmButtonColor: '#1a1a1a'
                });
            }
        } catch (e) {
            console.error('Error:', e);
        }
    }

    function actualizarProgreso(totalFlores, cumpleRequisito) {
        totalFloresEl.textContent = totalFlores;

        const porcentaje = Math.min(100, (totalFlores / minFlores) * 100);
        progresoFillEl.style.width = porcentaje + '%';

        if (totalFlores < minFlores) {
            progresoEstadoEl.innerHTML = `<span class="text-warning"><i class="bi bi-exclamation-circle"></i> Faltan ${minFlores - totalFlores} flores</span>`;
            btnContinuar.disabled = true;
        } else if (totalFlores <= maxFlores) {
            progresoEstadoEl.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> Listo</span>`;
            btnContinuar.disabled = false;
        } else {
            progresoEstadoEl.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle"></i> Maximo alcanzado</span>`;
            btnContinuar.disabled = true;
        }
    }

    // Boton continuar
    btnContinuar.addEventListener('click', async function() {
        try {
            const res = await fetch('{{ route("arma-tu-ramo.flores.confirmar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atencion',
                    text: data.message,
                    confirmButtonColor: '#1a1a1a'
                });
            }
        } catch (e) {
            console.error('Error:', e);
        }
    });
});
</script>
@endpush
@endsection
