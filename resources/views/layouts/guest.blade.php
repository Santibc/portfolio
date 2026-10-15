<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}"/>
    <title>{{ config('app.name', 'El Pan Dorado DC') }}@hasSection('title') · @yield('title')@endif</title>

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

    {{-- Decorativo: espiga de trigo sutil de fondo --}}
    <svg class="pointer-events-none absolute -top-32 -right-32 w-[480px] h-[480px] text-primary-200/40 dark:text-primary-900/30" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M32 58V22M32 22c-3-3-3-9 0-14 3 5 3 11 0 14zM32 32c-5.5 0-10-4.5-10-10 5.5 0 10 4.5 10 10zM32 32c5.5 0 10-4.5 10-10-5.5 0-10 4.5-10 10zM32 42c-5.5 0-10-4.5-10-10 5.5 0 10 4.5 10 10zM32 42c5.5 0 10-4.5 10-10-5.5 0-10 4.5-10 10zM32 52c-5.5 0-10-4.5-10-10 5.5 0 10 4.5 10 10zM32 52c5.5 0 10-4.5 10-10-5.5 0-10 4.5-10 10z" />
    </svg>
    <svg class="pointer-events-none absolute -bottom-24 -left-24 w-96 h-96 text-accent-200/40 dark:text-accent-900/30" viewBox="0 0 200 200" fill="currentColor">
        <circle cx="100" cy="100" r="80" />
    </svg>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4 py-10">
        {{-- Logo + Brand --}}
        <a href="{{ url('/') }}" class="mb-8 flex flex-col items-center gap-2">
            {{-- El logo ya incluye nombre y lema; la variante oscura aclara los bronces --}}
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'El Pan Dorado DC') }}" class="h-36 sm:h-40 w-auto dark:hidden">
            <img src="{{ asset('images/logo-dark.png') }}" alt="{{ config('app.name', 'El Pan Dorado DC') }}" class="hidden h-36 sm:h-40 w-auto dark:block">
        </a>

        {{-- Auth card --}}
        <div class="w-full max-w-md surface-elevated p-8 rounded-3xl">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-cream-600 dark:text-cream-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'El Pan Dorado DC') }}
        </p>
    </div>
</body>
</html>
