<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vestido largo Lino - Brasilia Theme</title>

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
                            <a href="#" class="nav-list-link">Liquidación</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <?php
    // Datos hardcodeados del producto
    $producto = [
        'nombre' => 'Vestido largo Lino',
        'precio' => 87000,
        'precio_antes' => 120000,
        'descuento' => 28,
        'descripcion' => 'El diseño presenta una silueta fluida y relajada que se adapta perfectamente a la estación, con una caída suave que añade movimiento y elegancia a cada paso. Confeccionado en lino de alta calidad, este vestido ofrece una textura ligera y transpirable, ideal para los días cálidos.',
        'imagenes' => [
            'https://dcdn-us.mitiendanube.com/stores/004/486/324/products/producto-19-2988a7feaac8586fdf17115606618964-1024-1024.webp',
            'https://dcdn-us.mitiendanube.com/stores/004/486/324/products/producto-20-22ec3a9e9b7e1c90d417115606626023-1024-1024.webp',
            'https://dcdn-us.mitiendanube.com/stores/004/486/324/products/producto-21-a7d83c3edb1ea85f4917115606632859-1024-1024.webp',
            'https://dcdn-us.mitiendanube.com/stores/004/486/324/products/producto-22-c10a11353c1d0c4d8a17115606639412-1024-1024.webp',
        ],
        'variantes' => [
            ['talla' => 'S', 'stock' => 3],
            ['talla' => 'M', 'stock' => 5],
            ['talla' => 'L', 'stock' => 1],
            ['talla' => 'XL', 'stock' => 4],
        ],
    ];
    ?>

    <!-- Product Detail -->
    <div id="single-product" class="product-detail-page">
        <div class="container pt-3 pt-md-4 pb-4">
            <div class="row">
                <!-- Product Images -->
                <div class="col-md-7 mb-4 mb-md-0">
                    <div class="product-detail-images">
                        <!-- Main Image Slider -->
                        <div class="swiper product-main-swiper mb-3">
                            <div class="swiper-wrapper">
                                @foreach($producto['imagenes'] as $imagen)
                                <div class="swiper-slide">
                                    <div class="product-detail-image-container">
                                        <img src="{{ $imagen }}" alt="{{ $producto['nombre'] }}" class="product-detail-image">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <!-- Navigation -->
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>

                        <!-- Thumbnails -->
                        <div class="swiper product-thumbs-swiper">
                            <div class="swiper-wrapper">
                                @foreach($producto['imagenes'] as $imagen)
                                <div class="swiper-slide">
                                    <div class="product-thumb-container">
                                        <img src="{{ $imagen }}" alt="{{ $producto['nombre'] }}" class="product-thumb-image">
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
                        <h1 class="product-detail-title mb-3">{{ $producto['nombre'] }}</h1>

                        <!-- Price -->
                        <div class="product-detail-price mb-3">
                            @if(isset($producto['precio_antes']))
                            <div class="price-compare mb-1">
                                ${{ number_format($producto['precio_antes'], 0, ',', '.') }}
                            </div>
                            @endif
                            <div class="d-flex align-items-center">
                                <span class="price-current h3 mb-0">
                                    ${{ number_format($producto['precio'], 0, ',', '.') }}
                                </span>
                                @if(isset($producto['descuento']))
                                <span class="price-discount-badge ml-2">
                                    {{ $producto['descuento'] }}% OFF
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Saved Money -->
                        <div class="saved-money-message mb-3">
                            Ahorrás ${{ number_format($producto['precio_antes'] - $producto['precio'], 0, ',', '.') }}
                        </div>

                        <!-- Installments -->
                        <div class="installments-info mb-4">
                            <div class="installments-badge mb-2">
                                <svg class="icon-inline icon-sm mr-1"><use xlink:href="#credit-card"></use></svg>
                                <strong>3 cuotas sin interés de ${{ number_format($producto['precio'] / 3, 0, ',', '.') }}</strong>
                            </div>
                            <div class="installments-total">
                                <span>Total en 1 pago: </span>
                                <strong>${{ number_format($producto['precio'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="installments-cards">con todas las tarjetas.</div>
                        </div>

                        <!-- Free Shipping -->
                        <div class="free-shipping-message mb-4">
                            <svg class="icon-inline icon-lg mr-2"><use xlink:href="#truck"></use></svg>
                            <span><strong class="text-accent">Envío gratis</strong> superando los $56.000</span>
                        </div>

                        <!-- Product Form -->
                        <form id="product_form" class="product-form" method="post">
                            @csrf

                            <!-- Variants (Sizes) -->
                            <div class="product-variants mb-4">
                                <label class="variant-label mb-2">Talle:</label>
                                <div class="variant-options">
                                    @foreach($producto['variantes'] as $index => $variante)
                                    <label class="variant-option">
                                        <input type="radio" name="variant" value="{{ $variante['talla'] }}" {{ $index === 0 ? 'checked' : '' }}>
                                        <span class="variant-button">{{ $variante['talla'] }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Quantity and Add to Cart in same row -->
                            <div class="product-quantity-cart mb-4">
                                <div class="quantity-section">
                                    <label class="quantity-label mb-2">Cantidad:</label>
                                    <div class="quantity-selector">
                                        <button type="button" class="quantity-btn quantity-minus">
                                            <svg class="icon-inline"><use xlink:href="#minus"></use></svg>
                                        </button>
                                        <input type="number" name="quantity" value="1" min="1" max="10" class="quantity-input">
                                        <button type="button" class="quantity-btn quantity-plus">
                                            <svg class="icon-inline"><use xlink:href="#plus"></use></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="cart-section">
                                    <button type="submit" class="btn btn-primary btn-big">
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>

                            <!-- Guaranteed Purchase & Returns -->
                            <div class="product-guarantees mb-4">
                                <div class="guarantee-item mb-3">
                                    <div class="guarantee-icon">
                                        <svg class="icon-inline icon-lg"><use xlink:href="#security"></use></svg>
                                    </div>
                                    <div class="guarantee-text">
                                        <div class="guarantee-title">Compra protegida</div>
                                        <div class="guarantee-desc">Tus datos cuidados durante toda la compra.</div>
                                    </div>
                                </div>
                                <div class="guarantee-item">
                                    <div class="guarantee-icon">
                                        <svg class="icon-inline icon-lg"><use xlink:href="#returns"></use></svg>
                                    </div>
                                    <div class="guarantee-text">
                                        <div class="guarantee-title">Cambios y devoluciones</div>
                                        <div class="guarantee-desc">Si no te gusta, podés cambiarlo por otro o devolverlo.</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="product-description">
                        <h2 class="description-title mb-3">Descripción</h2>
                        <div class="description-content">
                            <p>{{ $producto['descripcion'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="section-title mb-4">Productos relacionados</h2>
                    <div class="swiper related-products-swiper">
                        <div class="swiper-wrapper">
                            @for($i = 0; $i < 6; $i++)
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image-wrapper">
                                        <a href="#" class="product-image-link">
                                            <div class="product-image-container-square">
                                                <img src="https://dcdn-us.mitiendanube.com/stores/004/486/324/products/producto-{{ $i + 1 }}-2988a7feaac8586fdf17115606618964-1024-1024.webp"
                                                     alt="Producto relacionado"
                                                     class="product-image">
                                            </div>
                                        </a>
                                        <span class="product-badge product-badge-discount">25% OFF</span>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-name"><a href="#">Producto Relacionado {{ $i + 1 }}</a></h3>
                                        <div class="product-price-container">
                                            <span class="product-price-compare">$65.000</span>
                                            <span class="product-price">${{ 45000 + ($i * 5000) }}</span>
                                        </div>
                                        <p class="product-installments">3 cuotas sin interés de ${{ number_format((45000 + ($i * 5000)) / 3, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
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

    <script>
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
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
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
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
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
        document.querySelector('.quantity-minus').addEventListener('click', function() {
            const input = document.querySelector('.quantity-input');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        });

        document.querySelector('.quantity-plus').addEventListener('click', function() {
            const input = document.querySelector('.quantity-input');
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.max);
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
            }
        });
    </script>
</body>
</html>
