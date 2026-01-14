@extends('tienda.layout')

@section('title', 'Paso 4: Resumen - Arma tu Ramo')

@section('content')
<div class="arma-ramo-wizard">
    <div class="container py-4">
        {{-- Header del wizard --}}
        <div class="wizard-header text-center mb-5">
            <h1 class="wizard-title">Arma tu Ramo</h1>
            <p class="wizard-subtitle">Crea un arreglo unico y personalizado</p>
        </div>

        {{-- Indicador de pasos --}}
        @include('tienda.arma-tu-ramo.partials.steps-indicator', ['pasoActual' => 4])

        {{-- Contenido del paso --}}
        <div class="wizard-content">
            <div class="paso-header text-center mb-4">
                <span class="paso-numero">Paso 4</span>
                <h2 class="paso-titulo">Resumen de tu Ramo</h2>
                <p class="paso-descripcion">Revisa tu creacion y agregala al carrito</p>
            </div>

            <div class="resumen-container">
                <div class="row">
                    {{-- Columna izquierda: Detalles del ramo --}}
                    <div class="col-lg-7">
                        {{-- Estilo seleccionado --}}
                        <div class="resumen-seccion">
                            <div class="resumen-seccion-header">
                                <span class="resumen-seccion-numero">1</span>
                                <h3 class="resumen-seccion-titulo">Estilo</h3>
                                <a href="{{ route('arma-tu-ramo.paso', ['paso' => 1]) }}" class="btn-editar">
                                    <i class="bi bi-pencil"></i> Cambiar
                                </a>
                            </div>
                            <div class="resumen-seccion-content">
                                @if($ramo->estilo)
                                <div class="estilo-resumen">
                                    <div class="estilo-icono" style="background: {{ $ramo->estilo->color_principal }}20; color: {{ $ramo->estilo->color_principal }}">
                                        <i class="bi {{ $ramo->estilo->icono }}"></i>
                                    </div>
                                    <div class="estilo-info">
                                        <h4>{{ $ramo->estilo->nombre }}</h4>
                                        <p>{{ $ramo->estilo->descripcion }}</p>
                                    </div>
                                    <div class="estilo-precio">
                                        ${{ number_format($ramo->estilo->precio_base, 0, ',', '.') }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Flores seleccionadas --}}
                        <div class="resumen-seccion">
                            <div class="resumen-seccion-header">
                                <span class="resumen-seccion-numero">2</span>
                                <h3 class="resumen-seccion-titulo">Flores ({{ $ramo->total_flores }})</h3>
                                <a href="{{ route('arma-tu-ramo.paso', ['paso' => 2]) }}" class="btn-editar">
                                    <i class="bi bi-pencil"></i> Cambiar
                                </a>
                            </div>
                            <div class="resumen-seccion-content">
                                <div class="flores-resumen-list">
                                    @foreach($ramo->resumen_flores as $flor)
                                    <div class="flor-resumen-item">
                                        <div class="flor-resumen-img">
                                            @if($flor['imagen'])
                                                <img src="{{ asset($flor['imagen']) }}" alt="{{ $flor['nombre'] }}">
                                            @else
                                                <div class="flor-placeholder">
                                                    <i class="bi bi-flower1"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flor-resumen-info">
                                            <span class="flor-nombre">{{ $flor['nombre'] }}</span>
                                            <span class="flor-cantidad">x{{ $flor['cantidad'] }}</span>
                                        </div>
                                        <div class="flor-resumen-precio">
                                            ${{ number_format($flor['subtotal'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Envoltura seleccionada --}}
                        <div class="resumen-seccion">
                            <div class="resumen-seccion-header">
                                <span class="resumen-seccion-numero">3</span>
                                <h3 class="resumen-seccion-titulo">Envoltura</h3>
                                <a href="{{ route('arma-tu-ramo.paso', ['paso' => 3]) }}" class="btn-editar">
                                    <i class="bi bi-pencil"></i> Cambiar
                                </a>
                            </div>
                            <div class="resumen-seccion-content">
                                @if($ramo->envoltura)
                                <div class="envoltura-resumen">
                                    <div class="envoltura-resumen-img">
                                        @if($ramo->envoltura->imagen)
                                            <img src="{{ asset($ramo->envoltura->imagen) }}" alt="{{ $ramo->envoltura->nombre }}">
                                        @else
                                            <div class="envoltura-placeholder">
                                                <i class="bi {{ $ramo->envoltura->icono ?? 'bi-box' }}"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="envoltura-resumen-info">
                                        <h4>{{ $ramo->envoltura->nombre }}</h4>
                                        <p>{{ $ramo->envoltura->descripcion }}</p>
                                    </div>
                                    <div class="envoltura-resumen-precio">
                                        @if($ramo->envoltura->precio_adicional > 0)
                                            +${{ number_format($ramo->envoltura->precio_adicional, 0, ',', '.') }}
                                        @else
                                            <span class="precio-incluido">Incluido</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Adicionales opcionales --}}
                        @php
                            $adicionalesDisponibles = collect($adicionales)->flatten();
                            $adicionalesSeleccionados = collect($ramo->adicionales ?? [])->pluck('adicional_id')->toArray();
                        @endphp
                        @if($adicionalesDisponibles->count() > 0)
                        <div class="resumen-seccion">
                            <div class="resumen-seccion-header">
                                <span class="resumen-seccion-numero"><i class="bi bi-plus-circle"></i></span>
                                <h3 class="resumen-seccion-titulo">Adicionales (Opcional)</h3>
                            </div>
                            <div class="resumen-seccion-content">
                                <div class="adicionales-grid">
                                    @foreach($adicionalesDisponibles as $adicional)
                                    <div class="adicional-item {{ in_array($adicional->id, $adicionalesSeleccionados) ? 'selected' : '' }}"
                                         data-adicional-id="{{ $adicional->id }}">
                                        <div class="adicional-check">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div class="adicional-img">
                                            @if($adicional->imagen)
                                                <img src="{{ asset($adicional->imagen) }}" alt="{{ $adicional->nombre }}">
                                            @else
                                                <i class="bi {{ $adicional->icono ?? 'bi-gift' }}"></i>
                                            @endif
                                        </div>
                                        <div class="adicional-info">
                                            <span class="adicional-nombre">{{ $adicional->nombre }}</span>
                                            <span class="adicional-precio">+${{ number_format($adicional->precio, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Mensaje para la tarjeta --}}
                        <div class="resumen-seccion">
                            <div class="resumen-seccion-header">
                                <span class="resumen-seccion-numero"><i class="bi bi-chat-heart"></i></span>
                                <h3 class="resumen-seccion-titulo">Mensaje para la Tarjeta (Opcional)</h3>
                            </div>
                            <div class="resumen-seccion-content">
                                <textarea class="form-control mensaje-tarjeta"
                                          id="mensaje_tarjeta"
                                          rows="3"
                                          maxlength="200"
                                          placeholder="Escribe un mensaje especial para acompanar tu ramo...">{{ $ramo->mensaje_tarjeta }}</textarea>
                                <div class="mensaje-contador">
                                    <span id="mensaje-chars">{{ strlen($ramo->mensaje_tarjeta ?? '') }}</span>/200 caracteres
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Columna derecha: Resumen de precio --}}
                    <div class="col-lg-5">
                        <div class="precio-resumen-card">
                            <h3 class="precio-resumen-titulo">Resumen del Pedido</h3>

                            <div class="precio-desglose">
                                <div class="precio-linea">
                                    <span>Estilo {{ $ramo->estilo->nombre ?? '' }}</span>
                                    <span id="precio-estilo">${{ number_format($ramo->estilo->precio_base ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="precio-linea">
                                    <span>Flores ({{ $ramo->total_flores }} unidades)</span>
                                    <span id="precio-flores">${{ number_format($ramo->subtotal_flores, 0, ',', '.') }}</span>
                                </div>
                                <div class="precio-linea">
                                    <span>Envoltura</span>
                                    <span id="precio-envoltura">
                                        @if(($ramo->envoltura->precio_adicional ?? 0) > 0)
                                            +${{ number_format($ramo->envoltura->precio_adicional, 0, ',', '.') }}
                                        @else
                                            Incluido
                                        @endif
                                    </span>
                                </div>
                                <div class="precio-linea adicionales-linea" style="{{ count($ramo->adicionales ?? []) == 0 ? 'display: none;' : '' }}">
                                    <span>Adicionales</span>
                                    <span id="precio-adicionales">+${{ number_format($ramo->subtotal_adicionales, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="precio-total">
                                <span>Total</span>
                                <span id="precio-total">${{ number_format($ramo->total, 0, ',', '.') }}</span>
                            </div>

                            <button type="button" class="btn btn-primary btn-lg w-100 btn-agregar-carrito">
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>

                            <button type="button" class="btn btn-outline-secondary w-100 mt-3 btn-reiniciar">
                                <i class="bi bi-arrow-counterclockwise"></i> Empezar de Nuevo
                            </button>
                        </div>

                        {{-- Vista previa visual --}}
                        @php
                            $floresResumen = $ramo->resumen_flores;
                        @endphp
                        <div class="ramo-preview-card mt-4">
                            <h4 class="preview-titulo">Tu Ramo Personalizado</h4>
                            <div class="preview-visual">
                                <div class="preview-estilo" style="border-color: {{ $ramo->estilo->color_principal ?? '#e91e63' }}">
                                    <i class="bi {{ $ramo->estilo->icono ?? 'bi-heart' }}" style="color: {{ $ramo->estilo->color_principal ?? '#e91e63' }}"></i>
                                </div>
                                <div class="preview-flores">
                                    @foreach(array_slice($floresResumen, 0, 5) as $flor)
                                    <div class="preview-flor" title="{{ $flor['nombre'] }} x{{ $flor['cantidad'] }}">
                                        @if($flor['imagen'])
                                            <img src="{{ asset($flor['imagen']) }}" alt="{{ $flor['nombre'] }}">
                                        @else
                                            <i class="bi bi-flower1"></i>
                                        @endif
                                    </div>
                                    @endforeach
                                    @if(count($floresResumen) > 5)
                                    <div class="preview-flor preview-more">
                                        +{{ count($floresResumen) - 5 }}
                                    </div>
                                    @endif
                                </div>
                                <div class="preview-envoltura">
                                    <i class="bi {{ $ramo->envoltura->icono ?? 'bi-box' }}"></i>
                                    <span>{{ $ramo->envoltura->nombre ?? 'Envoltura' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boton anterior --}}
            <div class="wizard-actions mt-5">
                <a href="{{ route('arma-tu-ramo.paso', ['paso' => 3]) }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Anterior
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
@include('tienda.arma-tu-ramo.partials.wizard-styles')
<style>
    .resumen-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .resumen-seccion {
        background: #fff;
        border: 1px solid var(--flores-border);
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .resumen-seccion-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: #fafafa;
        border-bottom: 1px solid var(--flores-border);
    }

    .resumen-seccion-numero {
        width: 28px;
        height: 28px;
        background: var(--flores-primary);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .resumen-seccion-titulo {
        font-family: var(--font-serif);
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        flex: 1;
    }

    .btn-editar {
        font-size: 0.85rem;
        color: var(--flores-primary);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-editar:hover {
        text-decoration: underline;
    }

    .resumen-seccion-content {
        padding: 20px;
    }

    /* Estilo resumen */
    .estilo-resumen {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .estilo-icono {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .estilo-info {
        flex: 1;
    }

    .estilo-info h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 4px 0;
    }

    .estilo-info p {
        font-size: 0.9rem;
        color: var(--flores-text-light);
        margin: 0;
    }

    .estilo-precio {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--flores-primary);
    }

    /* Flores resumen */
    .flores-resumen-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .flor-resumen-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        background: #fafafa;
        border-radius: 8px;
    }

    .flor-resumen-img {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .flor-resumen-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .flor-placeholder {
        width: 100%;
        height: 100%;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--flores-text-light);
    }

    .flor-resumen-info {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .flor-nombre {
        font-weight: 500;
    }

    .flor-cantidad {
        background: var(--flores-primary);
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .flor-resumen-precio {
        font-weight: 600;
        color: var(--flores-text);
    }

    /* Envoltura resumen */
    .envoltura-resumen {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .envoltura-resumen-img {
        width: 80px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .envoltura-resumen-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .envoltura-placeholder {
        font-size: 1.5rem;
        color: var(--flores-text-light);
    }

    .envoltura-resumen-info {
        flex: 1;
    }

    .envoltura-resumen-info h4 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 4px 0;
    }

    .envoltura-resumen-info p {
        font-size: 0.85rem;
        color: var(--flores-text-light);
        margin: 0;
    }

    .envoltura-resumen-precio {
        font-weight: 600;
        color: var(--flores-primary);
    }

    .precio-incluido {
        color: #28a745;
        font-weight: 500;
    }

    /* Adicionales */
    .adicionales-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
    }

    .adicional-item {
        position: relative;
        padding: 12px;
        border: 2px solid var(--flores-border);
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .adicional-item:hover {
        border-color: var(--flores-primary);
    }

    .adicional-item.selected {
        border-color: var(--flores-primary);
        background: #fef5f7;
    }

    .adicional-check {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 22px;
        height: 22px;
        background: var(--flores-primary);
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
    }

    .adicional-item.selected .adicional-check {
        opacity: 1;
        transform: scale(1);
    }

    .adicional-img {
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }

    .adicional-img img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .adicional-img i {
        font-size: 2rem;
        color: var(--flores-text-light);
    }

    .adicional-nombre {
        display: block;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .adicional-precio {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--flores-primary);
    }

    /* Mensaje tarjeta */
    .mensaje-tarjeta {
        border: 1px solid var(--flores-border);
        border-radius: 8px;
        resize: none;
    }

    .mensaje-tarjeta:focus {
        border-color: var(--flores-primary);
        box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
    }

    .mensaje-contador {
        text-align: right;
        font-size: 0.8rem;
        color: var(--flores-text-light);
        margin-top: 8px;
    }

    /* Precio resumen card */
    .precio-resumen-card {
        background: #fff;
        border: 2px solid var(--flores-primary);
        border-radius: 16px;
        padding: 24px;
        position: sticky;
        top: 100px;
    }

    .precio-resumen-titulo {
        font-family: var(--font-serif);
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--flores-border);
    }

    .precio-desglose {
        margin-bottom: 20px;
    }

    .precio-linea {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 0.95rem;
        border-bottom: 1px dashed var(--flores-border);
    }

    .precio-total {
        display: flex;
        justify-content: space-between;
        padding: 16px 0;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--flores-primary);
        border-top: 2px solid var(--flores-text);
        margin-bottom: 20px;
    }

    .btn-agregar-carrito {
        padding: 14px 24px;
        font-size: 1.1rem;
        font-weight: 600;
    }

    /* Preview card */
    .ramo-preview-card {
        background: linear-gradient(135deg, #fef5f7 0%, #fff5f8 100%);
        border: 1px solid var(--flores-border);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
    }

    .preview-titulo {
        font-family: var(--font-serif);
        font-size: 1.1rem;
        margin-bottom: 16px;
        color: var(--flores-text);
    }

    .preview-visual {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .preview-estilo {
        width: 64px;
        height: 64px;
        border: 3px solid;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: #fff;
    }

    .preview-flores {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
    }

    .preview-flor {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview-flor img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-flor i {
        font-size: 1.2rem;
        color: var(--flores-primary);
    }

    .preview-more {
        background: var(--flores-primary);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .preview-envoltura {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #fff;
        border-radius: 20px;
        font-size: 0.85rem;
        color: var(--flores-text-light);
    }

    @media (max-width: 992px) {
        .precio-resumen-card {
            position: static;
            margin-top: 24px;
        }
    }

    @media (max-width: 768px) {
        .estilo-resumen {
            flex-wrap: wrap;
        }

        .estilo-precio {
            width: 100%;
            text-align: right;
            margin-top: 8px;
        }

        .adicionales-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';

    // Toggle adicionales
    const adicionales = document.querySelectorAll('.adicional-item');
    adicionales.forEach(item => {
        item.addEventListener('click', async function() {
            const adicionalId = this.dataset.adicionalId;

            try {
                const res = await fetch('{{ route("arma-tu-ramo.adicional.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ adicional_id: adicionalId })
                });

                const data = await res.json();
                if (data.success) {
                    this.classList.toggle('selected', data.agregado);
                    actualizarPrecios(data);
                }
            } catch (e) {
                console.error('Error:', e);
            }
        });
    });

    // Guardar mensaje tarjeta
    const mensajeTextarea = document.getElementById('mensaje_tarjeta');
    const charsCounter = document.getElementById('mensaje-chars');
    let mensajeTimeout;

    mensajeTextarea.addEventListener('input', function() {
        charsCounter.textContent = this.value.length;

        clearTimeout(mensajeTimeout);
        mensajeTimeout = setTimeout(async () => {
            try {
                await fetch('{{ route("arma-tu-ramo.mensaje") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ mensaje: this.value })
                });
            } catch (e) {
                console.error('Error:', e);
            }
        }, 500);
    });

    // Reiniciar configurador
    const btnReiniciar = document.querySelector('.btn-reiniciar');
    btnReiniciar.addEventListener('click', async function() {
        const result = await Swal.fire({
            title: '¿Empezar de nuevo?',
            text: 'Se perderan todas las selecciones actuales.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--flores-primary)',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Si, reiniciar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const res = await fetch('{{ route("arma-tu-ramo.reiniciar") }}', {
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
                }
            } catch (e) {
                console.error('Error:', e);
            }
        }
    });

    // Agregar al carrito
    const btnAgregarCarrito = document.querySelector('.btn-agregar-carrito');
    btnAgregarCarrito.addEventListener('click', async function() {
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
                // Mostrar mensaje de exito
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado al carrito!',
                    text: 'Tu ramo personalizado ha sido agregado al carrito.',
                    confirmButtonColor: 'var(--flores-primary)',
                    confirmButtonText: 'Ver Carrito'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("tienda.carrito") }}';
                    } else {
                        window.location.href = '{{ route("arma-tu-ramo") }}';
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
            this.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al Carrito';
        }
    });

    function actualizarPrecios(data) {
        document.getElementById('precio-adicionales').textContent = '+$' + new Intl.NumberFormat('es-CO').format(data.subtotal_adicionales);
        document.getElementById('precio-total').textContent = '$' + new Intl.NumberFormat('es-CO').format(data.total);

        const lineaAdicionales = document.querySelector('.adicionales-linea');
        if (data.subtotal_adicionales > 0) {
            lineaAdicionales.style.display = 'flex';
        } else {
            lineaAdicionales.style.display = 'none';
        }

        // Actualizar resumen flotante si existe
        const resumenTotal = document.getElementById('resumen-total');
        if (resumenTotal) {
            resumenTotal.textContent = '$' + new Intl.NumberFormat('es-CO').format(data.total);
        }
    }
});
</script>
@endpush
@endsection
