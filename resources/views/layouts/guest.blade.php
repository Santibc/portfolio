<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>@yield('title', 'Autenticación') · CLC & CIA S.A.S.</title>

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
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Form side --}}
        <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16">
            <div class="mx-auto w-full max-w-md">
                {{-- Logo CLC & CIA --}}
                <a href="/" class="mb-8 inline-flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="CLC & CIA S.A.S." class="h-16 w-16 object-contain">
                    <div class="leading-tight">
                        <div class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">CLC & CIA S.A.S.</div>
                        <div class="text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Plataforma de facturación</div>
                    </div>
                </a>

                {{ $slot }}
            </div>

            <div class="mx-auto mt-10 w-full max-w-md text-center text-xs text-zinc-400 dark:text-zinc-500">
                © {{ date('Y') }} CLC & CIA S.A.S. · NIT 901.249.576-9
            </div>
        </div>

        {{-- Visual side --}}
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-primary-500 via-primary-600 to-primary-800 lg:flex lg:items-center lg:justify-center">
            <div class="absolute -left-20 -top-20 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -right-20 -bottom-20 h-96 w-96 rounded-full bg-primary-300/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(255,255,255,0.08),transparent_60%)]"></div>

            <div class="relative z-10 max-w-md px-12 text-white">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur">
                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                    Facturación DIAN · Multi-moneda
                </div>
                <h2 class="text-4xl font-bold leading-tight tracking-tight">
                    Factura a tus clientes<br/>del mundo entero.
                </h2>
                <p class="mt-4 text-lg text-white/80">
                    Gestiona productos, clientes nacionales e internacionales, plantillas personalizadas y facturación electrónica DIAN — todo desde un solo lugar.
                </p>

                <div class="mt-10 grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-white/10 p-4 backdrop-blur">
                        <i class="bi bi-currency-exchange text-2xl"></i>
                        <div class="mt-2 text-xs font-medium">Multi-moneda</div>
                    </div>
                    <div class="rounded-xl bg-white/10 p-4 backdrop-blur">
                        <i class="bi bi-file-earmark-check text-2xl"></i>
                        <div class="mt-2 text-xs font-medium">DIAN vía Siigo</div>
                    </div>
                    <div class="rounded-xl bg-white/10 p-4 backdrop-blur">
                        <i class="bi bi-palette text-2xl"></i>
                        <div class="mt-2 text-xs font-medium">Plantillas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
