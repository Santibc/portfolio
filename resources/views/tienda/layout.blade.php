<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', $empresa->nombre . ' - Tienda Online')</title>
  <meta name="description" content="@yield('description', $empresa->descripcion)">
  <meta name="keywords" content="@yield('keywords', '')">

  <!-- Favicons -->
  <link href="{{ $empresa->logo_url }}" rel="icon">
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

  @stack('styles')
</head>

<body class="@yield('body-class', 'index-page')">

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
            </div>
          </div>

          <div class="col-lg-4 d-none d-lg-block">
            <div class="d-flex justify-content-end">
              <div class="top-bar-item dropdown me-3">
                <a href="#" class="" data-bs-toggle="dropdown">
                  <i class="bi bi-translate me-2"></i>ES
                </a>
              </div>
              <div class="top-bar-item dropdown">
                <a href="#" class="" data-bs-toggle="dropdown">
                  <i class="bi bi-currency-dollar me-2"></i>COP
                </a>
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
          <form class="search-form desktop-search-form" action="#" method="GET">
            <div class="input-group">
              <input type="text" name="buscar" class="form-control" placeholder="Buscar productos">
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
            <li><a href="{{ route('tienda.empresa', $empresa->slug) }}" class="@yield('nav-inicio', '')">Inicio</a></li>
            <li><a href="#about" class="@yield('nav-about', '')">Acerca de</a></li>
            <li><a href="#productos" class="@yield('nav-productos', '')">Productos</a></li>
            <li><a href="{{ route('tienda.categorias', $empresa->slug) }}" class="@yield('nav-categorias', '')">Categorías</a></li>
            <li><a href="#contacto" class="@yield('nav-contacto', '')">Contacto</a></li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Mobile Search Form -->
    <div class="collapse" id="mobileSearch">
      <div class="container">
        <form class="search-form" action="#" method="GET">
          <div class="input-group">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar productos">
            <button class="btn" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

  </header>

  <main class="main">
    @yield('content')
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
    });
  </script>

  @stack('scripts')

</body>

</html>