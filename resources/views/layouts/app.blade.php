<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <title>@yield('title', 'Inicio') · {{ config('app.name', 'Manzer') }}</title>

    {{-- Theme init inline para evitar flash --}}
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

    @stack('styles')
</head>
<body class="min-h-screen">
    {{-- Skip to content (a11y) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-primary-500 focus:px-4 focus:py-2 focus:text-white">
        Saltar al contenido
    </a>

    <div x-data class="relative flex min-h-screen">
        {{-- Overlay móvil --}}
        <div
            x-show="$store.sidebar.open"
            x-transition.opacity
            @click="$store.sidebar.close()"
            class="fixed inset-0 z-30 bg-zinc-900/50 backdrop-blur-sm lg:hidden"
            aria-hidden="true"
            style="display: none;"
        ></div>

        {{-- Sidebar --}}
        <aside
            :class="$store.sidebar.open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-zinc-200 bg-white transition-transform duration-300 ease-smooth dark:border-zinc-800 dark:bg-zinc-900 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
        >
            {{-- Sidebar header / logo --}}
            <div class="flex h-16 shrink-0 items-center gap-3 border-b border-zinc-200 px-6 dark:border-zinc-800">
                <img src="{{ asset('images/logo.png') }}" alt="CLC & CIA" class="h-10 w-10 object-contain">
                <div class="leading-tight">
                    <div class="text-sm font-bold tracking-tight">CLC & CIA</div>
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400">S.A.S.</div>
                </div>
            </div>

            {{-- Nav --}}
            <div class="flex-1 overflow-y-auto scrollbar-thin px-3 py-4">
                @include('layouts.navigation-vertical')
            </div>

            {{-- Sidebar footer --}}
            <div class="border-t border-zinc-200 p-3 dark:border-zinc-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 hover:text-red-600 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-red-400">
                        <i class="bi bi-box-arrow-left text-base"></i>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Contenido --}}
        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Header --}}
            <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-3 border-b border-zinc-200 bg-white/80 px-4 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/80 sm:px-6">
                {{-- Toggle sidebar (mobile) --}}
                <button
                    type="button"
                    @click="$store.sidebar.toggle()"
                    class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 lg:hidden"
                    aria-label="Abrir menú"
                    :aria-expanded="$store.sidebar.open.toString()"
                >
                    <i class="bi bi-list text-xl"></i>
                </button>

                {{-- Spacer --}}
                <div class="flex-1"></div>

                {{-- Actions --}}
                <div class="flex items-center gap-1">
                    {{-- Theme toggle --}}
                    <button
                        type="button"
                        @click="$store.theme.toggle()"
                        class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                        aria-label="Cambiar tema"
                    >
                        <i class="bi bi-sun-fill text-lg dark:hidden"></i>
                        <i class="bi bi-moon-fill hidden text-lg dark:inline"></i>
                    </button>

                    {{-- User dropdown --}}
                    <div x-data="{ open: false }" @click.away="open = false" class="relative ms-1">
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex items-center gap-2 rounded-lg p-1 pr-3 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                            :aria-expanded="open.toString()"
                            aria-haspopup="true"
                        >
                            @if(Auth::user()->hasProfilePhoto())
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-xs font-semibold text-primary-700 dark:bg-primary-900 dark:text-primary-300">
                                    {{ Auth::user()->initials }}
                                </div>
                            @endif
                            <div class="hidden text-left sm:block">
                                <div class="text-sm font-medium leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ Auth::user()->roles->first()->name ?? 'Usuario' }}</div>
                            </div>
                            <i class="bi bi-chevron-down hidden text-xs text-zinc-400 sm:inline"></i>
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-smooth duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-smooth duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl bg-white p-1 shadow-lg ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-800"
                            role="menu"
                            style="display: none;"
                        >
                            <div class="border-b border-zinc-200 px-3 py-2 dark:border-zinc-800">
                                <div class="text-sm font-medium">{{ Auth::user()->name }}</div>
                                <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ Auth::user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" role="menuitem">
                                <i class="bi bi-person-gear"></i> Mi perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950" role="menuitem">
                                    <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main --}}
            <main id="main-content" class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <x-flash-messages />

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
