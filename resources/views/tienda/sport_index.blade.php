@extends('tienda.sport_layout')

@section('title', 'Baires Skate - Tienda Online de Skateboarding')
@section('description', 'Comprá productos de skateboarding por internet. Tenemos calzado, boards y más.')

@push('styles')
<style>
    /* Sección: Banners de Categorías */
    .section-banners-home {
        padding: 0;
        margin-bottom: var(--section-distance-huge);
    }

    .banner-grid {
        display: grid;
        gap: 0;
    }

    .banner-grid.grid-1 {
        grid-template-columns: 1fr;
    }

    .banner-grid.grid-2 {
        grid-template-columns: repeat(2, 1fr);
    }

    .banner-grid.grid-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    @media (max-width: 768px) {
        .banner-grid.grid-2,
        .banner-grid.grid-3 {
            grid-template-columns: 1fr;
        }
    }

    .textbanner {
        position: relative;
        height: 800px;
        overflow: hidden;
    }

    .textbanner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .textbanner:hover img {
        transform: scale(1.05);
    }

    .textbanner-text {
        position: absolute;
        bottom: 40px;
        left: 40px;
        color: #fff;
        z-index: 10;
    }

    .textbanner-text h3 {
        font-size: var(--h1-huge);
        margin-bottom: 10px;
        line-height: 0.9;
    }

    .textbanner .overlay::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    }

    /* Mensaje Institucional */
    .home-institutional {
        padding: var(--section-distance-huge) 0;
    }

    .home-institutional h2 {
        font-size: 88px;
        margin-bottom: 0;
        display: inline-block;
        margin-right: 40px;
    }

    .institutional-content {
        display: flex;
        align-items: center;
    }

    .institutional-text {
        max-width: 400px;
        text-align: left;
    }

    @media (min-width: 768px) {
        .home-institutional h2 {
            font-size: 184px;
            line-height: 0.9;
        }
    }

    /* Texto Animado - Primera sección (derecha a izquierda) */
    .section-home-text-animated {
        background-color: var(--main-foreground);
        color: var(--main-background);
        padding: 8px 0;
        margin: 5px 0;
        overflow: hidden;
    }

    .home-text-animated {
        display: flex;
        white-space: nowrap;
        animation: marquee 40s linear infinite;
    }

    .home-text {
        font-size: 53px;
        font-family: var(--heading-font);
        font-weight: 700;
        margin-right: 60px;
    }

    @keyframes marquee {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    /* Texto Animado Invertido (izquierda a derecha) */
    .section-home-text-animated-reverse {
        background-color: #FFFFFF;
        color: #000000;
        padding: 8px 0;
        margin: 5px 0;
        overflow: hidden;
    }

    .home-text-animated-reverse {
        display: flex;
        white-space: nowrap;
        animation: marquee-reverse 40s linear infinite;
    }

    .home-text-animated-reverse .home-text {
        color: #000000;
    }

    @keyframes marquee-reverse {
        0% {
            transform: translateX(-50%);
        }
        100% {
            transform: translateX(0);
        }
    }

    /* Productos Destacados */
    .section-featured-home {
        padding: var(--section-distance-huge) 0;
    }

    .products-section-container {
        display: flex;
        gap: 40px;
        align-items: flex-start;
    }

    .products-title-container {
        flex-shrink: 0;
        width: 200px;
    }

    .section-title {
        font-size: 48px;
        font-weight: 700;
        line-height: 1;
        text-transform: uppercase;
        writing-mode: horizontal-tb;
        margin: 0;
    }

    @media (min-width: 768px) {
        .section-title {
            font-size: 64px;
        }
    }

    .products-grid-container {
        flex: 1;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .product-item {
        background: #fff;
        border-radius: var(--border-radius);
        overflow: hidden;
        transition: all 0.3s;
    }

    .product-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .product-image-container {
        position: relative;
        height: 280px;
        overflow: hidden;
        background-color: #f5f5f5;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .product-item:hover .product-image {
        transform: scale(1.1);
    }

    .product-label {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #000000;
        color: #FFFFFF;
        padding: 5px 15px;
        border-radius: var(--border-radius);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .product-info {
        padding: 15px;
    }

    .product-name {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .product-price {
        font-size: 12px;
        font-weight: 600;
        color: var(--accent-color);
        margin-bottom: 10px;
    }

    .btn-add-to-cart {
        width: 100%;
        margin-top: 10px;
        padding: 10px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .section-featured-home .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .products-section-container {
            flex-direction: column;
            gap: 15px;
        }

        .products-title-container {
            width: 100%;
        }

        .products-grid-container {
            width: 100%;
            max-width: 100%;
        }

        .products-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            width: 100%;
        }

        .product-item {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .product-image-container {
            height: 250px;
            width: 100%;
        }

        .product-image {
            width: 100%;
            height: 100%;
        }

        .product-info {
            padding: 10px;
        }

        .product-name {
            font-size: 13px;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .product-price {
            font-size: 10px;
            margin-bottom: 6px;
        }

        .btn-add-to-cart {
            font-size: 11px;
            padding: 8px 6px;
        }
    }

    /* Slider Home */
    .section-slider-home {
        margin: var(--section-distance-huge) 0;
    }

    .home-slider {
        position: relative;
        height: 600px;
    }

    .swiper-slide {
        height: 600px;
    }

    .slide-content {
        position: absolute;
        top: 50%;
        left: 10%;
        transform: translateY(-50%);
        color: #fff;
        z-index: 10;
        max-width: 600px;
    }

    .slide-content h2 {
        font-size: var(--h1-huge);
        margin-bottom: 20px;
        line-height: 0.9;
    }

    @media (min-width: 768px) {
        .slide-content h2 {
            font-size: var(--h1-huge-md);
        }
    }

    .slide-content p {
        font-size: var(--font-large);
        margin-bottom: 30px;
    }

    .slide-bg {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .slide-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to right, rgba(0,0,0,0.7), transparent);
    }

    /* Swiper Navigation */
    .swiper-button-prev,
    .swiper-button-next {
        color: var(--main-foreground);
    }

    .swiper-pagination-bullet-active {
        background-color: var(--main-foreground);
    }

    /* Newsletter */
    .section-newsletter {
        background-color: #f8f9fa;
        padding: 80px 0;
        text-align: center;
    }

    .section-newsletter h2 {
        font-size: var(--h2-huge);
        margin-bottom: 20px;
    }

    .section-newsletter p {
        font-size: var(--font-large);
        margin-bottom: 30px;
        opacity: 0.8;
    }

    .newsletter-form-center {
        max-width: 500px;
        margin: 0 auto;
        display: flex;
        gap: 10px;
    }

    .newsletter-form-center input {
        flex: 1;
        padding: 15px 20px;
        border: 2px solid var(--main-foreground);
        border-radius: var(--border-radius);
        font-family: var(--body-font);
    }

    .newsletter-form-center button {
        padding: 15px 40px;
    }

    /* Producto Destacado Individual */
    .section-single-product {
        padding: var(--section-distance-huge) 0;
        background-color: #f8f9fa;
    }

    .section-single-product .row.justify-content-md-center {
        margin-top: 20px;
    }

    /* Gallery - Swiper Carousel */
    .section-single-product .product-image-column {
        max-width: 100%;
        margin-bottom: 30px;
    }

    .section-single-product .product-info-column {
        max-width: 100%;
        padding-top: 20px;
    }

    @media (min-width: 768px) {
        .section-single-product .product-image-column {
            max-width: 650px;
            width: 100%;
            position: sticky;
            top: 100px;
            align-self: flex-start;
        }

        .section-single-product .product-info-column {
            max-width: 550px;
            width: 100%;
            padding-top: 0;
            padding-left: 60px;
        }
    }

    @media (min-width: 992px) {
        .section-single-product .product-image-column {
            max-width: 700px;
        }
    }

    @media (min-width: 1200px) {
        .section-single-product .product-image-column {
            max-width: 800px;
        }
    }

    .section-single-product .js-swiper-product-home {
        width: 100%;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .section-single-product .product-slide-small {
        width: 100%;
        background: #fff;
    }

    .section-single-product .swiper-wrapper {
        display: flex;
        align-items: center;
    }

    .section-single-product .js-product-slide-link {
        position: relative;
        display: block;
        overflow: hidden;
    }

    .section-single-product .product-slider-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .section-single-product .img-absolute {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .section-single-product .img-absolute-centered {
        object-fit: contain;
        object-position: center;
    }

    .section-single-product .swiper-buttons {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }

    .section-single-product .swiper-button-prev,
    .section-single-product .swiper-button-next {
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

    .section-single-product .swiper-button-prev:after,
    .section-single-product .swiper-button-next:after {
        content: '';
    }

    .section-single-product .swiper-button-prev:hover,
    .section-single-product .swiper-button-next:hover {
        background-color: #000;
        border-color: #000;
    }

    .section-single-product .swiper-button-prev:hover svg,
    .section-single-product .swiper-button-next:hover svg {
        fill: #fff;
    }

    .section-single-product .swiper-button-disabled {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
    }

    .section-single-product .icon-inline {
        width: 24px;
        height: 24px;
        fill: currentColor;
        display: inline-block;
    }

    .section-single-product .icon-lg {
        width: 32px;
        height: 32px;
    }

    .section-single-product .icon-flip-horizontal {
        transform: scaleX(-1);
    }

    .section-single-product .svg-icon-text {
        color: #000;
    }

    .section-single-product .swiper-pagination-fraction {
        font-family: var(--heading-font);
        font-size: 16px;
        font-weight: 700;
        color: #000;
    }

    .section-single-product .labels {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .section-single-product .label {
        display: inline-block;
        padding: 8px 16px;
        font-family: var(--heading-font);
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        text-align: center;
    }

    .section-single-product .label-big {
        padding: 10px 20px;
        font-size: 16px;
    }

    .section-single-product .label-accent {
        background-color: #000;
        color: #fff;
    }

    .section-single-product .label-offer-percentage,
    .section-single-product .label-offer-percentage-text {
        font-family: var(--heading-font);
        font-weight: 700;
    }

    .section-single-product .swiper-slide {
        background: #fff;
    }

    /* Single Product Info */
    .section-single-product .product-info-column {
        background: #fff;
        position: relative;
        z-index: 10;
    }

    .section-single-product .product-title {
        font-size: var(--h3-huge);
        margin-bottom: 20px;
    }

    @media (min-width: 768px) {
        .section-single-product .product-title {
            font-size: var(--h2-huge);
        }
    }

    .section-single-product .price-container {
        margin-bottom: 30px;
    }

    .section-single-product .current-price {
        font-size: 24px;
        font-weight: 600;
        color: var(--accent-color);
    }

    .section-single-product .old-price {
        font-size: 18px;
        text-decoration: line-through;
        opacity: 0.5;
        margin-left: 10px;
    }

    .section-single-product .discount-percentage {
        display: inline-block;
        background-color: #000000;
        color: #FFFFFF;
        padding: 4px 12px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 700;
        margin-left: 10px;
    }

    .section-single-product .installments-info {
        font-size: 14px;
        opacity: 0.7;
        margin-top: 10px;
    }

    .single-product-form {
        margin-bottom: 30px;
        padding: 30px;
        background-color: #fff;
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

    .section-single-product .product-description {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid rgba(0,0,0,0.1);
    }

    .section-single-product .description-title {
        font-size: var(--h4);
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .section-single-product .description-content {
        line-height: 1.8;
        opacity: 0.8;
    }

    /* Responsive Single Product */
    @media (max-width: 768px) {
        .section-single-product .row.justify-content-md-center {
            margin-top: 10px;
        }

        .section-single-product .product-image-column {
            max-width: 100%;
            margin-bottom: 20px;
        }

        .section-single-product .product-info-column {
            max-width: 100%;
            padding-left: 0;
        }

        .section-single-product .swiper-button-prev,
        .section-single-product .swiper-button-next {
            width: 40px;
            height: 40px;
        }

        .section-single-product .icon-lg {
            width: 24px;
            height: 24px;
        }

        .section-single-product .swiper-pagination-fraction {
            font-size: 14px;
        }

        .section-single-product .product-title {
            font-size: 32px;
        }

        .section-single-product .current-price {
            font-size: 32px;
        }

        .single-product-form {
            padding: 20px;
        }

        .quantity-selector {
            flex-direction: column;
            gap: 15px;
        }

        .quantity-controls {
            justify-content: center;
        }

        .btn-add-to-cart-big {
            width: 100%;
        }
    }

    /* Instagram Section */
    .section-instagram {
        padding: 0;
        margin: var(--section-distance-huge) 0;
    }

    .instagram-header {
        padding: 40px 0;
    }

    .instagram-handle {
        font-size: var(--h2-huge);
        margin-bottom: 10px;
        text-decoration: none;
        color: var(--main-foreground);
        display: block;
    }

    .instagram-follow {
        text-transform: uppercase;
        font-weight: 700;
        color: var(--accent-color);
    }

    /* Categorías Animadas (solo texto, sin imágenes) */
    .section-categories-animated {
        background-color: #FFFFFF;
        padding: 8px 0;
        margin: 5px 0;
        overflow: hidden;
    }

    .categories-animated {
        display: flex;
        white-space: nowrap;
        animation: marquee 60s linear infinite;
    }

    .category-text {
        font-size: 53px;
        font-family: var(--heading-font);
        font-weight: 700;
        text-transform: uppercase;
        color: #000000;
        margin-right: 60px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .banner-grid {
            grid-template-columns: 1fr;
        }

        .textbanner {
            height: 400px;
        }

        .home-institutional h2 {
            font-size: 51px;
        }

        .institutional-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .institutional-text {
            margin-top: 20px;
        }

        .home-text {
            font-size: 32px;
        }

        .category-text {
            font-size: 32px;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }

        .slide-content h2 {
            font-size: 48px;
        }

        .single-product-container {
            grid-template-columns: 1fr;
        }

        .main-image-container-single {
            height: 400px;
        }
    }
</style>
@endpush

@section('content')

<!-- Sección 1: Banners de Categorías -->
@php
    $categoriasAleatorias = $categorias->shuffle()->take(3);
    $totalCategorias = $categoriasAleatorias->count();
    $gridClass = 'grid-' . $totalCategorias;
@endphp

@if($totalCategorias > 0)
<section class="section-banners-home">
    <div class="banner-grid {{ $gridClass }}">
        @foreach($categoriasAleatorias as $index => $categoria)
        <div class="textbanner">
            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}">
                <div class="overlay">
                    @if($categoria->imagen)
                        <img src="{{ asset($categoria->imagen) }}" alt="{{ $categoria->nombre }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1564982752979-3f7bc974b29e?w=800&h=1200&fit=crop" alt="{{ $categoria->nombre }}">
                    @endif
                    <div class="textbanner-text">
                        <div style="font-size: 18px; margin-bottom: 10px;">{{ $index + 1 }} / {{ $totalCategorias }}</div>
                        <h3 class="h1-huge">{{ strtoupper($categoria->nombre) }}</h3>
                        <div class="btn btn-link">VER MÁS</div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</section>
@endif

<!-- Sección 2: Mensaje Institucional -->
<section class="home-institutional">
    <div class="container-fluid">
        <div class="institutional-content">
            <h2>{{ strtoupper($empresa->nombre) }}</h2>
            <div class="institutional-text">
                @if($empresa->descripcion)
                    <p>{{ $empresa->descripcion }}</p>
                @else
                    <p>Bienvenido a nuestra tienda online. Encuentra los mejores productos al mejor precio.</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Sección 3: Anuncio Animado (derecha a izquierda) -->
@if($empresa->planMembresia && $empresa->planMembresia->esGratuito())
<section class="section-home-text-animated">
    <div class="home-text-animated">
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
    </div>
</section>
@endif

<!-- Nueva: Anuncio Animado Invertido (izquierda a derecha) -->
@if($empresa->planMembresia && $empresa->planMembresia->esGratuito())
<section class="section-home-text-animated-reverse">
    <div class="home-text-animated-reverse">
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
        <span class="home-text"><a href="/" target="_blank">PÁGINA CREADA EN BETOGETHER</a></span>
    </div>
</section>
@endif

<!-- Sección 4: Productos LO NUEVO -->
@if($productosNuevos && $productosNuevos->count() > 0)
<section class="section-featured-home">
    <div class="container-fluid">
        <div class="products-section-container">
            <div class="products-title-container">
                <h2 class="section-title">LO<br>NUEVO</h2>
            </div>

            <div class="products-grid-container">
                <div class="products-grid">
                    @foreach($productosNuevos as $nuevo)
                    @php
                        // Buscar descuentos activos para este producto
                        $descuentoNuevo = null;
                        $textoDescuentoNuevo = null;
                        $precioActualNuevo = is_object($nuevo->precio_actual) ? $nuevo->precio_actual->precio : $nuevo->precio_actual;
                        $precioConDescuentoNuevo = $precioActualNuevo;
                        $montoDescuentoNuevo = 0;

                        if (isset($descuentosActivos)) {
                            foreach ($descuentosActivos as $desc) {
                                $aplica = false;

                                if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                                    $aplica = true;
                                } elseif ($desc->aplica_a === 'producto' && in_array($nuevo->id, $desc->productos_aplicables ?? [])) {
                                    $aplica = true;
                                } elseif ($desc->aplica_a === 'categoria' && in_array($nuevo->categoria_id, $desc->categorias_aplicables ?? [])) {
                                    $aplica = true;
                                }

                                if ($aplica) {
                                    $descuentoNuevo = $desc;
                                    if ($desc->tipo === 'porcentaje') {
                                        $montoDescuentoNuevo = ($precioActualNuevo * $desc->valor) / 100;
                                        $textoDescuentoNuevo = round($desc->valor) . '% OFF';
                                    } else {
                                        $montoDescuentoNuevo = $desc->valor;
                                        $textoDescuentoNuevo = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                                    }
                                    $precioConDescuentoNuevo = $precioActualNuevo - $montoDescuentoNuevo;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <div class="product-item">
                        <div class="product-image-container">
                            <a href="{{ route('tienda.producto', [$empresa->slug, $nuevo->id]) }}">
                                <img src="{{ $nuevo->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $nuevo->nombre }}" class="product-image">
                            </a>
                            @if($descuentoNuevo)
                                <span class="product-label">{{ $textoDescuentoNuevo }}</span>
                            @else
                                <span class="product-label">NUEVO</span>
                            @endif
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">{{ strtoupper($nuevo->nombre) }}</h3>
                            <div class="product-price">
                                @if($precioActualNuevo)
                                    @if($descuentoNuevo)
                                        <span style="text-decoration: line-through; opacity: 0.6; margin-right: 8px;">${{ number_format($precioActualNuevo, 0, ',', '.') }}</span>
                                        ${{ number_format($precioConDescuentoNuevo, 0, ',', '.') }}
                                    @else
                                        ${{ number_format($precioActualNuevo, 0, ',', '.') }}
                                    @endif
                                @else
                                    Consultar precio
                                @endif
                            </div>
                            <button class="btn btn-add-to-cart" onclick="alert('Funcionalidad de carrito próximamente')">AGREGAR AL CARRITO</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Sección 6: Slider Home -->
@if($empresa->carruselImagenesActivas && $empresa->carruselImagenesActivas->count() > 0)
<section class="section-slider-home">
    <div class="swiper home-slider">
        <div class="swiper-wrapper">
            @foreach($empresa->carruselImagenesActivas as $imagen)
            <div class="swiper-slide">
                <img src="{{ $imagen->imagen_url }}" alt="{{ $imagen->titulo ?? 'Slide' }}" class="slide-bg">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    @if($imagen->titulo)
                        <h2>{{ strtoupper($imagen->titulo) }}</h2>
                    @endif
                    @if($imagen->descripcion)
                        <p>{{ $imagen->descripcion }}</p>
                    @endif
                    @if($imagen->link)
                        <a href="{{ $imagen->link }}" class="btn">VER MÁS</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($empresa->carruselImagenesActivas->count() > 1)
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        @endif
    </div>
</section>
@endif

<!-- Producto Destacado Individual -->
@if($productoAleatorio)
@php
    // Buscar descuentos activos para este producto
    $descuentoActivoHome = null;
    $textoDescuentoHome = null;
    $precioActualHome = is_object($productoAleatorio->precio_actual) ? $productoAleatorio->precio_actual->precio : $productoAleatorio->precio_actual;
    $precioConDescuentoHome = $precioActualHome;
    $montoDescuentoHome = 0;

    if (isset($descuentosActivos)) {
        foreach ($descuentosActivos as $desc) {
            $aplica = false;

            if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                $aplica = true;
            } elseif ($desc->aplica_a === 'producto' && in_array($productoAleatorio->id, $desc->productos_aplicables ?? [])) {
                $aplica = true;
            } elseif ($desc->aplica_a === 'categoria' && in_array($productoAleatorio->categoria_id, $desc->categorias_aplicables ?? [])) {
                $aplica = true;
            }

            if ($aplica) {
                $descuentoActivoHome = $desc;
                if ($desc->tipo === 'porcentaje') {
                    $montoDescuentoHome = ($precioActualHome * $desc->valor) / 100;
                    $textoDescuentoHome = round($desc->valor) . '% OFF';
                } else {
                    $montoDescuentoHome = $desc->valor;
                    $textoDescuentoHome = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                }
                $precioConDescuentoHome = $precioActualHome - $montoDescuentoHome;
                break;
            }
        }
    }
@endphp
<section class="section-single-product">
    <div class="container-fluid">
        <!-- Product Container -->
        <div class="row justify-content-md-center">
            <!-- Product Gallery - Swiper Carousel -->
            <div class="col-md-auto product-image-column">
                <div data-store="product-image-home-{{ $productoAleatorio->id }}">
                    @if($productoAleatorio->imagenes && $productoAleatorio->imagenes->count() > 1)
                    <div class="swiper-buttons p-0 mr-2">
                        <div class="js-swiper-product-home-prev swiper-button-prev svg-icon-text">
                            <svg class="icon-inline icon-lg icon-flip-horizontal"><use xlink:href="#arrow-long"></use></svg>
                        </div>
                        <div class="js-swiper-product-home-next swiper-button-next svg-icon-text">
                            <svg class="icon-inline icon-lg"><use xlink:href="#arrow-long"></use></svg>
                        </div>
                    </div>
                    <div class="js-swiper-product-home-pagination swiper-pagination d-inline-block w-auto position-relative pt-3 text-left swiper-pagination-fraction">
                        <span class="swiper-pagination-current">1</span> / <span class="swiper-pagination-total">{{ $productoAleatorio->imagenes->count() }}</span>
                    </div>
                    @endif

                    <div class="js-swiper-product-home swiper-container" data-product-images-amount="{{ $productoAleatorio->imagenes ? $productoAleatorio->imagenes->count() : 1 }}" style="visibility: hidden; height: 0;">

                        <!-- Discount Labels -->
                        @if($descuentoActivoHome)
                        <div class="labels js-labels-floating-group labels" data-store="product-item-labels">
                            <div class="js-offer-label-private label js-offer-label mb-2 label label-accent label-big" data-store="product-item-offer-label">
                                <span class="label-offer-percentage">
                                    -<span class="js-offer-percentage">{{ round(($montoDescuentoHome / $precioActualHome) * 100) }}</span>%
                                </span>
                                <span class="label-offer-percentage-text"> OFF</span>
                            </div>
                        </div>
                        @endif

                        <div class="swiper-wrapper">
                            @if($productoAleatorio->imagenes && $productoAleatorio->imagenes->count() > 0)
                                @foreach($productoAleatorio->imagenes as $imagen)
                                <div class="js-product-slide swiper-slide product-slide-small slider-slide" data-image="{{ $imagen->id }}" data-image-position="{{ $loop->index }}">
                                    <div class="js-product-slide-link d-block position-relative" style="padding-bottom: 106.15%;">
                                        <img
                                            src="{{ $imagen->url }}"
                                            class="js-product-slide-img product-slider-image img-absolute img-absolute-centered lazyloaded"
                                            width="100%"
                                            height="100%"
                                            alt="{{ $productoAleatorio->nombre }} - {{ $loop->iteration }}"
                                        >
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="js-product-slide swiper-slide product-slide-small slider-slide" data-image="default" data-image-position="0">
                                    <div class="js-product-slide-link d-block position-relative" style="padding-bottom: 106.15%;">
                                        <img
                                            src="{{ $productoAleatorio->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}"
                                            class="js-product-slide-img product-slider-image img-absolute img-absolute-centered lazyloaded"
                                            width="100%"
                                            height="100%"
                                            alt="{{ $productoAleatorio->nombre }}"
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
                <h2 class="product-title">{{ strtoupper($productoAleatorio->nombre) }}</h2>

                <div class="price-container">
                    @if($precioActualHome)
                    <div>
                        @if($descuentoActivoHome)
                        <span class="current-price">${{ number_format($precioConDescuentoHome, 0, ',', '.') }}</span>
                        <span class="old-price">${{ number_format($precioActualHome, 0, ',', '.') }}</span>
                        <span class="discount-percentage">{{ $textoDescuentoHome }}</span>
                        @else
                        <span class="current-price">${{ number_format($precioActualHome, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="installments-info">
                        3 cuotas sin interés de ${{ number_format(($descuentoActivoHome ? $precioConDescuentoHome : $precioActualHome) / 3, 0, ',', '.') }}
                    </div>
                    @else
                    <div>
                        <span class="current-price" style="font-size: 18px;">Consultar precio</span>
                    </div>
                    @endif
                </div>

                <a href="{{ route('tienda.producto', [$empresa->slug, $productoAleatorio->id]) }}" class="btn btn-add-to-cart-big" style="display: inline-block; width: 100%; max-width: 400px; text-align: center; text-decoration: none; margin-bottom: 20px;">VER PRODUCTO COMPLETO</a>

                @if($productoAleatorio->descripcion)
                <div class="product-description">
                    <h3 class="description-title">Descripción</h3>
                    <div class="description-content">
                        <p>{{ Str::limit($productoAleatorio->descripcion, 300) }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

<!-- Sección Instagram -->
@if($empresa->instagram_url)
<section class="section-instagram">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 instagram-header">
                @php
                    // Extract Instagram username from URL
                    $instagramHandle = preg_match('/instagram\.com\/([^\/]+)/', $empresa->instagram_url, $matches) ? '@' . $matches[1] : '@' . $empresa->nombre;
                @endphp
                <a href="{{ $empresa->instagram_url }}" target="_blank" class="instagram-handle">
                    {{ $instagramHandle }}
                </a>
                <p class="instagram-follow">Seguinos en Instagram</p>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Categorías Animadas (solo texto, sin imágenes) -->
@if($categorias && $categorias->count() > 0)
<section class="section-categories-animated">
    <div class="categories-animated">
        @foreach($categorias as $categoria)
        <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}" class="category-text">{{ strtoupper($categoria->nombre) }}</a>
        @endforeach
        <!-- Duplicados para efecto infinito -->
        @foreach($categorias as $categoria)
        <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}" class="category-text">{{ strtoupper($categoria->nombre) }}</a>
        @endforeach
    </div>
</section>
@endif

<!-- Sección: Más Productos -->
@if($productosDestacados && $productosDestacados->count() > 0)
<section class="section-featured-home">
    <div class="container-fluid">
        <div class="products-section-container">
            <div class="products-title-container">
                <h2 class="section-title">MÁS<br>PRODUCTOS</h2>
            </div>

            <div class="products-grid-container">
                <div class="products-grid">
                    @foreach($productosDestacados as $producto)
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
                    <div class="product-item">
                        <div class="product-image-container">
                            <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}">
                                <img src="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $producto->nombre }}" class="product-image">
                            </a>
                            @if($descuentoActivo)
                                <span class="product-label">{{ $textoDescuento }}</span>
                            @else
                                <span class="product-label">NUEVO</span>
                            @endif
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">{{ strtoupper($producto->nombre) }}</h3>
                            <div class="product-price">
                                @if($precioActual)
                                    @if($descuentoActivo)
                                        <span style="text-decoration: line-through; opacity: 0.6; margin-right: 8px;">${{ number_format($precioActual, 0, ',', '.') }}</span>
                                        ${{ number_format($precioConDescuento, 0, ',', '.') }}
                                    @else
                                        ${{ number_format($precioActual, 0, ',', '.') }}
                                    @endif
                                @else
                                    Consultar precio
                                @endif
                            </div>
                            <button class="btn btn-add-to-cart" onclick="alert('Funcionalidad de carrito próximamente')">AGREGAR AL CARRITO</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
    // Initialize Swiper
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.home-slider', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        // Featured Product Carousel on Homepage
        var productHomeSwiperInst = null;
        var product_images_amount_home = document.querySelector(".js-swiper-product-home")?.getAttribute("data-product-images-amount");

        if (product_images_amount_home > 0 && document.querySelector('.js-swiper-product-home')) {
            productHomeSwiperInst = new Swiper('.js-swiper-product-home', {
                lazy: true,
                slidesPerView: 1,
                threshold: 5,
                centerInsufficientSlides: true,
                watchOverflow: true,
                spaceBetween: 0,
                pagination: {
                    el: '.js-swiper-product-home-pagination',
                    type: 'fraction',
                },
                navigation: {
                    nextEl: '.js-swiper-product-home-next',
                    prevEl: '.js-swiper-product-home-prev',
                },
                on: {
                    init: function () {
                        const swiperEl = document.querySelector(".js-swiper-product-home");
                        if (swiperEl) {
                            swiperEl.style.visibility = "visible";
                            swiperEl.style.height = "auto";
                        }
                    },
                },
            });
        }

        // Add to cart functionality (placeholder)
        document.querySelectorAll('.btn-add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                alert('Producto agregado al carrito!');
            });
        });
    });

    // Single Product Gallery
    function changeImageSingle(thumbnail, imageUrl) {
        document.getElementById('mainImageSingle').src = imageUrl;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    // Select variant
    function selectVariantSingle(button, size) {
        document.querySelectorAll('.variant-option').forEach(v => v.classList.remove('selected'));
        button.classList.add('selected');
        document.getElementById('selectedSizeSingle').textContent = size;
    }

    // Quantity controls
    function decreaseQuantitySingle() {
        const input = document.getElementById('quantitySingle');
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }

    function increaseQuantitySingle() {
        const input = document.getElementById('quantitySingle');
        const currentValue = parseInt(input.value);
        const maxValue = parseInt(input.max);
        if (currentValue < maxValue) {
            input.value = currentValue + 1;
        }
    }

    // Form submission
    document.getElementById('singleProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const size = document.getElementById('selectedSizeSingle').textContent;
        const quantity = document.getElementById('quantitySingle').value;
        alert(`Producto agregado al carrito!\nTalle: ${size}\nCantidad: ${quantity}`);
    });
</script>
@endpush
