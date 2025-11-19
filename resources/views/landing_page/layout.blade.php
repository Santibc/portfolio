<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    {{-- Lógica SEO del template anterior --}}
    <title>{{ $seo->meta_title ?? ($layoutConfig->site_title ?? 'Clean Me') }}</title>
    <meta name="description"
        content="{{ $seo->meta_description ?? 'Professional cleaning services for residential and commercial spaces in Wisconsin' }}">
    <meta name="keywords"
        content="{{ $seo->meta_keywords ?? 'cleaning services, house cleaning, commercial cleaning, Wisconsin, deep cleaning' }}">

    @if ($seo && $seo->canonical_url)
        <link rel="canonical" href="{{ $seo->canonical_url }}">
    @endif

    @if ($seo && $seo->robots)
        <meta name="robots" content="{{ $seo->robots }}">
    @endif

    <!-- Favicons -->
    <link href="{{ asset('images/logo.png') }}" rel="icon">
    <link href="{{ asset('devin_assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Questrial:wght@400&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('devin_assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('devin_assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('devin_assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('devin_assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('devin_assets/css/main.css') }}" rel="stylesheet">

    <!-- Custom Logo Styles -->
    <style>
        .header .logo img {
            max-height: 50px;
            max-width: 180px;
            width: auto;
            height: auto;
            object-fit: contain;
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .header .logo img {
                max-height: 40px;
                max-width: 140px;
            }

            .header .logo h1.sitename {
                font-size: 18px;
            }
        }

        /* Header navigation colors for dark blue header */
        .header .navmenu a,
        .header .navmenu a:focus {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .header .navmenu li:hover>a,
        .header .navmenu .active {
            color: #46cdcf !important;
        }

        .header .mobile-nav-toggle {
            color: #ffffff;
        }

        /* Header background override - Lighter blue */
        .header {
            --background-color: rgba(114, 135, 156, 0.95) !important;
        }

        .scrolled .header {
            --background-color: rgba(114, 135, 156, 1) !important;
        }
    </style>
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

            <a href="{{ route('welcome') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Clean Me">
                {{--         <h1 class="sitename">{{ $layoutConfig->site_title ?? 'Clean Me' }}</h1> --}}
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('welcome') }}"
                            @if (Route::currentRouteName() == 'welcome') class="active" @endif>Home</a></li>
                    <li><a href="{{ route('nosotros') }}"
                            @if (Route::currentRouteName() == 'nosotros') class="active" @endif>About</a></li>
                    <li><a href="{{ route('servicios') }}"
                            @if (Route::currentRouteName() == 'servicios') class="active" @endif>Services</a></li>
                    <li><a href="{{ route('services.calculator') }}"
                            @if (Route::currentRouteName() == 'services.calculator') class="active" @endif>BOOK</a></li>
                    <li><a href="{{ route('contacto') }}"
                            @if (Route::currentRouteName() == 'contacto') class="active" @endif>Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>

    <footer id="footer" class="footer position-relative light-background">

        <div class="container">
            <div class="row gy-5">

                <div class="col-lg-4">
                    <div class="footer-content">
                        <a href="{{ route('welcome') }}" class="logo d-flex align-items-center justify-content-center">
                            @if ($layoutConfig && $layoutConfig->footer_logo_path)
                                <img style="max-width: 60%" src="{{ asset($layoutConfig->footer_logo_path) }}"
                                    alt="{{ $layoutConfig->site_title ?? 'Clean Me' }}">
                            @else
                                <img style="max-width: 60%" src="{{ asset('images/logo.png') }}" alt="Clean Me">
                            @endif
                            {{--         <h1 class="sitename">{{ $layoutConfig->site_title ?? 'Clean Me' }}</h1> --}}
                        </a>
                        <p class="mb-4">
                            {{ $layoutConfig->footer_description ?? 'Excellence and professionalism in residential and commercial cleaning services.' }}
                        </p>

                        <div class="social-links d-flex mt-4">
                            @if ($layoutConfig && $layoutConfig->facebook_url)
                                <a href="{{ $layoutConfig->facebook_url }}" target="_blank" class="social-link"><i
                                        class="bi bi-facebook"></i></a>
                            @endif
                            @if ($layoutConfig && $layoutConfig->twitter_url)
                                <a href="{{ $layoutConfig->twitter_url }}" target="_blank" class="social-link"><i
                                        class="bi bi-tiktok"></i></a>
                            @endif
                            @if ($layoutConfig && $layoutConfig->instagram_url)
                                <a href="{{ $layoutConfig->instagram_url }}" target="_blank" class="social-link"><i
                                        class="bi bi-instagram"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="footer-links">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="{{ route('welcome') }}"><i class="bi bi-chevron-right"></i> Home</a></li>
                            <li><a href="{{ route('nosotros') }}"><i class="bi bi-chevron-right"></i> About</a></li>
                            <li><a href="{{ route('servicios') }}"><i class="bi bi-chevron-right"></i> Services</a>
                            </li>
                            <li><a href="{{ route('contacto') }}"><i class="bi bi-chevron-right"></i> Contact</a></li>
                        </ul>
                    </div>
                </div>


                <div class="col-lg-3">
                    <div class="footer-contact">
                        <h4>Contact Us</h4>
                        <p>
                            <strong>Email:</strong>
                            <span>{{ $layoutConfig->footer_email ?? 'info@cleanme.com' }}</span><br>
                            <strong>Phone:</strong>
                            <span>{{ $layoutConfig->footer_phone ?? '+1 (555) 000-0000' }}</span><br>
                            <strong>Location:</strong>
                            <span>{{ $layoutConfig->footer_city ?? 'Wisconsin, USA' }}</span>
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong
                    class="px-1 sitename">{{ $layoutConfig->copyright_company ?? 'Clean Me' }}</strong> <span>All
                    Rights Reserved</span></p>
        </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('devin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('devin_assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('devin_assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('devin_assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('devin_assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('devin_assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('devin_assets/js/main.js') }}"></script>

    @stack('scripts')

</body>

</html>
