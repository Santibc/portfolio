<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/logoico.png') }}"/>
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css']) 
        @livewireStyles

        {{-- Iconos y Bootstrap opcional --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    </head>
    <body class="bg-background text-foreground">
        <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-gray-100 dark:bg-gray-900">
            {{-- Sidebar izquierdo --}}
            <aside class="flex-shrink-0 bg-white dark:bg-gray-800 shadow-lg transition-all duration-300"
                   :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }">
                @include('layouts.navigation-vertical')
            </aside>

            {{-- Contenido principal --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                {{-- Encabezado superior --}}
                <header class="flex justify-between items-center px-4 h-16 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-300 focus:outline-none">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>

                    <div class="flex items-center gap-4 text-sm text-gray-700 dark:text-gray-300">
                        <div class="text-right">
                            <div class="font-semibold">{{ Auth::user()->name }}</div>
                            <div class="text-xs">{{ Auth::user()->email }}</div>
                        </div>
                        <div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff"
                                alt="Avatar"
                                class="w-8 h-8 rounded-full object-cover">
                        </div>
                    </div>
                </header>

                {{-- Contenido dinámico --}}
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 dark:bg-gray-700">
                    <div class="container mx-auto px-6 py-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        {{-- Scripts de Vite --}}
        @vite(['resources/js/app.js'])

        {{-- Scripts Livewire --}}
        @livewireScripts

        {{-- Scripts individuales de cada página --}}
        @stack('scripts')

        {{-- Bootstrap JS opcional --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    </body>
</html>
