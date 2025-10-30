@extends('tienda.lima_layout')

@section('title', $producto->nombre . ' - ' . $empresa->nombre)

@section('content')
<?php
// Preparar datos del producto
$precioActual = is_object($producto->precio_actual) ? $producto->precio_actual->precio : $producto->precio_actual;
$precioAnterior = is_object($producto->precio_actual) && isset($producto->precio_actual->precio_anterior) ? $producto->precio_actual->precio_anterior : null;

// Buscar descuentos activos
$descuentoActivo = null;
$textoDescuento = null;
$precioConDescuento = $precioActual;
$montoDescuento = 0;

if (isset($descuentosActivos)) {
    foreach ($descuentosActivos as $desc) {
        $aplica = false;

        if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
            $aplica = true;
        } elseif ($desc->aplica_a === 'producto' && in_array($producto->id, $desc->productos_aplicables ?? [])) {
            $aplica = true;
        } elseif ($desc->aplica_a === 'categoria' && in_array($producto->categoria_id, $desc->categorias_aplicables ?? [])) {
            $aplica = true;
        }

        if ($aplica) {
            $descuentoActivo = $desc;
            if ($desc->tipo === 'porcentaje') {
                $montoDescuento = ($precioActual * $desc->valor) / 100;
                $textoDescuento = round($desc->valor) . '% OFF';
            } else {
                $montoDescuento = $desc->valor;
                $textoDescuento = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
            }
            $precioConDescuento = $precioActual - $montoDescuento;
            break;
        }
    }
}

// Preparar imágenes
$imagenes = [];
if ($producto->imagenes && $producto->imagenes->count() > 0) {
    foreach ($producto->imagenes as $img) {
        $imagenes[] = $img->url;
    }
} else {
    $imagenes[] = $producto->url_imagen_principal ?? asset('images/no-image.png');
}
?>

