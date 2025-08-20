<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>{{ $empresa->nombre }} - Tienda Online</title>
  <meta name="description" content="{{ $empresa->descripcion }}">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link  href="{{ $empresa->logo_url }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/drift-zoom/drift-basic.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page">

  <header id="header" class="header sticky-top">
    <!-- Top Bar -->
    <div class="top-bar py-2">
      <div class="container-fluid container-xl">
        <div class="row align-items-center">
          <div class="col-lg-4 d-none d-lg-flex">
            <div class="top-bar-item">
              <i class="bi bi-telephone-fill me-2"></i>
              <span>¿Necesitas ayuda? Llámanos: </span>
              <a href="tel:{{ $empresa->telefono }}">{{ $empresa->telefono ?? '+1 (234) 567-890' }}</a>
            </div>
          </div>

          <div class="col-lg-4 col-md-12 text-center">
            <div class="announcement-slider swiper init-swiper">
              <script type="application/json" class="swiper-config">
                {
                  "loop": true,
                  "speed": 600,
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": 1,
                  "direction": "vertical",
                  "effect": "slide"
                }
              </script>
{{--               <div class="swiper-wrapper">
                <div class="swiper-slide">🚚 Envío gratis en compras mayores a $50</div>
                <div class="swiper-slide">💰 30 días de garantía de devolución</div>
                <div class="swiper-slide">🎁 20% de descuento en tu primera orden</div>
              </div> --}}
            </div>
          </div>

          <div class="col-lg-4 d-none d-lg-block">
            <div class="d-flex justify-content-end">
              <div class="top-bar-item dropdown me-3">
                <a href="#" class="" data-bs-toggle="dropdown">
                  <i class="bi bi-translate me-2"></i>ES
                </a>
{{--                 <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#"><i class="bi bi-check2 me-2 selected-icon"></i>Español</a></li>
                  <li><a class="dropdown-item" href="#">English</a></li>
                </ul> --}}
              </div>
              <div class="top-bar-item dropdown">
                <a href="#" class="" data-bs-toggle="dropdown">
                  <i class="bi bi-currency-dollar me-2"></i>COP
                </a>
{{--                 <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#"><i class="bi bi-check2 me-2 selected-icon"></i>COP</a></li>
                  <li><a class="dropdown-item" href="#">USD</a></li>
                </ul> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
      <div class="container-fluid container-xl">
        <div class="d-flex py-3 align-items-center justify-content-between">

          <!-- Logo -->
          <a href="{{ route('tienda.empresa', $empresa->slug) }}" class="logo d-flex align-items-center">
            @if($empresa->logo_url)
              <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre }}" style="max-height: 50px;">
            @else
              <h1 class="sitename">{{ $empresa->nombre }}</h1>
            @endif
          </a>

          <!-- Search -->
          <form class="search-form desktop-search-form" action="{{ route('tienda.empresa', $empresa->slug) }}" method="GET">
            <div class="input-group">
              <input type="text" name="buscar" class="form-control" placeholder="Buscar productos" value="{{ request('buscar') }}">
              <button class="btn" type="submit">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>

          <!-- Actions -->
          <div class="header-actions d-flex align-items-center justify-content-end">

            <!-- Mobile Search Toggle -->
            <button class="header-action-btn mobile-search-toggle d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-expanded="false" aria-controls="mobileSearch">
              <i class="bi bi-search"></i>
            </button>

            <!-- Account -->
{{--             <div class="dropdown account-dropdown">
              <button class="header-action-btn" data-bs-toggle="dropdown">
                <i class="bi bi-person"></i>
              </button>
              <div class="dropdown-menu">
                <div class="dropdown-header">
                  <h6>Bienvenido a <span class="sitename">{{ $empresa->nombre }}</span></h6>
                  <p class="mb-0">Accede a tu cuenta y gestiona tus pedidos</p>
                </div>
                <div class="dropdown-body">
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-person-circle me-2"></i>
                    <span>Mi Perfil</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-bag-check me-2"></i>
                    <span>Mis Pedidos</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-heart me-2"></i>
                    <span>Lista de Deseos</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-gear me-2"></i>
                    <span>Configuración</span>
                  </a>
                </div>
                <div class="dropdown-footer">
                  <a href="#" class="btn btn-primary w-100 mb-2">Iniciar Sesión</a>
                  <a href="#" class="btn btn-outline-primary w-100">Registrarse</a>
                </div>
              </div>
            </div> --}}

            <!-- Cart -->
            <a href="{{ route('tienda.carrito', $empresa->slug) }}" class="header-action-btn">
              <i class="bi bi-cart3"></i>
              @if($carrito->total_items > 0)
                <span class="badge">{{ $carrito->total_items }}</span>
              @endif
            </a>

            <!-- Mobile Navigation Toggle -->
            <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>

          </div>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="header-nav">
      <div class="container-fluid container-xl position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="{{ route('tienda.empresa', $empresa->slug) }}" class="active">Inicio</a></li>
            <li><a href="#about">Acerca de</a></li>
            <li><a href="#productos">Productos</a></li>
            <li><a href="#categorias">Categorías</a></li>
            <li><a href="#contacto">Contacto</a></li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Mobile Search Form -->
    <div class="collapse" id="mobileSearch">
      <div class="container">
        <form class="search-form" action="{{ route('tienda.empresa', $empresa->slug) }}" method="GET">
          <div class="input-group">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar productos" value="{{ request('buscar') }}">
            <button class="btn" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

  </header>

  <main class="main">
    <section id="hero" class="hero section">

      <div class="hero-container">
        <div class="hero-content">
          <div class="content-wrapper" data-aos="fade-up" data-aos-delay="100">
            <h1 class="hero-title">{{ $empresa->nombre }}</h1>
            <p class="hero-description">{{ $empresa->descripcion ?? 'Tu tienda online de confianza.' }}</p>
            <div class="hero-actions" data-aos="fade-up" data-aos-delay="200">
              <a href="#products" class="btn-primary">Comprar ahora</a>
              <a href="#categories" class="btn-secondary">Categorias</a>
            </div>
            <div class="features-list" data-aos="fade-up" data-aos-delay="300">
              <div class="feature-item">
                <i class="bi bi-truck"></i>
                <span>Envío</span>
              </div>
              <div class="feature-item">
                <i class="bi bi-award"></i>
                <span>Certificado</span>
              </div>
              <div class="feature-item">
                <i class="bi bi-headset"></i>
                <span>Llamanos</span>
              </div>
            </div>
          </div>
        </div>

