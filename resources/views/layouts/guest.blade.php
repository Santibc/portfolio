<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Miracle') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}"/>

        <!-- Fonts - Miracle Brand -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --miracle-pink: #FF84D5;
                --miracle-pink-light: #FFE4F3;
                --miracle-pink-hover: #ff6bc9;
                --miracle-lilac: #BCA9F5;
                --miracle-lilac-light: #E8E1FA;
                --miracle-dark: #382E65;
            }

            body {
                font-family: 'Roboto', sans-serif;
                background: linear-gradient(135deg, #faf8fc 0%, var(--miracle-lilac-light) 100%);
            }

            h1, h2, h3, h4, h5, h6, .fw-semibold {
                font-family: 'Comfortaa', cursive;
            }

            /* Botón primario con estilo Miracle */
            button[type="submit"], .btn-primary {
                background-color: var(--miracle-pink) !important;
                border-color: var(--miracle-pink) !important;
                color: white !important;
                font-family: 'Comfortaa', cursive;
                font-weight: 500;
            }

            button[type="submit"]:hover, .btn-primary:hover {
                background-color: var(--miracle-pink-hover) !important;
                border-color: var(--miracle-pink-hover) !important;
            }

            /* Input focus con colores Miracle */
            input:focus {
                border-color: var(--miracle-pink) !important;
                box-shadow: 0 0 0 3px rgba(255, 132, 213, 0.25) !important;
            }

            /* Links con colores Miracle */
            a {
                color: var(--miracle-pink);
            }

            a:hover {
                color: var(--miracle-pink-hover);
            }

            /* Card con borde Miracle */
            .auth-card {
                border-top: 4px solid var(--miracle-pink);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <img class="mx-auto" style="width: 200px;" src="{{ asset('images/logo.png') }}" alt="Miracle Beauty Experts">

            <div class="auth-card w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-lg overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <p class="mt-4 text-sm text-gray-500">&copy; {{ date('Y') }} Miracle Beauty Experts</p>
        </div>
    </body>
</html>
