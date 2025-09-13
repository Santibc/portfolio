<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>{{ $seo->meta_title ?? ($layoutConfig->site_title ?? 'Montano&Co') }}</title>
  <meta name="description" content="{{ $seo->meta_description ?? '' }}">
  <meta name="keywords" content="{{ $seo->meta_keywords ?? '' }}">
  
  @if($seo && $seo->canonical_url)
  <link rel="canonical" href="{{ $seo->canonical_url }}">
  @endif
  
  @if($seo && $seo->robots)
  <meta name="robots" content="{{ $seo->robots }}">
  @endif

  <!-- Favicons -->
  <link href="{{ asset('images/logo.png') }}" rel="icon">
  <link href="{{ asset('montano_assets/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Archivos CSS de terceros -->
  <link href="{{ asset('montano_assets/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Bootstrap Icons CDN - Versión completa con todos los iconos -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
    
    /* Mobile Menu Styles */
    .mobile-menu-toggle {
      background: none;
      border: none;
      padding: 8px;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      width: 40px;
      height: 40px;
      border-radius: 6px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1001;
    }
    
    .mobile-menu-toggle:hover {
      background-color: rgba(3, 35, 68, 0.1);
    }
    
    .hamburger-line {
      width: 24px;
      height: 2px;
      background-color: #032344;
      margin: 2px 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      transform-origin: center;
    }
    
    .mobile-menu-toggle.active .hamburger-line:nth-child(1) {
      transform: rotate(45deg) translateY(6px);
    }
    
    .mobile-menu-toggle.active .hamburger-line:nth-child(2) {
      opacity: 0;
      transform: scaleX(0);
    }
    
    .mobile-menu-toggle.active .hamburger-line:nth-child(3) {
      transform: rotate(-45deg) translateY(-6px);
    }
    
    .mobile-menu-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(3, 35, 68, 0.95);
      backdrop-filter: blur(10px);
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex !important;
      align-items: center;
      justify-content: center;
      pointer-events: none;
      overflow: hidden;
      padding: 20px;
      box-sizing: border-box;
    }
    
    .mobile-menu-overlay.active {
      opacity: 1;
      visibility: visible;
      pointer-events: all;
    }
    
    .mobile-menu-content {
      position: relative;
      width: 95%;
      max-width: 380px;
      background: #ffffff !important;
      border-radius: 20px;
      padding: 40px 25px 30px 25px;
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
      transform: scale(1) translateY(0) !important;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 10000;
      min-height: 350px;
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      max-height: 80vh;
      overflow-x: hidden;
      overflow-y: auto;
    }
    
    .mobile-menu-close {
      position: absolute;
      top: 15px;
      right: 15px;
      background: none;
      border: none;
      font-size: 28px;
      color: #032344;
      cursor: pointer;
      padding: 8px;
      border-radius: 50%;
      transition: all 0.3s ease;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .mobile-menu-close:hover {
      background-color: rgba(3, 35, 68, 0.1);
      transform: rotate(90deg);
    }
    
    .mobile-menu-links {
      list-style: none !important;
      padding: 0 !important;
      margin: 0 !important;
      width: 100%;
      display: block !important;
    }
    
    .mobile-menu-links li {
      margin: 0 0 12px 0 !important;
      opacity: 1 !important;
      transform: translateX(0) !important;
      transition: all 0.3s ease;
      display: block !important;
      visibility: visible !important;
      width: 100%;
      list-style: none !important;
    }
    
    .mobile-menu-links a {
      display: block !important;
      padding: 14px 16px;
      color: #032344 !important;
      text-decoration: none !important;
      font-weight: 500;
      font-size: 18px !important;
      border-radius: 12px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: visible;
      visibility: visible !important;
      opacity: 1 !important;
      z-index: 1;
      background-color: rgba(3, 35, 68, 0.06) !important;
      border: 2px solid rgba(3, 35, 68, 0.15);
      margin-bottom: 6px;
      width: calc(100% - 8px);
      max-width: 350px;
      margin-left: auto;
      margin-right: auto;
      box-sizing: border-box;
      text-align: center;
      letter-spacing: 0.3px;
    }
    
    .mobile-menu-links a:hover,
    .mobile-menu-links a.active {
      background-color: rgba(3, 35, 68, 0.12) !important;
      transform: translateX(4px);
      box-shadow: 0 2px 8px rgba(3, 35, 68, 0.15);
    }
    
    .mobile-menu-links a.active {
      background-color: rgba(3, 35, 68, 0.15) !important;
      font-weight: 600;
    }
    
    /* Hide desktop menu on mobile */
    @media (max-width: 1199px) {
      .desktop-menu {
        display: none;
      }
      
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
    
    /* Show desktop menu on larger screens */
    @media (min-width: 1200px) {
      .mobile-menu-toggle,
      .mobile-menu-overlay {
        display: none !important;
      }
    }
    
    /* Scroll to top button styles */
    .scroll-top {
      position: fixed;
      visibility: hidden;
      opacity: 0;
      right: 15px;
      bottom: 15px;
      z-index: 99999;
      background-color: #032344;
      width: 44px;
      height: 44px;
      border-radius: 50px;
      transition: all 0.4s ease;
    }
    
    .scroll-top i {
      font-size: 24px;
      color: #ffffff;
      line-height: 0;
    }
    
    .scroll-top:hover {
      background-color: rgba(3, 35, 68, 0.8);
      color: #ffffff;
    }
    
    .scroll-top.active {
      visibility: visible;
      opacity: 1;
    }
    
    /* Preloader styles */
    #preloader {
      position: fixed;
      inset: 0;
      z-index: 9999;
      overflow: hidden;
      background-color: #ffffff;
      transition: all 0.6s ease-out;
    }
    
    #preloader:before {
      content: "";
      position: fixed;
      top: calc(50% - 30px);
      left: calc(50% - 30px);
      border: 6px solid #032344;
      border-color: #032344 transparent #032344 transparent;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: animate-preloader 1.5s linear infinite;
    }
    
    @keyframes animate-preloader {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
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
          <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:{{ $layoutConfig->topbar_email ?? 'contacto@ejemplo.com' }}">{{ $layoutConfig->topbar_email ?? 'contacto@ejemplo.com' }}</a></i>
          <i class="bi bi-phone d-flex align-items-center ms-4"><span>{{ $layoutConfig->topbar_phone ?? '+57 310 000 0000' }}</span></i>
        </div>
        <div class="social-links d-none d-md-flex align-items-center">
          @if($layoutConfig && $layoutConfig->twitter_url)
            <a href="{{ $layoutConfig->twitter_url }}" target="_blank" class="twitter"><i class="bi bi-twitter-x"></i></a>
          @endif
          @if($layoutConfig && $layoutConfig->facebook_url)
            <a href="{{ $layoutConfig->facebook_url }}" target="_blank" class="facebook"><i class="bi bi-facebook"></i></a>
          @endif
          @if($layoutConfig && $layoutConfig->instagram_url)
            <a href="{{ $layoutConfig->instagram_url }}" target="_blank" class="instagram"><i class="bi bi-instagram"></i></a>
          @endif
          @if($layoutConfig && $layoutConfig->linkedin_url)
            <a href="{{ $layoutConfig->linkedin_url }}" target="_blank" class="linkedin"><i class="bi bi-linkedin"></i></a>
          @endif
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
          <ul class="desktop-menu">
            <li><a href="{{ route('welcome') }}" @if(Route::currentRouteName()=='welcome') class="active" @endif >Inicio</a></li>
            <li><a href="{{ route('nosotros') }}" @if(Route::currentRouteName()=='nosotros') class="active" @endif>Nosotros</a></li>
            <li><a href="{{ route('equipo') }}" @if(Route::currentRouteName()=='equipo') class="active" @endif>Equipo</a></li>
            <li><a href="{{ route('contacto') }}" @if(Route::currentRouteName()=='contacto') class="active" @endif>Contacto</a></li>
          </ul>
          
          <!-- Mobile Menu Toggle Button -->
          <button class="mobile-menu-toggle d-xl-none" type="button" aria-label="Toggle navigation">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
          </button>
          
          <!-- Mobile Menu Overlay -->
          <div class="mobile-menu-overlay d-xl-none">
            <div class="mobile-menu-content">
              <button class="mobile-menu-close" type="button" aria-label="Close navigation">
                <i class="bi bi-x"></i>
              </button>
              <ul class="mobile-menu-links">
                <li><a href="{{ route('welcome') }}" @if(Route::currentRouteName()=='welcome') class="active" @endif>Inicio</a></li>
                <li><a href="{{ route('nosotros') }}" @if(Route::currentRouteName()=='nosotros') class="active" @endif>Nosotros</a></li>
                <li><a href="{{ route('equipo') }}" @if(Route::currentRouteName()=='equipo') class="active" @endif>Equipo</a></li>
                <li><a href="{{ route('contacto') }}" @if(Route::currentRouteName()=='contacto') class="active" @endif>Contacto</a></li>
              </ul>
            </div>
          </div>
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
            <li><a href="{{ route('contacto') }}">Contacto</a></li>
{{--             <li><a href="#">Términos de servicio</a></li>
            <li><a href="#">Política de privacidad</a></li> --}}
          </ul>
        </div>


        <div class="col-12 col-md-5 footer-newsletter">
          <h4>Empresa</h4>
                      <div class="footer-contact ">
              <p>{{ $layoutConfig->footer_address ?? 'Calle 108 #10-20' }}</p>
              <p>{{ $layoutConfig->footer_city ?? 'Bogotá, Colombia' }}</p>
              <p class="mt-3"><strong>Teléfono:</strong> <span>{{ $layoutConfig->footer_phone ?? '+57 310 000 0000' }}</span></p>
              <p><strong>Email:</strong> <span>{{ $layoutConfig->footer_email ?? 'info@ejemplo.com' }}</span></p>
            </div>
                        <div class="social-links d-flex mt-4">
              @if($layoutConfig && $layoutConfig->twitter_url)
                <a href="{{ $layoutConfig->twitter_url }}" target="_blank"><i class="bi bi-twitter-x"></i></a>
              @endif
              @if($layoutConfig && $layoutConfig->facebook_url)
                <a href="{{ $layoutConfig->facebook_url }}" target="_blank"><i class="bi bi-facebook"></i></a>
              @endif
              @if($layoutConfig && $layoutConfig->instagram_url)
                <a href="{{ $layoutConfig->instagram_url }}" target="_blank"><i class="bi bi-instagram"></i></a>
              @endif
              @if($layoutConfig && $layoutConfig->linkedin_url)
                <a href="{{ $layoutConfig->linkedin_url }}" target="_blank"><i class="bi bi-linkedin"></i></a>
              @endif
            </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Derechos de autor</span> <strong class="px-1 sitename">{{ $layoutConfig->copyright_company ?? 'Montano&Co.' }}</strong> <span>Todos los derechos reservados</span></p>
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
  
  <!-- Custom main.js replacement to avoid conflicts -->
  <script>
    // Initialize AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });
    }

    // Scroll to top button
    document.addEventListener('DOMContentLoaded', function() {
      const scrollTop = document.querySelector('.scroll-top');
      if (scrollTop) {
        function toggleScrollTop() {
          if (window.scrollY > 100) {
            scrollTop.classList.add('active');
          } else {
            scrollTop.classList.remove('active');
          }
        }
        window.addEventListener('scroll', toggleScrollTop);
        scrollTop.addEventListener('click', (e) => {
          e.preventDefault();
          window.scrollTo({
            top: 0,
            behavior: 'smooth'
          });
        });
      }

      // Preloader
      const preloader = document.querySelector('#preloader');
      if (preloader) {
        window.addEventListener('load', () => {
          preloader.remove();
        });
      }

      // Initialize GLightbox
      if (typeof GLightbox !== 'undefined') {
        const glightbox = GLightbox({
          selector: '.glightbox'
        });
      }

      // Initialize Swiper
      document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
        if (typeof Swiper !== 'undefined') {
          let config = JSON.parse(
            swiperElement.querySelector(".swiper-config").innerHTML.trim()
          );
          if (swiperElement.classList.contains("swiper-tab")) {
            initSwiperWithCustomPagination(swiperElement, config);
          } else {
            new Swiper(swiperElement, config);
          }
        }
      });

      // Initialize Isotope
      document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
        if (typeof Isotope !== 'undefined') {
          let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
          let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
          let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

          let initIsotope;
          imagesLoaded(isotopeItem.querySelector('.isotope-container'), function() {
            initIsotope = new Isotope(isotopeItem.querySelector('.isotope-container'), {
              itemSelector: '.isotope-item',
              layoutMode: layout,
              filter: filter,
              sortBy: sort
            });
          });

          isotopeItem.querySelectorAll('.isotope-filters li').forEach(function(filters) {
            filters.addEventListener('click', function() {
              isotopeItem.querySelector('.isotope-filters .filter-active').classList.remove('filter-active');
              this.classList.add('filter-active');
              initIsotope.arrange({
                filter: this.getAttribute('data-filter')
              });
              if (typeof aosInit === 'function') {
                aosInit();
              }
            }, false);
          });
        }
      });
    });
  </script>

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

  <!-- Script para el menú hamburguesa -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Mobile menu script loading...');
      
      const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
      const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
      const mobileMenuClose = document.querySelector('.mobile-menu-close');
      const mobileMenuLinks = document.querySelectorAll('.mobile-menu-links a');
      const body = document.body;
      
      console.log('Elements found:', {
        toggle: !!mobileMenuToggle,
        overlay: !!mobileMenuOverlay,
        close: !!mobileMenuClose,
        links: mobileMenuLinks.length
      });
      
      // Función para abrir el menú
      function openMobileMenu() {
        console.log('Opening mobile menu');
        if (mobileMenuToggle) mobileMenuToggle.classList.add('active');
        if (mobileMenuOverlay) mobileMenuOverlay.classList.add('active');
        body.style.overflow = 'hidden';
      }
      
      // Función para cerrar el menú
      function closeMobileMenu() {
        console.log('Closing mobile menu');
        if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
        if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('active');
        body.style.overflow = '';
      }
      
      // Event listeners
      if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          console.log('Toggle clicked');
          
          if (mobileMenuOverlay && mobileMenuOverlay.classList.contains('active')) {
            closeMobileMenu();
          } else {
            openMobileMenu();
          }
        });
      }
      
      if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          console.log('Close clicked');
          closeMobileMenu();
        });
      }
      
      // Cerrar menú al hacer click en el overlay (fondo)
      if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function(e) {
          if (e.target === mobileMenuOverlay) {
            console.log('Overlay clicked');
            closeMobileMenu();
          }
        });
      }
      
      // Cerrar menú al hacer click en un enlace
      mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
          console.log('Menu link clicked');
          closeMobileMenu();
        });
      });
      
      // Cerrar menú con la tecla Escape
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenuOverlay && mobileMenuOverlay.classList.contains('active')) {
          console.log('Escape pressed');
          closeMobileMenu();
        }
      });
      
      // Handle resize events
      window.addEventListener('resize', function() {
        if (window.innerWidth >= 1200 && mobileMenuOverlay && mobileMenuOverlay.classList.contains('active')) {
          closeMobileMenu();
        }
      });
      
      console.log('Mobile menu script loaded successfully');
    });
  </script>

  @stack('scripts')

</body>

</html>