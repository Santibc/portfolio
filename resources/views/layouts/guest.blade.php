<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.brand-head')
    <title>{{ config('app.name', 'DorilokosMix') }}@hasSection('title') · @yield('title')@endif</title>

    {{-- Theme bootstrap (anti-FOUC) --}}
    <script>
        (function () {
            var stored = localStorage.getItem('dorilokos-theme') || 'auto';
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
{{-- El fondo de marca (morado + fuego) es igual en ambos modos; la tarjeta sí respeta claro/oscuro --}}
<body class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-800 to-primary-950 dark:from-primary-800 dark:via-primary-950 dark:to-surface-dark font-sans antialiased relative overflow-x-hidden">

    {{-- Resplandores fuego --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-32 -right-24 w-[420px] h-[420px] rounded-full bg-accent-500/30 blur-3xl"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -bottom-40 -left-32 w-[460px] h-[460px] rounded-full bg-flame-500/25 blur-3xl"></div>

    {{-- Triángulos tipo nacho --}}
    <svg aria-hidden="true" class="pointer-events-none absolute top-10 -left-10 w-72 h-72 -rotate-12 text-accent-300/25" viewBox="0 0 200 200" fill="none">
        <path d="M20 30 L185 95 L55 180 Z" stroke="currentColor" stroke-width="6" stroke-linejoin="round" />
        <path d="M45 55 L155 97 L68 152 Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round" opacity=".6" />
    </svg>
    <svg aria-hidden="true" class="pointer-events-none absolute bottom-6 -right-8 w-64 h-64 rotate-[160deg] text-flame-400/25" viewBox="0 0 200 200" fill="none">
        <path d="M20 30 L185 95 L55 180 Z" stroke="currentColor" stroke-width="6" stroke-linejoin="round" />
    </svg>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4 py-10">
        {{-- Logo + Brand --}}
        <a href="{{ url('/') }}" class="mb-6 flex flex-col items-center gap-1">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'DorilokosMix') }}" class="h-36 sm:h-44 w-auto drop-shadow-[0_10px_30px_rgba(243,144,48,0.35)] transition-transform duration-300 hover:scale-105 hover:-rotate-2">
            <span class="brand-script text-lg tracking-wide text-accent-300">¡Sabor bien loko!</span>
        </a>

        {{-- Auth card --}}
        <div class="w-full max-w-md surface-elevated p-8 rounded-3xl ring-1 ring-white/10">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-primary-100/70">
            &copy; {{ date('Y') }} {{ config('app.name', 'DorilokosMix') }}
        </p>
    </div>
</body>
</html>
