<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Manzer') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --manzer-primary: #1E40AF;
                --manzer-primary-dark: #1E3A8A;
                --manzer-secondary: #059669;
            }

            .manzer-auth-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
                background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 50%, #059669 100%);
            }

            .manzer-logo {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 2rem;
                font-weight: 700;
                color: white;
                margin-bottom: 2rem;
            }

            .manzer-logo i {
                font-size: 2.5rem;
            }

            .manzer-auth-card {
                width: 100%;
                max-width: 28rem;
                background: white;
                border-radius: 1rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                padding: 2rem;
            }

            .manzer-auth-title {
                text-align: center;
                font-size: 1.5rem;
                font-weight: 600;
                color: #1F2937;
                margin-bottom: 1.5rem;
            }

            .manzer-footer {
                margin-top: 2rem;
                text-align: center;
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.875rem;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="manzer-auth-container">
            <div class="manzer-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Manzer Logo" style="height: 50px; margin-right: 12px;">
                <span>Manzer</span>
            </div>

            <div class="manzer-auth-card">
                {{ $slot }}
            </div>

            <div class="manzer-footer">
                <p>&copy; {{ date('Y') }} Manzer</p>
            </div>
        </div>
    </body>
</html>
