<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', isset($empresa) ? $empresa->nombre . ' - Tienda Online' : 'Lima Theme - Tienda Online')</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts - Lexend Exa & Lexend -->
    <link href="https://fonts.googleapis.com/css?family=Lexend+Exa:400,700|Lexend:400,700&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@4.4.2/dist/css/swiper.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/lima-theme.css') }}">

    <style>
        /* Apply Lexend fonts globally */
        :root {
            --heading-font: "Lexend Exa", sans-serif;
            --body-font: "Lexend", sans-serif;

            /* Font sizes */
            --h1: 28px;
            --h1-huge: 40px;
            --h1-huge-md: 54px;
            --h2: 24px;
            --h3: 20px;
            --h4: 18px;
            --h5: 16px;
            --h6: 14px;

            --font-hugest: 28px;
            --font-huge: 24px;
            --font-largest: 20px;
            --font-large: 18px;
            --font-big: 16px;
            --font-base: 14px;
            --font-small: 12px;
            --font-smallest: 10px;

            --title-font-weight: 700;
        }

        body {
            font-family: var(--body-font) !important;
            font-size: var(--font-base);
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: var(--heading-font) !important;
            font-weight: var(--title-font-weight);
        }

        h1 { font-size: var(--h1); }
        h2 { font-size: var(--h2); }
        h3 { font-size: var(--h3); }
        h4 { font-size: var(--h4); }
        h5 { font-size: var(--h5); }
        h6 { font-size: var(--h6); }
    </style>

    @stack('styles')
</head>
<body class="@yield('body-class')">

    <!-- Header -->
    @include('tienda.partials.lima.header')

    <!-- Main Content -->
    <main id="main-content" @yield('main-class')>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('tienda.partials.lima.footer')

    <!-- Modals -->
    @include('tienda.partials.lima.modals')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/swiper@4.4.2/dist/js/swiper.min.js"></script>
    <script src="{{ asset('js/lima-theme.js') }}"></script>

    @stack('scripts')
</body>
</html>
