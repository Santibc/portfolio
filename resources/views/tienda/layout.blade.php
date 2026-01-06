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
      --flores-primary: #6B7456;
      --flores-primary-hover: #5a6248;
      --flores-beige: #F5EDD8;
      --flores-text: #333333;
      --flores-text-light: #666666;
      --flores-bg: #FFFFFF;
      --flores-border: #E5E5E5;
      --font-serif: 'Playfair Display', Georgia, serif;
      --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      font-family: var(--font-sans);
      color: var(--flores-text);
    }

    /* ============ CONTENEDOR PERSONALIZADO ============ */
    .container {
      max-width: 1400px;
      padding-left: 24px;
      padding-right: 24px;
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

    /* ============ NUEVO HEADER MINIMALISTA ============ */
    .header {
      background: var(--flores-bg);
      box-shadow: none;
      border-bottom: 1px solid var(--flores-border);
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
      gap: 24px;
    }

    /* Logo */
    .header-minimal .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      flex-shrink: 0;
    }

    .header-minimal .logo-icon {
      font-size: 20px;
      color: var(--flores-primary);
    }

    .header-minimal .logo-text {
      font-family: var(--font-serif);
      font-size: 18px;
      font-weight: 600;
      color: var(--flores-text);
      margin: 0;
    }

    /* Navigation */
    .header-minimal .nav-links {
      display: flex;
      align-items: center;
      gap: 32px;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .header-minimal .nav-links a {
      font-size: 14px;
      font-weight: 500;
      color: var(--flores-text);
      text-decoration: none;
      transition: color 0.2s;
      white-space: nowrap;
    }

    .header-minimal .nav-links a:hover {
      color: var(--flores-primary);
    }

    .header-minimal .nav-links a.active {
      color: var(--flores-primary);
    }

    /* Header Actions */
    .header-minimal .header-actions {
      display: flex;
      align-items: center;
      gap: 20px;
      flex-shrink: 0;
    }

    .header-minimal .header-action-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: transparent;
      color: var(--flores-text);
      text-decoration: none;
      transition: all 0.2s;
      position: relative;
      border: none;
      cursor: pointer;
    }

    .header-minimal .header-action-btn:hover {
      background: var(--flores-beige);
      color: var(--flores-primary);
    }

    .header-minimal .header-action-btn i {
      font-size: 20px;
    }

    .header-minimal .header-action-btn .badge {
      position: absolute;
      top: -2px;
      right: -2px;
      background: var(--flores-primary);
      color: white;
      font-size: 10px;
      font-weight: 600;
      padding: 2px 6px;
      border-radius: 10px;
      min-width: 18px;
      text-align: center;
    }

    /* Mobile Toggle */
    .header-minimal .mobile-nav-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 24px;
      color: var(--flores-text);
      cursor: pointer;
      padding: 8px;
    }

    /* Hide old header elements */
    .header .header-nav,
    .header .search-form.desktop-search-form,
    .header .old-header-actions {
      display: none !important;
    }

    /* Auth Buttons */
    .btn-auth-link {
      font-size: 14px;
      font-weight: 500;
      color: var(--flores-text);
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 30px;
      transition: all 0.2s;
      white-space: nowrap;
    }

    .btn-auth-link:hover {
      color: var(--flores-primary);
    }

    .btn-auth-register {
      font-size: 14px;
      font-weight: 500;
      color: white;
      background: var(--flores-primary);
      text-decoration: none;
      padding: 8px 20px;
      border-radius: 30px;
      transition: all 0.2s;
      white-space: nowrap;
    }

    .btn-auth-register:hover {
      background: var(--flores-primary-hover);
      color: white;
    }

    @media (max-width: 991px) {
      .header-minimal .nav-links {
        display: none;
      }

      .header-minimal .mobile-nav-toggle {
        display: flex;
      }

      .header-minimal {
        padding: 12px 0;
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
      right: -300px;
      width: 300px;
      height: 100%;
      background: white;
      z-index: 9999;
      padding: 24px;
      transition: right 0.3s;
      overflow-y: auto;
    }

    .mobile-nav-menu.active {
      right: 0;
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
      padding: 60px 0 40px;
    }

    .footer-brand .footer-logo {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--font-serif);
      font-size: 20px;
      font-weight: 600;
      color: var(--flores-text);
      text-decoration: none;
      margin-bottom: 16px;
    }

    .footer-brand .footer-logo i {
      color: var(--flores-primary);
    }

    .footer-description {
      font-size: 14px;
      color: var(--flores-text-light);
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .footer-social {
      display: flex;
      gap: 12px;
    }

    .footer-social a {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: white;
      border: 1px solid var(--flores-border);
      border-radius: 50%;
      color: var(--flores-text);
      transition: all 0.2s;
    }

    .footer-social a:hover {
      background: var(--flores-primary);
      border-color: var(--flores-primary);
      color: white;
    }

    .footer-links-minimal h5,
    .footer-contact-minimal h5 {
      font-size: 14px;
      font-weight: 600;
      color: var(--flores-text);
      margin-bottom: 20px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .footer-links-minimal ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links-minimal ul li {
      margin-bottom: 12px;
    }

    .footer-links-minimal ul li a {
      font-size: 14px;
      color: var(--flores-text-light);
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer-links-minimal ul li a:hover {
      color: var(--flores-primary);
    }

    .contact-item-minimal {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 16px;
      font-size: 14px;
      color: var(--flores-text-light);
    }

    .contact-item-minimal i {
      color: var(--flores-primary);
      margin-top: 2px;
    }

    .footer-bottom-minimal {
      padding: 24px 0;
      border-top: 1px solid var(--flores-border);
    }

    .footer-bottom-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
    }

    .copyright-minimal {
      font-size: 13px;
      color: var(--flores-text-light);
      margin: 0;
    }

    .footer-legal {
      display: flex;
      gap: 24px;
    }

    .footer-legal a {
      font-size: 13px;
      color: var(--flores-text-light);
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer-legal a:hover {
      color: var(--flores-primary);
    }

    @media (max-width: 767px) {
      .footer-main-minimal {
        padding: 40px 0 24px;
      }

      .footer-bottom-content {
        flex-direction: column;
        text-align: center;
      }

      .footer-legal {
        justify-content: center;
      }
    }
  </style>

  @stack('styles')
</head>

<body class="@yield('body-class', 'index-page')">
  {{-- GTM noscript fallback --}}
  @include('components.analytics.body-scripts')

  <header id="header" class="header sticky-top">
    <!-- New Minimal Header -->
    <div class="main-header">
      <div class="container">
        <div class="header-minimal">
          <!-- Logo -->
          <a href="{{ route('home') }}" class="logo">
            <i class="bi bi-geo-alt-fill logo-icon"></i>
            <span class="logo-text">{{ $empresa->nombre }}</span>
          </a>

          <!-- Navigation Links -->
          <ul class="nav-links">
            <li><a href="{{ route('tienda.categorias') }}" class="@yield('nav-ocasiones', '')">Ocasiones</a></li>
            <li><a href="{{ route('tienda.categorias') }}" class="@yield('nav-tipo-flor', '')">Por tipo de flor</a></li>
            <li><a href="{{ route('arma-tu-ramo') }}" class="@yield('nav-arma-tu-ramo', '')">Arma tu ramo</a></li>
            <li><a href="#" class="@yield('nav-ayuda', '')">Ayuda</a></li>
          </ul>

          <!-- Header Actions -->
          <div class="header-actions">
            <!-- Search -->
            <button type="button" class="header-action-btn" id="searchToggle" title="Buscar">
              <i class="bi bi-search"></i>
            </button>

            <!-- Account -->
            @guest
              <a href="{{ route('login') }}" class="btn-auth-link d-none d-lg-inline-flex">Iniciar sesión</a>
              <a href="{{ route('register.cliente') }}" class="btn-auth-register d-none d-lg-inline-flex">Registrarse</a>
              <a href="{{ route('login') }}" class="header-action-btn d-lg-none" title="Mi cuenta">
                <i class="bi bi-person"></i>
              </a>
            @else
              <div class="dropdown">
                <a href="#" class="header-action-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="{{ auth()->user()->name }}">
                  <i class="bi bi-person"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->name }}</span></li>
                  <li><hr class="dropdown-divider"></li>
                  @if(auth()->user()->hasRole('cliente'))
                    <li><a class="dropdown-item" href="{{ route('cliente.compras') }}"><i class="bi bi-bag-check me-2"></i>Mis Compras</a></li>
                    <li><a class="dropdown-item" href="{{ route('cliente.perfil') }}"><i class="bi bi-person-gear me-2"></i>Mi Perfil</a></li>
                  @else
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a></li>
                  @endif
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</button>
                    </form>
                  </li>
                </ul>
              </div>
            @endguest

            <!-- Cart -->
            <a href="{{ route('tienda.carrito') }}" class="header-action-btn" title="Carrito">
              <i class="bi bi-bag"></i>
              @if($carrito->total_items > 0)
                <span class="badge">{{ $carrito->total_items }}</span>
              @endif
            </a>

            <!-- Mobile Toggle -->
            <button type="button" class="mobile-nav-toggle" id="mobileNavToggle">
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
      <li><a href="{{ route('tienda.categorias') }}">Ocasiones</a></li>
      <li><a href="{{ route('tienda.categorias') }}">Por tipo de flor</a></li>
      <li><a href="{{ route('arma-tu-ramo') }}">Arma tu ramo</a></li>
      <li><a href="#">Ayuda</a></li>
      @guest
        <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
        <li><a href="{{ route('register.cliente') }}">Registrarse</a></li>
      @else
        @if(auth()->user()->hasRole('cliente'))
          <li><a href="{{ route('cliente.compras') }}">Mis Compras</a></li>
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
        <div class="row gy-4">
          <!-- Logo y Descripción -->
          <div class="col-lg-4 col-md-6">
            <div class="footer-brand">
              <a href="{{ route('home') }}" class="footer-logo">
                <i class="bi bi-geo-alt-fill"></i>
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
          <div class="col-lg-2 col-md-6 col-6">
            <div class="footer-links-minimal">
              <h5>Tienda</h5>
              <ul>
                <li><a href="{{ route('tienda.categorias') }}">Todos los productos</a></li>
                <li><a href="{{ route('tienda.categorias', ['ocasion' => 'cumpleanos']) }}">Cumpleaños</a></li>
                <li><a href="{{ route('tienda.categorias', ['ocasion' => 'amor']) }}">Amor</a></li>
                <li><a href="{{ route('arma-tu-ramo') }}">Arma tu ramo</a></li>
              </ul>
            </div>
          </div>

          <!-- Ayuda -->
          <div class="col-lg-2 col-md-6 col-6">
            <div class="footer-links-minimal">
              <h5>Ayuda</h5>
              <ul>
                <li><a href="#">Preguntas frecuentes</a></li>
                <li><a href="#">Envíos y entregas</a></li>
                <li><a href="#">Cambios y devoluciones</a></li>
                <li><a href="#">Contacto</a></li>
              </ul>
            </div>
          </div>

          <!-- Contacto -->
          <div class="col-lg-4 col-md-6">
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
            <a href="#">Términos y condiciones</a>
            <a href="#">Política de privacidad</a>
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
      $('#mobileNavToggle').on('click', function() {
        $('#mobileNavOverlay').addClass('active');
        $('#mobileNavMenu').addClass('active');
        $('body').css('overflow', 'hidden');
      });

      $('#mobileNavClose, #mobileNavOverlay').on('click', function() {
        $('#mobileNavOverlay').removeClass('active');
        $('#mobileNavMenu').removeClass('active');
        $('body').css('overflow', '');
      });
    });
  </script>

  @stack('scripts')

</body>

</html>