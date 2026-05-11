<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>{{ config('app.name', 'Sopas y Sopitas') }}</title>

    <script>
        (function () {
            var stored = localStorage.getItem('sopas-theme') || 'auto';
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
<body class="min-h-full h-full bg-gradient-to-br from-cream-100 via-cream-50 to-accent-100 dark:from-cream-950 dark:via-surface-dark dark:to-cream-900 font-sans antialiased relative overflow-hidden">

    {{-- Blobs decorativos --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-40 -right-40 w-[520px] h-[520px] rounded-full bg-primary-200/40 dark:bg-primary-900/20 blur-3xl"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -bottom-40 -left-40 w-[420px] h-[420px] rounded-full bg-accent-200/40 dark:bg-accent-900/20 blur-3xl"></div>

    <main class="relative h-full flex flex-col px-4 py-8 text-center">
        <div class="flex-1 flex flex-col items-center justify-center">
            <div data-reveal class="mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Sopas y Sopitas') }}" class="max-w-[280px] h-auto" onerror="this.style.display='none'">
            </div>

            <h1 data-reveal class="font-display text-4xl md:text-6xl font-extrabold tracking-tight text-cream-900 dark:text-cream-50 max-w-3xl">
                Comida casera, hecha con cariño.
            </h1>

            <p data-reveal class="mt-5 max-w-xl text-base md:text-lg text-cream-700 dark:text-cream-300 leading-relaxed">
                Plataforma para gestionar tu negocio de comida desde un solo lugar — pedidos, clientes y mas.
            </p>

            <div data-reveal class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3.5 rounded-2xl shadow-soft hover:shadow-glow transition-all hover:-translate-y-0.5">
                            Ir al Dashboard
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3.5 rounded-2xl shadow-soft hover:shadow-glow transition-all hover:-translate-y-0.5">
                            Iniciar sesion
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @endauth
                @endif
            </div>
        </div>

        <p data-reveal class="text-xs text-cream-600 dark:text-cream-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Sopas y Sopitas') }}. Todos los derechos reservados.
        </p>
    </main>
</body>
</html>
