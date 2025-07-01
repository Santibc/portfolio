<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
       <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/logoico.png') }}"/>
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
<body class="bg-background text-foreground">
    {{-- El estado 'sidebarOpen' ahora controla la visibilidad y el ancho --}}
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-gray-100 dark:bg-gray-900">
        
        <!-- INICIO: Barra Lateral (Sidebar) -->
        {{--
            CAMBIO CLAVE:
            - Se cambia el ancho con clases 'w-64' (abierto) y 'w-20' (cerrado).
            - Se elimina el margen negativo '-ml-64' para un mejor efecto de redimensión.
            - La transición se aplica a 'width' para una animación suave.
        --}}
        <aside
            class="flex-shrink-0 bg-white dark:bg-gray-800 shadow-lg transition-all duration-300"
            :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
        >
            @include('layouts.navigation-vertical')
        </aside>
        <!-- FIN: Barra Lateral (Sidebar) -->

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- INICIO: Cabecera (Header) -->
            <header class="flex justify-between items-center ph-1 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-300 focus:outline-none">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                            @if (isset($header))
                <h1 class="text-xl ">{{ $header }}</h1>
            @endif
                
                
                {{-- Aquí puedes agregar el menú de usuario, notificaciones, etc. --}}
                <div></div> {{-- Elemento para mantener el título centrado si es necesario --}}
            </header>
            <!-- FIN: Cabecera (Header) -->

            <!-- INICIO: Contenido Principal (Main) -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 dark:bg-gray-700">
                <div class="container mx-auto px-6 py-8">
                    {{ $slot }}
                </div>
            </main>
            <!-- FIN: Contenido Principal (Main) -->
        </div>
    </div>
    @livewireScripts
     @stack('scripts')
</body>
</html>
