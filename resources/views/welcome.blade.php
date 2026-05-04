<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SINDEN') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $fondoLogin = \App\Models\ConfiguracionSistema::get('imagen_fondo_login');
        $colorTextoBienvenida = \App\Models\ConfiguracionSistema::get('color_texto_bienvenida', '#1f2937');
    @endphp
    @if($fondoLogin)
    <style>
        body.fondo-custom {
            background: url('{{ asset($fondoLogin) }}') center/cover no-repeat fixed !important;
        }
    </style>
    @endif
</head>
<body class="antialiased min-h-screen {{ $fondoLogin ? 'fondo-custom' : 'bg-gradient-to-br from-gray-50 to-gray-100' }}">
    <div class="flex flex-col items-center justify-center min-h-screen p-6">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'SINDEN') }}" class="mx-auto mb-4" style="width: 150px; height: auto;">
            <h1 class="text-5xl font-bold" style="color: {{ $colorTextoBienvenida }};">{{ config('app.name', 'SINDEN') }}</h1>
        </div>

        <!-- Mensaje principal -->
        <div class="text-center mb-8">
            <h2 class="text-4xl font-semibold mb-4" style="color: {{ $colorTextoBienvenida }};">
                Bienvenido a {{ config('app.name', 'SINDEN') }}
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
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'SINDEN') }}. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
