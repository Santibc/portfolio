<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SINDEN') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>

        <!-- Theme bootstrap (anti-FOUC) -->
        <script>
            (function () {
                var stored = localStorage.getItem('sinden-theme') || 'auto';
                var resolved = stored;
                if (stored === 'auto') {
                    resolved = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                var html = document.documentElement;
                html.setAttribute('data-theme', resolved);
                html.setAttribute('data-bs-theme', resolved);
                if (resolved === 'dark') html.classList.add('dark');
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @php $fondoLogin = \App\Models\ConfiguracionSistema::get('imagen_fondo_login'); @endphp
        <style>
            :root {
                --sinden-primary: #1E40AF;
                --sinden-primary-dark: #1E3A8A;
                --sinden-secondary: #64748b;
            }

            .sinden-auth-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
                @if($fondoLogin)
                background: url('{{ asset($fondoLogin) }}') center/cover no-repeat fixed;
                @else
                background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 50%, #475569 100%);
                @endif
            }

            .sinden-logo {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 2rem;
                font-weight: 700;
                color: white;
                margin-bottom: 2rem;
            }

            .sinden-logo i {
                font-size: 2.5rem;
            }

            .sinden-auth-card {
                width: 100%;
                max-width: 28rem;
                background: white;
                border-radius: 1rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                padding: 2rem;
            }

            [data-theme="dark"] .sinden-auth-card {
                background: #1e1e1e;
                color: #e5e7eb;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            }

            .sinden-auth-title {
                text-align: center;
                font-size: 1.5rem;
                font-weight: 600;
                color: #1F2937;
                margin-bottom: 1.5rem;
            }

            [data-theme="dark"] .sinden-auth-title {
                color: #f3f4f6;
            }

            .sinden-footer {
                margin-top: 2rem;
                text-align: center;
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.875rem;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="sinden-auth-container">
            <div class="sinden-logo">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'SINDEN') }}" style="height: 50px; margin-right: 12px;">
                <span>{{ config('app.name', 'SINDEN') }}</span>
            </div>

            <div class="sinden-auth-card">
                {{ $slot }}
            </div>

            <div class="sinden-footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'SINDEN') }}</p>
            </div>
        </div>
    </body>
</html>
