@extends('tienda.recife_layout')

@section('content')
    @if ($empresa->carruselImagenesActivas && $empresa->carruselImagenesActivas->count() > 0)
        <section class="js-main-slider-section section-slider-home" data-store="home-slider">
            <div class="slider-container">
                <div class="slider-wrapper">
                    <div class="slider-track">
                        @foreach ($empresa->carruselImagenesActivas as $imagen)
                            <div class="slide">
                                @if ($imagen->link)
                                    <a href="{{ $imagen->link }}">
                                        <img src="{{ $imagen->imagen_url }}"
                                            alt="{{ $imagen->titulo ?? 'Slide ' . $loop->iteration }}" loading="lazy">
                                    </a>
                                @else
                                    <img src="{{ $imagen->imagen_url }}"
                                        alt="{{ $imagen->titulo ?? 'Slide ' . $loop->iteration }}" loading="lazy">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Controles de navegación -->
                <button class="slider-btn slider-btn-prev" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="slider-btn slider-btn-next" aria-label="Siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <!-- Indicadores -->
                <div class="slider-indicators"></div>
            </div>

            <style>
                /* Contenedor principal */
                .slider-container {
                    position: relative;
                    width: 100%;
                    max-width: 1920px;
                    margin: 0 auto;
                    overflow: hidden;
                    background: #000;
                }

                /* Wrapper con aspect ratio más bajo para carrusel más pequeño */
                .slider-wrapper {
                    position: relative;
                    width: 100%;
                    aspect-ratio: 21/9;
                    overflow: hidden;
                }

                /* Track que contiene todos los slides */
                .slider-track {
                    display: flex;
                    width: 100%;
                    height: 100%;
                    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                    will-change: transform;
                }

                /* Cada slide */
                .slide {
                    flex: 0 0 100%;
                    width: 100%;
                    height: 100%;
                    position: relative;
                }

                .slide a {
                    display: block;
                    width: 100%;
                    height: 100%;
                }

                .slide img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                    user-select: none;
                    -webkit-user-drag: none;
                }

                /* Botones de navegación */
                .slider-btn {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.95);
                    border: none;
                    cursor: pointer;
                    z-index: 10;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    outline: none;
                }

                .slider-btn:hover {
                    background: #fff;
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
                    transform: translateY(-50%) scale(1.1);
                }

                .slider-btn:active {
                    transform: translateY(-50%) scale(0.95);
                }

                .slider-btn svg {
                    width: 24px;
                    height: 24px;
                    color: #333;
                }

                .slider-btn-prev {
                    left: 24px;
                }

                .slider-btn-next {
                    right: 24px;
                }

                /* Indicadores */
                .slider-indicators {
                    position: absolute;
                    bottom: 24px;
                    left: 50%;
                    transform: translateX(-50%);
                    display: flex;
                    gap: 10px;
                    z-index: 10;
                }

                .slider-indicator {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    border: 2px solid transparent;
                    cursor: pointer;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .slider-indicator:hover {
                    background: rgba(255, 255, 255, 0.8);
                    transform: scale(1.2);
                }

                .slider-indicator.active {
                    background: #fff;
                    width: 32px;
                    border-radius: 5px;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    .slider-wrapper {
                        aspect-ratio: 16/9;
                    }

                    .slider-btn {
                        width: 40px;
                        height: 40px;
                    }

                    .slider-btn svg {
                        width: 20px;
                        height: 20px;
                    }

                    .slider-btn-prev {
                        left: 12px;
                    }

                    .slider-btn-next {
                        right: 12px;
                    }

                    .slider-indicators {
                        bottom: 16px;
                        gap: 8px;
                    }

                    .slider-indicator {
                        width: 8px;
                        height: 8px;
                    }

                    .slider-indicator.active {
                        width: 24px;
                    }
                }

                @media (max-width: 480px) {
                    .slider-wrapper {
                        aspect-ratio: 4/3;
                    }
                }
            </style>

            <script>
                // Clase Slider moderna
                class ModernSlider {
                    constructor(container) {
                        this.container = container;
                        this.track = container.querySelector('.slider-track');
                        this.slides = container.querySelectorAll('.slide');
                        this.prevBtn = container.querySelector('.slider-btn-prev');
                        this.nextBtn = container.querySelector('.slider-btn-next');
                        this.indicatorsContainer = container.querySelector('.slider-indicators');

                        this.currentIndex = 0;
                        this.isAnimating = false;
                        this.autoplayInterval = null;
                        this.autoplayDelay = 5000;

                        // Touch/swipe support
                        this.touchStartX = 0;
                        this.touchEndX = 0;
                        this.minSwipeDistance = 50;

                        this.init();
                    }

                    init() {
                        this.createIndicators();
                        this.attachEvents();
                        this.startAutoplay();
                        this.updateSlider();
                    }

                    createIndicators() {
                        this.slides.forEach((_, index) => {
                            const indicator = document.createElement('div');
                            indicator.className = 'slider-indicator';
                            if (index === 0) indicator.classList.add('active');
                            indicator.addEventListener('click', () => this.goToSlide(index));
                            this.indicatorsContainer.appendChild(indicator);
                        });
                        this.indicators = this.indicatorsContainer.querySelectorAll('.slider-indicator');
                    }

                    attachEvents() {
                        this.prevBtn.addEventListener('click', () => this.prev());
                        this.nextBtn.addEventListener('click', () => this.next());

                        // Touch events para swipe
                        this.track.addEventListener('touchstart', (e) => this.handleTouchStart(e), {
                            passive: true
                        });
                        this.track.addEventListener('touchend', (e) => this.handleTouchEnd(e), {
                            passive: true
                        });

                        // Pausar autoplay al hacer hover
                        this.container.addEventListener('mouseenter', () => this.stopAutoplay());
                        this.container.addEventListener('mouseleave', () => this.startAutoplay());

                        // Keyboard navigation
                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'ArrowLeft') this.prev();
                            if (e.key === 'ArrowRight') this.next();
                        });
                    }

                    handleTouchStart(e) {
                        this.touchStartX = e.changedTouches[0].screenX;
                    }

                    handleTouchEnd(e) {
                        this.touchEndX = e.changedTouches[0].screenX;
                        this.handleSwipe();
                    }

                    handleSwipe() {
                        const diff = this.touchStartX - this.touchEndX;
                        if (Math.abs(diff) > this.minSwipeDistance) {
                            if (diff > 0) {
                                this.next();
                            } else {
                                this.prev();
                            }
                        }
                    }

                    prev() {
                        if (this.isAnimating) return;
                        this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
                        this.updateSlider();
                        this.resetAutoplay();
                    }

                    next() {
                        if (this.isAnimating) return;
                        this.currentIndex = (this.currentIndex + 1) % this.slides.length;
                        this.updateSlider();
                        this.resetAutoplay();
                    }

                    goToSlide(index) {
                        if (this.isAnimating || index === this.currentIndex) return;
                        this.currentIndex = index;
                        this.updateSlider();
                        this.resetAutoplay();
                    }

                    updateSlider() {
                        this.isAnimating = true;

                        // Mover el track
                        this.track.style.transform = `translateX(-${this.currentIndex * 100}%)`;

                        // Actualizar indicadores
                        this.indicators.forEach((indicator, index) => {
                            indicator.classList.toggle('active', index === this.currentIndex);
                        });

                        // Reset animation lock
                        setTimeout(() => {
                            this.isAnimating = false;
                        }, 600);
                    }

                    startAutoplay() {
                        this.stopAutoplay();
                        this.autoplayInterval = setInterval(() => {
                            this.next();
                        }, this.autoplayDelay);
                    }

                    stopAutoplay() {
                        if (this.autoplayInterval) {
                            clearInterval(this.autoplayInterval);
                            this.autoplayInterval = null;
                        }
                    }

                    resetAutoplay() {
                        this.stopAutoplay();
                        this.startAutoplay();
                    }

                    destroy() {
                        this.stopAutoplay();
                        this.prevBtn.removeEventListener('click', () => this.prev());
                        this.nextBtn.removeEventListener('click', () => this.next());
                    }
                }

                // Inicializar cuando el DOM esté listo
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initSlider);
                } else {
                    initSlider();
                }

                function initSlider() {
                    const sliderContainer = document.querySelector('.slider-container');
                    if (sliderContainer && !window.modernSlider) {
                        window.modernSlider = new ModernSlider(sliderContainer);
                    }
                }
            </script>
        </section>
    @endif

    <!-- Sección de Categorías NUEVA - Compacta -->
    @if (isset($categorias) && $categorias->count() > 0)
        <section class="recife-categories-section" style="margin: 30px 0; padding: 20px 0;">
            <div class="container">
                <h2 class="text-center mb-4" style="font-size: 1.5rem; font-weight: 700;">CATEGORÍAS</h2>

                <div class="recife-categories-grid">
                    @foreach ($categorias as $categoria)
                        <div class="recife-category-card">
                            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}"
                                class="recife-category-link">
                                <div class="recife-category-image">
                                    @if ($categoria->imagen)
                                        <img src="{{ asset($categoria->imagen) }}" alt="{{ $categoria->nombre }}"
                                            loading="lazy">
                                    @else
                                        <img src="https://via.placeholder.com/400x120/f0f0f0/666666?text={{ urlencode($categoria->nombre) }}"
                                            alt="{{ $categoria->nombre }}" loading="lazy">
                                    @endif
                                    <div class="recife-category-overlay">
                                        <span class="recife-category-name">{{ $categoria->nombre }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <style>
                .recife-categories-section {
                    background: #fafafa;
                }

                .recife-categories-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                    gap: 15px;
                    padding: 0;
                    margin: 0;
                }

                .recife-category-card {
                    position: relative;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                .recife-category-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }

                .recife-category-link {
                    display: block;
                    text-decoration: none;
                    color: inherit;
                }

                .recife-category-image {
                    position: relative;
                    width: 100%;
                    height: 100px;
                    overflow: hidden;
                    background: #e0e0e0;
                }

                .recife-category-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    object-position: center;
                    display: block;
                    transition: transform 0.3s ease;
                }

                .recife-category-card:hover .recife-category-image img {
                    transform: scale(1.05);
                }

                .recife-category-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
                    display: flex;
                    align-items: flex-end;
                    justify-content: center;
                    padding: 12px;
                }

                .recife-category-name {
                    color: white;
                    font-size: 0.95rem;
                    font-weight: 600;
                    text-align: center;
                    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
                    line-height: 1.2;
                }

                @media (max-width: 768px) {
                    .recife-categories-grid {
                        grid-template-columns: repeat(2, 1fr);
                        gap: 10px;
                    }

                    .recife-category-image {
                        height: 80px;
                    }

                    .recife-category-name {
                        font-size: 0.85rem;
                    }
                }

                @media (max-width: 480px) {
                    .recife-category-image {
                        height: 70px;
                    }

                    .recife-category-name {
                        font-size: 0.8rem;
                    }
                }
            </style>
        </section>
    @endif

    <!-- Sección de Novedades -->
    @if (isset($productosNuevos) && $productosNuevos->count() > 0)
        <section class="js-section-products-new section-home section-featured-home section-new-products-home"
            data-store="home-products-new">
            <div class="js-products-new-container container">
                <div class="row">
                    <div class="js-products-new-col col-12">
                        <h2 class="js-products-new-title section-title h3 mb-4 text-center">NOVEDADES</h2>

                        <div class="js-products-new-grid row row-grid" data-desktop-columns="5" data-mobile-columns="2"
                            data-desktop-format="grid" data-mobile-format="grid" data-mobile-slider-columns="2.25">
                            @foreach ($productosNuevos as $producto)
                                @php
                                    // Buscar descuentos activos para este producto
                                    $descuentoProducto = null;
                                    $textoDescuentoProducto = null;
                                    $precioNumerico = is_object($producto->precio_actual)
                                        ? $producto->precio_actual->precio
                                        : $producto->precio_actual;
                                    $precioConDescuentoProducto = $precioNumerico;
                                    $montoDescuentoProducto = 0;

                                    if (isset($descuentosActivos) && $precioNumerico) {
                                        foreach ($descuentosActivos as $desc) {
                                            $aplica = false;

                                            if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                                                $aplica = true;
                                            } elseif (
                                                $desc->aplica_a === 'producto' &&
                                                in_array($producto->id, $desc->productos_aplicables ?? [])
                                            ) {
                                                $aplica = true;
                                            } elseif (
                                                $desc->aplica_a === 'categoria' &&
                                                in_array($producto->categoria_id, $desc->categorias_aplicables ?? [])
                                            ) {
                                                $aplica = true;
                                            }

                                            if ($aplica) {
                                                $descuentoProducto = $desc;
                                                if ($desc->tipo === 'porcentaje') {
                                                    $montoDescuentoProducto = ($precioNumerico * $desc->valor) / 100;
                                                    $textoDescuentoProducto = round($desc->valor) . '% OFF';
                                                } else {
                                                    $montoDescuentoProducto = $desc->valor;
                                                    $textoDescuentoProducto =
                                                        '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                                                }
                                                $precioConDescuentoProducto = $precioNumerico - $montoDescuentoProducto;
                                                break;
                                            }
                                        }
                                    }
                                    $stockInfo = $producto->getStockInfo();
                                @endphp
                                <div class="js-item-product col-6 col-md-2-4 item-product col-grid" data-product-type="list"
                                    data-product-id="{{ $producto->id }}" data-store="product-item-{{ $producto->id }}">
                                    <div class="item">
                                        <div class="js-product-container position-relative">

                                            <!-- Imagen del producto -->
                                            <div class="js-product-item-image-container-private product-item-image-container item-image"
                                                data-store="product-item-image-{{ $producto->id }}">
                                                <div style="padding-bottom: 100%;"
                                                    class="js-item-image-padding position-relative d-block">
                                                    <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                        title="{{ $producto->nombre }}"
                                                        aria-label="{{ $producto->nombre }}"
                                                        class="js-product-item-image-link-private">

                                                        <img src="{{ $producto->url_imagen_principal ?? asset('images/placeholder.jpg') }}"
                                                            alt="{{ $producto->nombre }}"
                                                            class="js-product-item-image-private product-item-image js-item-image lazyautosizes img-absolute img-absolute-centered fade-in item-image-featured lazyloaded"
                                                            loading="lazy">

                                                        <div class="placeholder-fade"></div>
                                                    </a>

                                                    <!-- Labels de stock/envío y descuentos -->
                                                    <div class="labels js-labels-floating-group labels-absolute">
                                                        @if ($descuentoProducto)
                                                            <div class="label js-discount-label label-accent">
                                                                {{ $textoDescuentoProducto }}
                                                            </div>
                                                        @elseif($stockInfo['controlar_stock'] && !$stockInfo['hay_stock'])
                                                            <div class="label js-stock-label label-default">
                                                                Sin stock
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Información del producto -->
                                            <div class="item-description pt-3"
                                                data-store="product-item-info-{{ $producto->id }}">
                                                <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                    title="{{ $producto->nombre }}" aria-label="{{ $producto->nombre }}"
                                                    class="item-link">

                                                    <div class="js-item-name item-name mb-2 font-small opacity-80"
                                                        data-store="product-item-name-{{ $producto->id }}">
                                                        {{ $producto->nombre }}
                                                    </div>

                                                    <div class="item-price-container"
                                                        data-store="product-item-price-{{ $producto->id }}">
                                                        <div class="d-block mb-1 mr-1">
                                                            @if ($precioNumerico)
                                                                @if ($descuentoProducto)
                                                                    <span
                                                                        class="js-price-display item-price text-decoration-line-through opacity-50 mr-2"
                                                                        data-product-price="{{ $precioNumerico * 100 }}">
                                                                        ${{ number_format($precioNumerico, 0, ',', '.') }}
                                                                    </span>
                                                                    <span
                                                                        class="js-price-display item-price font-weight-bold text-accent"
                                                                        data-product-price="{{ $precioConDescuentoProducto * 100 }}">
                                                                        ${{ number_format($precioConDescuentoProducto, 0, ',', '.') }}
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="js-price-display item-price font-weight-bold"
                                                                        data-product-price="{{ $precioNumerico * 100 }}">
                                                                        ${{ number_format($precioNumerico, 0, ',', '.') }}
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">Precio no disponible</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                                <button type="button" class="btn btn-primary btn-add-to-cart-card w-100 mt-2"
                                                    onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarrito({{ $producto->id }}, this);"
                                                    {{ (!$stockInfo['hay_stock'] && $stockInfo['controlar_stock']) ? 'disabled' : '' }}>
                                                    {{ (!$stockInfo['hay_stock'] && $stockInfo['controlar_stock']) ? 'Sin stock' : 'Agregar al carrito' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Banner Página creada en BetoGether -->
    <section class="section-home section-welcome section-home-color section-welcome-home-colors section-welcome-animated"
        data-store="home-welcome-message">
        <div class="js-welcome-animated welcome-animated"
            style="animation: 118.175s linear 0s infinite normal none running marquee;">
            <a href="/">
                <span class="js-welcome-text-container">
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                    <span class="welcome-text h3 mr-4">
                        Página creada en <strong>BetoGether</strong>
                    </span>
                </span>
            </a>
        </div>
    </section>

    <!-- Sección de Productos -->
    <section class="js-section-products-featured section-home section-featured-home section-featured-products-home"
        data-store="home-products-featured">
        <div class="js-products-featured-container container">
            <div class="row">
                <div class="js-products-featured-col col-12">
                    <h2 class="js-products-featured-title section-title h3 mb-4 text-center">PRODUCTOS</h2>

                    <div class="js-products-featured-grid row row-grid" data-desktop-columns="6" data-mobile-columns="2"
                        data-desktop-format="grid" data-mobile-format="grid" data-mobile-slider-columns="2.25">
                        @forelse($productos as $producto)
                            @php
                                // Buscar descuentos activos para este producto
                                $descuentoProducto = null;
                                $textoDescuentoProducto = null;
                                $precioNumerico = is_object($producto->precio_actual)
                                    ? $producto->precio_actual->precio
                                    : $producto->precio_actual;
                                $precioConDescuentoProducto = $precioNumerico;
                                $montoDescuentoProducto = 0;

                                if (isset($descuentosActivos) && $precioNumerico) {
                                    foreach ($descuentosActivos as $desc) {
                                        $aplica = false;

                                        if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                                            $aplica = true;
                                        } elseif (
                                            $desc->aplica_a === 'producto' &&
                                            in_array($producto->id, $desc->productos_aplicables ?? [])
                                        ) {
                                            $aplica = true;
                                        } elseif (
                                            $desc->aplica_a === 'categoria' &&
                                            in_array($producto->categoria_id, $desc->categorias_aplicables ?? [])
                                        ) {
                                            $aplica = true;
                                        }

                                        if ($aplica) {
                                            $descuentoProducto = $desc;
                                            if ($desc->tipo === 'porcentaje') {
                                                $montoDescuentoProducto = ($precioNumerico * $desc->valor) / 100;
                                                $textoDescuentoProducto = round($desc->valor) . '% OFF';
                                            } else {
                                                $montoDescuentoProducto = $desc->valor;
                                                $textoDescuentoProducto =
                                                    '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                                            }
                                            $precioConDescuentoProducto = $precioNumerico - $montoDescuentoProducto;
                                            break;
                                        }
                                    }
                                }
                                $stockInfo = $producto->getStockInfo();
                            @endphp
                            <div class="js-item-product col-6 col-md-2 item-product col-grid" data-product-type="list"
                                data-product-id="{{ $producto->id }}" data-store="product-item-{{ $producto->id }}">
                                <div class="item">
                                    <div class="js-product-container position-relative">

                                        <!-- Imagen del producto -->
                                        <div class="js-product-item-image-container-private product-item-image-container item-image"
                                            data-store="product-item-image-{{ $producto->id }}">
                                            <div style="padding-bottom: 100%;"
                                                class="js-item-image-padding position-relative d-block">
                                                <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                    title="{{ $producto->nombre }}" aria-label="{{ $producto->nombre }}"
                                                    class="js-product-item-image-link-private">

                                                    <img src="{{ $producto->url_imagen_principal ?? asset('images/placeholder.jpg') }}"
                                                        alt="{{ $producto->nombre }}"
                                                        class="js-product-item-image-private product-item-image js-item-image lazyautosizes img-absolute img-absolute-centered fade-in item-image-featured lazyloaded"
                                                        loading="lazy">

                                                    <div class="placeholder-fade"></div>
                                                </a>

                                                <!-- Labels de stock/envío y descuentos -->
                                                <div class="labels js-labels-floating-group labels-absolute">
                                                    @if ($descuentoProducto)
                                                        <div class="label js-discount-label label-accent">
                                                            {{ $textoDescuentoProducto }}
                                                        </div>
                                                    @elseif($stockInfo['controlar_stock'] && !$stockInfo['hay_stock'])
                                                        <div class="label js-stock-label label-default">
                                                            Sin stock
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Información del producto -->
                                        <div class="item-description pt-3"
                                            data-store="product-item-info-{{ $producto->id }}">
                                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                title="{{ $producto->nombre }}" aria-label="{{ $producto->nombre }}"
                                                class="item-link">

                                                <div class="js-item-name item-name mb-2 font-small opacity-80"
                                                    data-store="product-item-name-{{ $producto->id }}">
                                                    {{ $producto->nombre }}
                                                </div>

                                                <div class="item-price-container"
                                                    data-store="product-item-price-{{ $producto->id }}">
                                                    <div class="d-block mb-1 mr-1">
                                                        @if ($precioNumerico)
                                                            @if ($descuentoProducto)
                                                                <span
                                                                    class="js-price-display item-price text-decoration-line-through opacity-50 mr-2"
                                                                    data-product-price="{{ $precioNumerico * 100 }}">
                                                                    ${{ number_format($precioNumerico, 0, ',', '.') }}
                                                                </span>
                                                                <span
                                                                    class="js-price-display item-price font-weight-bold text-accent"
                                                                    data-product-price="{{ $precioConDescuentoProducto * 100 }}">
                                                                    ${{ number_format($precioConDescuentoProducto, 0, ',', '.') }}
                                                                </span>
                                                            @else
                                                                <span class="js-price-display item-price font-weight-bold"
                                                                    data-product-price="{{ $precioNumerico * 100 }}">
                                                                    ${{ number_format($precioNumerico, 0, ',', '.') }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Precio no disponible</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                            <button type="button" class="btn btn-primary btn-add-to-cart-card w-100 mt-2"
                                                onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarrito({{ $producto->id }}, this);"
                                                {{ (!$stockInfo['hay_stock'] && $stockInfo['controlar_stock']) ? 'disabled' : '' }}>
                                                {{ (!$stockInfo['hay_stock'] && $stockInfo['controlar_stock']) ? 'Sin stock' : 'Agregar al carrito' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No hay productos disponibles en este momento.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginación -->
                    @if (isset($productos) && method_exists($productos, 'links'))
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $productos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>


    <!-- Sección de Ofertas -->
    @if (isset($productosConDescuento) && $productosConDescuento->count() > 0)
        <section class="js-section-products-offers section-home section-featured-home section-offers-products-home"
            data-store="home-products-offers">
            <div class="js-products-offers-container container">
                <div class="row">
                    <div class="js-products-offers-col col-12">
                        <h2 class="js-products-offers-title section-title h3 mb-4 text-center">OFERTAS</h2>

                        <div class="js-products-offers-grid row row-grid" data-desktop-columns="6"
                            data-mobile-columns="2" data-desktop-format="grid" data-mobile-format="grid"
                            data-mobile-slider-columns="2.25">
                            @foreach ($productosConDescuento as $producto)
                                @php
                                    // Calcular descuento
                                    $descuento = $producto->descuento_info;
                                    $precioNumerico = is_object($producto->precio_actual)
                                        ? $producto->precio_actual->precio
                                        : $producto->precio_actual;
                                    $precioConDescuento = $precioNumerico;
                                    $textoDescuento = null;

                                    if ($descuento && $precioNumerico) {
                                        if ($descuento->tipo === 'porcentaje') {
                                            $montoDescuento = ($precioNumerico * $descuento->valor) / 100;
                                            $textoDescuento = round($descuento->valor) . '% OFF';
                                        } else {
                                            $montoDescuento = $descuento->valor;
                                            $textoDescuento =
                                                '$' . number_format($descuento->valor, 0, ',', '.') . ' OFF';
                                        }
                                        $precioConDescuento = $precioNumerico - $montoDescuento;
                                    }
                                    $stockInfo = $producto->getStockInfo();
                                @endphp
                                <div class="js-item-product col-6 col-md-2 item-product col-grid" data-product-type="list"
                                    data-product-id="{{ $producto->id }}" data-store="product-item-{{ $producto->id }}">
                                    <div class="item">
                                        <div class="js-product-container position-relative">

                                            <!-- Imagen del producto -->
                                            <div class="js-product-item-image-container-private product-item-image-container item-image"
                                                data-store="product-item-image-{{ $producto->id }}">
                                                <div style="padding-bottom: 100%;"
                                                    class="js-item-image-padding position-relative d-block">
                                                    <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                        title="{{ $producto->nombre }}"
                                                        aria-label="{{ $producto->nombre }}"
                                                        class="js-product-item-image-link-private">

                                                        <img src="{{ $producto->url_imagen_principal ?? asset('images/placeholder.jpg') }}"
                                                            alt="{{ $producto->nombre }}"
                                                            class="js-product-item-image-private product-item-image js-item-image lazyautosizes img-absolute img-absolute-centered fade-in item-image-featured lazyloaded"
                                                            loading="lazy">

                                                        <div class="placeholder-fade"></div>
                                                    </a>

                                                    <!-- Labels de descuento -->
                                                    <div class="labels js-labels-floating-group labels-absolute">
                                                        @if ($textoDescuento)
                                                            <div class="label js-discount-label label-accent">
                                                                {{ $textoDescuento }}
                                                            </div>
                                                        @elseif($stockInfo['controlar_stock'] && !$stockInfo['hay_stock'])
                                                            <div class="label js-stock-label label-default">
                                                                Sin stock
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Información del producto -->
                                            <div class="item-description pt-3"
                                                data-store="product-item-info-{{ $producto->id }}">
                                                <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}"
                                                    title="{{ $producto->nombre }}" aria-label="{{ $producto->nombre }}"
                                                    class="item-link">

                                                    <div class="js-item-name item-name mb-2 font-small opacity-80"
                                                        data-store="product-item-name-{{ $producto->id }}">
                                                        {{ $producto->nombre }}
                                                    </div>

                                                    <div class="item-price-container"
                                                        data-store="product-item-price-{{ $producto->id }}">
                                                        <div class="d-block mb-1 mr-1">
                                                            @if ($precioNumerico)
                                                                <span
                                                                    class="js-price-display item-price text-decoration-line-through opacity-50 mr-2"
                                                                    data-product-price="{{ $precioNumerico * 100 }}">
                                                                    ${{ number_format($precioNumerico, 0, ',', '.') }}
                                                                </span>
                                                                <span
                                                                    class="js-price-display item-price font-weight-bold text-accent"
                                                                    data-product-price="{{ $precioConDescuento * 100 }}">
                                                                    ${{ number_format($precioConDescuento, 0, ',', '.') }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">Precio no disponible</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                                <button type="button" class="btn btn-primary btn-add-to-cart-card w-100 mt-2"
                                                    onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarrito({{ $producto->id }}, this);"
                                                    {{ (!$stockInfo['hay_stock'] && $stockInfo['controlar_stock']) ? 'disabled' : '' }}>
                                                    {{ (!$stockInfo['hay_stock'] && $stockInfo['controlar_stock']) ? 'Sin stock' : 'Agregar al carrito' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Sección de Instagram -->
    <section class="section-home section-instagram-home" data-store="home-instagram">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="instagram-box"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 30px 20px; text-align: center; box-shadow: 0 5px 20px rgba(102, 126, 234, 0.25);">
                        <div class="instagram-content">
                            <!-- Icono de Instagram -->
                            <div class="instagram-icon mb-3">
                                <svg width="50" height="50" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="2" width="20" height="20" rx="5" stroke="white"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="12" cy="12" r="4" stroke="white" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="17.5" cy="6.5" r="1.5" fill="white" />
                                </svg>
                            </div>

                            <!-- Título -->
                            <h2 class="h3 text-white mb-2" style="font-weight: 700; font-size: 1.5rem;">
                                Síguenos en Instagram
                            </h2>

                            <!-- Descripción -->
                            <p class="text-white mb-3"
                                style="font-size: 0.9rem; opacity: 0.9; max-width: 500px; margin-left: auto; margin-right: auto;">
                                Novedades, ofertas y contenido exclusivo
                            </p>

                            <!-- Handle de Instagram -->
                            @php
                                // Extraer handle de Instagram del URL o usar nombre de empresa
                                $instagramHandle =
                                    $empresa->instagram ?? ($empresa->nombre_fantasia ?? $empresa->nombre);

                                if (!empty($empresa->instagram)) {
                                    if (strpos($instagramHandle, 'instagram.com/') !== false) {
                                        $parts = explode('instagram.com/', $instagramHandle);
                                        if (isset($parts[1])) {
                                            $instagramHandle = '@' . trim($parts[1], '/');
                                        }
                                    } elseif (strpos($instagramHandle, '@') !== 0) {
                                        $instagramHandle = '@' . $instagramHandle;
                                    }
                                } else {
                                    $instagramHandle = '@' . ($empresa->nombre_fantasia ?? $empresa->nombre);
                                }

                                // URL de Instagram
                                $instagramUrl = 'https://instagram.com/';
                                if (!empty($empresa->instagram)) {
                                    if (strpos($empresa->instagram, 'http') === 0) {
                                        $instagramUrl = $empresa->instagram;
                                    } else {
                                        $instagramUrl .= trim($empresa->instagram, '@');
                                    }
                                } else {
                                    $instagramUrl .= str_replace(
                                        ' ',
                                        '',
                                        strtolower($empresa->nombre_fantasia ?? $empresa->nombre),
                                    );
                                }
                            @endphp
                            <div class="instagram-handle mb-2">
                                <span class="h6 text-white"
                                    style="font-weight: 600; letter-spacing: 0.5px; font-size: 0.95rem;">
                                    {{ $instagramHandle }}
                                </span>
                            </div>

                            <!-- Botón -->
                            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer"
                                class="btn btn-light btn-sm"
                                style="background: white; color: #667eea; font-weight: 600; padding: 8px 24px; border-radius: 25px; text-decoration: none; display: inline-block; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); font-size: 0.875rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                                    <rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" />
                                </svg>
                                Ver Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .instagram-box .btn-light:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                background: #f8f9fa;
            }

            .instagram-icon svg {
                filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.15));
            }

            @media (max-width: 768px) {
                .instagram-box {
                    padding: 20px 15px !important;
                }

                .instagram-box h2,
                .instagram-box .h3 {
                    font-size: 1.3rem !important;
                }

                .instagram-box p {
                    font-size: 0.85rem !important;
                }

                .instagram-icon svg {
                    width: 40px !important;
                    height: 40px !important;
                }
            }

            /* Estilos para botón agregar al carrito en cards */
            .btn-add-to-cart-card {
                font-size: 0.75rem;
                padding: 8px 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-radius: 4px;
                transition: all 0.3s ease;
            }

            .btn-add-to-cart-card:hover:not(:disabled) {
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }

            .btn-add-to-cart-card:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            @media (max-width: 576px) {
                .btn-add-to-cart-card {
                    font-size: 0.65rem;
                    padding: 6px 8px;
                }
            }
        </style>
    </section>
@endsection

@push('scripts')
<script>
    // Función para agregar al carrito
    function agregarAlCarrito(productoId, btn) {
        if (!btn) return;

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '...';

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
            btn.innerHTML = '✓ Agregado';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }, 2000);

            // Actualizar contador del carrito si existe
            const cartCounters = document.querySelectorAll('.js-cart-widget-amount, .cart-count');
            cartCounters.forEach(counter => {
                if (data.total_items !== undefined) {
                    counter.textContent = data.total_items;
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Error al agregar al carrito');
        });
    }
</script>
@endpush
