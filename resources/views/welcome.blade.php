<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sinden') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="flex flex-col items-center justify-center min-h-screen p-6">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Sinden') }}" class="mx-auto mb-4" style="width: 150px; height: auto;">
            <h1 class="text-5xl font-bold text-gray-800">{{ config('app.name', 'Sinden') }}</h1>
        </div>

        <!-- Mensaje principal -->
        <div class="text-center mb-8">
            <h2 class="text-4xl font-semibold text-gray-700 mb-4">
                Bienvenido a {{ config('app.name', 'Sinden') }}
            </h2>
        </div>

        <!-- Botones de accion -->
        <div class="flex gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 transform hover:scale-105">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 transform hover:scale-105">
                        Iniciar Sesion
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-8 py-3 bg-gray-700 text-white font-semibold rounded-lg shadow-lg hover:bg-gray-800 transition duration-300 transform hover:scale-105">
                            Registrarse
                        </a>
                    @endif
                @endauth
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-16 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Sinden') }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
