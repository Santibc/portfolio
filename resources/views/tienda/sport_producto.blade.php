@extends('tienda.sport_layout')

@section('title', $producto->nombre . ' - ' . $empresa->nombre)
@section('description', $producto->descripcion ?? 'Producto disponible en ' . $empresa->nombre)

@push('styles')
<style>
    /* Product Page Styles */
    .product-page {
        padding: 40px 0;
    }

    /* Breadcrumbs */
    .breadcrumbs {
        font-size: 14px;
        margin-bottom: 20px;
        opacity: 0.7;
    }

    .breadcrumbs a {
        text-decoration: none;
        opacity: 0.7;
        transition: opacity 0.3s;
    }

    .breadcrumbs a:hover {
        opacity: 1;
    }

    .breadcrumbs .separator {
        margin: 0 10px;
    }

    .breadcrumbs .active {
        opacity: 1;
        font-weight: 700;
    }

    /* Product Layout */
    .row.justify-content-md-center {
        margin-top: 20px;
    }

    /* Product Gallery - Swiper Carousel */
    .product-image-column {
        max-width: 100%;
        margin-bottom: 30px;
    }

    .product-info-column {
        max-width: 100%;
        padding-top: 20px;
    }

    @media (min-width: 768px) {
        .product-image-column {
            max-width: 650px;
            width: 100%;
            position: sticky;
            top: 100px;
            align-self: flex-start;
        }

        .product-info-column {
            max-width: 550px;
            width: 100%;
            padding-top: 0;
            padding-left: 60px;
        }
    }

    @media (min-width: 992px) {
        .product-image-column {
            max-width: 700px;
        }
    }

    @media (min-width: 1200px) {
        .product-image-column {
            max-width: 800px;
        }
    }

    /* Swiper Product Styles */
    .js-swiper-product {
        width: 100%;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .product-slide-small {
        width: 100%;
        background: #fff;
    }

    .swiper-wrapper {
        display: flex;
        align-items: center;
    }

    .js-product-slide-link {
        position: relative;
        display: block;
        overflow: hidden;
    }

    .product-slider-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* Image Positioning */
    .img-absolute {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .img-absolute-centered {
        object-fit: contain;
        object-position: center;
    }

    /* Swiper Navigation Buttons */
    .swiper-buttons {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }

    .swiper-button-prev,
    .swiper-button-next {
        position: static;
        width: 50px;
        height: 50px;
        margin-top: 0;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .swiper-button-prev:after,
    .swiper-button-next:after {
        content: '';
    }

    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        background-color: #000;
        border-color: #000;
    }

    .swiper-button-prev:hover svg,
    .swiper-button-next:hover svg {
        fill: #fff;
    }

    .swiper-button-disabled {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* SVG Icons */
    .icon-inline {
        width: 24px;
        height: 24px;
        fill: currentColor;
        display: inline-block;
    }

    .icon-lg {
        width: 32px;
        height: 32px;
    }

    .icon-flip-horizontal {
        transform: scaleX(-1);
    }

    .svg-icon-text {
        color: #000;
    }

    /* Swiper Pagination */
    .swiper-pagination-fraction {
        font-family: var(--heading-font);
        font-size: 16px;
        font-weight: 700;
        color: #000;
    }

    /* Product Labels */
    .labels {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .label {
        display: inline-block;
        padding: 8px 16px;
        font-family: var(--heading-font);
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        text-align: center;
    }

    .label-big {
        padding: 10px 20px;
        font-size: 16px;
    }

    .label-accent {
        background-color: #000;
        color: #fff;
    }

    .label-offer-percentage,
    .label-offer-percentage-text {
        font-family: var(--heading-font);
        font-weight: 700;
    }

    /* Product Info */
    .product-info-column {
        background: #fff;
        position: relative;
        z-index: 10;
    }

    .product-title {
        font-size: var(--h3-huge);
        margin-bottom: 20px;
    }

    @media (min-width: 768px) {
        .product-title {
            font-size: var(--h2-huge);
        }
    }

    .price-container {
        margin-bottom: 30px;
    }

    .current-price {
        font-size: 24px;
        font-weight: 600;
        color: var(--accent-color);
    }

    .old-price {
        font-size: 18px;
        text-decoration: line-through;
        opacity: 0.5;
        margin-left: 10px;
    }

    .discount-percentage {
        display: inline-block;
        background-color: #000000;
        color: #FFFFFF;
        padding: 4px 12px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 700;
        margin-left: 10px;
    }

    .installments-info {
        font-size: 14px;
        opacity: 0.7;
        margin-top: 10px;
    }

    /* Product Form */
    .product-form {
        margin-bottom: 40px;
        padding: 30px;
        background-color: #f8f9fa;
        border-radius: var(--border-radius);
    }

    .form-section {
        margin-bottom: 25px;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .variant-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .variant-option {
        padding: 10px 20px;
        border: 2px solid rgba(0,0,0,0.2);
        border-radius: var(--border-radius);
        background: #fff;
        cursor: pointer;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.3s;
    }

    .variant-option:hover {
        border-color: var(--accent-color);
    }

    .variant-option.selected {
        background-color: var(--accent-color);
        color: #fff;
        border-color: var(--accent-color);
    }

    .variant-option.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        border: 2px solid rgba(0,0,0,0.2);
        border-radius: var(--border-radius);
        background-color: #fff;
        flex-shrink: 0;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: background-color 0.3s;
    }

    .quantity-btn:hover {
        background-color: rgba(0,0,0,0.05);
    }

    .quantity-input {
        width: 60px;
        height: 40px;
        border: none;
        text-align: center;
        font-weight: 700;
        font-size: 16px;
    }

    .btn-add-to-cart-big {
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 700;
        white-space: nowrap;
        flex: 1;
    }

    /* Product Description */
    .product-description {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid rgba(0,0,0,0.1);
    }

    .description-title {
        font-size: var(--h4);
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .description-content {
        line-height: 1.8;
        opacity: 0.8;
    }

    /* Related Products */
    .related-products {
        margin-top: 80px;
    }

    .related-title {
        font-size: var(--h3-huge);
        font-weight: 700;
        margin-bottom: 40px;
        text-transform: uppercase;
        text-align: center;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .row.justify-content-md-center {
            margin-top: 10px;
        }

        .product-image-column {
            max-width: 100%;
            margin-bottom: 20px;
        }

        .product-info-column {
            max-width: 100%;
            padding-left: 0;
        }

        .swiper-button-prev,
        .swiper-button-next {
            width: 40px;
            height: 40px;
        }

        .icon-lg {
            width: 24px;
            height: 24px;
        }

        .swiper-pagination-fraction {
            font-size: 14px;
        }

        .product-title {
            font-size: 32px;
        }

        .current-price {
            font-size: 32px;
        }

        .product-form {
            padding: 20px;
        }
    }

    /* Ensure proper stacking */
    .swiper-slide {
        background: #fff;
    }
</style>
@endpush

@section('content')
@php
    // Buscar descuentos activos para este producto
    $descuentoActivo = null;
    $textoDescuento = null;
    $precioActual = is_object($producto->precio_actual) ? $producto->precio_actual->precio : $producto->precio_actual;
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
@endphp

<div class="product-page">
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        <div class="breadcrumbs">
            <a href="{{ route('tienda.empresa', $empresa->slug) }}">Inicio</a>
            <span class="separator">|</span>
            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $producto->categoria_id]) }}">{{ $producto->categoria->nombre }}</a>
            <span class="separator">|</span>
            <span class="active">{{ strtoupper($producto->nombre) }}</span>
        </div>

        <!-- Product Container -->
        <div class="row justify-content-md-center">
            <!-- Product Gallery - Swiper Carousel (from sporttemplate) -->
            <div class="col-md-auto product-image-column">
                <div data-store="product-image-{{ $producto->id }}">
                    @if($producto->imagenes && $producto->imagenes->count() > 1)
                    <div class="swiper-buttons p-0 mr-2">
                        <div class="js-swiper-product-prev swiper-button-prev svg-icon-text">
                            <svg class="icon-inline icon-lg icon-flip-horizontal"><use xlink:href="#arrow-long"></use></svg>
                        </div>
                        <div class="js-swiper-product-next swiper-button-next svg-icon-text">
                            <svg class="icon-inline icon-lg"><use xlink:href="#arrow-long"></use></svg>
                        </div>
                    </div>
                    <div class="js-swiper-product-pagination swiper-pagination d-inline-block w-auto position-relative pt-3 text-left swiper-pagination-fraction">
                        <span class="swiper-pagination-current">1</span> / <span class="swiper-pagination-total">{{ $producto->imagenes->count() }}</span>
                    </div>
                    @endif

                    <div class="js-swiper-product swiper-container" data-product-images-amount="{{ $producto->imagenes->count() }}" style="visibility: hidden; height: 0;">

                        <!-- Discount Labels -->
                        @if($descuentoActivo)
                        <div class="labels js-labels-floating-group labels" data-store="product-item-labels">
                            <div class="js-offer-label-private label js-offer-label mb-2 label label-accent label-big" data-store="product-item-offer-label">
                                <span class="label-offer-percentage">
                                    -<span class="js-offer-percentage">{{ round(($montoDescuento / $precioActual) * 100) }}</span>%
                                </span>
                                <span class="label-offer-percentage-text"> OFF</span>
                            </div>
                        </div>
                        @endif

                        <div class="swiper-wrapper">
                            @if($producto->imagenes && $producto->imagenes->count() > 0)
                                @foreach($producto->imagenes as $imagen)
                                <div class="js-product-slide swiper-slide product-slide-small slider-slide" data-image="{{ $imagen->id }}" data-image-position="{{ $loop->index }}">
                                    <div class="js-product-slide-link d-block position-relative" style="padding-bottom: 106.15%;">
                                        <img
                                            src="{{ $imagen->url }}"
                                            class="js-product-slide-img product-slider-image img-absolute img-absolute-centered lazyloaded"
                                            width="100%"
                                            height="100%"
                                            alt="{{ $producto->nombre }} - {{ $loop->iteration }}"
                                        >
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="js-product-slide swiper-slide product-slide-small slider-slide" data-image="default" data-image-position="0">
                                    <div class="js-product-slide-link d-block position-relative" style="padding-bottom: 106.15%;">
                                        <img
                                            src="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}"
                                            class="js-product-slide-img product-slider-image img-absolute img-absolute-centered lazyloaded"
                                            width="100%"
                                            height="100%"
                                            alt="{{ $producto->nombre }}"
                                        >
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-auto product-info-column">
                <h1 class="product-title">{{ strtoupper($producto->nombre) }}</h1>

                <div class="price-container">
                    @if($precioActual)
                    <div>
                        @if($descuentoActivo)
                        <span class="current-price">${{ number_format($precioConDescuento, 0, ',', '.') }}</span>
                        <span class="old-price">${{ number_format($precioActual, 0, ',', '.') }}</span>
                        <span class="discount-percentage">{{ $textoDescuento }}</span>
                        @else
                        <span class="current-price">${{ number_format($precioActual, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="installments-info">
                        3 cuotas sin interés de ${{ number_format(($descuentoActivo ? $precioConDescuento : $precioActual) / 3, 0, ',', '.') }}
                    </div>
                    @else
                    <div>
                        <span class="current-price" style="font-size: 18px;">Consultar precio</span>
                    </div>
                    @endif
                </div>

                <!-- Stock Info -->
                <div class="form-section" style="margin-bottom: 20px;">
                    @if($producto->tiene_variantes)
                        <div id="stockInfo" style="padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                            <i class="bi bi-info-circle"></i>
                            <span>Selecciona una variante para ver disponibilidad</span>
                        </div>
                    @else
                        @php
                            $stockInfo = $producto->getStockInfo();
                        @endphp
                        <div style="padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                            @if(!$stockInfo['controlar_stock'] || $stockInfo['permitir_venta_sin_stock'])
                                <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                                <span style="font-weight: 600;">Disponible</span>
                            @elseif($stockInfo['stock_disponible'] > 10)
                                <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                                <span style="font-weight: 600;">Disponible</span>
                            @elseif($stockInfo['stock_disponible'] > 0)
                                <i class="bi bi-exclamation-circle-fill" style="color: #f59e0b;"></i>
                                <span style="font-weight: 600;">Limitado - Solo {{ $stockInfo['stock_disponible'] }} unidades</span>
                            @else
                                <i class="bi bi-x-circle-fill" style="color: #ef4444;"></i>
                                <span style="font-weight: 600;">Sin stock</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Product Form -->
                <form class="product-form" id="productForm">
                    <!-- Variantes -->
                    @if($producto->tiene_variantes && $producto->variantes->count() > 0)
                    <div class="form-section">
                        <label class="form-label">Variante: <strong id="selectedVariant">-</strong></label>
                        <div class="variant-options">
                            @foreach($producto->variantes as $variante)
                                @php
                                    $varianteStockInfo = $producto->getStockInfo($variante->id);
                                    $tieneStockDisponible = $varianteStockInfo['hay_stock'];
                                    $nombreVariante = $variante->nombre_variante;
                                @endphp
                                <button type="button"
                                        class="variant-option {{ !$tieneStockDisponible && $producto->controlar_stock && !$producto->permitir_venta_sin_stock ? 'disabled' : '' }}"
                                        data-variante-id="{{ $variante->id }}"
                                        data-value="{{ $nombreVariante }}"
                                        data-stock-disponible="{{ $varianteStockInfo['stock_disponible'] }}"
                                        data-puede-agregar="{{ $varianteStockInfo['puede_agregar_sin_stock'] ? 'true' : 'false' }}"
                                        onclick="selectVariant(this)"
                                        {{ (!$tieneStockDisponible && $producto->controlar_stock && !$producto->permitir_venta_sin_stock) ? 'disabled' : '' }}>
                                    {{ $nombreVariante ?: 'Sin especificar' }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Cantidad y Agregar al Carrito -->
                    <div class="form-section">
                        <label class="form-label">Cantidad</label>
                        <div class="quantity-selector">
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" onclick="decreaseQuantity()">−</button>
                                <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="99" readonly>
                                <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                            </div>
                            <button type="button"
                                    class="btn btn-add-to-cart-big"
                                    id="addToCartBtn"
                                    onclick="agregarAlCarrito()"
                                    @php $stockInfo = $producto->getStockInfo(); @endphp
                                    {{ (!$precioActual || (!$stockInfo['hay_stock'] && $stockInfo['stock_limitado'])) ? 'disabled' : '' }}>
                                AGREGAR AL CARRITO
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Benefits List -->
                @if($producto->info_envio || $producto->dias_devolucion || $producto->garantia)
                <div class="form-section" style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-top: 20px;">
                    @if($producto->info_envio)
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <i class="bi bi-truck" style="font-size: 24px; margin-right: 15px;"></i>
                        <span>{{ $producto->info_envio }}</span>
                    </div>
                    @endif

                    @if($producto->dias_devolucion)
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <i class="bi bi-arrow-clockwise" style="font-size: 24px; margin-right: 15px;"></i>
                        <span>{{ $producto->dias_devolucion }} días para devolución</span>
                    </div>
                    @endif

                    @if($producto->garantia)
                    <div style="display: flex; align-items: center;">
                        <i class="bi bi-shield-check" style="font-size: 24px; margin-right: 15px;"></i>
                        <span>{{ $producto->garantia }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Product Description -->
                <div class="product-description">
                    <h2 class="description-title">Descripción</h2>
                    <div class="description-content">
                        <p>{{ $producto->descripcion ?: 'No hay descripción disponible para este producto.' }}</p>

                        @if($producto->referencia || $producto->unidad_venta || $producto->unidad_empaque)
                        <h4 style="margin-top: 20px; margin-bottom: 10px; font-weight: 700;">Información Técnica:</h4>
                        <ul>
                            @if($producto->referencia)
                            <li><strong>Referencia:</strong> {{ $producto->referencia }}</li>
                            @endif
                            @if($producto->unidad_venta)
                            <li><strong>Unidad de Venta:</strong> {{ $producto->unidad_venta }}</li>
                            @endif
                            @if($producto->unidad_empaque)
                            <li><strong>Unidad de Empaque:</strong> {{ $producto->unidad_empaque }}</li>
                            @endif
                            @if($producto->extension)
                            <li><strong>Extensión:</strong> {{ $producto->extension }}</li>
                            @endif
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if(isset($relacionados) && $relacionados->count() > 0)
        <div class="related-products">
            <h2 class="related-title">PRODUCTOS RELACIONADOS</h2>
            <div class="related-grid">
                @foreach($relacionados as $relacionado)
                @php
                    // Buscar descuentos activos para producto relacionado
                    $descuentoRel = null;
                    $textoDescuentoRel = null;
                    $precioActualRel = is_object($relacionado->precio_actual) ? $relacionado->precio_actual->precio : $relacionado->precio_actual;
                    $precioConDescuentoRel = $precioActualRel;

                    if (isset($descuentosActivos) && $precioActualRel) {
                        foreach ($descuentosActivos as $desc) {
                            $aplica = false;
                            if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                                $aplica = true;
                            } elseif ($desc->aplica_a === 'producto' && in_array($relacionado->id, $desc->productos_aplicables ?? [])) {
                                $aplica = true;
                            } elseif ($desc->aplica_a === 'categoria' && in_array($relacionado->categoria_id, $desc->categorias_aplicables ?? [])) {
                                $aplica = true;
                            }

                            if ($aplica) {
                                $descuentoRel = $desc;
                                if ($desc->tipo === 'porcentaje') {
                                    $montoDescRel = ($precioActualRel * $desc->valor) / 100;
                                    $textoDescuentoRel = round($desc->valor) . '% OFF';
                                } else {
                                    $montoDescRel = $desc->valor;
                                    $textoDescuentoRel = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                                }
                                $precioConDescuentoRel = $precioActualRel - $montoDescRel;
                                break;
                            }
                        }
                    }
                    $stockInfoRel = $relacionado->getStockInfo();
                @endphp
                <div class="product-card">
                    <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}" style="text-decoration: none; color: inherit;">
                        <div class="product-image-container">
                            <img src="{{ $relacionado->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $relacionado->nombre }}" class="product-image">
                            @if($descuentoRel || ($stockInfoRel['controlar_stock'] && !$stockInfoRel['permitir_venta_sin_stock']))
                            <div class="product-labels">
                                @if($descuentoRel)
                                    <span class="product-label">{{ $textoDescuentoRel }}</span>
                                @elseif($stockInfoRel['stock_disponible'] <= 5 && $stockInfoRel['stock_disponible'] > 0)
                                    <span class="product-label">¡ÚLTIMAS!</span>
                                @elseif($stockInfoRel['stock_disponible'] == 0)
                                    <span class="product-label">SIN STOCK</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="product-info">
                            <div class="product-content">
                                <h3 class="product-name">{{ strtoupper($relacionado->nombre) }}</h3>
                                <div class="product-prices">
                                    @if($precioActualRel)
                                        @if($descuentoRel)
                                            <span class="product-price">${{ number_format($precioConDescuentoRel, 0, ',', '.') }}</span>
                                            <span class="product-price-old">${{ number_format($precioActualRel, 0, ',', '.') }}</span>
                                        @else
                                            <span class="product-price">${{ number_format($precioActualRel, 0, ',', '.') }}</span>
                                        @endif
                                    @else
                                        <span class="product-price" style="font-size: 16px;">Consultar</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn btn-add-to-cart" onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarritoRelacionado({{ $relacionado->id }}, this);">AGREGAR AL CARRITO</button>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    // === Variantes disponibles del producto (JSON) ===
    const variantes = @json($producto->variantes);
    const tieneVariantes = {{ $producto->tiene_variantes ? 'true' : 'false' }};
    let selectedVariant = null;

    // === Selección de variantes ===
    function selectVariant(button) {
        // Remover selección previa
        document.querySelectorAll('.variant-option').forEach(v => v.classList.remove('selected'));

        // Agregar clase selected al botón clickeado
        button.classList.add('selected');

        // Obtener datos de la variante
        const varianteId = button.getAttribute('data-variante-id');
        const varianteNombre = button.getAttribute('data-value');
        const stockDisponible = parseInt(button.getAttribute('data-stock-disponible'));
        const puedeAgregar = button.getAttribute('data-puede-agregar') === 'true';

        // Actualizar texto del label
        document.getElementById('selectedVariant').textContent = varianteNombre;

        // Encontrar la variante en el array
        selectedVariant = variantes.find(v => v.id == varianteId);

        if (selectedVariant) {
            // Actualizar información de stock
            updateStockInfo(varianteId);

            // Habilitar/deshabilitar botón de agregar
            const addBtn = document.getElementById('addToCartBtn');
            if (addBtn) {
                addBtn.disabled = !(puedeAgregar || stockDisponible > 0);
            }

            // Actualizar límite de cantidad
            const quantityInput = document.getElementById('quantity');
            if (quantityInput && stockDisponible > 0) {
                quantityInput.max = stockDisponible;
                if (parseInt(quantityInput.value) > stockDisponible) {
                    quantityInput.value = stockDisponible;
                }
            }
        }
    }

    // === Actualizar información de stock vía AJAX ===
    function updateStockInfo(varianteId) {
        fetch("{{ route('tienda.stock.info', $empresa->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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
                stockHtml = `
                    <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                    <span style="font-weight: 600;">Disponible</span>
                `;
            } else if (stock > 10) {
                stockHtml = `
                    <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                    <span style="font-weight: 600;">Disponible</span>
                `;
            } else if (stock > 0) {
                stockHtml = `
                    <i class="bi bi-exclamation-circle-fill" style="color: #f59e0b;"></i>
                    <span style="font-weight: 600;">Limitado - Solo ${stock} unidades</span>
                `;
            } else {
                stockHtml = `
                    <i class="bi bi-x-circle-fill" style="color: #ef4444;"></i>
                    <span style="font-weight: 600;">Sin stock</span>
                `;
            }

            document.getElementById('stockInfo').innerHTML = stockHtml;
        })
        .catch(error => {
            console.error('Error al obtener stock:', error);
        });
    }

    // === Controles de cantidad ===
    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }

    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value);
        const maxValue = input.hasAttribute('max') ? parseInt(input.max) : 999;

        if (currentValue < maxValue) {
            input.value = currentValue + 1;
        } else if (maxValue < 999) {
            alert(`Solo hay ${maxValue} unidades disponibles`);
        }
    }

    // === Swiper Product Carousel Initialization (from sporttemplate) ===
    var productSwiper = null;
    var has_multiple_slides = false;
    var product_images_amount = document.querySelector(".js-swiper-product")?.getAttribute("data-product-images-amount");

    if(product_images_amount > 1) {
        has_multiple_slides = true;
    }

    function productSliderNav() {
        var width = window.innerWidth;

        productSwiper = new Swiper('.js-swiper-product', {
            lazy: true,
            slidesPerView: 1,
            threshold: 5,
            centerInsufficientSlides: true,
            watchOverflow: true,
            spaceBetween: 0,
            pagination: {
                el: '.js-swiper-product-pagination',
                type: 'fraction',
            },
            navigation: {
                nextEl: '.js-swiper-product-next',
                prevEl: '.js-swiper-product-prev',
            },
            on: {
                init: function () {
                    document.querySelector(".js-swiper-product").style.visibility = "visible";
                    document.querySelector(".js-swiper-product").style.height = "auto";
                },
            },
        });

        // Variant change support
        if(has_multiple_slides && tieneVariantes){
            // When variant changes, update the active slide
            // This is a placeholder for variant image switching
        }
    }

    // Initialize product slider after page loads
    if (document.querySelector('.js-swiper-product')) {
        productSliderNav();
    }

    // === Agregar al carrito (producto principal) ===
    function agregarAlCarrito() {
        const quantity = parseInt(document.getElementById('quantity').value);

        // Validar selección de variante si es necesario
        if (tieneVariantes && !selectedVariant) {
            alert('Por favor selecciona una variante del producto');
            return;
        }

        const btn = document.getElementById('addToCartBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span style="font-size: 14px;">...</span>';

        const data = {
            producto_id: {{ $producto->id }},
            cantidad: quantity
        };

        if (selectedVariant) {
            data.variante_id = selectedVariant.id;
        }

        fetch("{{ route('tienda.carrito.agregar', $empresa->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = '✓ AGREGADO AL CARRITO';

            // Actualizar badge del carrito usando función global
            if (typeof window.updateCartBadge === 'function' && data.total_items) {
                window.updateCartBadge(data.total_items);
            }

            // Restaurar botón después de 2 segundos
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = 'ERROR';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 2000);
        });
    }

    // === Agregar al carrito (productos relacionados) ===
    function agregarAlCarritoRelacionado(productoId, btn) {
        if (!btn) return;

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span style="font-size: 12px;">...</span>';

        fetch("{{ route('tienda.carrito.agregar', $empresa->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                producto_id: productoId,
                cantidad: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = '✓ AGREGADO';

            // Actualizar badge del carrito usando función global
            if (typeof window.updateCartBadge === 'function' && data.total_items) {
                window.updateCartBadge(data.total_items);
            }

            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = 'ERROR';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 2000);
        });
    }

    // === Inicialización al cargar la página ===
    document.addEventListener('DOMContentLoaded', function() {
        // Si no tiene variantes y tiene un form, agregar listener al form
        const productForm = document.getElementById('productForm');
        if (productForm) {
            productForm.addEventListener('submit', function(e) {
                e.preventDefault();
                agregarAlCarrito();
            });
        }

        // Si tiene variantes, pre-seleccionar la primera disponible
        if (tieneVariantes) {
            const firstAvailable = document.querySelector('.variant-option:not(.disabled):not([disabled])');
            if (firstAvailable) {
                selectVariant(firstAvailable);
            }
        }
    });
</script>
@endpush
