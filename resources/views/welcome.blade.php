<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="flex flex-col items-center justify-center min-h-screen p-6">
        <!-- Logo GVA -->
        <div class="mb-8">
            <img src="{{ asset('images/GVA_LOGO_AZUL.png') }}" alt="GVA" class="w-full max-w-md">
        </div>

        <!-- Mensaje principal -->
        <div class="text-center mb-8">
            <h2 class="text-4xl font-semibold text-gray-700 mb-4">
                Bienvenido a GVA Academy
            </h2>
            <p class="text-xl text-gray-600 max-w-md">
                Aprende, progresa y construye tu futuro con nuestra plataforma de cursos virtuales.
            </p>
        </div>

        <!-- Botones de acción -->
        <div class="flex gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 transform hover:scale-105">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 transform hover:scale-105">
                        Iniciar Sesión
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
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
