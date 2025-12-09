<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GVA') }} - Plataforma de Cursos</title>
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
                --gva-primary: #1E40AF;
                --gva-primary-dark: #1E3A8A;
                --gva-secondary: #059669;
            }

            .gva-auth-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
                background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 50%, #059669 100%);
            }

            .gva-logo {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 2rem;
                font-weight: 700;
                color: white;
                margin-bottom: 2rem;
            }

            .gva-logo i {
                font-size: 2.5rem;
            }

            .gva-auth-card {
                width: 100%;
                max-width: 28rem;
                background: white;
                border-radius: 1rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                padding: 2rem;
            }

            .gva-auth-title {
                text-align: center;
                font-size: 1.5rem;
                font-weight: 600;
                color: #1F2937;
                margin-bottom: 1.5rem;
            }

            .gva-footer {
                margin-top: 2rem;
                text-align: center;
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.875rem;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="gva-auth-container">
            <div class="gva-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>GVA</span>
            </div>

            <div class="gva-auth-card">
                {{ $slot }}
            </div>

            <div class="gva-footer">
                <p>&copy; {{ date('Y') }} GVA - Plataforma de Cursos Educativos</p>
            </div>
        </div>
    </body>
</html>