{{-- HERO VISUALS: primeros 3 productos si existen --}}
@if(($productos->count() ?? 0) > 0)
  @php
    // Si $productos es un paginator, obtén la colección; si no, úsala tal cual
    $base = method_exists($productos, 'getCollection') ? $productos->getCollection() : $productos;
    $destacados = $base->take(3)->values();
  @endphp

  <div class="hero-visuals">
    <div class="product-showcase" data-aos="fade-left" data-aos-delay="200">
      {{-- Producto destacado (el primero) --}}
      @if(isset($destacados[0]))
        <div class="product-card featured">
          <a href="{{ route('tienda.producto', [$empresa->slug, $destacados[0]->id]) }}">
            <img
              src="{{ $destacados[0]->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}"
              alt="{{ $destacados[0]->nombre }}"
              class="img-fluid">
          </a>
          <div class="product-badge">Destacado</div>
          <div class="product-info">
            <h4>
              <a href="{{ route('tienda.producto', [$empresa->slug, $destacados[0]->id]) }}">
                {{ $destacados[0]->nombre }}
              </a>
            </h4>
            <div class="price">
              @if($destacados[0]->precio_actual)
                <span class="sale-price">
                  ${{ number_format($destacados[0]->precio_actual, 0, ',', '.') }}
                </span>
              @else
                <span class="text-muted">Precio no disponible</span>
              @endif
            </div>
          </div>
        </div>
      @endif

      {{-- Grid de 2 minis (segundo y tercero si existen) --}}
      <div class="product-grid">
        @if(isset($destacados[1]))
          <div class="product-mini" data-aos="zoom-in" data-aos-delay="400">
            <a href="{{ route('tienda.producto', [$empresa->slug, $destacados[1]->id]) }}">
              <img
                src="{{ $destacados[1]->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}"
                alt="{{ $destacados[1]->nombre }}"
                class="img-fluid">
            </a>
            @if($destacados[1]->precio_actual)
              <span class="mini-price">
                ${{ number_format($destacados[1]->precio_actual, 0, ',', '.') }}
              </span>
            @endif
          </div>
        @endif

        @if(isset($destacados[2]))
          <div class="product-mini" data-aos="zoom-in" data-aos-delay="500">
            <a href="{{ route('tienda.producto', [$empresa->slug, $destacados[2]->id]) }}">
              <img
                src="{{ $destacados[2]->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}"
                alt="{{ $destacados[2]->nombre }}"
                class="img-fluid">
            </a>
            @if($destacados[2]->precio_actual)
              <span class="mini-price">
                ${{ number_format($destacados[2]->precio_actual, 0, ',', '.') }}
              </span>
            @endif
          </div>
        @endif
      </div>
    </div>

    {{-- Iconos flotantes (solo si hay productos) --}}
    <div class="floating-elements">
      <div class="floating-icon cart" data-aos="fade-up" data-aos-delay="600">
        <i class="bi bi-cart3"></i>
        <span class="notification-dot">3</span>
      </div>
      <div class="floating-icon wishlist" data-aos="fade-up" data-aos-delay="700">
        <i class="bi bi-heart"></i>
      </div>
      <div class="floating-icon search" data-aos="fade-up" data-aos-delay="800">
        <i class="bi bi-search"></i>
      </div>
    </div>
  </div>
