@php
    $navItem = function ($routeName, $icon, $label, $matchPattern = null) {
        $active = request()->routeIs($matchPattern ?? $routeName);
        $base = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200';
        $cls = $active
            ? 'bg-primary-100 text-primary-800 shadow-sm dark:bg-primary-900/40 dark:text-primary-100'
            : 'text-cream-700 hover:bg-cream-100 hover:text-cream-900 dark:text-cream-300 dark:hover:bg-cream-900 dark:hover:text-cream-50';
        return [$active, $base . ' ' . $cls];
    };
@endphp

<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

    <p class="px-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-cream-500">Principal</p>

    @php [$active, $cls] = $navItem('dashboard', 'home', 'Inicio'); @endphp
    <a href="{{ route('dashboard') }}" class="{{ $cls }}">
        <x-icon name="home" class="w-4 h-4" />
        <span class="flex-1">Inicio</span>
        @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
    </a>

    @php
        $mercadoActive = request()->routeIs('productos-mercado.*')
            || request()->routeIs('registro-mercado.*')
            || request()->routeIs('mercado-dashboard.*');
    @endphp
    <div x-data="{ open: @js($mercadoActive) }">
        <button type="button" @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       {{ $mercadoActive
                            ? 'bg-primary-100 text-primary-800 shadow-sm dark:bg-primary-900/40 dark:text-primary-100'
                            : 'text-cream-700 hover:bg-cream-100 hover:text-cream-900 dark:text-cream-300 dark:hover:bg-cream-900 dark:hover:text-cream-50' }}">
            <x-icon name="shopping-basket" class="w-4 h-4" />
            <span class="flex-1 text-left">Mercado</span>
            <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200"
                    x-bind:class="open ? 'rotate-180' : ''" />
        </button>

        <div x-show="open" x-transition.duration.150ms class="mt-1 ml-3 pl-3 border-l border-cream-200 dark:border-cream-800 space-y-1" x-cloak>
            @php [$active, $cls] = $navItem('mercado-dashboard.index', 'gauge', 'Dashboard', 'mercado-dashboard.*'); @endphp
            <a href="{{ route('mercado-dashboard.index') }}" class="{{ $cls }}">
                <x-icon name="gauge" class="w-4 h-4" />
                <span class="flex-1">Dashboard</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('productos-mercado.index', 'shopping-basket', 'Productos', 'productos-mercado.*'); @endphp
            <a href="{{ route('productos-mercado.index') }}" class="{{ $cls }}">
                <x-icon name="shopping-basket" class="w-4 h-4" />
                <span class="flex-1">Productos</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('registro-mercado.index', 'shopping-cart', 'Registrar', 'registro-mercado.*'); @endphp
            <a href="{{ route('registro-mercado.index') }}" class="{{ $cls }}">
                <x-icon name="shopping-cart" class="w-4 h-4" />
                <span class="flex-1">Registrar</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>
        </div>
    </div>

    <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-cream-500">Cuenta</p>

    @php [$active, $cls] = $navItem('profile.edit', 'user-cog', 'Mi perfil'); @endphp
    <a href="{{ route('profile.edit') }}" class="{{ $cls }}">
        <x-icon name="user-cog" class="w-4 h-4" />
        <span class="flex-1">Mi perfil</span>
        @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
    </a>

    <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-cream-500">Sistema</p>

    @php [$active, $cls] = $navItem('components.showcase', 'component', 'Componentes'); @endphp
    <a href="{{ route('components.showcase') }}" class="{{ $cls }}">
        <x-icon name="component" class="w-4 h-4" />
        <span class="flex-1">Componentes UI</span>
        @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
    </a>
</nav>
