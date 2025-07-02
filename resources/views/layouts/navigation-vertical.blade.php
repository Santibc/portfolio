<div class="flex flex-col h-full">
    {{-- Logo superior --}}
    <div class="flex items-center justify-center h-16 border-b dark:border-gray-700 flex-shrink-0">
        <a href="/" class="no-underline">
            {{-- Logo when sidebar is OPEN --}}
            <img x-show="sidebarOpen" 
                 src="{{ asset('images/logodorado2.png') }}" 
                 alt="Full Logo" 
                 class="block h-12 w-auto">

            {{-- Logo when sidebar is CLOSED --}}
            <img x-show="!sidebarOpen" 
                 src="{{ asset('images/logoico.png') }}" 
                 alt="Icon Logo" 
                 class="block h-12 w-auto">
        </a>
    </div>

    {{-- Menú de navegación --}}
    <nav class="flex-1 px-2 py-4 space-y-2">
        {{-- Inicio --}}
        <a href="/dashboard"
           class="flex items-center px-4 py-2 rounded-md transition-colors duration-200 no-underline text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
           {{ \App\Helpers\ActiveLinkHelper::activeLinkClasses(['dashboard']) }}"
           :class="{'justify-center': !sidebarOpen}">
            <svg class="h-6 w-6" :class="{'mr-3': sidebarOpen}" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity ease-in-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity ease-in-out duration-100"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="whitespace-nowrap">Inicio</span>
        </a>

        {{-- Usuarios --}}
        <a href="/usuarios"
           class="flex items-center px-4 py-2 rounded-md transition-colors duration-200 no-underline text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700
                  {{ \App\Helpers\ActiveLinkHelper::activeLinkClasses(['usuarios', 'usuarios/*']) }}"
           :class="{'justify-center': !sidebarOpen}">
            <svg class="h-6 w-6" :class="{'mr-3': sidebarOpen}" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity ease-in-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity ease-in-out duration-100"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="whitespace-nowrap">Usuarios</span>
        </a>

        {{-- Puedes agregar más enlaces aquí --}}

    </nav>

    {{-- Botón Salir --}}
    <div class="px-4 py-3 border-t dark:border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-start gap-3 text-red-600 hover:text-white hover:bg-red-600 dark:hover:bg-red-700 px-4 py-2 rounded-md transition"
                :class="{'justify-center': !sidebarOpen}">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span x-show="sidebarOpen"
                      x-transition:enter="transition-opacity ease-in-out duration-300"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in-out duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0">
                    Salir
                </span>
            </button>
        </form>
    </div>
</div>
