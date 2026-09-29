<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('layouts.partials.brand-head')
    <title>{{ config('app.name', 'DorilokosMix') }}</title>

    <script>
        (function () {
            var stored = localStorage.getItem('dorilokos-theme') || 'auto';
            var resolved = stored === 'auto'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : stored;
            var html = document.documentElement;
            html.setAttribute('data-theme', resolved);
            if (resolved === 'dark') html.classList.add('dark');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full h-full bg-gradient-to-br from-primary-600 via-primary-800 to-primary-950 dark:from-primary-800 dark:via-primary-950 dark:to-surface-dark font-sans antialiased relative overflow-hidden">

    {{-- Blobs decorativos --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-40 -right-40 w-[520px] h-[520px] rounded-full bg-accent-500/30 blur-3xl"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -bottom-40 -left-40 w-[420px] h-[420px] rounded-full bg-flame-500/25 blur-3xl"></div>

    <main class="relative h-full flex flex-col px-4 py-8 text-center">
        <div class="flex-1 flex flex-col items-center justify-center">
            <div data-reveal class="mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'DorilokosMix') }}" class="w-60 md:w-80 h-auto drop-shadow-[0_10px_30px_rgba(243,144,48,0.35)]" onerror="this.style.display='none'">
            </div>

            <h1 data-reveal class="font-display text-4xl md:text-6xl font-extrabold tracking-tight text-white max-w-3xl">
                ¡Sabor bien <span class="brand-script font-normal tracking-wide text-accent-300">loko</span>!
            </h1>

            <p data-reveal class="mt-5 max-w-xl text-base md:text-lg text-primary-100/85 leading-relaxed">
                Ventas, caja, menú, compras y gastos de DorilokosMix en un solo lugar.
            </p>

            <div data-reveal class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-400 text-primary-950 font-semibold px-7 py-3.5 rounded-2xl shadow-soft hover:shadow-glow transition-all hover:-translate-y-0.5">
                            Ir al Dashboard
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-400 text-primary-950 font-semibold px-7 py-3.5 rounded-2xl shadow-soft hover:shadow-glow transition-all hover:-translate-y-0.5">
                            Iniciar sesion
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @endauth
                @endif
            </div>
        </div>

        <p data-reveal class="text-xs text-primary-100/70">
            &copy; {{ date('Y') }} {{ config('app.name', 'DorilokosMix') }}. Todos los derechos reservados.
        </p>
    </main>
</body>
</html>
