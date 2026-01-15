<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', $empresa->nombre . ' - Tienda Online')</title>

  {{-- SEO Meta Tags --}}
  @hasSection('seo')
    @yield('seo')
  @else
    @include('components.seo.meta-tags', [
        'empresa' => $empresa,
        'title' => View::yieldContent('title'),
        'description' => View::yieldContent('description')
    ])
  @endif

  {{-- Structured Data --}}
  @include('components.seo.structured-data', [
      'type' => 'LocalBusiness',
      'empresa' => $empresa
  ])
  @include('components.seo.structured-data', [
      'type' => 'WebSite',
      'empresa' => $empresa
  ])
  @stack('structured-data')

  {{-- Analytics Scripts (GA4, Facebook Pixel, GTM) --}}
  @include('components.analytics.head-scripts')

  <!-- Favicons -->
  <link href="{{ $empresa->logo_url }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/drift-zoom/drift-basic.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

  <!-- Flores Minimal Theme Styles -->
  <style>
    :root {
      --flores-primary: #1a1a1a;
      --flores-primary-hover: #333333;
      --flores-beige: #f5f5f5;
      --flores-text: #1a1a1a;
      --flores-text-light: #666666;
      --flores-bg: #FFFFFF;
      --flores-border: #e0e0e0;
      --font-serif: 'Playfair Display', Georgia, serif;
      --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      font-family: var(--font-sans);
      color: var(--flores-text);
      /* overflow-x: hidden; */ /* Comentado para permitir secciones full-width */
    }

    * {
      box-sizing: border-box;
    }

    /* ============ CONTENEDOR PERSONALIZADO ============ */
    .container {
      max-width: 100%;
      padding-left: 16px;
      padding-right: 16px;
      margin-left: auto;
      margin-right: auto;
    }

    @media (min-width: 576px) {
      .container {
        max-width: 540px;
        padding-left: 20px;
        padding-right: 20px;
      }
    }

    @media (min-width: 768px) {
      .container {
        max-width: 720px;
        padding-left: 24px;
        padding-right: 24px;
      }
    }

    @media (min-width: 992px) {
      .container {
        max-width: 960px;
      }
    }

    @media (min-width: 1200px) {
      .container {
        max-width: 1320px;
        padding-left: 40px;
        padding-right: 40px;
      }
    }

    @media (min-width: 1400px) {
      .container {
        max-width: 1400px;
        padding-left: 48px;
        padding-right: 48px;
      }
    }

    /* ============ HEADER MINIMALISTA ============ */
    .header {
      background: #ffffff;
      box-shadow: none;
      border-bottom: 1px solid #f0f0f0;
    }

    .header .top-bar {
      display: none !important;
    }

    .header .main-header {
      padding: 0;
    }

    .header-minimal {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 0;
    }

    /* Logo */
    .header-minimal .logo {
      display: flex;
      align-items: center;
      text-decoration: none;
    }

    .header-minimal .logo-img {
      height: 70px !important;
      max-height: 70px !important;
      width: auto !important;
    }

    /* Override main.css .header .main-header .logo img */
    .header .main-header .header-minimal .logo .logo-img {
      height: 70px !important;
      max-height: 70px !important;
      width: auto !important;
    }

    .header-minimal .logo-text {
      font-family: var(--font-serif);
      font-size: 20px;
      font-weight: 500;
      color: #000000;
      margin: 0;
    }

    /* Header Actions */
    .header-minimal .header-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .header-minimal .header-action-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 0;
      background: transparent;
      color: #000000;
      text-decoration: none;
      transition: color 0.2s;
      position: relative;
      border: none;
      cursor: pointer;
    }

    .header-minimal .header-action-btn:hover {
      color: #666666;
    }

    .header-minimal .header-action-btn i {
      font-size: 20px;
    }

    .header-minimal .header-action-btn .badge {
      position: absolute;
      top: 2px;
      right: 2px;
      background: #000000;
      color: white;
      font-size: 10px;
      font-weight: 600;
      padding: 2px 6px;
      border-radius: 10px;
      min-width: 18px;
      text-align: center;
    }

    /* Auth Link */
    .header-auth-link {
      font-size: 14px;
      font-weight: 400;
      color: #000000;
      text-decoration: none;
      padding: 8px 0 8px 16px;
      white-space: nowrap;
      transition: color 0.2s;
    }

    .header-auth-link:hover {
      color: #666666;
    }

    .header-auth-link.dropdown-toggle::after {
      display: none;
    }

    /* Mobile Toggle */
    .header-minimal .mobile-menu-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 24px;
      color: #000000;
      cursor: pointer;
      padding: 8px;
    }

    /* Hide old header elements */
    .header .header-nav,
    .header .search-form.desktop-search-form,
    .header .old-header-actions {
      display: none !important;
    }

    @media (max-width: 767px) {
      .header-minimal .mobile-menu-toggle {
        display: flex;
      }

      .header-minimal {
        padding: 12px 0;
      }

      .header-minimal .logo-img {
        height: 45px !important;
        max-height: 45px !important;
      }

      .header-minimal .logo-text {
        font-size: 16px;
      }

      .header-auth-link {
        display: none;
      }

      .header-minimal .header-action-btn {
        width: 36px;
        height: 36px;
      }

      .header-minimal .header-action-btn i {
        font-size: 18px;
      }

      .header-minimal .header-actions {
        gap: 4px;
      }
    }

    /* Mobile Navigation Overlay */
    .mobile-nav-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      z-index: 9998;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
    }

    .mobile-nav-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .mobile-nav-menu {
      position: fixed;
      top: 0;
      right: 0;
      width: 300px;
      height: 100%;
      background: white;
      z-index: 9999;
      padding: 24px;
      overflow-y: auto;
      transform: translateX(100%);
      visibility: hidden;
      transition: transform 0.3s ease, visibility 0.3s;
    }

    .mobile-nav-menu.active {
      transform: translateX(0);
      visibility: visible;
    }

    .mobile-nav-menu .close-btn {
      position: absolute;
      top: 16px;
      right: 16px;
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: var(--flores-text);
      z-index: 10;
      padding: 8px;
      line-height: 1;
    }

    .mobile-nav-menu .close-btn:hover {
      color: var(--flores-primary);
    }

    .mobile-nav-menu .nav-links {
      display: flex;
      flex-direction: column;
      gap: 0;
      margin-top: 48px;
      list-style: none;
      padding: 0;
    }

    .mobile-nav-menu .nav-links li {
      border-bottom: 1px solid var(--flores-border);
    }

    .mobile-nav-menu .nav-links a {
      display: block;
      padding: 16px 0;
      font-size: 16px;
      font-weight: 500;
      color: var(--flores-text);
      text-decoration: none;
    }

    .mobile-nav-menu .nav-links a:hover {
      color: var(--flores-primary);
    }

    /* Mobile Menu - Centrado */
    @media (max-width: 991px) {
      .mobile-nav-menu {
        text-align: center;
      }

      .mobile-nav-menu .nav-links {
        text-align: center;
      }

      .mobile-nav-menu .nav-links a {
        text-align: center;
        display: block;
      }
    }

    /* ============ BOTONES ============ */
    .btn-flores {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 24px;
      background: var(--flores-primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
      cursor: pointer;
    }

    .btn-flores:hover {
      background: var(--flores-primary-hover);
      color: white;
    }

    .btn-flores-outline {
      background: transparent;
      border: 1px solid var(--flores-primary);
      color: var(--flores-primary);
    }

    .btn-flores-outline:hover {
      background: var(--flores-primary);
      color: white;
    }

    /* ============ TYPOGRAPHY ============ */
    .text-serif {
      font-family: var(--font-serif);
    }

    .section-title-minimal {
      font-family: var(--font-serif);
      font-size: 32px;
      font-weight: 500;
      color: var(--flores-text);
      margin-bottom: 8px;
    }

    .section-subtitle-minimal {
      font-size: 14px;
      color: var(--flores-text-light);
    }

    /* ============ FOOTER MINIMAL ============ */
    .footer-minimal {
      background: #fafafa;
      border-top: 1px solid var(--flores-border);
    }

    .footer-main-minimal {
      padding: 70px 0 50px;
    }

    .footer-brand .footer-logo {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      font-family: var(--font-serif);
      font-size: 22px;
      font-weight: 600;
      color: var(--flores-text);
      text-decoration: none;
      margin-bottom: 20px;
    }

    .footer-brand .footer-logo i {
      color: var(--flores-primary);
      font-size: 24px;
    }

    .footer-description {
      font-size: 15px;
      color: var(--flores-text-light);
      line-height: 1.7;
      margin-bottom: 24px;
      max-width: 350px;
    }

    .footer-social {
      display: flex;
      gap: 12px;
    }

    .footer-social a {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: white;
      border: 1px solid var(--flores-border);
      border-radius: 50%;
      color: var(--flores-text);
      font-size: 18px;
      transition: all 0.2s;
    }

    .footer-social a:hover {
      background: var(--flores-primary);
      border-color: var(--flores-primary);
      color: white;
      transform: translateY(-2px);
    }

    .footer-links-minimal h5,
    .footer-contact-minimal h5 {
      font-size: 16px;
      font-weight: 600;
      color: var(--flores-text);
      margin-bottom: 24px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .footer-links-minimal ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links-minimal ul li {
      margin-bottom: 14px;
    }

    .footer-links-minimal ul li a {
      font-size: 15px;
      color: var(--flores-text-light);
      text-decoration: none;
      transition: color 0.2s;
      display: inline-block;
    }

    .footer-links-minimal ul li a:hover {
      color: var(--flores-primary);
      transform: translateX(3px);
    }

    .contact-item-minimal {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      margin-bottom: 18px;
      font-size: 15px;
      color: var(--flores-text-light);
    }

    .contact-item-minimal i {
      color: var(--flores-primary);
      margin-top: 3px;
      font-size: 18px;
    }

    .footer-bottom-minimal {
      padding: 28px 0;
      border-top: 1px solid var(--flores-border);
    }

    .footer-bottom-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .copyright-minimal {
      font-size: 14px;
      color: var(--flores-text-light);
      margin: 0;
    }

    .footer-legal {
      display: flex;
      gap: 28px;
    }

    .footer-legal a {
      font-size: 14px;
      color: var(--flores-text-light);
      text-decoration: none;
      transition: color 0.2s;
      font-weight: 500;
    }

    .footer-legal a:hover {
      color: var(--flores-primary);
    }

    @media (max-width: 767px) {
      .footer-main-minimal {
        padding: 40px 0 24px;
      }

      /* Centrar todo el contenido del footer en mobile */
      .footer-brand {
        text-align: center;
      }

      .footer-brand .footer-logo {
        font-size: 20px;
        justify-content: center;
      }

      .footer-brand .footer-logo i {
        font-size: 22px;
      }

      .footer-description {
        font-size: 14px;
        max-width: 100%;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
      }

      .footer-social {
        justify-content: center;
      }

      .footer-links-minimal {
        text-align: center;
      }

      .footer-links-minimal h5,
      .footer-contact-minimal h5 {
        font-size: 15px;
        margin-bottom: 16px;
        text-align: center;
      }

      .footer-links-minimal ul {
        text-align: center;
      }

      .footer-links-minimal ul li {
        margin-bottom: 10px;
      }

      .footer-links-minimal ul li a {
        font-size: 14px;
      }

      .footer-contact-minimal {
        text-align: center;
      }

      .contact-item-minimal {
        font-size: 14px;
        margin-bottom: 14px;
        justify-content: center;
        text-align: center;
      }

      .footer-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: 12px;
      }

      .footer-legal {
        justify-content: center;
        flex-wrap: wrap;
        gap: 16px;
      }

      .copyright-minimal,
      .footer-legal a {
        font-size: 13px;
      }
    }

    /* ============ FIX PARA SELECT DROPDOWN ============ */
    /* Evitar que la flecha del select se superponga con el texto */
    .form-select,
    select.form-select {
      padding-right: 2.5rem !important;
      background-position: right 0.75rem center !important;
    }
  </style>

  @stack('styles')
</head>

<body class="@yield('body-class', 'index-page')">
  {{-- GTM noscript fallback --}}
  @include('components.analytics.body-scripts')

  <header id="header" class="header sticky-top">
    <!-- Minimal Header -->
    <div class="main-header">
      <div class="container">
        <div class="header-minimal">
          <!-- Logo -->
          <a href="{{ route('home') }}" class="logo">
            @if($empresa->logo_url)
              <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre }}" class="logo-img">
            @else
              <span class="logo-text">{{ $empresa->nombre }}</span>
            @endif
          </a>

          <!-- Header Actions -->
          <div class="header-actions">
            <!-- Search -->
            <button type="button" class="header-action-btn" id="searchToggle" title="Buscar">
              <i class="bi bi-search"></i>
            </button>

            <!-- Cart -->
            <a href="{{ route('tienda.carrito') }}" class="header-action-btn" title="Carrito">
              <i class="bi bi-bag"></i>
              @if($carrito->total_items > 0)
                <span class="badge">{{ $carrito->total_items }}</span>
              @endif
            </a>

            <!-- Account -->
            @guest
              <a href="{{ route('login') }}" class="header-auth-link">Iniciar sesión</a>
            @else
              <div class="dropdown">
                <a href="#" class="header-auth-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                  {{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  @if(auth()->user()->hasRole('cliente'))
                    <li><a class="dropdown-item" href="{{ route('cliente.compras') }}"><i class="bi bi-house me-2"></i>Inicio</a></li>
                    <li><a class="dropdown-item" href="{{ route('cliente.compras') }}"><i class="bi bi-bag-check me-2"></i>Mis Compras</a></li>
                    <li><a class="dropdown-item" href="{{ route('cliente.perfil') }}"><i class="bi bi-person-gear me-2"></i>Mi Perfil</a></li>
                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-shop me-2"></i>Ver Tienda</a></li>
                  @else
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a></li>
                  @endif
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</button>
                    </form>
                  </li>
                </ul>
              </div>
            @endguest

            <!-- Mobile Toggle -->
            <button type="button" class="mobile-menu-toggle" id="mobileNavToggle">
              <i class="bi bi-list"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Overlay -->
    <div class="search-overlay" id="searchOverlay" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; padding: 16px 0; border-bottom: 1px solid var(--flores-border); z-index: 100;">
      <div class="container">
        <form action="{{ route('tienda.categorias') }}" method="GET">
          <div class="input-group">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar productos..." style="border-radius: 8px 0 0 8px; border: 1px solid var(--flores-border);">
            <button class="btn btn-flores" type="submit" style="border-radius: 0 8px 8px 0;">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </header>

  <!-- Mobile Navigation Menu -->
  <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
  <div class="mobile-nav-menu" id="mobileNavMenu">
    <button type="button" class="close-btn" id="mobileNavClose">
      <i class="bi bi-x-lg"></i>
    </button>
    <ul class="nav-links">
      <li><a href="{{ route('home') }}">Inicio</a></li>
      <li><a href="{{ route('tienda.categorias') }}">Catálogo</a></li>
      <li><a href="{{ route('arma-tu-ramo') }}">Arma tu ramo</a></li>
      @guest
        <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
        <li><a href="{{ route('register.cliente') }}">Registrarse</a></li>
      @else
        @if(auth()->user()->hasRole('cliente'))
          <li><a href="{{ route('cliente.compras') }}">Inicio</a></li>
          <li><a href="{{ route('cliente.compras') }}">Mis Compras</a></li>
          <li><a href="{{ route('home') }}">Ver Tienda</a></li>
        @else
          <li><a href="{{ route('dashboard') }}">Panel Admin</a></li>
        @endif
        <li>
          <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar Sesión</a>
          </form>
        </li>
      @endguest
    </ul>
  </div>

  <main class="main">
    @yield('content')
  </main>

  <footer id="footer" class="footer-minimal">
    <div class="footer-main-minimal">
      <div class="container">
        <div class="row gy-5">
          <!-- Logo y Descripción -->
          <div class="col-lg-3 col-md-6">
            <div class="footer-brand">
              <a href="{{ route('home') }}" class="footer-logo">
                <i class="bi bi-flower1"></i>
                <span>{{ $empresa->nombre }}</span>
              </a>
              <p class="footer-description">
                Flores frescas entregadas el mismo día. Calidad garantizada en cada arreglo.
              </p>
              <div class="footer-social">
                @if($empresa->instagram_url)
                  <a href="{{ $empresa->instagram_url }}" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                @endif
                @if($empresa->facebook_url)
                  <a href="{{ $empresa->facebook_url }}" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                @endif
                @if($empresa->whatsapp)
                  <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $empresa->whatsapp) }}" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                @endif
              </div>
            </div>
          </div>

          <!-- Tienda -->
          <div class="col-lg-3 col-md-6">
            <div class="footer-links-minimal">
              <h5>Tienda</h5>
              <ul>
                <li><a href="{{ route('tienda.categorias') }}">Todos los productos</a></li>
                @foreach($categoriasMenu ?? [] as $categoria)
                  <li><a href="{{ route('tienda.categorias', ['categoria' => $categoria->id]) }}">{{ $categoria->nombre }}</a></li>
                @endforeach
              </ul>
            </div>
          </div>

          <!-- Enlaces Útiles -->
          <div class="col-lg-3 col-md-6">
            <div class="footer-links-minimal">
              <h5>Enlaces</h5>
              <ul>
                @guest
                  <li><a href="{{ route('login') }}">Iniciar sesión</a></li>
                  <li><a href="{{ route('register.cliente') }}">Registrarse</a></li>
                @else
                  @if(auth()->user()->hasRole('cliente'))
                    <li><a href="{{ route('cliente.compras') }}">Inicio</a></li>
                    <li><a href="{{ route('cliente.compras') }}">Mis Compras</a></li>
                    <li><a href="{{ route('cliente.puntos') }}">Mis Puntos</a></li>
                    <li><a href="{{ route('home') }}">Ver Tienda</a></li>
                  @endif
                @endguest
                <li><a href="{{ route('paginas.terminos') }}">Términos y condiciones</a></li>
              </ul>
            </div>
          </div>

          <!-- Contacto -->
          <div class="col-lg-3 col-md-6">
            <div class="footer-contact-minimal">
              <h5>Contacto</h5>
              @if($empresa->telefono)
                <div class="contact-item-minimal">
                  <i class="bi bi-telephone"></i>
                  <span>{{ $empresa->telefono }}</span>
                </div>
              @endif
              @if($empresa->email)
                <div class="contact-item-minimal">
                  <i class="bi bi-envelope"></i>
                  <span>{{ $empresa->email }}</span>
                </div>
              @endif
              @if($empresa->direccion)
                <div class="contact-item-minimal">
                  <i class="bi bi-geo-alt"></i>
                  <span>{{ $empresa->direccion }}</span>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom-minimal">
      <div class="container">
        <div class="footer-bottom-content">
          <p class="copyright-minimal">© {{ date('Y') }} {{ $empresa->nombre }}. Todos los derechos reservados.</p>
          <div class="footer-legal">
            <a href="{{ route('paginas.terminos') }}">Términos y condiciones</a>
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

  <!-- WhatsApp Floating Button -->
  @include('components.whatsapp-button', ['empresa' => $empresa])

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

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      // CSRF Token
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });

      // Search Toggle
      $('#searchToggle').on('click', function() {
        $('#searchOverlay').slideToggle(200);
        $('#searchOverlay input').focus();
      });

      // Mobile Navigation
      function openMobileNav() {
        $('#mobileNavOverlay').addClass('active');
        $('#mobileNavMenu').addClass('active');
        $('body').css('overflow', 'hidden');
      }

      function closeMobileNav() {
        $('#mobileNavOverlay').removeClass('active');
        $('#mobileNavMenu').removeClass('active');
        $('body').css('overflow', '');
      }

      $('#mobileNavToggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        openMobileNav();
      });

      $('#mobileNavClose').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeMobileNav();
      });

      $('#mobileNavOverlay').on('click', function() {
        closeMobileNav();
      });

      // Cerrar menú al hacer click en un enlace
      $('#mobileNavMenu .nav-links a').on('click', function() {
        closeMobileNav();
      });

      // Cerrar con tecla Escape
      $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
          closeMobileNav();
        }
      });
    });
  </script>

  @stack('scripts')

</body>

</html>