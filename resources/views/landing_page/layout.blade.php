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

  <!-- Estilos personalizados para el header dinámico -->
  <style>
    /* Estilos para el header dinámico */
    .header {
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
    }
    
    .header.scrolled {
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }
    
    .header .branding {
      transition: min-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .header.scrolled .branding {
      min-height: 28px;  /* Reducido 30% - era 40px */
    }
    
    /* Contenedor del logo con posición relativa para superponer imágenes */
    .header .logo {
      position: relative;
      display: flex;
      align-items: center;
      height: 100px;
      transition: height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .header.scrolled .logo {
      height: 35px;  /* Reducido 30% de 50px */
    }
    
    /* Ambas imágenes posicionadas absolutamente para superponerse */
    .header .logo img {
      position: absolute;
      left: 0;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Logo grande - visible por defecto */
    .header .logo .logo-large {
      max-height: 100px;
      padding: 5px;
      opacity: 1;
      transform: scale(1) translateY(0);
    }
    
    /* Logo pequeño - oculto por defecto */
    .header .logo .logo-small {
      max-height: 45px;  /* Reducido 30% de 50px */
      opacity: 0;
      transform: scale(0.8) translateY(10px);
    }
    
    /* Al hacer scroll - animaciones */
    .header.scrolled .logo .logo-large {
      opacity: 0;
      transform: scale(1.2) translateY(-10px);
    }
    
    .header.scrolled .logo .logo-small {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
    
    /* Efecto de morphing en el contenedor */
    .branding {
      overflow: hidden;
      position: relative;
    }
    
    .branding::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to bottom, 
        transparent 0%, 
        transparent 40%, 
        rgba(255,255,255,0.1) 50%, 
        transparent 60%, 
        transparent 100%);
      opacity: 0;
      transition: opacity 0.5s ease;
      pointer-events: none;
    }
    
    .header.scrolled .branding::after {
      opacity: 1;
    }
    
    /* Animación adicional para el contenedor del logo */
    .container {
      transition: padding 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .header.scrolled .container {
      padding-top: 5px;  /* Reducido de 10px */
      padding-bottom: 5px;  /* Reducido de 10px */
    }
    
    /* Estilos para el menú de navegación con transiciones */
    .navmenu {
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .navmenu a,
    .navmenu a:focus {
      color: #999999 !important;
      background-color: transparent !important;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }
    
    /* Efecto de subrayado animado */
    .navmenu a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 50%;
      width: 0;
      height: 2px;
      background-color: #032344;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      transform: translateX(-50%);
    }
    
    .navmenu li:hover > a::after,
    .navmenu .active::after {
      width: 100%;
    }
    
    .navmenu li:hover > a,
    .navmenu .active,
    .navmenu .active:focus {
      color: #032344 !important;
      background-color: transparent !important;
      transform: translateY(-1px);
    }
    
    /* Animación de entrada escalonada para los items del menú */
    .navmenu li {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .header.scrolled .navmenu li {
      animation: slideIn 0.5s ease forwards;
    }
    
    @keyframes slideIn {
      from {
        opacity: 0.8;
        transform: translateY(5px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Retraso escalonado para cada item del menú */
    .navmenu li:nth-child(1) { animation-delay: 0.1s; }
    .navmenu li:nth-child(2) { animation-delay: 0.15s; }
    .navmenu li:nth-child(3) { animation-delay: 0.2s; }
    .navmenu li:nth-child(4) { animation-delay: 0.25s; }
    
    /* Para dispositivos móviles */
    @media (max-width: 1199px) {
      .navmenu a:hover,
      .navmenu .active,
      .navmenu .active:focus {
        color: #032344 !important;
        background-color: transparent !important;
      }
      
      .header .logo {
        height: 80px;
      }
      
      .header.scrolled .logo {
        height: 45px;  /* Reducido 30% de 45px para móviles */
      }
      
      .header .logo .logo-large {
        max-height: 80px;
      }
      
      .header .logo .logo-small {
        max-height: 45px;  /* Reducido 30% de 45px para móviles */
      }
    }
  </style>

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
        <a href="{{ route('welcome') }}" class="logo d-flex align-items-center">
          <img src="{{ asset('images/logo_largo.png') }}" alt="Logo" class="logo-large">
          <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-small">
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="{{ route('welcome') }}" @if(Route::currentRouteName()=='welcome') class="active" @endif >Inicio</a></li>
            <li><a href="{{ route('nosotros') }}" @if(Route::currentRouteName()=='nosotros') class="active" @endif>Nosotros</a></li>
            <li><a href="{{ route('equipo') }}" @if(Route::currentRouteName()=='equipo') class="active" @endif>Equipo</a></li>
            <li><a href="{{ route('contacto') }}" @if(Route::currentRouteName()=='contacto') class="active" @endif>Contacto</a></li>
            
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
            <li><a href="{{ route('welcome') }}">Inicio</a></li>
            <li><a href="{{ route('nosotros') }}">Nosotros</a></li>
            <li><a href="{{ route('equipo') }}">Equipo</a></li>
            <li><a href="{{ route('contacto') }}">contacto</a></li>
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

  <!-- Script para el header dinámico -->
  <script>
    // Función para manejar el scroll con throttling para mejor rendimiento
    let isScrolling = false;
    
    function handleScroll() {
      if (!isScrolling) {
        window.requestAnimationFrame(() => {
          const header = document.getElementById('header');
          if (window.scrollY > 50) {
            header.classList.add('scrolled');
          } else {
            header.classList.remove('scrolled');
          }
          isScrolling = false;
        });
        isScrolling = true;
      }
    }
    
    window.addEventListener('scroll', handleScroll);
    
    // Verificar el estado inicial
    handleScroll();
  </script>

</body>

</html>