@endif

      </div>

    </section><!-- /Hero Section -->
    <!-- Hero Section with Carousel -->
@if($empresa->carruselImagenesActivas->count() > 0)
<section id="portada" class="section p-0">
  <div id="heroCarousel" 
       class="carousel slide carousel-fade hero-carousel"
       data-bs-ride="carousel" 
       data-bs-interval="4500" 
       data-bs-pause="hover">

    @if($empresa->carruselImagenesActivas->count() > 1)
      <div class="carousel-indicators">
        @foreach($empresa->carruselImagenesActivas as $index => $img)
          <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                  class="{{ $index === 0 ? 'active' : '' }}"
                  aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                  aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
      </div>
    @endif

    <div class="carousel-inner">
      @foreach($empresa->carruselImagenesActivas as $index => $imagen)
        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
             style="background-image: url('{{ $imagen->imagen_url }}');">
          <div class="hero-overlay"></div>

          <div class="container h-100">
            <div class="d-flex align-items-center justify-content-center h-100">
              <div class="hero-caption text-center shadow rounded-4 p-4 p-md-5">
                @if($imagen->titulo)
                  <h2 class="fw-bold mb-2 hero-title">{{ $imagen->titulo }}</h2>
                @endif
                @if($imagen->descripcion)
                  <p class="mb-3 hero-desc">{{ $imagen->descripcion }}</p>
                @endif
                @if($imagen->link)
                  <a href="{{ $imagen->link }}" class="btn btn-primary btn-lg">Ver más</a>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    @if($empresa->carruselImagenesActivas->count() > 1)
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
      </button>
    @endif
  </div>
</section>
@endif



    <!-- Promo Cards Section - Categorías -->
<section id="categorias" class="promo-cards section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    @php
      // Ordenamos por 'orden' y tomamos máximo 5 para este bloque
      $cats = $categorias->sortBy('orden')->values()->take(5);
      $featured = $cats->first();
      $rest = $cats->slice(1);

      // Clases de color como el template original
      $colorClasses = ['cat-men','cat-kids','cat-cosmetics','cat-accessories'];
    @endphp

    @if($cats->isEmpty())
      <div class="row">
        <div class="col-12 text-center">
          <p>No hay categorías disponibles en este momento.</p>
        </div>
      </div>
    @else
      <div class="row gy-4">

        {{-- Columna izquierda: categoría destacada --}}
        <div class="col-lg-6">
          <div class="category-featured" data-aos="fade-right" data-aos-delay="200">
            @if($featured && $featured->imagen)
              <div class="category-image">
                <img
                  src="{{ asset($featured->imagen) }}"
                  alt="{{ $featured->nombre }}"
                  class="img-fluid"
                  loading="lazy">
              </div>
            @endif
            <div class="category-content {{ !($featured && $featured->imagen) ? 'no-image' : '' }}">
              <span class="category-tag">Destacado</span>
              <h2>{{ $featured->nombre }}</h2>
              <p>{{ $featured->descripcion ?? 'Descubre nuestra selección de productos en esta categoría.' }}</p>
              <a href="{{ route('tienda.empresa', [$empresa->slug, 'categoria' => $featured->id]) }}" class="btn-shop">
                Explorar Categoría <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        {{-- Columna derecha: hasta 4 categorías en grid 2x2 --}}
        <div class="col-lg-6">
          <div class="row gy-4">
            @foreach($rest as $i => $categoria)
              @php
                $catColor = $colorClasses[$i % count($colorClasses)];
                $delay = 300 + ($i * 100);
              @endphp
              <div class="col-xl-6">
                <div class="category-card {{ $catColor }} {{ !$categoria->imagen ? 'no-image' : '' }}" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                  @if($categoria->imagen)
                    <div class="category-image">
                      <img
                        src="{{ asset($categoria->imagen) }}"
                        alt="{{ $categoria->nombre }}"
                        class="img-fluid"
                        loading="lazy">
                    </div>
                  @endif
                  <div class="category-content">
                    <h4>{{ $categoria->nombre }}</h4>
                    <p>{{ $categoria->productos_count ?? 0 }} productos</p>
                    <a href="{{ route('tienda.empresa', [$empresa->slug, 'categoria' => $categoria->id]) }}" class="card-link">
                      Ver Productos <i class="bi bi-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

      </div>
    @endif
  </div>
