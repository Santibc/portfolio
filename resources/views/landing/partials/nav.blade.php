{{-- Barra superior: transparente arriba, vidrio esmerilado al hacer scroll (data-scrolled lo pone landing.js) --}}
<div x-data="navLanding" @keydown.escape.window="cerrar()">
    <header data-nav data-scrolled="false"
        class="group/nav fixed inset-x-0 top-0 z-50 transition-[background-color,box-shadow,backdrop-filter] duration-500 data-[scrolled=true]:bg-cream-50/80 data-[scrolled=true]:shadow-[0_12px_40px_-24px_rgb(52_37_24/0.45)] data-[scrolled=true]:backdrop-blur-xl dark:data-[scrolled=true]:bg-surface-dark/80"
        :class="abierto && '!bg-transparent !shadow-none !backdrop-blur-none'">
        <nav class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-5 transition-[height] duration-500 group-data-[scrolled=true]/nav:h-16 sm:px-8" aria-label="Principal">
            <a href="#inicio" class="shrink-0" aria-label="{{ $negocio->nombre }}, ir al inicio">
                <img src="{{ asset('images/logo-horizontal.png') }}" alt="{{ $negocio->nombre }}" width="720" height="129" class="h-9 w-auto sm:h-11 dark:hidden">
                <img src="{{ asset('images/logo-horizontal-dark.png') }}" alt="{{ $negocio->nombre }}" width="720" height="129" class="hidden h-9 w-auto sm:h-11 dark:block">
            </a>

            <ul class="hidden items-center gap-9 text-sm font-medium lg:flex">
                @foreach ($enlaces as $ancla => $texto)
                    <li>
                        <a href="#{{ $ancla }}"
                           class="relative py-2 text-cream-800 transition-colors after:absolute after:inset-x-0 after:-bottom-0.5 after:h-px after:origin-left after:scale-x-0 after:bg-current after:transition-transform after:duration-300 hover:text-primary-600 hover:after:scale-x-100 dark:text-cream-200 dark:hover:text-primary-300">
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" @click="alternarTema()"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full text-cream-800 transition-colors hover:bg-cream-900/5 dark:text-cream-200 dark:hover:bg-white/10"
                    aria-label="Cambiar entre modo claro y oscuro">
                    <span class="dark:hidden"><x-icon name="moon" class="h-5 w-5" /></span>
                    <span class="hidden dark:inline-flex"><x-icon name="sun" class="h-5 w-5" /></span>
                </button>

                <a href="{{ $negocio->whatsappUrl() }}" target="_blank" rel="noopener" class="btn-landing-primario hidden !px-5 !py-2.5 sm:inline-flex">
                    Haz tu pedido
                </a>

                <button type="button" @click="alternarMenu()"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full text-cream-900 transition-colors hover:bg-cream-900/5 lg:hidden dark:text-cream-100 dark:hover:bg-white/10"
                    :aria-expanded="abierto.toString()" aria-controls="menu-movil" :aria-label="abierto ? 'Cerrar menú' : 'Abrir menú'">
                    <span x-show="!abierto"><x-icon name="menu" class="h-6 w-6" /></span>
                    <span x-cloak x-show="abierto"><x-icon name="x" class="h-6 w-6" /></span>
                </button>
            </div>
        </nav>
    </header>

    {{-- Menú móvil a pantalla completa (fuera del header: su backdrop-filter recortaría un fixed hijo) --}}
    <div id="menu-movil" x-cloak x-show="abierto"
        x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition duration-300 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 overflow-y-auto bg-cream-50 lg:hidden dark:bg-surface-dark" data-lenis-prevent>
        <div class="flex min-h-full flex-col justify-between px-6 pb-28 pt-28">
            <ul class="space-y-1">
                @foreach ($enlaces as $ancla => $texto)
                    <li>
                        <a href="#{{ $ancla }}" @click.prevent="irA('#{{ $ancla }}')"
                           class="flex items-baseline gap-4 py-2 font-serif text-5xl text-cream-950 transition-colors hover:text-primary-600 dark:text-cream-50 dark:hover:text-primary-300">
                            <span class="font-sans text-xs text-accent-700 dark:text-accent-300">0{{ $loop->iteration }}</span>
                            {{ $texto }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-12 space-y-4 border-t border-cream-900/10 pt-8 dark:border-white/10">
                <a href="{{ $negocio->whatsappUrl() }}" target="_blank" rel="noopener" class="btn-landing-primario w-full">Haz tu pedido por WhatsApp</a>
                <a href="{{ $negocio->telUrl() }}" class="flex items-center justify-center gap-2 text-sm text-cream-700 dark:text-cream-300">
                    <x-icon name="phone" class="h-4 w-4" /> {{ $negocio->telefonoVisible() }}
                </a>
            </div>
        </div>
    </div>
</div>
