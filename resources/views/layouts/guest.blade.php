<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>{{ config('app.name', 'Sopas y Sopitas') }}@hasSection('title') · @yield('title')@endif</title>

    {{-- Theme bootstrap (anti-FOUC) --}}
    <script>
        (function () {
            var stored = localStorage.getItem('sopas-theme') || 'auto';
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-cream-100 via-cream-50 to-accent-100 dark:from-cream-950 dark:via-surface-dark dark:to-cream-900 font-sans antialiased relative overflow-x-hidden">

    {{-- Decorativo: cuenco SVG sutil de fondo --}}
    <svg class="pointer-events-none absolute -top-32 -right-32 w-[480px] h-[480px] text-primary-200/40 dark:text-primary-900/30" viewBox="0 0 200 200" fill="currentColor">
        <path d="M40 90h120v18a52 52 0 01-52 52H92a52 52 0 01-52-52V90zM72 70c0-10 6-10 6-20s-6-10-6-20M100 70c0-10 6-10 6-20s-6-10-6-20M128 70c0-10 6-10 6-20s-6-10-6-20" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" />
    </svg>
    <svg class="pointer-events-none absolute -bottom-24 -left-24 w-96 h-96 text-accent-200/40 dark:text-accent-900/30" viewBox="0 0 200 200" fill="currentColor">
        <circle cx="100" cy="100" r="80" />
    </svg>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4 py-10">
        {{-- Logo + Brand --}}
        <a href="{{ url('/') }}" class="mb-8 flex flex-col items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Sopas y Sopitas') }}" class="h-20 w-auto drop-shadow-soft">
            <span class="brand-script text-3xl text-primary-700 dark:text-primary-200">Sopas y Sopitas</span>
            <span class="text-xs text-cream-600 dark:text-cream-400 -mt-2">comida con cariño</span>
        </a>

        {{-- Auth card --}}
        <div class="w-full max-w-md surface-elevated p-8 rounded-3xl">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-cream-600 dark:text-cream-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Sopas y Sopitas') }}
        </p>
    </div>
</body>
</html>
