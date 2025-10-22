<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Brasilia Theme - Tienda de Indumentaria')</title>

    <!-- Google Fonts - Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/brasilia-theme.css') }}">

    <style>
        /* Apply Instrument Sans font globally */
        body, .font-family-body {
            font-family: 'Instrument Sans', sans-serif !important;
        }
        h1, h2, h3, h4, h5, h6, .font-family-heading {
            font-family: 'Instrument Sans', sans-serif !important;
        }
    </style>

    @stack('styles')
</head>
<body class="js-head-offset head-offset @yield('body-class')" style="padding-top: 206px;">

    <!-- SVG Sprite -->
    @include('tienda.partials.brasilia.svg-sprite')

    <!-- Header -->
    @include('tienda.partials.brasilia.header')

    <!-- Main Content -->
    <main @yield('main-class')>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('tienda.partials.brasilia.footer')

    <!-- Modals -->
    @include('tienda.partials.brasilia.modals')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/brasilia-theme.js') }}"></script>

    @stack('scripts')
</body>
</html>
