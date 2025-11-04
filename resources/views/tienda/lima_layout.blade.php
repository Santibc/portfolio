<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', $empresa->nombre . ' - Tienda Online')</title>
    <meta name="description" content="@yield('description', 'Compra productos de ' . $empresa->nombre . ' online.')">

    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Google Fonts: Lexend Exa & Lexend --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Lexend+Exa:wght@400;700&family=Lexend:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend+Exa:wght@400;700&family=Lexend:wght@400;700&display=swap">

    {{-- Lima Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/lima-custom.css') }}">

    {{-- Additional styles from sections --}}
    @stack('styles')
</head>
<body class="@yield('body-class', 'template-home')">

    {{-- Site overlay for modals/menus --}}
    <div class="js-overlay site-overlay" style="display: none;"></div>

    {{-- Header --}}
    @include('tienda.partials.lima.header')

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('tienda.partials.lima.footer')

    {{-- Scripts --}}
    {{-- Swiper JS for carousels --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Additional scripts from sections --}}
    @stack('scripts')

    {{-- Lima theme initialization --}}
    <script>
        // Initialize Swiper for adbar (black bar)
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.js-swiper-adbar')) {
                new Swiper('.js-swiper-adbar', {
                    loop: true,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.js-swiper-adbar-next',
                        prevEl: '.js-swiper-adbar-prev',
                    },
                });
            }

            // Initialize Swiper for promotional bar (pink bar)
            if (document.querySelector('.js-swiper-promotional')) {
                new Swiper('.js-swiper-promotional', {
                    loop: true,
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.js-swiper-promotional-next',
                        prevEl: '.js-swiper-promotional-prev',
                    },
                    speed: 600,
                    slidesPerView: 1,
                    centeredSlides: true,
                    spaceBetween: 0,
                });
            }

            // Header compress on scroll
            let lastScroll = 0;
            const header = document.querySelector('.js-head-main');
            const topbar = document.querySelector('.js-topbar');

            if (header) {
                window.addEventListener('scroll', function() {
                    const currentScroll = window.pageYOffset;

                    if (currentScroll > 30) {
                        header.classList.add('compress');
                        if (topbar) {
                            topbar.style.display = 'none';
                        }
                    } else {
                        header.classList.remove('compress');
                        if (topbar) {
                            topbar.style.display = 'block';
                        }
                    }

                    lastScroll = currentScroll;
                });
            }

            // Mobile menu toggle
            const menuButton = document.querySelector('[data-toggle="#nav-hamburger"]');
            const overlay = document.querySelector('.js-overlay');

            if (menuButton && overlay) {
                menuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    overlay.style.display = 'block';
                    // Add mobile menu logic here
                });

                overlay.addEventListener('click', function() {
                    overlay.style.display = 'none';
                });
            }

            // Make menu and banners row visible
            const menuRow = document.querySelector('.js-menu-and-banners-row');
            if (menuRow) {
                menuRow.style.visibility = 'visible';
            }

            // Category dropdown - SOLO CSS, sin JavaScript que interfiera
        });
    </script>
</body>
</html>
