<div class="flex flex-col h-full">
    <div class="flex items-center justify-center h-16 border-b dark:border-gray-700 flex-shrink-0">
        <a href="/">
            {{-- Logo when sidebar is OPEN --}}
            <img x-show="sidebarOpen" 
                 src="{{ Vite::asset('resources/images/logoico.png') }}" 
                 alt="Full Logo" 
                 class="block h-12 w-auto">

            {{-- Logo when sidebar is CLOSED --}}
            <img x-show="!sidebarOpen" 
                 src="{{ Vite::asset('resources/images/logoico.png') }}" 
                 alt="Icon Logo" 
                 class="block h-12 w-auto">
        </a>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-2">
    <a href="/"
       class="flex items-center px-4 py-2 rounded-md transition-colors duration-200
              {{ \App\Helpers\ActiveLinkHelper::activeLinkClasses('dashboard') }}"
       :class="{'justify-center': !sidebarOpen}">
            {{-- Icon (always visible) --}}
            <svg class="h-6 w-6" :class="{'mr-3': sidebarOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            {{-- Text (only visible when sidebarOpen is true) --}}
            <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in-out duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="whitespace-nowrap">Inicio</span>
        </a>
    <a href="/usuarios"
       class="flex items-center px-4 py-2 rounded-md transition-colors duration-200
              {{ \App\Helpers\ActiveLinkHelper::activeLinkClasses(['usuarios', 'usuarios/*']) }}"
       :class="{'justify-center': !sidebarOpen}">
            {{-- Icon (always visible) --}}
                <svg class="h-6 w-6" :class="{'mr-3': sidebarOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            {{-- Text (only visible when sidebarOpen is true) --}}
            <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in-out duration-100" 
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="whitespace-nowrap">Usuarios</span>
        </a>

        {{-- Example of a link with a submenu --}}
        <div x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md focus:outline-none" :class="{'justify-center': !sidebarOpen}">
                {{-- Icon (always visible) --}}
                <svg class="h-6 w-6" :class="{'mr-3': sidebarOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{-- Text (only visible when sidebarOpen is true) --}}
                <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in-out duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="whitespace-nowrap flex-1 text-left">Users</span>
                
                {{-- Dropdown arrow (only visible when sidebarOpen is true) --}}
                <svg x-show="sidebarOpen" :class="{'rotate-90': open, 'rotate-0': !open}" class="ml-2 h-4 w-4 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            {{-- Submenu (only visible when sidebarOpen is true and open is true) --}}
            <div x-show="sidebarOpen && open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-2 pl-4">
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md">
                    <span class="whitespace-nowrap">View All Users</span>
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md">
                    <span class="whitespace-nowrap">Add New User</span>
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md">
                    <span class="whitespace-nowrap">User Roles</span>
                </a>
            </div>
        </div>

        {{-- You can add more main links or links with submenus here --}}

    </nav>
</div>