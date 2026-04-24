<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLC & CIA S.A.S. · Plataforma de facturación</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>

    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <div class="relative min-h-screen overflow-hidden">
        {{-- Background decorations --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/4 top-0 h-96 w-96 rounded-full bg-primary-300/20 blur-3xl dark:bg-primary-600/10"></div>
            <div class="absolute bottom-0 right-1/4 h-96 w-96 rounded-full bg-primary-200/30 blur-3xl dark:bg-primary-800/20"></div>
        </div>

        {{-- Nav --}}
        <header class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="CLC & CIA" class="h-11 w-11 object-contain">
                <div class="leading-tight">
                    <div class="text-base font-bold tracking-tight">CLC & CIA S.A.S.</div>
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Facturación internacional</div>
                </div>
            </a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">
                            Ir al panel
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">
                            Iniciar sesión
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @endauth
                </nav>
            @endif
        </header>

        {{-- Hero --}}
        <main class="relative z-10 mx-auto max-w-6xl px-6 py-20 text-center sm:py-32">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/50 px-4 py-1.5 text-xs font-medium text-zinc-600 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-400">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-primary-500"></span>
                Facturación electrónica DIAN + clientes internacionales
            </div>

            <h1 class="mx-auto max-w-3xl text-5xl font-bold tracking-tight sm:text-7xl">
                Factura a tus clientes
                <span class="bg-gradient-to-r from-primary-500 to-primary-700 bg-clip-text text-transparent">del mundo entero.</span>
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                Plataforma interna de CLC & CIA S.A.S. para gestionar productos, clientes nacionales e internacionales,
                plantillas personalizables por cliente y emisión electrónica ante la DIAN vía Siigo.
            </p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary text-base px-6 py-3">
                        Ir al panel
                        <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary text-base px-6 py-3">
                        Iniciar sesión
                        <i class="bi bi-arrow-right"></i>
                    </a>
                @endauth
            </div>

            {{-- Feature bento --}}
            <div class="mx-auto mt-20 grid max-w-4xl grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="card text-left">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <h3 class="text-sm font-semibold">Multi-moneda</h3>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">COP, EUR, USD. La factura al cliente se muestra en su moneda y a la DIAN se envía en pesos.</p>
                </div>
                <div class="card text-left">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <h3 class="text-sm font-semibold">Electrónica DIAN</h3>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Integración con Siigo vía API oficial. Opcional por factura — tú decides cuándo emitirla.</p>
                </div>
                <div class="card text-left">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                        <i class="bi bi-palette"></i>
                    </div>
                    <h3 class="text-sm font-semibold">Plantillas por cliente</h3>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Editor visual tipo Word para personalizar el PDF que entregas a cada cliente.</p>
                </div>
            </div>
        </main>

        <footer class="relative z-10 border-t border-zinc-200 py-8 text-center text-xs text-zinc-400 dark:border-zinc-800 dark:text-zinc-500">
            © {{ date('Y') }} CLC & CIA S.A.S. · NIT 901.249.576-9 · Cali, Colombia
        </footer>
    </div>
</body>
</html>
