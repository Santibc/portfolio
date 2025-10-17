<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Brasilia Theme</title>

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
    <main class="category-page">
        <div class="container py-4">
            <!-- Category Controls -->
            <div class="category-controls mb-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <h1 class="category-title">Productos</h1>
                        <p class="category-count">Mostrando <span class="js-products-count">12</span> productos</p>
                    </div>
                    <div class="col-12 col-md-6 text-md-end">
                        <div class="d-flex justify-content-md-end gap-2 align-items-center">
                            <!-- Mobile Filter Button -->
                            <button class="btn btn-outline-secondary d-md-none js-modal-open-private" data-target="#modal-filters">
                                <svg class="icon-inline mr-1"><use xlink:href="#filter"></use></svg>
                                Filtros
                            </button>
                            <!-- Sort Select (Desktop) -->
                            <div class="form-group d-none d-md-inline-block mb-0">
                                <label class="font-small d-block mb-1">Ordenar por</label>
                                <select class="js-sort-by-private form-select form-select-sm" aria-label="Ordenar por">
                                    <option value="best-selling" selected>Más vendidos</option>
                                    <option value="price-ascending">Precio: menor a mayor</option>
                                    <option value="price-descending">Precio: mayor a menor</option>
                                    <option value="alpha-ascending">A - Z</option>
                                    <option value="alpha-descending">Z - A</option>
                                    <option value="created-descending">Más nuevo al más viejo</option>
                                    <option value="created-ascending">Más viejo al más nuevo</option>
                                    <option value="user">Destacado</option>
                                </select>
                            </div>
                            <!-- Sort Button (Mobile) -->
                            <button class="btn btn-outline-secondary d-md-none js-modal-open-private" data-target="#modal-sort-by">
                                <svg class="icon-inline mr-1"><use xlink:href="#sort"></use></svg>
                                Ordenar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid: Filters + Products -->
            <div class="category-grid">
                <!-- Sidebar Filters (Desktop) -->
                <aside class="category-sidebar d-none d-md-block">
                    <h2 class="filter-title mb-4">Filtrar por</h2>

                    <!-- Categories Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Categorías</h6>
                        <ul class="filter-list">
                            <li><a href="#" class="filter-link">Camisas</a></li>
                            <li><a href="#" class="filter-link">Tops</a></li>
                            <li><a href="#" class="filter-link">Remeras</a></li>
                            <li><a href="#" class="filter-link">Vestidos</a></li>
                            <li><a href="#" class="filter-link">Abrigos</a></li>
                            <li><a href="#" class="filter-link">Conjuntos</a></li>
                            <li><a href="#" class="filter-link">Pantalones</a></li>
                        </ul>
                    </div>

                    <!-- Color Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Color</h6>
                        <div class="filter-checkboxes">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="negro">
                                <span class="checkbox-label">
                                    Negro (2)
                                    <span class="checkbox-color" style="background-color: #000000;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="beige">
                                <span class="checkbox-label">
                                    Beige (1)
                                    <span class="checkbox-color" style="background-color: #f5f5dc;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="blanco">
                                <span class="checkbox-label">
                                    Blanco (5)
                                    <span class="checkbox-color" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="celeste">
                                <span class="checkbox-label">
                                    Celeste (1)
                                    <span class="checkbox-color" style="background-color: #87ceeb;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="rosa">
                                <span class="checkbox-label">
                                    Rosa (2)
                                    <span class="checkbox-color" style="background-color: #ffc0cb;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="verde">
                                <span class="checkbox-label">
                                    Verde (1)
                                    <span class="checkbox-color" style="background-color: #90ee90;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Talle Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Talle</h6>
                        <div class="filter-checkboxes">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="s">
                                <span class="checkbox-label">S (8)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="m">
                                <span class="checkbox-label">M (8)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="l">
                                <span class="checkbox-label">L (8)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="xl">
                                <span class="checkbox-label">XL (6)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="xxl">
                                <span class="checkbox-label">XXL (4)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Precio</h6>
                        <div class="price-range-container">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" placeholder="Mínimo" id="priceMin">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" placeholder="Máximo" id="priceMax">
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary w-100">Aplicar</button>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <div class="filter-clear">
                        <button class="btn btn-link text-decoration-none js-clear-filters">Limpiar filtros</button>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="category-products">
                    <div class="products-grid">
                        @php
                        $productos = [
                            ['nombre' => 'Camisa de Jean', 'precio' => 55000, 'precio_antes' => 75000, 'descuento' => '27% OFF', 'imagen' => 'https://images.unsplash.com/photo-1626497764746-6dc36546b388?w=400&h=400&fit=crop'],
                            ['nombre' => 'Top Blanco Elegante', 'precio' => 42000, 'imagen' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=400&fit=crop'],
                            ['nombre' => 'Vestido Largo Floral', 'precio' => 85000, 'precio_antes' => 120000, 'descuento' => '29% OFF', 'imagen' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=400&fit=crop'],
                            ['nombre' => 'Camisa Rosa Seda', 'precio' => 65000, 'imagen' => 'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=400&h=400&fit=crop'],
                            ['nombre' => 'Blusa Celeste', 'precio' => 48000, 'imagen' => 'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?w=400&h=400&fit=crop'],
                            ['nombre' => 'Conjunto Deportivo', 'precio' => 72000, 'precio_antes' => 90000, 'descuento' => '20% OFF', 'imagen' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=400&h=400&fit=crop'],
                            ['nombre' => 'Camisa Lino Beige', 'precio' => 58000, 'imagen' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=400&h=400&fit=crop'],
                            ['nombre' => 'Top Negro Básico', 'precio' => 35000, 'imagen' => 'https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?w=400&h=400&fit=crop'],
                            ['nombre' => 'Vestido Midi Verde', 'precio' => 78000, 'imagen' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=400&fit=crop'],
                            ['nombre' => 'Camisa Rayada', 'precio' => 52000, 'precio_antes' => 68000, 'descuento' => '24% OFF', 'imagen' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400&h=400&fit=crop'],
                            ['nombre' => 'Blusa Manga Larga', 'precio' => 46000, 'imagen' => 'https://images.unsplash.com/photo-1581044777550-4cfa60707c03?w=400&h=400&fit=crop'],
                            ['nombre' => 'Vestido Corto Negro', 'precio' => 69000, 'imagen' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=400&h=400&fit=crop'],
                        ];
                        @endphp

                        @foreach($productos as $index => $producto)
                        <div class="product-card-cat">
                            <div class="product-image-wrapper">
                                <a href="#" class="product-image-link">
                                    <div class="product-image-container-square">
                                        <img src="{{ $producto['imagen'] }}" alt="{{ $producto['nombre'] }}" class="product-image">
                                    </div>
                                </a>
                                @if(isset($producto['descuento']))
                                <span class="product-badge product-badge-discount">{{ $producto['descuento'] }}</span>
                                @endif
                                <div class="product-actions">
                                    <button class="btn btn-primary btn-small">
                                        <span class="js-open-quickshop-wording">Comprar</span>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">
                                    <a href="#">{{ $producto['nombre'] }}</a>
                                </h3>
                                <div class="product-price-container">
                                    @if(isset($producto['precio_antes']))
                                    <span class="product-price-old">${{ number_format($producto['precio_antes'], 0, ',', '.') }}</span>
                                    @endif
                                    <span class="product-price">${{ number_format($producto['precio'], 0, ',', '.') }}</span>
                                </div>
                                <p class="product-installments">3 cuotas sin interés de ${{ number_format($producto['precio'] / 3, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <nav class="pagination-container mt-5" aria-label="Paginación de productos">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Anterior</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Filters (Mobile) -->
    <div class="modal fade" id="modal-filters" tabindex="-1" aria-labelledby="modalFiltersLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFiltersLabel">Filtrar por</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Categories Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Categorías</h6>
                        <ul class="filter-list">
                            <li><a href="#" class="filter-link">Camisas</a></li>
                            <li><a href="#" class="filter-link">Tops</a></li>
                            <li><a href="#" class="filter-link">Remeras</a></li>
                            <li><a href="#" class="filter-link">Vestidos</a></li>
                            <li><a href="#" class="filter-link">Abrigos</a></li>
                            <li><a href="#" class="filter-link">Conjuntos</a></li>
                            <li><a href="#" class="filter-link">Pantalones</a></li>
                        </ul>
                    </div>

                    <!-- Color Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Color</h6>
                        <div class="filter-checkboxes">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="negro">
                                <span class="checkbox-label">
                                    Negro (2)
                                    <span class="checkbox-color" style="background-color: #000000;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="beige">
                                <span class="checkbox-label">
                                    Beige (1)
                                    <span class="checkbox-color" style="background-color: #f5f5dc;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="blanco">
                                <span class="checkbox-label">
                                    Blanco (5)
                                    <span class="checkbox-color" style="background-color: #ffffff; border: 1px solid #ddd;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="celeste">
                                <span class="checkbox-label">
                                    Celeste (1)
                                    <span class="checkbox-color" style="background-color: #87ceeb;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="rosa">
                                <span class="checkbox-label">
                                    Rosa (2)
                                    <span class="checkbox-color" style="background-color: #ffc0cb;"></span>
                                </span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="color" value="verde">
                                <span class="checkbox-label">
                                    Verde (1)
                                    <span class="checkbox-color" style="background-color: #90ee90;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Talle Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Talle</h6>
                        <div class="filter-checkboxes">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="s">
                                <span class="checkbox-label">S (8)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="m">
                                <span class="checkbox-label">M (8)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="l">
                                <span class="checkbox-label">L (8)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="xl">
                                <span class="checkbox-label">XL (6)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="talle" value="xxl">
                                <span class="checkbox-label">XXL (4)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-section mb-4">
                        <h6 class="filter-subtitle">Precio</h6>
                        <div class="price-range-container">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" placeholder="Mínimo">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" placeholder="Máximo">
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary w-100">Aplicar</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-decoration-none js-clear-filters">Limpiar filtros</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ver productos</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sort By -->
    <div class="modal fade" id="modal-sort-by" tabindex="-1" aria-labelledby="modalSortByLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSortByLabel">Ordenar por</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="sort-options">
                        <label class="sort-option">
                            <input type="radio" name="sort" value="best-selling" checked>
                            <span class="sort-label">Más vendidos</span>
                        </label>
                        <label class="sort-option">
                            <input type="radio" name="sort" value="price-ascending">
                            <span class="sort-label">Menor precio</span>
                        </label>
                        <label class="sort-option">
                            <input type="radio" name="sort" value="price-descending">
                            <span class="sort-label">Mayor precio</span>
                        </label>
                        <label class="sort-option">
                            <input type="radio" name="sort" value="created-descending">
                            <span class="sort-label">Más nuevo al más viejo</span>
                        </label>
                        <label class="sort-option">
                            <input type="radio" name="sort" value="created-ascending">
                            <span class="sort-label">Más viejo al más nuevo</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aplicar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('tienda.partials.brasilia-footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/brasilia-theme.js') }}"></script>
</body>
</html>
