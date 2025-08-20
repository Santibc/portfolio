<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Montano&Co</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('images/logo.png') }}" rel="icon">
  <link href="{{ asset('montano_assets/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Archivos CSS de terceros -->
  <link href="{{ asset('montano_assets/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('montano_assets/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('montano_assets/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('montano_assets/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('montano_assets/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">

  <!-- Archivo CSS principal -->
  <link href="{{ asset('montano_assets/assets/css/main.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Nombre de la plantilla: Day
  * URL de la plantilla: https://bootstrapmade.com/day-multipurpose-html-template-for-free/
  * Actualizado: 07 Ago 2024 con Bootstrap v5.3.3
  * Autor: BootstrapMade.com
  * Licencia: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header fixed-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contacto@ejemplo.com">contacto@ejemplo.com</a></i>
          <i class="bi bi-phone d-flex align-items-center ms-4"><span>+57 310 000 0000</span></i>
        </div>
        <div class="social-links d-none d-md-flex align-items-center">
          <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div><!-- Fin de barra superior -->

    <div class="branding d-flex align-items-center">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.html" class="logo d-flex align-items-center">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Inicio</a></li>
            <li><a href="#about">Nosotros</a></li>
            <li><a href="#portfolio">Portafolio</a></li>
            <li><a href="#team">Equipo</a></li>
            <li><a href="#contact">Contacto</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
      </div>

    </div>

  </header>

  <main class="main">
    @yield('content')
  </main>

  <footer id="footer" class="footer position-relative dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-12 col-md-4">
          <div class="footer-about">
            <a href="index.html" class="logo sitename">
              <img style="width: 100%" src="{{ asset('images/logo.png') }}" alt="Logo">
            </a>

          </div>
        </div>

        <div class="col-12 col-md-3 footer-links">
          <h4>Enlaces útiles</h4>
          <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Nosotros</a></li>
            <li><a href="#">Servicios</a></li>
            <li><a href="#">Términos de servicio</a></li>
            <li><a href="#">Política de privacidad</a></li>
          </ul>
        </div>


        <div class="col-12 col-md-5 footer-newsletter">
          <h4>Empresa</h4>
                      <div class="footer-contact ">
              <p>Calle 108 #10-20</p>
              <p>Bogotá, Colombia</p>
              <p class="mt-3"><strong>Teléfono:</strong> <span>+57 310 000 0000</span></p>
              <p><strong>Email:</strong> <span>info@ejemplo.com</span></p>
            </div>
                        <div class="social-links d-flex mt-4">
              <a href=""><i class="bi bi-twitter-x"></i></a>
              <a href=""><i class="bi bi-facebook"></i></a>
              <a href=""><i class="bi bi-instagram"></i></a>
              <a href=""><i class="bi bi-linkedin"></i></a>
            </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Derechos de autor</span> <strong class="px-1 sitename">Montano&Co.</strong> <span>Todos los derechos reservados</span></p>
    </div>

  </footer>

  <!-- Botón para subir -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Archivos JS de terceros -->
  <script src="{{ asset('montano_assets/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('montano_assets/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('montano_assets/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('montano_assets/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('montano_assets/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('montano_assets/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('montano_assets/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('montano_assets/assets/js/main.js') }}"></script>

</body>

</html>
