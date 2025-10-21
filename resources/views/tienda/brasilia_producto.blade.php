@extends('tienda.brasilia_layout')

@section('title', $producto->nombre . ' - ' . $empresa->nombre)

@section('content')
    <?php
    // Preparar datos del producto para usar en el template
    $precioActual = is_object($producto->precio_actual) ? $producto->precio_actual->precio : $producto->precio_actual;
    $precioAnterior = is_object($producto->precio_actual) && isset($producto->precio_actual->precio_anterior) ? $producto->precio_actual->precio_anterior : null;
    $descuento = 0;

    if ($precioAnterior && $precioAnterior > $precioActual) {
        $descuento = round((($precioAnterior - $precioActual) / $precioAnterior) * 100);
    }

    // Preparar imágenes
    $imagenes = [];
    if ($producto->imagenes && $producto->imagenes->count() > 0) {
        foreach ($producto->imagenes as $img) {
            $imagenes[] = $img->url;
        }
    } else {
        $imagenes[] = $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp');
    }
    ?>

    <!-- Product Detail -->
    <div id="single-product" class="product-detail-page">
        <div class="container pt-3 pt-md-4 pb-4">
            <div class="row">
                <!-- Product Images -->
                <div class="col-md-7 mb-4 mb-md-0">
                    <div class="product-detail-images">
                        <!-- Main Image Slider -->
                        <div class="swiper product-main-swiper mb-3" style="max-width: 500px; margin: 0 auto;">
                            <div class="swiper-wrapper">
                                @foreach($imagenes as $imagen)
                                <div class="swiper-slide">
                                    <div class="product-detail-image-container" style="padding-top: 100% !important; max-width: 500px; margin: 0 auto; border-radius: 0;">
                                        <img src="{{ $imagen }}" alt="{{ $producto->nombre }}" class="product-detail-image">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <!-- Navigation -->
                            @if(count($imagenes) > 1)
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                            @endif
                        </div>

                        <!-- Thumbnails -->
                        <div class="swiper product-thumbs-swiper" style="max-width: 500px; margin: 0 auto;">
                            <div class="swiper-wrapper">
                                @foreach($imagenes as $imagen)
                                <div class="swiper-slide" style="max-width: 120px;">
                                    <div class="product-thumb-container" style="padding-top: 100% !important; border-radius: 0;">
                                        <img src="{{ $imagen }}" alt="{{ $producto->nombre }}" class="product-thumb-image">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-md-5">
                    <div class="product-detail-info">
                        <h1 class="product-detail-title mb-3">{{ $producto->nombre }}</h1>

                        <!-- Price -->
                        @if($precioActual)
                        <div class="product-detail-price mb-3">
                            @if($precioAnterior && $precioAnterior > $precioActual)
                            <div class="price-compare mb-1">
                                ${{ number_format($precioAnterior, 0, ',', '.') }}
                            </div>
                            @endif
                            <div class="d-flex align-items-center">
                                <span class="price-current h3 mb-0">
                                    ${{ number_format($precioActual, 0, ',', '.') }}
                                </span>
                                @if($descuento > 0)
                                <span class="price-discount-badge ml-2">
                                    {{ $descuento }}% OFF
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Saved Money -->
                        @if($precioAnterior && $precioAnterior > $precioActual)
                        <div class="saved-money-message mb-3">
                            Ahorrás ${{ number_format($precioAnterior - $precioActual, 0, ',', '.') }}
                        </div>
                        @endif

                        <!-- Installments -->
                        <div class="installments-info mb-4">
                            <div class="installments-badge mb-2">
                                <svg class="icon-inline icon-sm mr-1"><use xlink:href="#credit-card"></use></svg>
                                <strong>3 cuotas sin interés de ${{ number_format($precioActual / 3, 0, ',', '.') }}</strong>
                            </div>
                            <div class="installments-total">
                                <span>Total en 1 pago: </span>
                                <strong>${{ number_format($precioActual, 0, ',', '.') }}</strong>
                            </div>
                            <div class="installments-cards">con todas las tarjetas.</div>
                        </div>
                        @else
                        <div class="alert alert-warning">Precio no disponible</div>
                        @endif

                        <!-- Free Shipping -->
                        @if($producto->info_envio)
                        <div class="free-shipping-message mb-4">
                            <svg class="icon-inline icon-lg mr-2"><use xlink:href="#truck"></use></svg>
                            <span>{{ $producto->info_envio }}</span>
                        </div>
                        @else
                        <div class="free-shipping-message mb-4">
                            <svg class="icon-inline icon-lg mr-2"><use xlink:href="#truck"></use></svg>
                            <span><strong class="text-accent">Envío gratis</strong> superando los $56.000</span>
                        </div>
                        @endif

                        <!-- Product Form -->
                        <form id="product_form" class="product-form" method="post">
                            @csrf

                            <!-- Variants (Sizes) -->
                            @if($producto->tiene_variantes && $producto->variantes->count() > 0)
                            <div class="product-variants mb-4">
                                <label class="variant-label mb-2">Variantes:</label>
                                <div class="variant-options">
                                    @foreach($producto->variantes as $index => $variante)
                                        @php
                                            $varianteStockInfo = $producto->getStockInfo($variante->id);
                                            $tieneStockDisponible = $varianteStockInfo['hay_stock'];
                                            $nombreVariante = $variante->nombre_variante;
                                        @endphp
                                        <label class="variant-option {{ !$tieneStockDisponible && $producto->controlar_stock && !$producto->permitir_venta_sin_stock ? 'disabled' : '' }}">
                                            <input type="radio"
                                                   name="variant"
                                                   value="{{ $variante->id }}"
                                                   data-variante-id="{{ $variante->id }}"
                                                   data-talla="{{ $variante->talla }}"
                                                   data-color="{{ $variante->color }}"
                                                   data-value="{{ $nombreVariante }}"
                                                   data-stock-disponible="{{ $varianteStockInfo['stock_disponible'] }}"
                                                   data-puede-agregar-sin-stock="{{ $varianteStockInfo['puede_agregar_sin_stock'] ? 'true' : 'false' }}"
                                                   {{ $index === 0 && $tieneStockDisponible ? 'checked' : '' }}
                                                   {{ (!$tieneStockDisponible && $producto->controlar_stock && !$producto->permitir_venta_sin_stock) ? 'disabled' : '' }}>
                                            <span class="variant-button">{{ $nombreVariante ?: 'Sin especificar' }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-2 text-muted small">
                                    <span id="selectedVariant">
                                        @if($producto->variantes->first())
                                            {{ $producto->variantes->first()->nombre_variante ?: 'Sin especificar' }}
                                        @endif
                                    </span>
                                </div>
                                <div class="mt-2" id="stockInfo">
                                    @php
                                        $primeraVariante = $producto->variantes->first();
                                        if ($primeraVariante) {
                                            $stockInfo = $producto->getStockInfo($primeraVariante->id);
                                            $stock = $stockInfo['stock_disponible'];
                                        }
                                    @endphp
                                    @if(isset($stockInfo))
                                        @if(!$stockInfo['controlar_stock'] || $stockInfo['permitir_venta_sin_stock'])
                                            <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
                                            <span>Disponible</span>
                                        @elseif($stockInfo['controlar_stock'] && !$stockInfo['permitir_venta_sin_stock'])
                                            @if($stock > 10)
                                                <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
                                                <span>Disponible</span>
                                            @elseif($stock > 0)
                                                <i class="bi bi-exclamation-circle-fill" style="color: #f59e0b;"></i>
                                                <span>Solo {{ $stock }} disponibles</span>
                                            @else
                                                <i class="bi bi-x-circle-fill" style="color: #ef4444;"></i>
                                                <span>Sin stock</span>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @else
                                <!-- Stock sin variantes -->
                                <div class="mb-4" id="stockInfo">
                                    @php
                                        $stockInfo = $producto->getStockInfo();
                                        $stock = $stockInfo['stock_disponible'];
                                    @endphp
                                    @if(!$stockInfo['controlar_stock'] || $stockInfo['permitir_venta_sin_stock'])
                                        <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
                                        <span>Disponible</span>
                                    @elseif($stockInfo['controlar_stock'] && !$stockInfo['permitir_venta_sin_stock'])
                                        @if($stock > 10)
                                            <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
                                            <span>Disponible</span>
                                        @elseif($stock > 0)
                                            <i class="bi bi-exclamation-circle-fill" style="color: #f59e0b;"></i>
                                            <span>Solo {{ $stock }} disponibles</span>
                                        @else
                                            <i class="bi bi-x-circle-fill" style="color: #ef4444;"></i>
                                            <span>Sin stock</span>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            <!-- Quantity and Add to Cart in same row -->
                            <div class="product-quantity-cart mb-4">
                                <div class="quantity-section">
                                    <label class="quantity-label mb-2">Cantidad:</label>
                                    <div class="quantity-selector">
                                        <button type="button" class="quantity-btn quantity-minus">
                                            <svg class="icon-inline"><use xlink:href="#minus"></use></svg>
                                        </button>
                                        <input type="number" name="quantity" value="1" min="1" max="99" class="quantity-input">
                                        <button type="button" class="quantity-btn quantity-plus">
                                            <svg class="icon-inline"><use xlink:href="#plus"></use></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="cart-section">
                                    <button type="button" class="btn btn-primary btn-big" id="addToCartBtn"
                                        @php
                                            $stockInfo = $producto->getStockInfo();
                                            $puedeAgregar = $precioActual && ($stockInfo['hay_stock'] || !$stockInfo['stock_limitado']);
                                        @endphp
                                        {{ !$puedeAgregar ? 'disabled' : '' }}>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>

                            <!-- Product Description -->
                            @if($producto->descripcion)
                            <div class="product-short-description mb-4">
                                <p class="text-muted mb-0">{{ $producto->descripcion }}</p>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Specifications -->
            @if($producto->unidad_venta || $producto->unidad_empaque || $producto->extension || $producto->referencia)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="product-description">
                        <h2 class="description-title mb-3">Especificaciones</h2>
                        <div class="description-content">
                            <ul class="list-unstyled">
                                @if($producto->referencia)
                                <li class="mb-2"><strong>Referencia:</strong> {{ $producto->referencia }}</li>
                                @endif
                                @if($producto->unidad_venta)
                                <li class="mb-2"><strong>Unidad de Venta:</strong> {{ $producto->unidad_venta }}</li>
                                @endif
                                @if($producto->unidad_empaque)
                                <li class="mb-2"><strong>Unidad de Empaque:</strong> {{ $producto->unidad_empaque }}</li>
                                @endif
                                @if($producto->extension)
                                <li class="mb-2"><strong>Extensión:</strong> {{ $producto->extension }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Related Products -->
            @if(isset($relacionados) && $relacionados->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="section-title mb-4">Productos relacionados</h2>
                    <div class="swiper related-products-swiper">
                        <div class="swiper-wrapper">
                            @foreach($relacionados as $relacionado)
                                @php
                                    $precioRel = is_object($relacionado->precio_actual) ? $relacionado->precio_actual->precio : $relacionado->precio_actual;
                                    $precioAntRel = is_object($relacionado->precio_actual) && isset($relacionado->precio_actual->precio_anterior) ? $relacionado->precio_actual->precio_anterior : null;
                                    $descuentoRel = 0;

                                    if ($precioAntRel && $precioAntRel > $precioRel) {
                                        $descuentoRel = round((($precioAntRel - $precioRel) / $precioAntRel) * 100);
                                    }
                                @endphp
                                <div class="swiper-slide">
                                    <div class="product-card" style="max-width: 300px;">
                                        <div class="product-image-wrapper">
                                            <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}" class="product-image-link">
                                                <div class="product-image-container-square" style="max-height: 300px;">
                                                    <img src="{{ $relacionado->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}"
                                                         alt="{{ $relacionado->nombre }}"
                                                         class="product-image"
                                                         loading="lazy">
                                                </div>
                                            </a>
                                            @if($descuentoRel > 0)
                                            <span class="product-badge product-badge-discount">{{ $descuentoRel }}% OFF</span>
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <h3 class="product-name">
                                                <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}">
                                                    {{ $relacionado->nombre }}
                                                </a>
                                            </h3>
                                            <div class="product-price-container">
                                                @if($precioAntRel && $precioAntRel > $precioRel)
                                                <span class="product-price-compare">${{ number_format($precioAntRel, 0, ',', '.') }}</span>
                                                @endif
                                                @if($precioRel)
                                                <span class="product-price">${{ number_format($precioRel, 0, ',', '.') }}</span>
                                                @else
                                                <span class="product-price text-muted">Consultar</span>
                                                @endif
                                            </div>
                                            @if($precioRel)
                                            <p class="product-installments">3 cuotas sin interés de ${{ number_format($precioRel / 3, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($relacionados->count() > 1)
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // === Variables globales ===
    const tieneVariantes = {{ $producto->tiene_variantes ? 'true' : 'false' }};
    const variantes = @json($producto->variantes);
    let selectedVariantId = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar variante seleccionada si existe
        @if($producto->tiene_variantes && $producto->variantes->count() > 0)
            selectedVariantId = {{ $producto->variantes->first()->id }};
        @endif

        // Product Image Sliders
        const productThumbsSwiper = new Swiper('.product-thumbs-swiper', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });

        const productMainSwiper = new Swiper('.product-main-swiper', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.product-main-swiper .swiper-button-next',
                prevEl: '.product-main-swiper .swiper-button-prev',
            },
            thumbs: {
                swiper: productThumbsSwiper,
            },
        });

        // Related Products Slider
        const relatedProductsSwiper = new Swiper('.related-products-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: '.related-products-swiper .swiper-button-next',
                prevEl: '.related-products-swiper .swiper-button-prev',
            },
            breakpoints: {
                480: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 4,
                },
            },
        });

        // Quantity Selector
        const quantityMinus = document.querySelector('.quantity-minus');
        const quantityPlus = document.querySelector('.quantity-plus');
        const quantityInput = document.querySelector('.quantity-input');

        if (quantityMinus) {
            quantityMinus.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
        }

        if (quantityPlus) {
            quantityPlus.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxValue = parseInt(quantityInput.getAttribute('max')) || 99;
                if (currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                }
            });
        }

        // Selección de variante
        const variantInputs = document.querySelectorAll('input[name="variant"]');
        variantInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                if (!this.disabled) {
                    selectedVariantId = this.dataset.varianteId;
                    const varianteName = this.dataset.value;
                    const selectedVariantEl = document.getElementById('selectedVariant');
                    if (selectedVariantEl) {
                        selectedVariantEl.textContent = varianteName;
                    }

                    // Actualizar stock info
                    updateStockInfo(selectedVariantId);

                    // Habilitar/deshabilitar botón de agregar al carrito
                    const puedeAgregar = this.dataset.puedeAgregarSinStock === 'true' || parseInt(this.dataset.stockDisponible) > 0;
                    const addToCartBtn = document.getElementById('addToCartBtn');
                    if (addToCartBtn) {
                        addToCartBtn.disabled = !puedeAgregar;
                    }
                }
            });
        });

        // Agregar al carrito
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const quantity = parseInt(quantityInput.value);

                if (tieneVariantes && !selectedVariantId) {
                    showToast('error', 'Por favor selecciona una variante del producto');
                    return;
                }

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Agregando...';

                const data = {
                    producto_id: {{ $producto->id }},
                    cantidad: quantity
                };
                if (selectedVariantId) data.variante_id = selectedVariantId;

                fetch("{{ route('tienda.carrito.agregar', $empresa->slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    showToast('success', 'Producto agregado al carrito');
                    if (data && typeof data.total_items !== 'undefined') {
                        updateCartBadge(data.total_items);
                    }
                    addToCartBtn.innerHTML = '<i class="bi bi-check"></i> Agregado';

                    setTimeout(() => {
                        addToCartBtn.disabled = false;
                        addToCartBtn.innerHTML = 'Agregar al carrito';
                    }, 2000);
                })
                .catch(error => {
                    const errorMsg = error.message || 'Error al agregar al carrito';
                    showToast('error', errorMsg);
                    addToCartBtn.disabled = false;
                    addToCartBtn.innerHTML = 'Agregar al carrito';
                });
            });
        }
    });

    // Actualizar información de stock
    function updateStockInfo(varianteId) {
        fetch("{{ route('tienda.stock.info', $empresa->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                producto_id: {{ $producto->id }},
                variante_id: varianteId
            })
        })
        .then(response => response.json())
        .then(stockInfo => {
            const stock = stockInfo.stock_disponible || 0;
            let stockHtml = '';

            if (!stockInfo.controlar_stock || stockInfo.permitir_venta_sin_stock) {
                stockHtml = '<i class="bi bi-check-circle-fill" style="color: #10b981;"></i> <span>Disponible</span>';
            } else if (stockInfo.controlar_stock && !stockInfo.permitir_venta_sin_stock) {
                if (stock > 10) {
                    stockHtml = '<i class="bi bi-check-circle-fill" style="color: #10b981;"></i> <span>Disponible</span>';
                } else if (stock > 0) {
                    stockHtml = '<i class="bi bi-exclamation-circle-fill" style="color: #f59e0b;"></i> <span>Solo ' + stock + ' disponibles</span>';
                } else {
                    stockHtml = '<i class="bi bi-x-circle-fill" style="color: #ef4444;"></i> <span>Sin stock</span>';
                }
            }

            const stockInfoEl = document.getElementById('stockInfo');
            if (stockInfoEl) {
                stockInfoEl.innerHTML = stockHtml;
            }

            // Actualizar límite de cantidad
            const quantityInput = document.querySelector('.quantity-input');
            if (quantityInput) {
                if (stockInfo.stock_limitado && stock > 0) {
                    quantityInput.setAttribute('max', stock);
                } else {
                    quantityInput.setAttribute('max', 99);
                }
            }
        })
        .catch(error => {
            console.error('Error al actualizar stock:', error);
        });
    }

    // Toast notification
    function showToast(type, message) {
        const toastEl = document.getElementById('cartToast');
        if (!toastEl) {
            console.error('Toast element not found');
            alert(message);
            return;
        }

        const toast = new bootstrap.Toast(toastEl);

        const toastBody = toastEl.querySelector('.toast-body');
        if (toastBody) {
            toastBody.textContent = message;
        }

        const toastIcon = toastEl.querySelector('.toast-header i');
        if (toastIcon) {
            if (type === 'error') {
                toastIcon.classList.remove('text-success', 'bi-check-circle-fill');
                toastIcon.classList.add('text-danger', 'bi-exclamation-circle-fill');
            } else {
                toastIcon.classList.remove('text-danger', 'bi-exclamation-circle-fill');
                toastIcon.classList.add('text-success', 'bi-check-circle-fill');
            }
        }

        toast.show();
    }

    // Actualiza el badge del carrito
    function updateCartBadge(count) {
        const cartBtn = document.querySelector('.header-action-btn');
        if (!cartBtn) return;

        let badge = cartBtn.querySelector('.badge');

        if (count > 0) {
            if (badge) {
                badge.textContent = count;
            } else {
                badge = document.createElement('span');
                badge.className = 'badge';
                badge.textContent = count;
                cartBtn.appendChild(badge);
            }
        } else {
            if (badge) {
                badge.remove();
            }
        }
    }
</script>
@endpush
