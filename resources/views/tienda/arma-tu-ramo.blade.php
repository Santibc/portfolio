@extends('tienda.layout')

@section('title', 'Arma tu Ramo - Flores y Algo Más')

@section('content')
<div class="container arma-ramo-container">
    {{-- Header --}}
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold" style="color: #e91e63;">
            <i class="bi bi-flower1"></i> Arma tu Ramo Personalizado
        </h1>
        <p class="lead text-muted">Crea un ramo único seleccionando tus flores favoritas</p>
    </div>

    <div class="row g-4">
        {{-- Panel Izquierdo: Selector de Flores --}}
        <div class="col-lg-8">
            {{-- Paso 1: Seleccionar Tamaño --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <span class="badge bg-primary rounded-pill me-2">1</span>
                        Selecciona el tamaño de tu ramo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="tamanos-container">
                        @foreach($tamanos as $tamano)
                        <div class="col-md-4">
                            <div class="tamano-card card h-100 cursor-pointer {{ $ramoEnProgreso->tamano_ramo_id == $tamano->id ? 'border-primary selected' : 'border' }}"
                                 data-tamano-id="{{ $tamano->id }}"
                                 data-min="{{ $tamano->cantidad_flores_min }}"
                                 data-max="{{ $tamano->cantidad_flores_max }}">
                                <div class="card-body text-center">
                                    @if($tamano->imagen)
                                        <img src="{{ asset($tamano->imagen) }}" alt="{{ $tamano->nombre }}" class="mb-2" style="height: 60px;">
                                    @else
                                        <i class="bi bi-flower2 text-primary" style="font-size: 2.5rem;"></i>
                                    @endif
                                    <h6 class="mb-1">{{ $tamano->nombre }}</h6>
                                    <small class="text-muted d-block">{{ $tamano->cantidad_flores_min }} - {{ $tamano->cantidad_flores_max }} flores</small>
                                    <span class="badge bg-light text-dark mt-2">
                                        Base: ${{ number_format($tamano->precio_base, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if($ramoEnProgreso->tamano_ramo_id == $tamano->id)
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <i class="bi bi-check-circle-fill text-primary"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Paso 2: Seleccionar Flores --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <h5 class="mb-0">
                            <span class="badge bg-primary rounded-pill me-2">2</span>
                            Selecciona tus flores
                        </h5>
                        <span class="badge bg-secondary" id="contador-flores">
                            <span id="total-flores">{{ $ramoEnProgreso->total_flores }}</span> flores seleccionadas
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="flores-container">
                        @foreach($flores as $flor)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="flor-card card h-100 border" data-flor-id="{{ $flor->id }}" data-precio="{{ $flor->precio_unidad }}" data-stock="{{ $flor->stock }}">
                                <div class="position-relative">
                                    @if($flor->imagen)
                                        <img src="{{ asset($flor->imagen) }}" class="card-img-top" alt="{{ $flor->nombre }}" style="height: 120px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                            <i class="bi bi-flower1 text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    @if($flor->color)
                                        <span class="position-absolute top-0 start-0 m-2 badge" style="background-color: {{ $flor->color }}; width: 20px; height: 20px; border-radius: 50%;"></span>
                                    @endif
                                </div>
                                <div class="card-body p-2 text-center">
                                    <h6 class="card-title mb-1 small">{{ $flor->nombre }}</h6>
                                    <p class="text-primary fw-bold mb-2 small">${{ number_format($flor->precio_unidad, 0, ',', '.') }}/u</p>

                                    <div class="input-group input-group-sm flor-cantidad-control">
                                        <button class="btn btn-outline-secondary btn-flor-minus" type="button">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number" class="form-control text-center flor-cantidad" value="0" min="0" max="{{ $flor->stock }}" readonly>
                                        <button class="btn btn-outline-primary btn-flor-plus" type="button">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">Stock: {{ $flor->stock }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Paso 3: Adicionales (Opcional) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <span class="badge bg-secondary rounded-pill me-2">3</span>
                        Agrega un detalle especial <span class="text-muted small">(opcional)</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($adicionales as $categoria => $items)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-{{ $categoria == 'chocolate' ? 'gift' : ($categoria == 'peluche' ? 'emoji-smile' : ($categoria == 'globo' ? 'balloon' : 'box')) }}"></i>
                            {{ \App\Models\ProductoAdicional::CATEGORIAS[$categoria] ?? ucfirst($categoria) }}
                        </h6>
                        <div class="row g-2">
                            @foreach($items as $adicional)
                            <div class="col-6 col-md-3">
                                <div class="adicional-card card h-100 border cursor-pointer" data-adicional-id="{{ $adicional->id }}">
                                    <div class="card-body p-2 text-center">
                                        @if($adicional->imagen)
                                            <img src="{{ asset($adicional->imagen) }}" alt="{{ $adicional->nombre }}" class="mb-2" style="height: 50px; object-fit: contain;">
                                        @else
                                            <i class="bi bi-gift text-muted" style="font-size: 2rem;"></i>
                                        @endif
                                        <p class="small mb-1">{{ $adicional->nombre }}</p>
                                        <span class="badge bg-light text-primary">+${{ number_format($adicional->precio, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="adicional-check position-absolute top-0 end-0 p-1 d-none">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Paso 4: Mensaje en Tarjeta --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <span class="badge bg-secondary rounded-pill me-2">4</span>
                        Escribe tu mensaje <span class="text-muted small">(opcional)</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="mensaje-tarjeta" style="height: 150px" maxlength="250" placeholder="Tu mensaje...">{{ $ramoEnProgreso->mensaje_tarjeta }}</textarea>
                                <label for="mensaje-tarjeta">Tu mensaje para la tarjeta</label>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted"><span id="caracteres-count">{{ strlen($ramoEnProgreso->mensaje_tarjeta ?? '') }}</span>/250 caracteres</small>
                                <button class="btn btn-sm btn-outline-primary" id="btn-guardar-mensaje">
                                    <i class="bi bi-check"></i> Guardar mensaje
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Vista Previa de Tarjeta --}}
                            <div class="tarjeta-preview p-4 rounded-3" style="background: linear-gradient(135deg, #fff5f8 0%, #ffe4ec 100%); min-height: 180px; border: 2px dashed #e91e63;">
                                <div class="text-center mb-2">
                                    <i class="bi bi-heart-fill text-danger"></i>
                                </div>
                                <p class="mb-0 text-center fst-italic" id="mensaje-preview" style="font-family: 'Georgia', serif; color: #555;">
                                    {{ $ramoEnProgreso->mensaje_tarjeta ?: 'Tu mensaje aparecerá aquí...' }}
                                </p>
                                <div class="text-center mt-3">
                                    <small class="text-muted">~ Flores y Algo Más ~</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Derecho: Resumen --}}
        <div class="col-lg-4">
            <div class="resumen-ramo-sticky">
                <div class="card border-0 shadow rounded-4 overflow-hidden">
                    {{-- Header con gradiente --}}
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);">
                        <h5 class="mb-0 text-white d-flex align-items-center">
                            <i class="bi bi-bag-heart-fill me-2"></i> Tu Ramo
                        </h5>
                    </div>

                    <div class="card-body p-3">
                        {{-- Vista previa visual del ramo --}}
                        <div class="ramo-preview mb-3 p-3 rounded-3 text-center position-relative" style="background: linear-gradient(180deg, #fce4ec 0%, #fff 100%); min-height: 120px; border: 2px dashed #f8bbd9;">
                            <div id="ramo-visual" class="d-flex flex-wrap justify-content-center align-items-center gap-1" style="min-height: 80px;">
                                <div class="text-muted">
                                    <i class="bi bi-flower1" style="font-size: 2rem; color: #e91e63; opacity: 0.5;"></i>
                                    <p class="small mb-0 mt-1">Selecciona flores</p>
                                </div>
                            </div>
                        </div>

                        {{-- Indicador de progreso compacto --}}
                        <div class="mb-3 p-2 rounded-3" style="background: #f5f5f5;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted fw-medium">Progreso</small>
                                <span class="badge bg-secondary" id="contador-flores-badge">
                                    <span id="total-flores-badge">{{ $ramoEnProgreso->total_flores }}</span> flores
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" id="barra-progreso" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #e91e63, #f06292);"></div>
                            </div>
                            <small class="text-muted d-block mt-1" id="texto-progreso">Selecciona un tamaño para comenzar</small>
                        </div>

                        {{-- Resumen de selección --}}
                        <div id="resumen-flores" class="mb-3">
                            <h6 class="small fw-bold text-uppercase text-muted mb-2">
                                <i class="bi bi-flower2 me-1"></i> Flores seleccionadas
                            </h6>
                            <div id="lista-flores-resumen" class="small" style="max-height: 150px; overflow-y: auto;">
                                <p class="text-muted mb-0 fst-italic">Ninguna flor seleccionada</p>
                            </div>
                        </div>

                        {{-- Adicionales seleccionados --}}
                        <div id="resumen-adicionales" class="mb-3 d-none">
                            <h6 class="small fw-bold text-uppercase text-muted mb-2">
                                <i class="bi bi-gift me-1"></i> Adicionales
                            </h6>
                            <div id="lista-adicionales-resumen" class="small"></div>
                        </div>

                        {{-- Totales --}}
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Tamaño (base):</span>
                                <span class="fw-medium" id="precio-base">${{ number_format($ramoEnProgreso->precio_base, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Flores:</span>
                                <span class="fw-medium" id="subtotal-flores">${{ number_format($ramoEnProgreso->subtotal_flores, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small" id="row-adicionales" style="display: none !important;">
                                <span class="text-muted">Adicionales:</span>
                                <span class="fw-medium" id="subtotal-adicionales">${{ number_format($ramoEnProgreso->subtotal_adicionales, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total:</strong>
                                <strong class="fs-4" style="color: #e91e63;" id="total-ramo">${{ number_format($ramoEnProgreso->total, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <button class="btn w-100 mb-2 py-2 fw-bold" id="btn-agregar-carrito"
                                style="background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%); color: white; border: none;"
                                {{ $ramoEnProgreso->total_flores == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-cart-plus me-1"></i> Agregar al Carrito
                        </button>
                        <button class="btn btn-outline-secondary w-100 btn-sm" id="btn-reiniciar">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Empezar de nuevo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }

    /* Contenedor principal con espacio superior en mobile */
    .arma-ramo-container {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    @media (max-width: 767px) {
        .arma-ramo-container {
            padding-top: 2rem;
        }

        .arma-ramo-container .text-center.mb-5 {
            margin-bottom: 2rem !important;
        }

        .arma-ramo-container .display-5 {
            font-size: 1.5rem;
        }

        .arma-ramo-container .lead {
            font-size: 0.95rem;
        }
    }

    /* Panel sticky mejorado */
    .resumen-ramo-sticky {
        position: sticky;
        top: 90px;
        z-index: 100;
    }

    @media (max-width: 991.98px) {
        .resumen-ramo-sticky {
            position: relative;
            top: 0;
        }
    }

    .tamano-card { transition: all 0.3s ease; }
    .tamano-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .tamano-card.selected { border-color: #e91e63 !important; background-color: #fff5f8; }

    .flor-card { transition: all 0.3s ease; }
    .flor-card:hover { transform: scale(1.02); }
    .flor-card.activa { border-color: #e91e63 !important; background-color: #fff5f8; }

    .adicional-card { transition: all 0.3s ease; position: relative; }
    .adicional-card:hover { transform: scale(1.05); }
    .adicional-card.selected { border-color: #4caf50 !important; background-color: #e8f5e9; }

    .flor-cantidad { width: 50px !important; }

    .ramo-preview { transition: all 0.3s ease; }

    .flor-icono {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        animation: aparecer 0.3s ease;
        margin: 2px;
    }

    @keyframes aparecer {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .tarjeta-preview {
        transition: all 0.3s ease;
    }

    /* Botón agregar carrito con hover */
    #btn-agregar-carrito:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.4);
    }

    #btn-agregar-carrito:disabled {
        background: #ccc !important;
        cursor: not-allowed;
    }

    /* Scrollbar personalizado para lista de flores */
    #lista-flores-resumen::-webkit-scrollbar {
        width: 4px;
    }
    #lista-flores-resumen::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    #lista-flores-resumen::-webkit-scrollbar-thumb {
        background: #e91e63;
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';

    // Estado actual
    let estadoRamo = {
        tamano_id: {{ $ramoEnProgreso->tamano_ramo_id ?? 'null' }},
        flores: @json($ramoEnProgreso->flores_seleccionadas ?? []),
        adicionales: @json($ramoEnProgreso->adicionales ?? [])
    };

    // Actualizar cantidades iniciales en los inputs
    estadoRamo.flores.forEach(f => {
        const card = document.querySelector(`.flor-card[data-flor-id="${f.flor_id}"]`);
        if (card) {
            card.querySelector('.flor-cantidad').value = f.cantidad;
            if (f.cantidad > 0) card.classList.add('activa');
        }
    });

    // Marcar adicionales seleccionados
    estadoRamo.adicionales.forEach(a => {
        const card = document.querySelector(`.adicional-card[data-adicional-id="${a.adicional_id}"]`);
        if (card) {
            card.classList.add('selected');
            card.querySelector('.adicional-check').classList.remove('d-none');
        }
    });

    // Seleccionar tamaño
    document.querySelectorAll('.tamano-card').forEach(card => {
        card.addEventListener('click', async function() {
            const tamanoId = this.dataset.tamanoId;

            try {
                const res = await fetch('/arma-tu-ramo/tamano', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ tamano_id: tamanoId })
                });

                const data = await res.json();
                if (data.success) {
                    document.querySelectorAll('.tamano-card').forEach(c => {
                        c.classList.remove('selected', 'border-primary');
                        c.querySelector('.position-absolute')?.remove();
                    });

                    this.classList.add('selected', 'border-primary');
                    this.innerHTML += '<div class="position-absolute top-0 end-0 p-2"><i class="bi bi-check-circle-fill text-primary"></i></div>';

                    estadoRamo.tamano_id = tamanoId;
                    actualizarResumen(data);
                }
            } catch (e) {
                console.error(e);
            }
        });
    });

    // Control de cantidad de flores
    document.querySelectorAll('.btn-flor-plus').forEach(btn => {
        btn.addEventListener('click', async function() {
            const card = this.closest('.flor-card');
            const florId = card.dataset.florId;
            const input = card.querySelector('.flor-cantidad');
            const max = parseInt(card.dataset.stock);
            const actual = parseInt(input.value);

            if (actual < max) {
                try {
                    const res = await fetch('/arma-tu-ramo/flor/agregar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ flor_id: florId, cantidad: 1 })
                    });

                    const data = await res.json();
                    if (data.success) {
                        input.value = actual + 1;
                        card.classList.add('activa');
                        actualizarResumen(data);
                        actualizarVistaRamo(data.flores);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: data.message,
                            confirmButtonColor: '#8d6c4f'
                        });
                    }
                } catch (e) {
                    console.error(e);
                }
            }
        });
    });

    document.querySelectorAll('.btn-flor-minus').forEach(btn => {
        btn.addEventListener('click', async function() {
            const card = this.closest('.flor-card');
            const florId = card.dataset.florId;
            const input = card.querySelector('.flor-cantidad');
            const actual = parseInt(input.value);

            if (actual > 0) {
                try {
                    const res = await fetch('/arma-tu-ramo/flor/actualizar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ flor_id: florId, cantidad: actual - 1 })
                    });

                    const data = await res.json();
                    if (data.success) {
                        input.value = actual - 1;
                        if (actual - 1 === 0) card.classList.remove('activa');
                        actualizarResumen(data);
                        actualizarVistaRamo(data.flores);
                    }
                } catch (e) {
                    console.error(e);
                }
            }
        });
    });

    // Toggle adicionales
    document.querySelectorAll('.adicional-card').forEach(card => {
        card.addEventListener('click', async function() {
            const adicionalId = this.dataset.adicionalId;

            try {
                const res = await fetch('/arma-tu-ramo/adicional/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ adicional_id: adicionalId })
                });

                const data = await res.json();
                if (data.success) {
                    if (data.agregado) {
                        this.classList.add('selected');
                        this.querySelector('.adicional-check').classList.remove('d-none');
                    } else {
                        this.classList.remove('selected');
                        this.querySelector('.adicional-check').classList.add('d-none');
                    }
                    actualizarResumen(data);
                }
            } catch (e) {
                console.error(e);
            }
        });
    });

    // Mensaje en tarjeta
    const mensajeInput = document.getElementById('mensaje-tarjeta');
    const mensajePreview = document.getElementById('mensaje-preview');
    const caracteresCount = document.getElementById('caracteres-count');

    mensajeInput.addEventListener('input', function() {
        const texto = this.value;
        caracteresCount.textContent = texto.length;
        mensajePreview.textContent = texto || 'Tu mensaje aparecerá aquí...';
    });

    document.getElementById('btn-guardar-mensaje').addEventListener('click', async function() {
        try {
            const res = await fetch('/arma-tu-ramo/mensaje', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ mensaje: mensajeInput.value })
            });

            const data = await res.json();
            if (data.success) {
                this.innerHTML = '<i class="bi bi-check"></i> Guardado!';
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-check"></i> Guardar mensaje';
                }, 2000);
            }
        } catch (e) {
            console.error(e);
        }
    });

    // Agregar al carrito
    document.getElementById('btn-agregar-carrito').addEventListener('click', async function() {
        console.log('Botón agregar al carrito clickeado');
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Agregando...';

        try {
            console.log('Enviando request a /arma-tu-ramo/agregar-carrito');
            const res = await fetch('/arma-tu-ramo/agregar-carrito', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            console.log('Response status:', res.status);
            const data = await res.json();
            console.log('Response data:', data);

            if (data.success) {
                // Actualizar contador del carrito
                const carritoCount = document.querySelector('.carrito-count');
                if (carritoCount) carritoCount.textContent = data.carrito_count;

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '¡Ramo agregado al carrito!',
                    confirmButtonColor: '#8d6c4f',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = '/carrito';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#8d6c4f'
                });
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al Carrito';
            }
        } catch (e) {
            console.error('Error al agregar al carrito:', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al agregar el ramo al carrito. Por favor intenta de nuevo.',
                confirmButtonColor: '#8d6c4f'
            });
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al Carrito';
        }
    });

    // Reiniciar
    document.getElementById('btn-reiniciar').addEventListener('click', async function() {
        const result = await Swal.fire({
            icon: 'question',
            title: '¿Estás seguro?',
            text: '¿Deseas empezar de nuevo? Se perderán todos los cambios.',
            showCancelButton: true,
            confirmButtonColor: '#8d6c4f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, empezar de nuevo',
            cancelButtonText: 'Cancelar'
        });

        if (!result.isConfirmed) return;

        try {
            const res = await fetch('/arma-tu-ramo/reiniciar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: 'Comenzando de nuevo...',
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo reiniciar. Intenta de nuevo.',
                confirmButtonColor: '#8d6c4f'
            });
        }
    });

    // Funciones de actualización
    function actualizarResumen(data) {
        const totalFlores = data.total_flores || 0;
        document.getElementById('total-flores').textContent = totalFlores;
        document.getElementById('total-flores-badge').textContent = totalFlores;
        document.getElementById('precio-base').textContent = '$' + formatNumber(data.ramo?.precio_base || 0);
        document.getElementById('subtotal-flores').textContent = '$' + formatNumber(data.ramo?.subtotal_flores || data.subtotal_flores || 0);
        document.getElementById('subtotal-adicionales').textContent = '$' + formatNumber(data.ramo?.subtotal_adicionales || data.subtotal_adicionales || 0);
        document.getElementById('total-ramo').textContent = '$' + formatNumber(data.total || data.ramo?.total || 0);

        // Mostrar/ocultar adicionales
        const rowAdicionales = document.getElementById('row-adicionales');
        const subtotalAdicionales = data.ramo?.subtotal_adicionales || data.subtotal_adicionales || 0;
        rowAdicionales.style.display = subtotalAdicionales > 0 ? 'flex' : 'none';

        // Actualizar lista de flores
        const listaFlores = document.getElementById('lista-flores-resumen');
        const flores = data.flores || [];
        if (flores.length > 0) {
            listaFlores.innerHTML = flores.map(f =>
                `<div class="d-flex justify-content-between mb-1">
                    <span>${f.cantidad}x ${f.nombre}</span>
                    <span>$${formatNumber(f.subtotal)}</span>
                </div>`
            ).join('');
        } else {
            listaFlores.innerHTML = '<p class="text-muted">Ninguna flor seleccionada</p>';
        }

        // Actualizar lista de adicionales
        const adicionales = data.adicionales || data.ramo?.adicionales || [];
        const resumenAdicionales = document.getElementById('resumen-adicionales');
        const listaAdicionales = document.getElementById('lista-adicionales-resumen');
        if (adicionales.length > 0) {
            resumenAdicionales.classList.remove('d-none');
            listaAdicionales.innerHTML = adicionales.map(a =>
                `<div class="d-flex justify-content-between mb-1">
                    <span>${a.nombre}</span>
                    <span>$${formatNumber(a.precio)}</span>
                </div>`
            ).join('');
        } else {
            resumenAdicionales.classList.add('d-none');
        }

        // Botón agregar carrito
        const btnAgregar = document.getElementById('btn-agregar-carrito');
        btnAgregar.disabled = (data.total_flores || 0) === 0;

        // Barra de progreso
        actualizarProgreso(data.total_flores || 0);
    }

    function actualizarVistaRamo(flores) {
        const container = document.getElementById('ramo-visual');

        if (!flores || flores.length === 0) {
            container.innerHTML = `<div class="text-muted">
                <i class="bi bi-flower1" style="font-size: 2rem; color: #e91e63; opacity: 0.5;"></i>
                <p class="small mb-0 mt-1">Selecciona flores</p>
            </div>`;
            return;
        }

        let html = '';
        const colores = ['#e91e63', '#9c27b0', '#f44336', '#ff9800', '#ffeb3b', '#4caf50', '#2196f3'];

        flores.forEach((f, idx) => {
            for (let i = 0; i < Math.min(f.cantidad, 8); i++) {
                const color = colores[idx % colores.length];
                html += `<span class="flor-icono" style="background-color: ${color}20; color: ${color};">
                    <i class="bi bi-flower1"></i>
                </span>`;
            }
            if (f.cantidad > 8) {
                html += `<span class="badge bg-light text-dark mx-1">+${f.cantidad - 8}</span>`;
            }
        });

        container.innerHTML = html;
    }

    function actualizarProgreso(totalFlores) {
        const tamanoCard = document.querySelector('.tamano-card.selected');
        const barra = document.getElementById('barra-progreso');
        const texto = document.getElementById('texto-progreso');
        const badge = document.getElementById('contador-flores-badge');

        if (!tamanoCard) {
            barra.style.width = '0%';
            barra.style.background = 'linear-gradient(90deg, #e91e63, #f06292)';
            texto.textContent = 'Selecciona un tamaño para comenzar';
            badge.className = 'badge bg-secondary';
            return;
        }

        const min = parseInt(tamanoCard.dataset.min);
        const max = parseInt(tamanoCard.dataset.max);

        if (totalFlores < min) {
            const progreso = (totalFlores / min) * 100;
            barra.style.width = progreso + '%';
            barra.style.background = 'linear-gradient(90deg, #ff9800, #ffb74d)';
            texto.textContent = `Faltan ${min - totalFlores} flores para el mínimo`;
            badge.className = 'badge bg-warning text-dark';
        } else if (totalFlores >= min && totalFlores <= max) {
            barra.style.width = '100%';
            barra.style.background = 'linear-gradient(90deg, #4caf50, #81c784)';
            texto.textContent = '¡Listo para agregar al carrito!';
            badge.className = 'badge bg-success';
        } else {
            barra.style.width = '100%';
            barra.style.background = 'linear-gradient(90deg, #f44336, #ef5350)';
            texto.textContent = `Máximo ${max} flores para este tamaño`;
            badge.className = 'badge bg-danger';
        }
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0 }).format(num);
    }

    // Inicializar
    actualizarProgreso({{ $ramoEnProgreso->total_flores }});
    actualizarVistaRamo(@json($ramoEnProgreso->resumen_flores));
});
</script>
@endpush
@endsection
