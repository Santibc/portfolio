@php
    $secciones = [
        ['#que-es', '¿Qué es?'],
        ['#como-se-arma', 'Así se arma'],
        ['#menu', 'Menú'],
        ['#el-local', 'El local'],
        ['#ubicacion', 'Ubicación'],
    ];
@endphp

{{-- Sello superior del empaque: barra fija con borde crimpado --}}
<div class="sticky top-0 z-40 drop-shadow-[0_10px_16px_rgb(var(--sombra-honda)/0.22)]">
    <header class="sello dientes-abajo" style="--diente: 8px">
        <nav class="contenedor flex h-[4.25rem] items-center gap-3 pb-1.5 lg:h-[4.75rem] lg:gap-8" aria-label="Principal">
            <a href="#inicio" class="flex shrink-0 items-center gap-2.5 rounded-xl" aria-label="{{ $negocio['nombre'] }}, ir al inicio">
                <img src="{{ asset('images/landing/logo-192.webp') }}" alt="" width="44" height="44" class="h-10 w-10 lg:h-11 lg:w-11">
                <span class="hidden font-brand text-[1.55rem] leading-none texto-tinta xs:inline">{{ $negocio['nombre'] }}</span>
            </a>

            <ul class="rotulo hidden items-center gap-7 text-[0.95rem] lg:flex">
                @foreach ($secciones as [$href, $texto])
                    <li><a href="{{ $href }}" class="texto-tinta rounded-md decoration-[3px] underline-offset-[6px] hover:underline">{{ $texto }}</a></li>
                @endforeach
            </ul>

            <div class="ml-auto flex items-center gap-2 sm:gap-3">
                <button type="button" id="themeToggleBtn" class="texto-tinta grid h-10 w-10 place-items-center rounded-full transition-colors hover:bg-[color:var(--sello-estria)]" aria-label="Cambiar entre tema claro y oscuro">
                    <x-icon name="moon" class="h-5 w-5 dark:hidden" />
                    <x-icon name="sun" class="hidden h-5 w-5 dark:block" />
                </button>

                <x-button variant="pedido" size="sm" :href="$enlaces['whatsapp']" target="_blank" rel="noopener" class="!rounded-full !px-4 !py-2">
                    <x-brand-icon name="whatsapp" class="h-4 w-4" />
                    Pedir
                </x-button>

                <button type="button" class="texto-tinta grid h-10 w-10 place-items-center rounded-full lg:hidden" data-menu-abrir aria-controls="menu-movil" aria-expanded="false">
                    <x-icon name="menu" class="h-6 w-6" />
                    <span class="sr-only">Abrir menú</span>
                </button>
            </div>
        </nav>
    </header>
</div>

{{-- Menú móvil: el reverso del empaque a pantalla completa --}}
<dialog id="menu-movil" class="menu-movil foil texto-tinta m-0 h-[100dvh] max-h-none w-full max-w-none border-0 p-0" aria-labelledby="menu-movil-titulo">
    <div class="flex h-full flex-col">
        <div class="sello dientes-abajo" style="--diente: 8px">
            <div class="contenedor flex h-[4.25rem] items-center justify-between pb-1.5">
                <p id="menu-movil-titulo" class="font-brand text-[1.55rem] leading-none">{{ $negocio['nombre'] }}</p>
                <button type="button" class="grid h-10 w-10 place-items-center rounded-full" data-menu-cerrar>
                    <x-icon name="x" class="h-6 w-6" />
                    <span class="sr-only">Cerrar menú</span>
                </button>
            </div>
        </div>

        <nav class="contenedor flex-1 overflow-y-auto py-8" aria-label="Secciones">
            <ul class="space-y-1">
                @foreach ($secciones as [$href, $texto])
                    <li>
                        <a href="{{ $href }}" class="block rounded-xl py-2 font-brand text-[2.6rem] uppercase leading-none" data-menu-enlace>{{ $texto }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="contenedor space-y-4 pb-[max(1.5rem,env(safe-area-inset-bottom))]">
            <x-button variant="pedido" size="xl" :href="$enlaces['whatsapp']" target="_blank" rel="noopener" class="w-full">
                <x-brand-icon name="whatsapp" class="h-6 w-6" />
                Pide por WhatsApp
            </x-button>
            <p class="letra-legal text-center">{{ $negocio['direccion'] }} · {{ $negocio['barrio'] }}, {{ $negocio['ciudad'] }}</p>
        </div>
    </div>
</dialog>
