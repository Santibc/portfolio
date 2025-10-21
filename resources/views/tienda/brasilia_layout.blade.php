<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Brasilia Theme - Tienda de Indumentaria')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/brasilia-theme.css') }}">

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
    <script src="{{ asset('js/brasilia-theme.js') }}"></script>

    @stack('scripts')
</body>
</html>