</section>

<style>
/* Ajustes para las imágenes de categorías */
.promo-cards .category-featured {
  min-height: 400px; /* Reducido de 500px */
}

.promo-cards .category-featured .category-image {
  position: absolute;
  top: 0;
  right: 0;
  width: 55%;
  height: 100%;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.promo-cards .category-featured .category-image img {
  width: 100%;
  height: 100%;
  object-fit: contain; /* Cambiado de cover a contain */
  object-position: center;
  transition: transform 0.6s ease;
  padding: 20px; /* Añade espacio alrededor de la imagen */
}

/* Ajustes cuando no hay imagen en categoría destacada */
.promo-cards .category-featured .category-content.no-image {
  max-width: 100%;
  padding: 50px 60px;
  text-align: center;
  justify-content: center;
  background: linear-gradient(135deg, #f8f5ff 0%, #f0ebff 100%);
}

/* Ajustes para las categorías pequeñas */
.promo-cards .category-card {
  height: 200px; /* Reducido de 240px */
}

.promo-cards .category-card .category-image {
  position: absolute;
  top: 0;
  right: 0;
  width: 45%; /* Reducido de 50% */
  height: 100%;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.promo-cards .category-card .category-image img {
  width: 100%;
  height: 100%;
  object-fit: contain; /* Cambiado de cover a contain */
  padding: 15px; /* Añade espacio alrededor */
  transition: transform 0.6s ease;
}

/* Ajustes cuando no hay imagen en categorías pequeñas */
.promo-cards .category-card.no-image {
  text-align: center;
}

.promo-cards .category-card.no-image .category-content {
  width: 100%;
  text-align: center;
  padding: 30px 20px;
}

/* Hover effects para imágenes */
.promo-cards .category-featured:hover .category-image img,
.promo-cards .category-card:hover .category-image img {
  transform: scale(1.05); /* Reducido de 1.05 para evitar recortes */
}

/* Responsive */
@media (max-width: 991.98px) {
  .promo-cards .category-featured {
    height: 380px;
  }
  
  .promo-cards .category-featured .category-image img {
    padding: 15px;
  }
}

@media (max-width: 767.98px) {
  .promo-cards .category-featured {
    height: auto;
    min-height: 300px;
  }

  .promo-cards .category-featured .category-image {
    position: relative;
    width: 100%;
    height: 200px;
    padding: 20px;
  }

  .promo-cards .category-featured .category-content {
    max-width: 100%;
    padding: 30px;
  }

  .promo-cards .category-card {
    height: 180px;
  }

  .promo-cards .category-card .category-image {
    width: 40%;
  }

  .promo-cards .category-card .category-image img {
    padding: 10px;
  }

  .promo-cards .category-card .category-content {
    width: 60%;
    padding: 20px;
  }
}

@media (max-width: 575.98px) {
  .promo-cards .category-card {
    height: 160px;
  }

  .promo-cards .category-card .category-content {
    width: 65%;
  }
}
</style>




    <!-- Products Section -->
    <section id="productos" class="best-sellers section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Productos</h2>
        <p>Explora nuestra selección de productos de alta calidad</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">

          @forelse($productos as $producto)
          <div class="col-lg-3 col-md-6">
            <div class="product-item">
              <div class="product-image">
                @if($producto->stock_disponible <= 5 && $producto->stock_disponible > 0)
                  <div class="product-badge">¡Últimas unidades!</div>
                @elseif($producto->stock_disponible == 0 && !$producto->permitir_venta_sin_stock)
                  <div class="product-badge sale-badge">Sin Stock</div>
                @endif
                <img src="{{ $producto->url_imagen_principal }}" alt="{{ $producto->nombre }}" class="img-fluid" loading="lazy">
                <div class="product-actions">
                  <button class="action-btn wishlist-btn">
                    <i class="bi bi-heart"></i>
                  </button>
                  <button class="action-btn compare-btn">
                    <i class="bi bi-arrow-left-right"></i>
                  </button>
                  <button class="action-btn quickview-btn">
                    <i class="bi bi-zoom-in"></i>
                  </button>
                </div>
                @if($producto->tiene_variantes)
                  <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}" class="cart-btn">Ver Opciones</a>
                @else
                  <button class="cart-btn quick-add-btn" 
                          data-producto-id="{{ $producto->id }}"
                          data-precio="{{ $producto->precio_actual }}"
                          {{ (!$producto->hayStock(1) && !$producto->permitir_venta_sin_stock) ? 'disabled' : '' }}>
                    {{ (!$producto->hayStock(1) && !$producto->permitir_venta_sin_stock) ? 'Sin Stock' : 'Agregar al Carrito' }}
                  </button>
                @endif
              </div>
              <div class="product-info">
                <div class="product-category">{{ $producto->categoria->nombre }}</div>
                <h4 class="product-name">
                  <a href="{{ route('tienda.producto', [$empresa->slug, $producto->id]) }}">{{ $producto->nombre }}</a>
                </h4>
                <div class="product-rating">
                  <div class="stars">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star"></i>
                  </div>
                  <span class="rating-count">({{ rand(10, 50) }})</span>
                </div>
                @if($producto->precio_actual)
                  <div class="product-price">${{ number_format($producto->precio_actual, 0, ',', '.') }}</div>
                @else
                  <div class="product-price text-muted">Precio no disponible</div>
                @endif
              </div>
            </div>
          </div>
          @empty
          <div class="col-12">
            <div class="alert alert-info text-center">
              <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
              <p class="mb-0">No se encontraron productos.</p>
            </div>
          </div>
          @endforelse

        </div>

        <!-- Pagination -->
        @if($productos->hasPages())
        <div class="mt-5 d-flex justify-content-center">
          {{ $productos->links('pagination::bootstrap-5') }}
        </div>
        @endif

      </div>

    </section>

  </main>

  <footer id="footer" class="footer dark-background">
    <div class="footer-main">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6">
            <div class="footer-widget footer-about">
              <a href="{{ route('tienda.empresa', $empresa->slug) }}" class="logo">
                <span class="sitename">{{ $empresa->nombre }}</span>
              </a>
            

              <div class="social-links mt-4">
                <h5>Conéctate con Nosotros</h5>
                <div class="social-icons">
                  @if($empresa->facebook_url)
                    <a href="{{ $empresa->facebook_url }}" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                  @endif
                  @if($empresa->instagram_url)
                    <a href="{{ $empresa->instagram_url }}" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                  @endif
                  @if($empresa->twitter_url)
                    <a href="{{ $empresa->twitter_url }}" target="_blank" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                  @endif
                  @if($empresa->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $empresa->whatsapp) }}" target="_blank" aria-label="WhatsApp">
                      <i class="bi bi-whatsapp"></i>
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="footer-widget">
              <h4>Tienda</h4>
              <ul class="footer-links">
                <li><a href="{{ route('tienda.empresa', $empresa->slug) }}">Productos</a></li>
                <li><a href="#categorias">Categorías</a></li>
              </ul>
            </div>
          </div>



          <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="footer-widget">
              <h4>Horario</h4>
              <div class="footer-contact">
                @if($empresa->horario_atencion)
                <div class="contact-item">
                  <i class="bi bi-clock"></i>
                  <span>
                    @php
                      $dias = ['lunes' => 'Lun', 'martes' => 'Mar', 'miercoles' => 'Mié', 
                               'jueves' => 'Jue', 'viernes' => 'Vie', 'sabado' => 'Sáb', 'domingo' => 'Dom'];
                      $horarioTexto = [];
                      foreach($dias as $key => $dia) {
                        if(isset($empresa->horario_atencion[$key])) {
                          if($empresa->horario_atencion[$key]['cerrado'] ?? false) {
                            $horarioTexto[] = $dia . ': Cerrado';
                          } else {
                            $horarioTexto[] = $dia . ': ' . ($empresa->horario_atencion[$key]['apertura'] ?? '09:00') . ' - ' . 
                                            ($empresa->horario_atencion[$key]['cierre'] ?? '18:00');
                          }
                        }
                      }
                      echo implode('<br>', $horarioTexto);
                    @endphp
                  </span>
                </div>
                @endif
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="footer-widget">
              <h4>Información de Contacto</h4>
              <div class="footer-contact">
                @if($empresa->direccion)
                <div class="contact-item">
                  <i class="bi bi-geo-alt"></i>
                  <span>{{ $empresa->direccion }}</span>
                </div>
                @endif
                @if($empresa->telefono)
                <div class="contact-item">
                  <i class="bi bi-telephone"></i>
                  <span>{{ $empresa->telefono }}</span>
                </div>
                @endif
                @if($empresa->email)
                <div class="contact-item">
                  <i class="bi bi-envelope"></i>
                  <span>{{ $empresa->email }}</span>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <div class="row gy-3 align-items-center">
          <div class="col-lg-6 col-md-12">
            <div class="copyright">
              <p>© <span>Copyright</span> <strong class="sitename">{{ $empresa->nombre }}</strong>. Todos los derechos reservados.</p>
            </div>
          </div>

          <div class="col-lg-6 col-md-12">
            <div class="d-flex flex-wrap justify-content-lg-end justify-content-center align-items-center gap-4">
              <div class="payment-methods">
                <div class="payment-icons">
                  <i class="bi bi-credit-card" aria-label="Tarjeta de Crédito"></i>
                  <i class="bi bi-paypal" aria-label="PayPal"></i>
                  <i class="bi bi-cash" aria-label="Efectivo"></i>
                </div>
              </div>

              <div class="legal-links">
                <a href="#">Términos</a>
                <a href="#">Privacidad</a>
                <a href="#">Cookies</a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </footer>

  <!-- Toast Container -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="cartToast" class="toast" role="alert">
      <div class="toast-header">
        <i class="bi bi-check-circle-fill text-success me-2"></i>
        <strong class="me-auto">Carrito</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
      </div>
      <div class="toast-body"></div>
    </div>
  </div>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/drift-zoom/Drift.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <!-- jQuery for AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    $(document).ready(function() {
      // CSRF Token
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });

      // Quick add to cart
      $('.quick-add-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const btn = $(this);
        const productoId = btn.data('producto-id');
        const precio = btn.data('precio');
        
        if (!precio) {
          showToast('error', 'Este producto no tiene precio configurado');
          return;
        }
        
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
          url: "{{ route('tienda.carrito.agregar', $empresa->slug) }}",
          method: 'POST',
          data: {
            producto_id: productoId,
            cantidad: 1
          },
          success: function(response) {
            showToast('success', 'Producto agregado al carrito');
            updateCartBadge(response.total_items);
            btn.html('<i class="bi bi-check"></i> Agregado');
            setTimeout(() => {
              btn.prop('disabled', false);
              btn.html('Agregar al Carrito');
            }, 1500);
          },
          error: function(xhr) {
            const error = xhr.responseJSON?.error || 'Error al agregar al carrito';
            showToast('error', error);
            btn.prop('disabled', false);
            btn.html('Agregar al Carrito');
          }
        });
      });

      // Show toast notification
      function showToast(type, message) {
        const toastEl = document.getElementById('cartToast');
        const toast = new bootstrap.Toast(toastEl);
        
        $('.toast-body').text(message);
        if (type === 'error') {
          $('.toast-header i').removeClass('text-success').addClass('text-danger');
          $('.toast-header i').removeClass('bi-check-circle-fill').addClass('bi-exclamation-circle-fill');
        } else {
          $('.toast-header i').removeClass('text-danger').addClass('text-success');
          $('.toast-header i').removeClass('bi-exclamation-circle-fill').addClass('bi-check-circle-fill');
        }
        
        toast.show();
      }

      // Update cart badge
      function updateCartBadge(count) {
        if (count > 0) {
          if ($('.header-action-btn .badge').length) {
            $('.header-action-btn .badge').text(count);
          } else {
            $('.header-action-btn').append('<span class="badge">' + count + '</span>');
          }
        } else {
          $('.header-action-btn .badge').remove();
        }
      }
    });
  </script>

</body>

</html>