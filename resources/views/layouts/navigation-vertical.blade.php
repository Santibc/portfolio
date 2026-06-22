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

    @php [$active, $cls] = $navItem('consolidado.index', 'pie-chart', 'Consolidado', 'consolidado.*'); @endphp
    <a href="{{ route('consolidado.index') }}" class="{{ $cls }}">
        <x-icon name="pie-chart" class="w-4 h-4" />
        <span class="flex-1">Consolidado</span>
        @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
    </a>

    @php
        $mercadoActive = request()->routeIs('productos-mercado.*')
            || request()->routeIs('registro-mercado.*')
            || request()->routeIs('mercado-dashboard.*')
            || request()->routeIs('lista-mercado.*');
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

            @php
                $listaActive = request()->routeIs('lista-mercado.index')
                    || request()->routeIs('lista-mercado.tipo')
                    || request()->routeIs('lista-mercado.iniciar')
                    || request()->routeIs('lista-mercado.finalizar')
                    || request()->routeIs('lista-mercado.cancelar')
                    || request()->routeIs('lista-mercado.completado')
                    || request()->routeIs('lista-mercado.item.*');
                $listaCls = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 ' . (
                    $listaActive
                        ? 'bg-primary-100 text-primary-800 shadow-sm dark:bg-primary-900/40 dark:text-primary-100'
                        : 'text-cream-700 hover:bg-cream-100 hover:text-cream-900 dark:text-cream-300 dark:hover:bg-cream-900 dark:hover:text-cream-50'
                );
            @endphp
            <a href="{{ route('lista-mercado.index') }}" class="{{ $listaCls }}">
                <x-icon name="clipboard-list" class="w-4 h-4" />
                <span class="flex-1">Lista mercado</span>
                @if ($listaActive)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('lista-mercado.plantilla.index', 'list-checks', 'Plantilla', 'lista-mercado.plantilla.*'); @endphp
            <a href="{{ route('lista-mercado.plantilla.index') }}" class="{{ $cls }}">
                <x-icon name="list-checks" class="w-4 h-4" />
                <span class="flex-1">Plantilla</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>
        </div>
    </div>

    @php
        $cajaActive = request()->routeIs('caja.*')
            || request()->routeIs('caja-dashboard.*')
            || request()->routeIs('menu-items.*')
            || request()->routeIs('menu-dia.*')
            || request()->routeIs('metodos-pago.*')
            || request()->routeIs('gastos.*')
            || request()->routeIs('trabajadores-turno.*')
            || request()->routeIs('pagos-ahorros.*');
    @endphp
    <div x-data="{ open: @js($cajaActive) }">
        <button type="button" @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       {{ $cajaActive
                            ? 'bg-primary-100 text-primary-800 shadow-sm dark:bg-primary-900/40 dark:text-primary-100'
                            : 'text-cream-700 hover:bg-cream-100 hover:text-cream-900 dark:text-cream-300 dark:hover:bg-cream-900 dark:hover:text-cream-50' }}">
            <x-icon name="wallet" class="w-4 h-4" />
            <span class="flex-1 text-left">Caja</span>
            <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200"
                    x-bind:class="open ? 'rotate-180' : ''" />
        </button>

        <div x-show="open" x-transition.duration.150ms class="mt-1 ml-3 pl-3 border-l border-cream-200 dark:border-cream-800 space-y-1" x-cloak>
            @php [$active, $cls] = $navItem('caja.index', 'shopping-cart', 'Registrar', 'caja.index'); @endphp
            <a href="{{ route('caja.index') }}" class="{{ $cls }}">
                <x-icon name="shopping-cart" class="w-4 h-4" />
                <span class="flex-1">Registrar</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('menu-items.index', 'utensils-crossed', 'Menú', 'menu-items.*'); @endphp
            <a href="{{ route('menu-items.index') }}" class="{{ $cls }}">
                <x-icon name="utensils-crossed" class="w-4 h-4" />
                <span class="flex-1">Menú</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('menu-dia.index', 'calendar', 'Menú por día', 'menu-dia.*'); @endphp
            <a href="{{ route('menu-dia.index') }}" class="{{ $cls }}">
                <x-icon name="calendar" class="w-4 h-4" />
                <span class="flex-1">Menú por día</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('caja-dashboard.index', 'gauge', 'Dashboard', 'caja-dashboard.*'); @endphp
            <a href="{{ route('caja-dashboard.index') }}" class="{{ $cls }}">
                <x-icon name="gauge" class="w-4 h-4" />
                <span class="flex-1">Dashboard</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('metodos-pago.index', 'credit-card', 'Métodos de pago', 'metodos-pago.*'); @endphp
            <a href="{{ route('metodos-pago.index') }}" class="{{ $cls }}">
                <x-icon name="credit-card" class="w-4 h-4" />
                <span class="flex-1">Métodos de pago</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('gastos.index', 'wallet', 'Gastos', 'gastos.*'); @endphp
            <a href="{{ route('gastos.index') }}" class="{{ $cls }}">
                <x-icon name="wallet" class="w-4 h-4" />
                <span class="flex-1">Gastos</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('trabajadores-turno.index', 'users', 'Trabajadores turno', 'trabajadores-turno.*'); @endphp
            <a href="{{ route('trabajadores-turno.index') }}" class="{{ $cls }}">
                <x-icon name="users" class="w-4 h-4" />
                <span class="flex-1">Trabajadores turno</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('pagos-ahorros.index', 'piggy-bank', 'Pagos ahorros', 'pagos-ahorros.*'); @endphp
            <a href="{{ route('pagos-ahorros.index') }}" class="{{ $cls }}">
                <x-icon name="piggy-bank" class="w-4 h-4" />
                <span class="flex-1">Pagos ahorros</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>
        </div>
    </div>

    @php
        $nominaActive = request()->routeIs('nomina.*')
            || request()->routeIs('nomina-dashboard.*')
            || request()->routeIs('empleados.*')
            || request()->routeIs('nomina-pagos.*')
            || request()->routeIs('prestaciones.*')
            || request()->routeIs('nomina-ahorros.*');
    @endphp
    <div x-data="{ open: @js($nominaActive) }">
        <button type="button" @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       {{ $nominaActive
                            ? 'bg-primary-100 text-primary-800 shadow-sm dark:bg-primary-900/40 dark:text-primary-100'
                            : 'text-cream-700 hover:bg-cream-100 hover:text-cream-900 dark:text-cream-300 dark:hover:bg-cream-900 dark:hover:text-cream-50' }}">
            <x-icon name="banknote" class="w-4 h-4" />
            <span class="flex-1 text-left">Nómina</span>
            <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200"
                    x-bind:class="open ? 'rotate-180' : ''" />
        </button>

        <div x-show="open" x-transition.duration.150ms class="mt-1 ml-3 pl-3 border-l border-cream-200 dark:border-cream-800 space-y-1" x-cloak>
            @php [$active, $cls] = $navItem('nomina-dashboard.index', 'gauge', 'Dashboard', 'nomina-dashboard.*'); @endphp
            <a href="{{ route('nomina-dashboard.index') }}" class="{{ $cls }}">
                <x-icon name="gauge" class="w-4 h-4" />
                <span class="flex-1">Dashboard</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('nomina.index', 'banknote', 'Nóminas', 'nomina.*'); @endphp
            <a href="{{ route('nomina.index') }}" class="{{ $cls }}">
                <x-icon name="banknote" class="w-4 h-4" />
                <span class="flex-1">Nóminas</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('empleados.index', 'users', 'Empleados', 'empleados.*'); @endphp
            <a href="{{ route('empleados.index') }}" class="{{ $cls }}">
                <x-icon name="users" class="w-4 h-4" />
                <span class="flex-1">Empleados</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('nomina-pagos.masivo', 'credit-card', 'Pago masivo', 'nomina-pagos.*'); @endphp
            <a href="{{ route('nomina-pagos.masivo') }}" class="{{ $cls }}">
                <x-icon name="credit-card" class="w-4 h-4" />
                <span class="flex-1">Pago masivo</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('prestaciones.index', 'receipt', 'Prestaciones', 'prestaciones.*'); @endphp
            <a href="{{ route('prestaciones.index') }}" class="{{ $cls }}">
                <x-icon name="receipt" class="w-4 h-4" />
                <span class="flex-1">Prestaciones</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('nomina-ahorros.index', 'piggy-bank', 'Ahorros', 'nomina-ahorros.*'); @endphp
            <a href="{{ route('nomina-ahorros.index') }}" class="{{ $cls }}">
                <x-icon name="piggy-bank" class="w-4 h-4" />
                <span class="flex-1">Ahorros</span>
                @if ($active)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>
        </div>
    </div>

    @php
        $gastosFijosActive = request()->routeIs('gastos-fijos.*');
    @endphp
    <div x-data="{ open: @js($gastosFijosActive) }">
        <button type="button" @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       {{ $gastosFijosActive
                            ? 'bg-primary-100 text-primary-800 shadow-sm dark:bg-primary-900/40 dark:text-primary-100'
                            : 'text-cream-700 hover:bg-cream-100 hover:text-cream-900 dark:text-cream-300 dark:hover:bg-cream-900 dark:hover:text-cream-50' }}">
            <x-icon name="receipt" class="w-4 h-4" />
            <span class="flex-1 text-left">Gastos fijos</span>
            <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200"
                    x-bind:class="open ? 'rotate-180' : ''" />
        </button>

        <div x-show="open" x-transition.duration.150ms class="mt-1 ml-3 pl-3 border-l border-cream-200 dark:border-cream-800 space-y-1" x-cloak>
            @php
                $registrarActive = request()->routeIs('gastos-fijos.index', 'gastos-fijos.create', 'gastos-fijos.edit');
                $registrarCls = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 ' . (
                    $registrarActive
                        ? 'bg-primary-100 text-primary-800 shadow-sm dark:bg-primary-900/40 dark:text-primary-100'
                        : 'text-cream-700 hover:bg-cream-100 hover:text-cream-900 dark:text-cream-300 dark:hover:bg-cream-900 dark:hover:text-cream-50'
                );
            @endphp
            <a href="{{ route('gastos-fijos.index') }}" class="{{ $registrarCls }}">
                <x-icon name="receipt" class="w-4 h-4" />
                <span class="flex-1">Registrar</span>
                @if ($registrarActive)<span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>@endif
            </a>

            @php [$active, $cls] = $navItem('gastos-fijos.conceptos.index', 'settings', 'Conceptos', 'gastos-fijos.conceptos.*'); @endphp
            <a href="{{ route('gastos-fijos.conceptos.index') }}" class="{{ $cls }}">
                <x-icon name="settings" class="w-4 h-4" />
                <span class="flex-1">Conceptos</span>
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
