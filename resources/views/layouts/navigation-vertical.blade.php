<nav aria-label="Navegación principal" class="space-y-6">
    <div>
        <div class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
            General
        </div>
        <ul class="space-y-1">
            <li>
                <a
                    href="{{ route('dashboard') }}"
                    @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('dashboard'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('dashboard'),
                    ])
                    @if(request()->routeIs('dashboard')) aria-current="page" @endif
                >
                    <i class="bi bi-house-door text-base"></i>
                    <span>Inicio</span>
                </a>
            </li>
        </ul>
    </div>

    <div>
        <div class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
            Operación
        </div>
        <ul class="space-y-1">
            <li>
                <a
                    href="{{ route('facturacion.facturas.index') }}"
                    @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('facturacion.*'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('facturacion.*'),
                    ])
                >
                    <i class="bi bi-receipt text-base"></i>
                    <span>Facturas</span>
                </a>
            </li>
        </ul>
    </div>

    <div>
        <div class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
            Catálogos
        </div>
        <ul class="space-y-1">
            <li>
                <a
                    href="{{ route('catalogos.productos.index') }}"
                    @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('catalogos.productos.*'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('catalogos.productos.*'),
                    ])
                >
                    <i class="bi bi-box-seam text-base"></i>
                    <span>Productos</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('catalogos.clientes.index') }}"
                    @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('catalogos.clientes.*'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('catalogos.clientes.*'),
                    ])
                >
                    <i class="bi bi-people text-base"></i>
                    <span>Clientes</span>
                </a>
            </li>
        </ul>
    </div>

    <div x-data="{ open: {{ request()->routeIs('admin.*') ? 'true' : 'false' }} }">
        <div class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
            Administración
        </div>
        <ul class="space-y-1">
            <li>
                <button
                    type="button"
                    @click="open = !open"
                    @class([
                        'w-full group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('admin.*'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.*'),
                    ])
                >
                    <i class="bi bi-sliders text-base"></i>
                    <span>Configuración</span>
                    <i class="bi bi-chevron-down ml-auto text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-collapse class="ml-6 mt-1 space-y-1 border-l border-zinc-200 pl-2 dark:border-zinc-800">
                    <li>
                        <a href="{{ route('admin.index') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.index'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.index'),
                        ])>
                            <i class="bi bi-speedometer2 text-xs"></i>
                            <span>Resumen</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.empresa.edit') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.empresa.*'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.empresa.*'),
                        ])>
                            <i class="bi bi-building text-xs"></i>
                            <span>Empresa y banco</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.monedas.index') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.monedas.*'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.monedas.*'),
                        ])>
                            <i class="bi bi-currency-exchange text-xs"></i>
                            <span>Monedas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.impuestos.index') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.impuestos.*'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.impuestos.*'),
                        ])>
                            <i class="bi bi-percent text-xs"></i>
                            <span>Impuestos</span>
                        </a>
                    </li>
                    {{-- Oculto temporalmente: Tipos de descuento (volver a habilitar después)
                    <li>
                        <a href="{{ route('admin.tipos-descuento.index') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.tipos-descuento.*'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.tipos-descuento.*'),
                        ])>
                            <i class="bi bi-tag text-xs"></i>
                            <span>Tipos de descuento</span>
                        </a>
                    </li>
                    --}}
                    <li>
                        <a href="{{ route('admin.incoterms.index') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.incoterms.*'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.incoterms.*'),
                        ])>
                            <i class="bi bi-globe text-xs"></i>
                            <span>Incoterms</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.puertos.index') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.puertos.*'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.puertos.*'),
                        ])>
                            <i class="bi bi-geo-alt-fill text-xs"></i>
                            <span>Puertos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tipos-pago.index') }}" @class([
                            'flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium transition',
                            'nav-item-active' => request()->routeIs('admin.tipos-pago.*'),
                            'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.tipos-pago.*'),
                        ])>
                            <i class="bi bi-cash-coin text-xs"></i>
                            <span>Tipos de pago</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a
                    href="{{ route('admin.siigo.edit') }}"
                    @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('admin.siigo.*'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.siigo.*'),
                    ])
                >
                    <i class="bi bi-cloud-arrow-up text-base"></i>
                    <span>Integración Siigo</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('admin.plantillas.index') }}"
                    @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('admin.plantillas.*'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('admin.plantillas.*'),
                    ])
                >
                    <i class="bi bi-file-earmark-code text-base"></i>
                    <span>Plantillas de factura</span>
                </a>
            </li>
        </ul>
    </div>

    <div>
        <div class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
            Cuenta
        </div>
        <ul class="space-y-1">
            <li>
                <a
                    href="{{ route('profile.edit') }}"
                    @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                        'nav-item-active' => request()->routeIs('profile.*'),
                        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! request()->routeIs('profile.*'),
                    ])
                    @if(request()->routeIs('profile.*')) aria-current="page" @endif
                >
                    <i class="bi bi-person-gear text-base"></i>
                    <span>Mi perfil</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