<div class="container py-4">
    {{-- Breadcrumbs --}}
    <div class="breadcrumbs-container mb-4">
        <ul class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="{{ route('tienda.index', $empresa->slug) }}">
                    <span itemprop="name">Inicio</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            @if($producto->categoria)
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $producto->categoria_id]) }}">
                    <span itemprop="name">{{ $producto->categoria->nombre }}</span>
                </a>
                <meta itemprop="position" content="2">
            </li>
            @endif
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name">{{ $producto->nombre }}</span>
                <meta itemprop="position" content="{{ $producto->categoria ? '3' : '2' }}">
            </li>
        </ul>
    </div>

    {{-- Product Detail --}}
    <div id="single-product" class="product-detail-page">
        <div class="row">
            {{-- Product Images --}}
            <div class="col-md-7 mb-4 mb-md-0">
                <div class="product-detail-images">
                    {{-- Main Image Slider --}}
                    <div class="js-product-slider swiper-container product-main-swiper mb-3">
                        <div class="swiper-wrapper">
                            @foreach($imagenes as $imagen)
                            <div class="swiper-slide">
                                <div class="product-detail-image-container">
                                    <img src="{{ $imagen }}"
                                         alt="{{ $producto->nombre }}"
                                         class="product-detail-image">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        {{-- Navigation --}}
                        @if(count($imagenes) > 1)
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                        @endif
                        <div class="swiper-pagination"></div>
                    </div>

                    {{-- Thumbnails --}}
                    @if(count($imagenes) > 1)
                    <div class="js-product-thumbs swiper-container product-thumbs-swiper">
                        <div class="swiper-wrapper">
                            @foreach($imagenes as $imagen)
                            <div class="swiper-slide">
                                <div class="product-thumb-container">
                                    <img src="{{ $imagen }}"
                                         alt="{{ $producto->nombre }}"
                                         class="product-thumb-image">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-md-5">
                <div class="product-detail-info">
                    <h1 class="product-detail-title h2 mb-3">{{ $producto->nombre }}</h1>

                    {{-- Price --}}
                    @if($precioActual)
                    <div class="product-detail-price mb-3">
                        @if($descuentoActivo)
                            @if($descuentoActivo->nombre)
                            <div class="alert alert-success py-2 mb-2">
                                <svg class="icon-inline icon-md mr-1" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5zm2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5z"/>
                                </svg>
                                <strong>{{ $descuentoActivo->nombre }}</strong>
                                @if($descuentoActivo->descripcion)
                                    <small class="d-block">{{ $descuentoActivo->descripcion }}</small>
                                @endif
                            </div>
                            @endif
                            <div class="price-compare mb-1">
                                ${{ number_format($precioActual, 0, ',', '.') }}
                            </div>
                        @endif
                        <div class="d-flex align-items-center">
                            <span class="price-current h3 mb-0">
                                ${{ number_format($precioConDescuento, 0, ',', '.') }}
                            </span>
                            @if($descuentoActivo)
                            <span class="price-discount-badge ml-2">
                                {{ $textoDescuento }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Saved Money --}}
                    @if($descuentoActivo && $montoDescuento > 0)
                    <div class="saved-money-message mb-3">
                        Ahorrás ${{ number_format($montoDescuento, 0, ',', '.') }}
                    </div>
                    @endif

                    {{-- Installments --}}
                    <div class="installments-info mb-4">
                        <div class="installments-badge mb-2">
                            <svg class="icon-inline icon-sm mr-1" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4zm0 3v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1z"/>
                            </svg>
                            <strong>3 cuotas sin interés de ${{ number_format($precioConDescuento / 3, 0, ',', '.') }}</strong>
                        </div>
                        <div class="installments-total">
                            <span>Total en 1 pago: </span>
                            <strong>${{ number_format($precioConDescuento, 0, ',', '.') }}</strong>
                        </div>
                        <div class="installments-cards">con todas las tarjetas.</div>
                    </div>
                    @else
                    <div class="alert alert-warning">Precio no disponible</div>
                    @endif

                    {{-- Shipping Info --}}
                    @if($producto->info_envio)
                    <div class="free-shipping-message mb-4">
                        <svg class="icon-inline icon-lg mr-2" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                        </svg>
                        <span>{{ $producto->info_envio }}</span>
                    </div>
                    @endif

                    {{-- Product Form --}}
                    <form id="product_form" class="product-form" method="post">
                        @csrf

                        {{-- Variants --}}
                        @if($producto->tiene_variantes && $producto->variantes->count() > 0)
                        <div class="product-variants mb-4">
                            <label class="variant-label mb-2 font-weight-bold">Variantes:</label>
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
                                        <span class="badge badge-success">Disponible</span>
                                    @elseif($stockInfo['controlar_stock'] && !$stockInfo['permitir_venta_sin_stock'])
                                        @if($stock > 10)
                                            <span class="badge badge-success">Disponible</span>
                                        @elseif($stock > 0)
                                            <span class="badge badge-warning">Solo {{ $stock }} disponibles</span>
                                        @else
                                            <span class="badge badge-danger">Sin stock</span>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                        @else
                            {{-- Stock sin variantes --}}
                            <div class="mb-4" id="stockInfo">
                                @php
                                    $stockInfo = $producto->getStockInfo();
                                    $stock = $stockInfo['stock_disponible'];
                                @endphp
                                @if(!$stockInfo['controlar_stock'] || $stockInfo['permitir_venta_sin_stock'])
                                    <span class="badge badge-success">Disponible</span>
                                @elseif($stockInfo['controlar_stock'] && !$stockInfo['permitir_venta_sin_stock'])
                                    @if($stock > 10)
                                        <span class="badge badge-success">Disponible</span>
                                    @elseif($stock > 0)
                                        <span class="badge badge-warning">Solo {{ $stock }} disponibles</span>
                                    @else
                                        <span class="badge badge-danger">Sin stock</span>
                                    @endif
                                @endif
                            </div>
                        @endif

                        {{-- Quantity and Add to Cart --}}
                        <div class="product-quantity-cart mb-4">
                            <div class="row">
                                <div class="col-auto">
                                    <label class="quantity-label mb-2">Cantidad:</label>
                                    <div class="quantity-selector">
                                        <button type="button" class="quantity-btn quantity-minus">
                                            <svg class="icon-inline" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
                                            </svg>
                                        </button>
                                        <input type="number" name="quantity" value="1" min="1" max="99" class="quantity-input">
                                        <button type="button" class="quantity-btn quantity-plus">
                                            <svg class="icon-inline" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="mb-2">&nbsp;</label>
                                    <button type="button" class="btn btn-primary btn-block btn-lg" id="addToCartBtn"
                                        @php
                                            $stockInfo = $producto->getStockInfo();
                                            $puedeAgregar = $precioActual && ($stockInfo['hay_stock'] || !$stockInfo['stock_limitado']);
                                        @endphp
                                        {{ !$puedeAgregar ? 'disabled' : '' }}>
                                        Agregar al Carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Product Description --}}
                        @if($producto->descripcion)
                        <div class="product-short-description mb-4">
                            <h3 class="h5">Descripción</h3>
                            <p class="text-muted mb-0">{{ $producto->descripcion }}</p>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        {{-- Product Specifications --}}
        @if($producto->unidad_venta || $producto->unidad_empaque || $producto->extension || $producto->referencia)
        <div class="row mt-5">
            <div class="col-12">
                <div class="product-description">
                    <h2 class="h4 mb-3">Especificaciones</h2>
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

        {{-- Related Products --}}
        @if(isset($relacionados) && $relacionados->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="h3 mb-4">Productos relacionados</h2>
                <div class="js-related-products swiper-container related-products-swiper">
                    <div class="swiper-wrapper">
                        @foreach($relacionados as $relacionado)
                            @php
                                $precioRel = is_object($relacionado->precio_actual) ? $relacionado->precio_actual->precio : $relacionado->precio_actual;
                            @endphp
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image-wrapper">
                                        <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}">
                                            <img src="{{ $relacionado->url_imagen_principal ?? asset('images/no-image.png') }}"
                                                 alt="{{ $relacionado->nombre }}"
                                                 class="product-image">
                                        </a>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-name">
                                            <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}">
                                                {{ $relacionado->nombre }}
                                            </a>
                                        </h3>
                                        @if($precioRel)
                                        <div class="product-price">
                                            ${{ number_format($precioRel, 0, ',', '.') }}
                                        </div>
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
        // Inicializar variante seleccionada
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
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            thumbs: {
                swiper: productThumbsSwiper,
            },
        });

        // Related Products Slider
        const relatedProductsSwiper = new Swiper('.related-products-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            navigation: {
                nextEl: '.related-products-swiper .swiper-button-next',
                prevEl: '.related-products-swiper .swiper-button-prev',
            },
            breakpoints: {
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

        // Variant selection
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

                    // Update stock info
                    const stockDisponible = parseInt(this.dataset.stockDisponible);
                    const puedeAgregarSinStock = this.dataset.puedeAgregarSinStock === 'true';
                    updateStockDisplay(stockDisponible, puedeAgregarSinStock);

                    // Enable/disable add to cart button
                    const addToCartBtn = document.getElementById('addToCartBtn');
                    if (addToCartBtn) {
                        addToCartBtn.disabled = !puedeAgregarSinStock && stockDisponible <= 0;
                    }
                }
            });
        });

        // Add to cart
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const quantity = parseInt(quantityInput.value);

                if (tieneVariantes && !selectedVariantId) {
                    alert('Por favor selecciona una variante del producto');
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        addToCartBtn.disabled = false;
                        addToCartBtn.innerHTML = 'Agregar al Carrito';
                    } else {
                        // Update cart badge
                        const cartBadge = document.querySelector('.js-cart-widget-amount');
                        if (cartBadge && data.total_items) {
                            cartBadge.textContent = data.total_items;
                        }

                        addToCartBtn.innerHTML = 'Producto Agregado';
                        setTimeout(() => {
                            addToCartBtn.disabled = false;
                            addToCartBtn.innerHTML = 'Agregar al Carrito';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al agregar al carrito');
                    addToCartBtn.disabled = false;
                    addToCartBtn.innerHTML = 'Agregar al Carrito';
                });
            });
        }
    });

    function updateStockDisplay(stock, puedeAgregarSinStock) {
        const stockInfoEl = document.getElementById('stockInfo');
        if (!stockInfoEl) return;

        let stockHtml = '';
        if (puedeAgregarSinStock || stock > 10) {
            stockHtml = '<span class="badge badge-success">Disponible</span>';
        } else if (stock > 0) {
            stockHtml = '<span class="badge badge-warning">Solo ' + stock + ' disponibles</span>';
        } else {
            stockHtml = '<span class="badge badge-danger">Sin stock</span>';
        }

        stockInfoEl.innerHTML = stockHtml;
    }
</script>
@endpush
