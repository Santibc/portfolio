<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brasilia Theme - Tienda de Indumentaria</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/brasilia-theme.css') }}">
</head>
<body class="js-head-offset head-offset" style="padding-top: 206px;">

    <!-- SVG Sprite -->
    @include('tienda.partials.brasilia-svg-sprite')

    <!-- Header -->
    <header class="js-head-main head-main head-colors position-sticky position-fixed-md transition-soft" data-store="head" data-header-md-fixed="true" style="top: 0px;">

        <!-- Adbar Primary (Gris Oscuro) -->
        <div class="js-adbar js-adbar-primary adbar-primary adbar adbar-colors adbar-with-messages" data-active="true" data-messages="1" data-animated="false">
            <div class="js-adbar-content js-swiper-adbar-primary swiper-container text-center container">
                <div class="js-adbar-messages-container js-adbar-primary-messages-container swiper-wrapper adbar-text-container align-items-center">
                    <span class="js-adbar-message-container js-adbar-primary-message-container adbar-message swiper-slide slide-container">
                        CUPÓN DEL 10% OFF USANDO #DESCUENTO10
                    </span>
                </div>
            </div>
        </div>

        <!-- Adbar Secondary (Verde - Animated) -->
        <div class="js-adbar js-adbar-secondary adbar-secondary adbar adbar-animated adbar-colors adbar-with-messages" data-active="true" data-messages="1" data-animated="true">
            <div class="js-adbar-content js-swiper-adbar-secondary adbar-content-animated">
                <div class="js-adbar-messages-container js-adbar-secondary-messages-container swiper-wrapper adbar-text-container align-items-center">
                    @for($i = 0; $i < 16; $i++)
                    <span class="js-adbar-message-container js-adbar-secondary-message-container adbar-message mr-4">
                        ENVÍO GRATIS A PARTIR DE $56.000
                    </span>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="js-head-row head-row container logo-center logo-md-left">

            <!-- Mobile Menu Button -->
            <div class="menu-container d-md-none">
                <button class="js-modal-open-private header-utility" data-target="#nav-hamburger" aria-label="Menú">
                    <svg class="icon-inline utility-icon icon-lg"><use xlink:href="#bars"></use></svg>
                </button>
            </div>

            <!-- Logo -->
            <div class="js-logo-container logo-container" style="width: 163px;">
                <div id="logo" class="logo-img-container">
                    <a href="{{ route('tienda.brasilia') }}" title="Brasilia Theme">
                        <img src="https://dcdn-us.mitiendanube.com/stores/004/486/324/themes/common/logo-429079951-1719412309-105224c351f272540c4b787ddee151421719412309-480-0.webp"
                             alt="Brasilia Theme"
                             class="logo-img transition-soft"
                             width="250"
                             height="92">
                    </a>
                    <h1 style="display: none;">Brasilia Theme</h1>
                </div>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form class="js-search-form search-form" action="/search/" method="get">
                    <div class="form-group position-relative m-0">
                        <input class="js-search-input form-control search-input"
                               autocomplete="off"
                               type="search"
                               name="q"
                               placeholder="¿Qué estás buscando?"
                               aria-label="¿Qué estás buscando?">
                        <button type="submit" class="js-search-input-submit search-btn search-submit-btn svg-icon-mask" value="Buscar" aria-label="Buscar">
                        </button>
                        <a href="#" class="js-empty-search search-btn search-empty-btn svg-icon-mask" style="display: none;">
                        </a>
                    </div>
                </form>
                <div class="js-search-form-suggestions search-suggestions" style="display: none;"></div>
            </div>

            <!-- Utilities -->
            <div class="utilities-container">
                <!-- User Account (Mobile) -->
                <span class="js-header-utility-icon js-header-utility-icon-only header-utility d-md-none utility-icon-md-colors">
                    <a href="/account/login/" class="header-icon">
                        <svg class="icon-inline utility-icon icon-lg"><use xlink:href="#user"></use></svg>
                    </a>
                </span>

                <!-- User Account (Desktop) -->
                <span class="js-header-utility-with-text header-utility d-none d-md-grid">
                    <span class="js-header-utility-icon utility-icon-md-colors">
                        <svg class="icon-inline utility-icon icon-lg"><use xlink:href="#user"></use></svg>
                    </span>
                    <span class="utility-text">
                        <div class="font-weight-bold">
                            <a href="/account/login/" title="">Entrá</a> /
                        </div>
                        <div>
                            <a href="/account/register" title="">Registráte</a>
                        </div>
                    </span>
                </span>

                <!-- Cart -->
                <span id="ajax-cart" data-component="cart-button">
                    <a href="#" data-target="#modal-cart" class="js-modal-open-private header-utility">
                        <span class="js-header-utility-icon header-icon-big utility-icon-md-colors">
                            <svg class="icon-inline utility-icon icon-lg"><use xlink:href="#bag"></use></svg>
                            <span class="js-cart-widget-amount badge d-md-none">0</span>
                        </span>
                        <div class="js-header-utility-text js-header-utility-text-cart utility-text d-none d-md-grid">
                            <div class="font-weight-bold d-flex">
                                <span class="mr-1">Carrito</span>
                                <span>(<span class="js-cart-widget-amount">0</span>)</span>
                            </div>
                            <div class="js-cart-widget-total" data-priceraw="0">$0,00</div>
                        </div>
                    </a>
                </span>
            </div>
        </div>

        <!-- Desktop Navigation Menu -->
        <div class="js-head-row-nav head-row-nav d-none d-md-block">
            <nav class="js-desktop-nav desktop-nav container">
                <div class="nav-list-container">
                    <ul class="nav-list nav-list-left">
                        <li class="nav-list-item nav-dropdown-parent">
                            <a href="#" class="nav-list-link">Categorías</a>
                            <div class="nav-dropdown-menu">
                                <div class="nav-dropdown-content">
                                    <div class="nav-dropdown-column">
                                        <a href="#" class="nav-dropdown-item">Abrigos</a>
                                        <a href="#" class="nav-dropdown-item">Shorts</a>
                                        <a href="#" class="nav-dropdown-item">Lentes</a>
                                        <a href="#" class="nav-dropdown-item">Remeras</a>
                                    </div>
                                    <div class="nav-dropdown-column">
                                        <a href="#" class="nav-dropdown-item">Zapatos</a>
                                        <a href="#" class="nav-dropdown-item">Carteras</a>
                                        <a href="#" class="nav-dropdown-item">Camisas</a>
                                        <a href="#" class="nav-dropdown-item">Vestidos</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-list-item">
                            <a href="#" class="nav-list-link">Lo nuevo</a>
                        </li>
                        <li class="nav-list-item">
                            <a href="#" class="nav-list-link">Liquidación</a>
                        </li>
                        <li class="nav-list-item">
                            <a href="#" class="nav-list-link">Ofertas</a>
                        </li>
                    </ul>
                    <ul class="nav-list nav-list-right">
                        <li class="nav-list-item">
                            <a href="#" class="nav-list-link">Medios de pago</a>
                        </li>
                        <li class="nav-list-item">
                            <a href="#" class="nav-list-link">Cómo comprar</a>
                        </li>
                        <li class="nav-list-item">
                            <a href="#" class="nav-list-link">Preguntas frecuentes</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section with Video -->
        <section class="hero-section-video">
            <div class="container-fluid p-0">
                <div class="hero-video-wrapper">
                    <video class="hero-video" autoplay muted loop playsinline>
                        <source src="https://d26lpennugtm8s.cloudfront.net/stores/004/486/324/themes/common/video-1735055695-1735055695.mp4" type="video/mp4">
                    </video>
                    <div class="hero-video-overlay">
                        <div class="hero-content">
                            <h2 class="hero-title">SALE 50% OFF</h2>
                            <p class="hero-subtitle">Hasta agotar stock disponible</p>
                            <a href="#" class="btn btn-primary btn-hero">Ver ofertas</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Carousel -->
        <section class="categories-carousel-section py-4">
            <div class="container">
                <div class="swiper categories-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <a href="#" class="category-carousel-item">
                                <div class="category-carousel-icon">
                                    <img src="https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?w=100&h=100&fit=crop" alt="Abrigos" class="category-carousel-image">
                                </div>
                                <span class="category-carousel-name">Abrigos</span>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="#" class="category-carousel-item">
                                <div class="category-carousel-icon">
                                    <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?w=100&h=100&fit=crop" alt="Shorts" class="category-carousel-image">
                                </div>
                                <span class="category-carousel-name">Shorts</span>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="#" class="category-carousel-item">
                                <div class="category-carousel-icon">
                                    <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=100&h=100&fit=crop" alt="Lentes" class="category-carousel-image">
                                </div>
                                <span class="category-carousel-name">Lentes</span>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="#" class="category-carousel-item">
                                <div class="category-carousel-icon">
                                    <img src="https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=100&h=100&fit=crop" alt="Remeras" class="category-carousel-image">
                                </div>
                                <span class="category-carousel-name">Remeras</span>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="#" class="category-carousel-item">
                                <div class="category-carousel-icon">
                                    <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?w=100&h=100&fit=crop" alt="Zapatos" class="category-carousel-image">
                                </div>
                                <span class="category-carousel-name">Zapatos</span>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="#" class="category-carousel-item">
                                <div class="category-carousel-icon">
                                    <img src="https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=100&h=100&fit=crop" alt="Carteras" class="category-carousel-image">
                                </div>
                                <span class="category-carousel-name">Carteras</span>
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="#" class="category-carousel-item">
                                <div class="category-carousel-icon">
                                    <img src="https://images.unsplash.com/photo-1603217039863-aa853ba3a22b?w=100&h=100&fit=crop" alt="Camisas" class="category-carousel-image">
                                </div>
                                <span class="category-carousel-name">Camisas</span>
                            </a>
                        </div>
                    </div>
                    <div class="swiper-button-prev categories-btn-prev"></div>
                    <div class="swiper-button-next categories-btn-next"></div>
                </div>
            </div>
        </section>

        <!-- Products Section (4 productos con carrusel) -->
        <section class="products-section py-5">
            <div class="container">
                <div class="section-header mb-4">
                    <h2 class="section-title">Destacados</h2>
                </div>
                <div class="swiper products-swiper">
                    <div class="swiper-wrapper">
                        @php
                        $productos = [
                            ['nombre' => 'Camisa de Jean', 'precio' => 55000, 'precio_antes' => 75000, 'descuento' => '27% OFF', 'imagen' => 'https://images.unsplash.com/photo-1626497764746-6dc36546b388?w=400&h=400&fit=crop'],
                            ['nombre' => 'Top manga larga', 'precio' => 32000, 'precio_antes' => 40000, 'descuento' => '20% OFF', 'imagen' => 'https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?w=400&h=400&fit=crop'],
                            ['nombre' => 'Pantalón oxford crema', 'precio' => 55000, 'precio_antes' => 110000, 'descuento' => '50% OFF', 'imagen' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&h=400&fit=crop'],
                            ['nombre' => 'Remera con mangas de seda', 'precio' => 32000, 'precio_antes' => 40000, 'descuento' => '20% OFF', 'imagen' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=400&h=400&fit=crop'],
                        ];
                        @endphp

                        @foreach($productos as $producto)
                        <div class="swiper-slide">
                            <div class="product-card-square">
                                <div class="product-image-container-square">
                                    <span class="product-badge-square badge-green">{{ $producto['descuento'] }}</span>
                                    <img src="{{ $producto['imagen'] }}" alt="{{ $producto['nombre'] }}" class="product-image-square">
                                </div>
                                <div class="product-info-square">
                                    <span class="product-badge-text-square">LLEVA 2 Y PAGA 1</span>
                                    <h4 class="product-name-square">{{ $producto['nombre'] }}</h4>
                                    <div class="product-price-square">
                                        <span class="price-old-square">${{ number_format($producto['precio_antes'], 0, ',', '.') }}</span>
                                        <span class="price-current-square">${{ number_format($producto['precio'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="product-installments-square">3 x ${{ number_format($producto['precio'] / 3, 0, ',', '.') }} sin interés</div>
                                    <button class="btn btn-comprar-square w-100">Comprar</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev products-btn-prev"></div>
                    <div class="swiper-button-next products-btn-next"></div>
                </div>
            </div>
        </section>

        <!-- Three Image Cards Section -->
        <section class="three-cards-section py-5">
            <div class="container">
                <div class="row ">
                    <div class="col-md-4">
                        <div class="image-card-square">
                            <img src="https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=600&h=600&fit=crop" alt="Vestidos" class="image-card-image">
                            <div class="image-card-overlay">
                                <h3 class="image-card-title">Vestidos</h3>
                                <p class="image-card-subtitle">Miles de vestidos para este verano</p>
                                <a href="#" class="btn btn-light-card">Ver más</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="image-card-square">
                            <img src="https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=600&h=600&fit=crop" alt="Comfort" class="image-card-image">
                            <div class="image-card-overlay">
                                <h3 class="image-card-title">Comfort</h3>
                                <p class="image-card-subtitle">Toda nuestra selección: ropa cómoda</p>
                                <a href="#" class="btn btn-light-card">Ver más</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="image-card-square">
                            <img src="https://images.unsplash.com/photo-1562157873-818bc0726f68?w=600&h=600&fit=crop" alt="De vestir" class="image-card-image">
                            <div class="image-card-overlay">
                                <h3 class="image-card-title">De vestir</h3>
                                <p class="image-card-subtitle">Todos los looks para lucir elegante</p>
                                <a href="#" class="btn btn-light-card">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Lo Nuevo Section -->
        <section class="lo-nuevo-section py-5">
            <div class="container">
                <div class="section-header mb-4">
                    <h2 class="section-title">Lo nuevo</h2>
                </div>

                <div class="swiper lo-nuevo-swiper">
                    <div class="swiper-wrapper">
                        @php
                        $nuevos = [
                            ['nombre' => 'Camiseta con cuello', 'precio' => 25000, 'imagen' => 'https://images.unsplash.com/photo-1485231183945-fffde7cc051e?w=300&h=400&fit=crop'],
                            ['nombre' => 'Trench con cinturón', 'precio' => 55000, 'precio_antes' => 75000, 'descuento' => '73% OFF', 'imagen' => 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=300&h=400&fit=crop'],
                            ['nombre' => 'Polera desgastada con cadena', 'precio' => 20000, 'precio_antes' => 44000, 'descuento' => '55% OFF', 'imagen' => 'https://images.unsplash.com/photo-1578932750294-f5075e85f44a?w=300&h=400&fit=crop'],
                            ['nombre' => 'Remera blusa', 'precio' => 32000, 'precio_antes' => 83000, 'descuento' => '40% OFF', 'imagen' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=300&h=400&fit=crop'],
                            ['nombre' => 'Blazer entero corto', 'precio' => 55000, 'precio_antes' => 150000, 'descuento' => '63% OFF', 'imagen' => 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?w=300&h=400&fit=crop'],
                        ];
                        @endphp

                        @foreach($nuevos as $nuevo)
                        <div class="swiper-slide">
                            <div class="product-card-nuevo">
                                @if(isset($nuevo['descuento']))
                                <span class="product-badge-nuevo badge-green">GRATIS</span>
                                @endif
                                <div class="product-image-container-nuevo">
                                    <img src="{{ $nuevo['imagen'] }}" alt="{{ $nuevo['nombre'] }}" class="product-image-nuevo">
                                </div>
                                <div class="product-info-nuevo">
                                    @if(isset($nuevo['descuento']))
                                    <span class="product-badge-text-nuevo">LLEVA 2 Y PAGA 1</span>
                                    @endif
                                    <h4 class="product-name-nuevo">{{ $nuevo['nombre'] }}</h4>
                                    <div class="product-price-nuevo">
                                        @if(isset($nuevo['precio_antes']))
                                        <span class="price-old-nuevo">${{ number_format($nuevo['precio_antes'], 0, ',', '.') }}</span>
                                        @endif
                                        <span class="price-current-nuevo">${{ number_format($nuevo['precio'], 0, ',', '.') }}</span>
                                    </div>
                                    @if(isset($nuevo['descuento']))
                                    <div class="product-discount-nuevo">{{ $nuevo['descuento'] }}</div>
                                    @endif
                                    <div class="product-installments-nuevo">3 x ${{ number_format($nuevo['precio'] / 3, 0, ',', '.') }} sin interés</div>
                                    <button class="btn btn-comprar-nuevo w-100">Comprar</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev lo-nuevo-btn-prev"></div>
                    <div class="swiper-button-next lo-nuevo-btn-next"></div>
                </div>
            </div>
        </section>

        <!-- Estilo Auténtico Section -->
        <section class="estilo-autentico-section py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title mb-3">Estilo Auténtico</h2>
                    <p class="section-description">Nos dedicamos a ofrecerte prendas de calidad que no solo visten tu cuerpo, sino que también cuentan tu historia.<br>Explorá nuestra colección.</p>
                    <a href="#" class="link-conocer-mas">Conocer más</a>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="estilo-images-wrapper">
                            <div class="estilo-thumbnails">
                                <div class="estilo-thumbnail active" data-image="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&h=800&fit=crop">
                                    <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=100&h=100&fit=crop" alt="Vista 1">
                                </div>
                                <div class="estilo-thumbnail" data-image="https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=600&h=800&fit=crop">
                                    <img src="https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=100&h=100&fit=crop" alt="Vista 2">
                                </div>
                                <div class="estilo-thumbnail" data-image="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&h=800&fit=crop">
                                    <img src="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=100&h=100&fit=crop" alt="Vista 3">
                                </div>
                                <div class="estilo-thumbnail" data-image="https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=600&h=800&fit=crop">
                                    <img src="https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=100&h=100&fit=crop" alt="Vista 4">
                                </div>
                            </div>
                            <div class="estilo-main-image">
                                <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&h=800&fit=crop" alt="Camisa seda fría" class="js-estilo-main-image" id="estiloMainImage">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="estilo-content">
                            <h3 class="estilo-title">Camisa seda fría</h3>
                            <div class="estilo-price">
                                <span class="price-old-estilo">$140.000,00</span>
                                <span class="price-current-estilo">$100.000,00</span>
                                <span class="price-discount-estilo">29% OFF</span>
                            </div>
                            <p class="estilo-savings">Ahorras: $40.000,00</p>
                            <p class="estilo-installments">Precio en impuestos: $82.64,00<br>3 cuotas sin interés de $33.333,33</p>
                            <div class="estilo-options mb-3">
                                <p class="mb-2"><strong>Talle:</strong> S</p>
                                <div class="talle-options">
                                    <button class="talle-btn">S</button>
                                    <button class="talle-btn">M</button>
                                    <button class="talle-btn">L</button>
                                    <button class="talle-btn">XL</button>
                                    <button class="talle-btn">XXL</button>
                                </div>
                            </div>
                            <div class="estilo-options mb-3">
                                <p class="mb-2"><strong>Color:</strong> Rosa</p>
                                <div class="color-options">
                                    <button class="color-btn" style="background-color: #f5d7e3;"></button>
                                    <button class="color-btn" style="background-color: #e8d5d5;"></button>
                                    <button class="color-btn" style="background-color: #d4b5a0;"></button>
                                    <button class="color-btn" style="background-color: #e0c9c9;"></button>
                                </div>
                            </div>
                            <div class="estilo-quantity mb-3">
                                <button class="qty-btn qty-minus">-</button>
                                <input type="number" value="1" class="qty-input" min="1">
                                <button class="qty-btn qty-plus">+</button>
                            </div>
                            <button class="btn btn-agregar-carrito w-100">Agregar al carrito</button>
                            <p class="estilo-stock">en stock</p>
                            <div class="estilo-description mt-3">
                                <p class="estilo-description-text">Camisa confeccionada en seda fría de alta calidad. Diseño elegante y versátil, perfecto para cualquier ocasión. Disponible en varios colores y talles.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3 Cuotas / Envío / WhatsApp Section -->
        <section class="features-banner-section py-4">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="feature-banner-item">
                            <svg class="feature-banner-icon mb-2"><use xlink:href="#credit-card"></use></svg>
                            <h5 class="feature-banner-title">3 cuotas sin interés</h5>
                            <p class="feature-banner-text">Con todas las tarjetas de crédito</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-banner-item">
                            <svg class="feature-banner-icon mb-2"><use xlink:href="#truck"></use></svg>
                            <h5 class="feature-banner-title">Envío gratis</h5>
                            <p class="feature-banner-text">En compras superiores a $50.000</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-banner-item">
                            <svg class="feature-banner-icon mb-2"><use xlink:href="#whatsapp"></use></svg>
                            <h5 class="feature-banner-title">Asesorate por WhatsApp</h5>
                            <p class="feature-banner-text">¡Comunicate con nosotros!</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nos Acompañan Section -->
        <section class="brands-section py-5">
            <div class="container text-center">
                <h3 class="brands-title mb-4">Nos acompañan</h3>
                <div class="brands-logos">
                    <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=YSL" alt="YSL" class="brand-logo">
                    <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=ZARA" alt="ZARA" class="brand-logo">
                    <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=CK" alt="CK" class="brand-logo">
                    <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=Bershka" alt="Bershka" class="brand-logo">
                    <img src="https://via.placeholder.com/120x50/f0f0f0/999999?text=Levis" alt="Levis" class="brand-logo">
                </div>
            </div>
        </section>

        <!-- Liquidación en Carteras (Video Section) -->
        <section class="liquidacion-video-section py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="liquidacion-content">
                            <h2 class="liquidacion-title">Liquidación en carteras</h2>
                            <p class="liquidacion-subtitle">30% OFF en toda la colección Otoño/Invierno</p>
                            <a href="#" class="btn btn-primary btn-liquidacion">Ver ofertas</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="liquidacion-video-wrapper">
                            <video class="liquidacion-video" controls poster="https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=800&h=600&fit=crop">
                                <source src="https://d26lpennugtm8s.cloudfront.net/stores/004/486/324/themes/common/video-1735055695-1735055695.mp4" type="video/mp4">
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Ofertas Section -->
        <section class="ofertas-section py-5">
            <div class="container">
                <div class="section-header mb-4">
                    <h2 class="section-title">Ofertas</h2>
                </div>

                <div class="swiper ofertas-swiper">
                    <div class="swiper-wrapper">
                        @php
                        $ofertas = [
                            ['nombre' => 'Vestido largo Lino', 'precio' => 87000, 'precio_antes' => 120000, 'descuento' => '28% OFF', 'stock' => 'Solo quedan 3 en stock!', 'imagen' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=300&h=400&fit=crop'],
                            ['nombre' => 'Conjunto Pink', 'precio' => 70000, 'precio_antes' => 90000, 'descuento' => '13% OFF', 'imagen' => 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?w=300&h=400&fit=crop'],
                            ['nombre' => 'Vestido largo Flowers', 'precio' => 60000, 'precio_antes' => 110000, 'descuento' => '23% OFF', 'stock' => 'Solo quedan 2 en stock!', 'imagen' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=300&h=400&fit=crop'],
                            ['nombre' => 'Camisa seda fría', 'precio' => 100000, 'precio_antes' => 140000, 'descuento' => '20% MO MAS', 'imagen' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=300&h=400&fit=crop'],
                            ['nombre' => 'Blazer entero corto', 'precio' => 55000, 'precio_antes' => 150000, 'descuento' => '63% OFF', 'imagen' => 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?w=300&h=400&fit=crop'],
                        ];
                        @endphp

                        @foreach($ofertas as $oferta)
                        <div class="swiper-slide">
                            <div class="product-card-oferta">
                                <span class="product-badge-oferta badge-green">GRATIS</span>
                                <div class="product-image-container-oferta">
                                    <img src="{{ $oferta['imagen'] }}" alt="{{ $oferta['nombre'] }}" class="product-image-oferta">
                                </div>
                                <div class="product-info-oferta">
                                    <span class="product-badge-text-oferta">LLEVA 2 Y PAGA 1</span>
                                    <h4 class="product-name-oferta">{{ $oferta['nombre'] }}</h4>
                                    <div class="product-price-oferta">
                                        <span class="price-old-oferta">${{ number_format($oferta['precio_antes'], 0, ',', '.') }}</span>
                                        <span class="price-current-oferta">${{ number_format($oferta['precio'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="product-discount-oferta">{{ $oferta['descuento'] }}</div>
                                    @if(isset($oferta['stock']))
                                    <div class="product-stock-alert">{{ $oferta['stock'] }}</div>
                                    @endif
                                    <div class="product-installments-oferta">3 x ${{ number_format($oferta['precio'] / 3, 0, ',', '.') }} sin interés</div>
                                    <button class="btn btn-comprar-oferta w-100">Comprar</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev ofertas-btn-prev"></div>
                    <div class="swiper-button-next ofertas-btn-next"></div>
                </div>
            </div>
        </section>

        <!-- Opiniones de Clientes Section -->
        <section class="testimonials-section py-5 bg-light">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">Opiniones de clientes</h2>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <img src="https://i.pravatar.cc/60?img=1" alt="Cami" class="testimonial-avatar">
                                <div class="testimonial-author">
                                    <h5 class="testimonial-name">Cami</h5>
                                    <div class="testimonial-stars">
                                        ★★★★★
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-body">
                                <h4 class="testimonial-title">Variedad de colores y talles!</h4>
                                <p class="testimonial-text">Camisetas de calidad, buena tela, tallaje real, cómodas y bien ajustadas. Las divertidas son que las colores no son 100% como en fotos.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <img src="https://i.pravatar.cc/60?img=5" alt="Laura" class="testimonial-avatar">
                                <div class="testimonial-author">
                                    <h5 class="testimonial-name">Laura</h5>
                                    <div class="testimonial-stars">
                                        ★★★★★
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-body">
                                <h4 class="testimonial-title">Buena atención, llegó rápido</h4>
                                <p class="testimonial-text">Me atendieron re rápido y me ayudaron con la elección para un regalo. Las super recomiendo!</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <img src="https://i.pravatar.cc/60?img=9" alt="Patri" class="testimonial-avatar">
                                <div class="testimonial-author">
                                    <h5 class="testimonial-name">Patri</h5>
                                    <div class="testimonial-stars">
                                        ★★★★★
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-body">
                                <h4 class="testimonial-title">La calidad es excelente!</h4>
                                <p class="testimonial-text">Compré online y lo que llegó es idéntico a lo que ves en la web, me encantó!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Instagram Section -->
        <section class="instagram-section py-5">
            <div class="container text-center">
                <div class="instagram-header mb-4">
                    <svg class="instagram-icon mb-3"><use xlink:href="#instagram"></use></svg>
                    <h3 class="instagram-title">Seguinos en @brasilia.theme.fashion</h3>
                </div>
                <div class="instagram-grid">
                    <div class="instagram-item">
                        <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=300&h=300&fit=crop" alt="Instagram" class="instagram-image">
                    </div>
                    <div class="instagram-item">
                        <img src="https://images.unsplash.com/photo-1445205170230-053b83016050?w=300&h=300&fit=crop" alt="Instagram" class="instagram-image">
                    </div>
                    <div class="instagram-item">
                        <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=300&h=300&fit=crop" alt="Instagram" class="instagram-image">
                    </div>
                    <div class="instagram-item">
                        <img src="https://images.unsplash.com/photo-1591369822096-ffd140ec948f?w=300&h=300&fit=crop" alt="Instagram" class="instagram-image">
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="newsletter-section-brasilia py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="newsletter-image-wrapper">
                            <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=600&h=800&fit=crop" alt="Newsletter" class="newsletter-image-brasilia">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="newsletter-content-brasilia">
                            <h3 class="newsletter-title-brasilia">Newsletter</h3>
                            <p class="newsletter-text-brasilia">¿Querés recibir nuestras ofertas? ¡Registrate ya mismo y comenzá a disfrutarlas!</p>
                            <form class="newsletter-form-brasilia">
                                <div class="input-group">
                                    <input type="email" class="form-control newsletter-input-brasilia" placeholder="Email" required>
                                    <button class="btn btn-primary newsletter-btn-brasilia" type="submit">Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer-brasilia py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <!-- Logo -->
                    <div class="footer-logo mb-4">
                        <img src="https://dcdn-us.mitiendanube.com/stores/004/486/324/themes/common/logo-429079951-1719412309-105224c351f272540c4b787ddee151421719412309-480-0.webp" alt="Brasilia" class="footer-logo-img" width="150">
                    </div>
                    <p class="footer-description">Nuestra tienda ofrece una variada colección de productos frescos y de mayor calidad en indumentaria, todos enfocados en vestir tu cuerpo.</p>
                    <!-- Social Links -->
                    <div class="footer-social mt-3">
                        <a href="#" class="footer-social-link"><svg class="icon-inline"><use xlink:href="#instagram"></use></svg></a>
                        <a href="#" class="footer-social-link"><svg class="icon-inline"><use xlink:href="#facebook-f"></use></svg></a>
                        <a href="#" class="footer-social-link"><svg class="icon-inline"><use xlink:href="#twitter"></use></svg></a>
                        <a href="#" class="footer-social-link"><svg class="icon-inline"><use xlink:href="#whatsapp"></use></svg></a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <h5 class="footer-title">Categorías</h5>
                    <ul class="footer-links">
                        <li><a href="#">Lo nuevo</a></li>
                        <li><a href="#">Liquidación</a></li>
                        <li><a href="#">Ofertas</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-6">
                    <h5 class="footer-title">Información</h5>
                    <ul class="footer-links">
                        <li><a href="#">Medios de pago</a></li>
                        <li><a href="#">Cómo comprar</a></li>
                        <li><a href="#">Preguntas frecuentes</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-6">
                    <h5 class="footer-title">Newsletter</h5>
                    <p class="footer-newsletter-text">Sé parte de nuestra comunidad y recibí nuestras novedades.</p>
                    <form class="footer-newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control footer-newsletter-input" placeholder="Email" required>
                            <button class="btn btn-primary footer-newsletter-btn" type="submit">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="footer-divider my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="footer-info mb-2">
                        <svg class="icon-inline mr-2"><use xlink:href="#phone"></use></svg>
                        +54 911 4545-4545
                    </p>
                    <p class="footer-info mb-2">
                        <svg class="icon-inline mr-2"><use xlink:href="#email"></use></svg>
                        brasilia@tiendanube.com
                    </p>
                    <p class="footer-info mb-0">
                        <svg class="icon-inline mr-2"><use xlink:href="#store"></use></svg>
                        Cabildo 4000, CABA
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-payments mb-3">
                        <img src="https://d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/visa@2x.png" alt="Visa" class="payment-logo">
                        <img src="https://d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/mastercard@2x.png" alt="Mastercard" class="payment-logo">
                        <img src="https://d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/amex@2x.png" alt="Amex" class="payment-logo">
                        <img src="https://d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/mercadopago@2x.png" alt="MercadoPago" class="payment-logo">
                    </div>
                </div>
            </div>
            <hr class="footer-divider my-3">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="footer-copyright mb-0">
                        Defensa de las y los consumidores. Para reclamos <a href="#" class="footer-copyright-link">ingresá acá</a> | <a href="#" class="footer-copyright-link">Botón de arrepentimiento</a><br>
                        Copyright © Brasilia Theme - 2778777625 - 2025. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Modal -->
    <div class="modal fade" id="modal-cart" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-end">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Carrito de compras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="empty-cart text-center py-5">
                        <svg class="icon-inline icon-xl mb-3"><use xlink:href="#cart"></use></svg>
                        <p>El carrito de compras está vacío.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Modal -->
    <div class="modal fade" id="nav-hamburger" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Menú</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <nav class="mobile-nav">
                        <ul class="mobile-nav-list">
                            <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Categorías</a></li>
                            <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Lo nuevo</a></li>
                            <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Liquidación</a></li>
                            <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Ofertas</a></li>
                            <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Medios de pago</a></li>
                            <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Cómo comprar</a></li>
                            <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Preguntas frecuentes</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/brasilia-theme.js') }}"></script>
</body>
</html>
