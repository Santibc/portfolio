@php
    $destacado = $menu['destacado'];
    $loQueLleva = array_slice($ingredientes, 1); // sin la base de Doritos
@endphp

{{-- Frente del empaque --}}
<section id="inicio" class="foil-placa relative overflow-hidden" aria-labelledby="titulo-inicio">
    {{-- Triángulo de fuego del logo, gigante, saliendo por la izquierda --}}
    <svg class="pointer-events-none absolute -left-[38%] top-[60vh] h-[60vh] w-auto max-w-none sm:-left-[22%] sm:top-[38vh] sm:h-[72vh] lg:-left-[19%] lg:top-0 lg:h-full"
         viewBox="0 0 1100 820" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="fuego-inicio" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" class="[stop-color:theme(colors.accent.300)]" />
                <stop offset=".5" class="[stop-color:theme(colors.accent.500)]" />
                <stop offset="1" class="[stop-color:theme(colors.flame.500)]" />
            </linearGradient>
        </defs>
        <polygon points="60,40 1060,420 300,800" style="fill: var(--triangulo)" stroke="url(#fuego-inicio)" stroke-width="34" stroke-linejoin="round" />
        <polygon points="190,150 870,425 335,690" fill="none" stroke="url(#fuego-inicio)" stroke-width="7" stroke-linejoin="round" opacity=".35" />
    </svg>

    <div class="contenedor relative grid min-h-[calc(100svh-4.75rem)] grid-cols-1 items-center gap-x-6 gap-y-10 pb-14 pt-10 lg:grid-cols-12 lg:pb-16 lg:pt-4">
        <div class="sobre-3d lg:col-span-5">
            <h1 id="titulo-inicio" class="titular text-[clamp(3.3rem,9.2vw,9.25rem)] lg:text-[clamp(3.3rem,8.6vw,9.25rem)]">El paquete<br><span class="acento">es el</span> plato</h1>

            <p class="texto-suave mt-6 max-w-[34rem] text-lg leading-relaxed sm:text-xl">
                Abrimos tu paquete de Doritos y lo cargamos hasta arriba con frijol, carne desmechada, chicharrón, maduro, queso, guacamole y salsa de la casa. Hecho al momento en {{ $negocio['barrio'] }}.
            </p>

            <div class="corte mt-9 flex-wrap sm:flex-nowrap" id="cta-inicio">
                <x-icon name="scissors" class="corte__tijera h-7 w-7" />
                <span class="corte__linea !flex-1 sm:!flex-none" aria-hidden="true"></span>
                <x-button variant="pedido" size="xl" :href="$enlaces['whatsapp']" target="_blank" rel="noopener" class="w-full sm:w-auto sm:whitespace-nowrap">
                    <x-brand-icon name="whatsapp" class="h-6 w-6" />
                    <span class="sm:hidden">Pide por WhatsApp</span>
                    <span class="hidden sm:inline">Abre aquí · Pide por WhatsApp</span>
                </x-button>
            </div>

            <a href="#que-es" class="enlace mt-6 inline-flex items-center gap-2">
                ¿Qué lleva?
                <x-icon name="arrow-down" class="h-4 w-4" />
            </a>
        </div>

        {{-- Ancla del paquete 3D (el póster lo reemplaza mientras carga o si no hay 3D) --}}
        <div class="relative h-[46vh] min-h-[320px] lg:col-span-4 lg:h-[66vh]"
             data-bag-anchor="hero" data-bag-mode="hero" data-bag-rot="0.06,-0.42,-0.14">
            <img src="{{ asset('images/landing/paquete-dorilokos.webp') }}" alt="Paquete de Dorilokos Mix"
                 width="620" height="900" fetchpriority="high" decoding="async"
                 class="poster-paquete absolute left-1/2 top-1/2 h-[112%] w-auto max-w-none -translate-x-1/2 -translate-y-1/2 drop-shadow-[0_28px_36px_rgb(var(--sombra-honda)/0.45)]">
            @include('landing.partials.sticker-google', ['clases' => 'absolute -right-1 bottom-[2%] z-[35] sm:right-[8%] lg:-right-4 lg:bottom-[6%]'])
        </div>

        <aside class="panel-nutri w-full max-w-sm justify-self-center p-5 lg:col-span-3 lg:justify-self-end" aria-labelledby="titulo-antojicional">
            <h2 id="titulo-antojicional" class="panel-nutri__titulo text-[2.05rem]">Información antojicional</h2>
            <div class="regla-gruesa mt-2"></div>
            <dl class="text-[0.95rem]">
                <div class="flex items-baseline justify-between gap-4 py-1.5">
                    <dt class="font-extrabold">Porción</dt>
                    <dd class="font-semibold">1 paquete cargado</dd>
                </div>
                <div class="regla-media"></div>
                @foreach ($loQueLleva as $ingrediente)
                    <div class="regla-fina flex items-center justify-between gap-4 py-[0.32rem] first:border-t-0">
                        <dt>{{ $ingrediente['nombre'] }}</dt>
                        <dd>
                            <x-icon name="check" class="h-4 w-4" />
                            <span class="sr-only">Incluido</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
            <div class="regla-media mt-1"></div>
            <div class="flex items-end justify-between gap-3 pt-2.5">
                <p class="leading-tight">
                    <span class="block font-extrabold">Dorilokos {{ mb_strtolower($destacado['nombre']) }}</span>
                    @if ($destacado['etiqueta'])
                        <span class="rotulo mt-1 inline-block rounded bg-accent-300 px-1.5 py-0.5 text-[0.68rem] text-primary-950">{{ $destacado['etiqueta'] }}</span>
                    @endif
                </p>
                <p class="text-right leading-none">
                    <span class="block whitespace-nowrap text-[2.4rem] font-extrabold tracking-tight">{{ $destacado['personal_formateado'] }}</span>
                    <span class="rotulo texto-panel-tenue text-[0.68rem]">Personal</span>
                </p>
            </div>
            <p class="letra-legal texto-panel-tenue mt-2">Familiar {{ $destacado['familiar_formateado'] }} · Domicilios {{ $negocio['whatsapp_visible'] }}</p>
        </aside>
    </div>


    <div class="contenedor letra-legal sobre-3d flex flex-wrap justify-between gap-x-6 gap-y-1 pb-7">
        <span>Contenido neto: 1 antojo grande</span>
        <span>{{ $negocio['direccion'] }} · {{ $negocio['barrio'] }}, {{ $negocio['ciudad'] }}</span>
        <span>{{ $negocio['instagram_usuario'] }}</span>
    </div>
</section>
