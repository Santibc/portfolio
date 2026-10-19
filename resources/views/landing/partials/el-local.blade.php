{{-- El local: ventanas del empaque esperando las fotos reales, prueba social y quiénes somos --}}
<section id="el-local" class="relative overflow-x-clip py-20 lg:py-28" aria-labelledby="titulo-local">
    {{-- El paquete se toma un descanso fuera de cuadro en esta sección densa --}}
    <div class="pointer-events-none absolute right-[-80vw] top-1/3 h-[40vh] w-[30vw]" data-bag-anchor="local" data-bag-mode="park" data-bag-rot="0.1,0.9,0.2" aria-hidden="true"></div>

    <div class="contenedor sobre-3d">
        <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <h2 id="titulo-local" class="titulo-seccion">El local</h2>
                <p class="texto-suave mt-5 max-w-[58ch] text-lg">
                    Estamos en la {{ $negocio['direccion'] }}, en pleno {{ $negocio['barrio'] }}. {{ $negocio['referencias'] }} Pasa por tu dorilokos o pídelo a domicilio.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-6 lg:col-span-5 lg:justify-end">
                @include('landing.partials.sticker-google', ['clases' => ''])
                <div class="max-w-[16rem] space-y-3">
                    <p class="text-[0.98rem] font-semibold leading-snug">En Google destacan: {{ mb_strtolower(implode(', ', array_slice($negocio['opiniones_destacan'], 0, -1))) }} y {{ mb_strtolower(last($negocio['opiniones_destacan'])) }}.</p>
                    <a href="{{ $enlaces['google'] }}" target="_blank" rel="noopener" class="enlace inline-flex items-center gap-2">
                        Leer las opiniones
                        <x-icon name="arrow-up-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>

        <ul class="mt-14 grid grid-cols-2 gap-6 sm:gap-8 lg:grid-cols-4">
            @foreach ($galeria as $foto)
                <li>
                    @if ($foto['imagen'] && file_exists(public_path($foto['imagen'])))
                        <figure class="ventana aspect-[4/5]">
                            <img src="{{ asset($foto['imagen']) }}" alt="{{ $foto['titulo'] }} de {{ $negocio['nombre'] }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
                        </figure>
                    @else
                        <figure class="ventana ventana--vacia grid aspect-[4/5] place-items-center p-4 text-center">
                            <figcaption class="texto-tenue flex flex-col items-center gap-2">
                                <x-icon name="camera" class="h-8 w-8" />
                                <span class="font-brand text-xl uppercase leading-none texto-tinta">{{ $foto['titulo'] }}</span>
                                <span class="letra-legal">Foto pendiente</span>
                            </figcaption>
                        </figure>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="mt-12 flex flex-wrap items-center gap-3">
            <x-button variant="contorno" size="lg" :href="$enlaces['instagram']" target="_blank" rel="noopener" class="!rounded-full">
                <x-brand-icon name="instagram" class="h-5 w-5" />
                Síguenos {{ $negocio['instagram_usuario'] }}
            </x-button>
            @if ($enlaces['tiktok'])
                <x-button variant="contorno" size="lg" :href="$enlaces['tiktok']" target="_blank" rel="noopener" class="!rounded-full">
                    <x-brand-icon name="tiktok" class="h-5 w-5" />
                    TikTok
                </x-button>
            @endif
        </div>

        <div class="mt-24 grid gap-10 lg:grid-cols-12 lg:items-start">
            <div class="lg:col-span-5">
                <h3 class="titulo-seccion !text-[clamp(2rem,4vw,3.25rem)]">Quiénes somos</h3>
                @if ($institucional['pendiente'])
                    <x-badge variant="warning" size="sm" class="mt-4 dark:!bg-amber-100 dark:!text-amber-900">Texto de ejemplo</x-badge>
                @endif
                <p class="texto-suave mt-4 max-w-[52ch] text-lg leading-relaxed">{{ $institucional['historia'] }}</p>
            </div>

            <div class="panel-nutri p-6 sm:p-8 lg:col-span-7">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="panel-nutri__titulo text-[1.9rem]">Ficha de la casa</p>
                    @if ($institucional['pendiente'])
                        <x-badge variant="warning" size="sm" class="dark:!bg-amber-100 dark:!text-amber-900">Texto de ejemplo</x-badge>
                    @endif
                </div>
                <div class="regla-gruesa mt-3"></div>
                <dl>
                    <div class="regla-fina grid gap-1 py-4 first:border-t-0 sm:grid-cols-[9rem_1fr] sm:gap-6">
                        <dt class="rotulo text-sm">Misión</dt>
                        <dd class="leading-relaxed">{{ $institucional['mision'] }}</dd>
                    </div>
                    <div class="regla-fina grid gap-1 py-4 sm:grid-cols-[9rem_1fr] sm:gap-6">
                        <dt class="rotulo text-sm">Visión</dt>
                        <dd class="leading-relaxed">{{ $institucional['vision'] }}</dd>
                    </div>
                    <div class="regla-fina grid gap-1 py-4 sm:grid-cols-[9rem_1fr] sm:gap-6">
                        <dt class="rotulo text-sm">Valores</dt>
                        <dd>
                            <ul class="flex flex-wrap gap-2">
                                @foreach ($institucional['valores'] as $valor)
                                    <li class="rounded-full border-2 border-[color:var(--regla)] px-3 py-1 text-sm font-bold">{{ $valor }}</li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>
