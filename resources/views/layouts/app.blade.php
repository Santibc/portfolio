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
            var serverTheme = @json(Auth::user()?->theme);
            var stored = localStorage.getItem('sopas-theme');
            var preference = stored || (serverTheme ? serverTheme : 'auto');
            var resolved = preference;
            if (preference === 'auto') {
                resolved = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (!stored && serverTheme) localStorage.setItem('sopas-theme', serverTheme);
            var html = document.documentElement;
            html.setAttribute('data-theme', resolved);
            html.setAttribute('data-bs-theme', resolved);
            if (resolved === 'dark') html.classList.add('dark');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-cream-50 dark:bg-surface-dark text-cream-900 dark:text-cream-100 font-sans antialiased">

    {{-- ============== SIDEBAR (drawer mobile + fijo desktop) ============== --}}
    <aside id="app-sidebar"
        class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform fixed top-0 start-0 bottom-0 z-[60] w-72 lg:translate-x-0 lg:end-auto lg:bottom-0 bg-white dark:bg-cream-950/80 backdrop-blur border-e border-cream-200 dark:border-cream-800 flex flex-col">

        {{-- Brand --}}
        <div class="px-6 py-5 flex items-center gap-3 border-b border-cream-200 dark:border-cream-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Sopas y Sopitas') }}" class="h-10 w-10 object-contain shrink-0">
                <span class="flex flex-col leading-tight">
                    <span class="font-display font-bold text-cream-900 dark:text-cream-50">{{ config('app.name', 'Sopas y Sopitas') }}</span>
                    <span class="brand-script text-xs text-primary-600 dark:text-primary-300 -mt-0.5">comida con cariño</span>
                </span>
            </a>
        </div>

        {{-- Navigation --}}
        @include('layouts.navigation-vertical')

        {{-- Sidebar footer (logout) --}}
        <div class="border-t border-cream-200 dark:border-cream-800 p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full inline-flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-cream-700 hover:bg-cream-100 hover:text-rose-600 dark:text-cream-300 dark:hover:bg-cream-900 transition-colors">
                    <x-icon name="log-out" class="w-4 h-4" />
                    Cerrar sesion
                </button>
            </form>
        </div>
    </aside>

    {{-- ============== HEADER ============== --}}
    <header class="lg:ms-72 sticky top-0 z-40 bg-cream-50/80 dark:bg-surface-dark/80 backdrop-blur-md border-b border-cream-200/80 dark:border-cream-800/80">
        <div class="flex items-center justify-between gap-3 h-16 px-4 sm:px-6">
            {{-- Mobile menu button --}}
            <button type="button"
                class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl text-cream-700 hover:bg-cream-100 dark:text-cream-300 dark:hover:bg-cream-800 transition-colors"
                data-hs-overlay="#app-sidebar"
                aria-label="Abrir menu">
                <x-icon name="menu" class="w-5 h-5" />
            </button>

            {{-- Page title slot --}}
            <div class="flex-1 min-w-0">
                @hasSection('header')
                    @yield('header')
                @endif
            </div>

            {{-- Right cluster --}}
            <div class="flex items-center gap-1.5">
                {{-- Theme toggle --}}
                <button type="button" id="themeToggleBtn"
                    class="theme-toggle-btn inline-flex items-center justify-center w-10 h-10 rounded-xl text-cream-700 hover:bg-cream-100 dark:text-cream-300 dark:hover:bg-cream-800 transition-colors"
                    aria-label="Cambiar tema">
                    <span class="dark:hidden"><x-icon name="moon" class="w-5 h-5" /></span>
                    <span class="hidden dark:inline-flex"><x-icon name="sun" class="w-5 h-5" /></span>
                </button>

                {{-- User menu --}}
                <div class="hs-dropdown relative inline-flex">
                    <button type="button" class="hs-dropdown-toggle inline-flex items-center gap-2.5 px-1.5 py-1 rounded-xl hover:bg-cream-100 dark:hover:bg-cream-800 transition-colors">
                        <x-avatar :src="Auth::user()->hasProfilePhoto() ? Auth::user()->profile_photo_url : null" :name="Auth::user()->name" size="sm" />
                        <span class="hidden sm:flex flex-col items-start leading-tight pr-1">
                            <span class="text-sm font-semibold text-cream-900 dark:text-cream-50 truncate max-w-[12ch]">{{ Auth::user()->name }}</span>
                            <span class="text-[11px] text-cream-600 dark:text-cream-400">{{ Auth::user()->roles->first()->name ?? 'Usuario' }}</span>
                        </span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-cream-500 hidden sm:inline" />
                    </button>

                    <div class="hs-dropdown-menu transition-[opacity,margin] duration-200 hs-dropdown-open:opacity-100 opacity-0 hidden min-w-56 z-50 mt-2 surface-elevated p-2 end-0">
                        <div class="px-3 py-2.5 border-b border-cream-200 dark:border-cream-800 mb-1">
                            <p class="text-sm font-semibold text-cream-900 dark:text-cream-50 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-cream-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-item href="{{ route('profile.edit') }}" icon="user-cog">
                            Mi perfil
                        </x-dropdown-item>
                        <x-dropdown-item href="{{ route('components.showcase') }}" icon="component">
                            Componentes UI
                        </x-dropdown-item>
                        <div class="my-1 h-px bg-cream-200 dark:bg-cream-800"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-x-2.5 py-2 px-3 rounded-lg text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors">
                                <x-icon name="log-out" class="w-4 h-4" />
                                Cerrar sesion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ============== MAIN ============== --}}
    <main class="lg:ms-72 px-4 sm:px-6 py-6">
        {{-- Flash --}}
        @if (session('success'))
            <x-alert variant="success" dismissible class="mb-4" data-reveal>
                {{ session('success') }}
            </x-alert>
        @endif
        @if (session('error'))
            <x-alert variant="danger" dismissible class="mb-4" data-reveal>
                {{ session('error') }}
            </x-alert>
        @endif
        @if (session('warning'))
            <x-alert variant="warning" dismissible class="mb-4" data-reveal>
                {{ session('warning') }}
            </x-alert>
        @endif
        @if (session('status') === 'profile-updated')
            <x-alert variant="success" dismissible class="mb-4">
                Perfil actualizado.
            </x-alert>
        @endif
        @if (session('status') === 'password-updated')
            <x-alert variant="success" dismissible class="mb-4">
                Contrasena actualizada.
            </x-alert>
        @endif

        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    @stack('scripts')
</body>
</html>
