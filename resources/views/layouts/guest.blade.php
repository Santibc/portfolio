<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any"/>
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}"/>
    <meta name="theme-color" content="#e4550a"/>
    <title>{{ config('app.name', 'Papas del Alma') }}@hasSection('title') · @yield('title')@endif</title>

    {{-- Theme bootstrap (anti-FOUC) --}}
    <script>
        (function () {
            var stored = localStorage.getItem('papas-theme') || 'auto';
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

    {{-- Decorativo: cono de papas fritas sutil de fondo --}}
    <svg class="pointer-events-none absolute -top-24 -right-28 w-[440px] h-[440px] rotate-12 text-primary-200/60 dark:text-primary-900/40" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
        <path d="M70 34l8 58M92 22l4 70M112 26l-2 66M132 36l-8 56M56 48l14 44M146 50l-14 42" />
        <path d="M50 92h100l-16 86H66L50 92z" fill="currentColor" fill-opacity="0.35" />
        <path d="M84 122a16 16 0 0032 0" />
    </svg>
    <svg class="pointer-events-none absolute -bottom-24 -left-24 w-96 h-96 text-accent-200/40 dark:text-accent-900/30" viewBox="0 0 200 200" fill="currentColor">
        <circle cx="100" cy="100" r="80" />
    </svg>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4 py-10">
        {{-- Logo + Brand --}}
        <a href="{{ url('/') }}" class="mb-8 flex flex-col items-center gap-2">
            <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('app.name', 'Papas del Alma') }}" class="h-28 w-28 rounded-3xl object-cover shadow-soft-lg ring-4 ring-white/80 dark:ring-cream-900/80">
            <span class="brand-script text-4xl text-primary-600 dark:text-primary-300">{{ config('app.name', 'Papas del Alma') }}</span>
            <span class="text-xs text-cream-600 dark:text-cream-400 -mt-2">hechas con el alma</span>
        </a>

        {{-- Auth card --}}
        <div class="w-full max-w-md surface-elevated p-8 rounded-3xl">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-cream-600 dark:text-cream-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Papas del Alma') }}
        </p>
    </div>
</body>
</html>
