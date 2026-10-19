{{-- Ubicación: el paquete aterriza sobre el pin del mapa --}}
<section id="ubicacion" class="relative py-20 lg:py-28" aria-labelledby="titulo-ubicacion">
    <div class="contenedor grid items-center gap-12 lg:grid-cols-12">
        <div class="sobre-3d lg:col-span-5">
            <h2 id="titulo-ubicacion" class="titulo-seccion">Dónde estamos</h2>
            <address class="mt-6 not-italic">
                <p class="text-[clamp(1.75rem,3vw,2.4rem)] font-extrabold leading-tight tracking-tight">{{ $negocio['direccion'] }}</p>
                <p class="texto-suave text-lg">{{ $negocio['barrio'] }}, {{ $negocio['ciudad'] }}</p>
                <p class="texto-tenue mt-2 max-w-[40ch]">{{ $negocio['referencias'] }}</p>
            </address>

            <div class="panel-nutri mt-8 p-5 sm:p-6">
                <p class="panel-nutri__titulo flex items-center gap-2 text-[1.75rem]">
                    <x-icon name="clock" class="h-6 w-6" />
                    Horario
                </p>
                <div class="regla-gruesa mt-2"></div>
                <dl>
                    @foreach ($horario['dias'] as $franja)
                        <div class="regla-fina flex flex-wrap items-baseline justify-between gap-x-4 py-2 first:border-t-0">
                            <dt class="font-bold">{{ $franja['dias'] }}</dt>
                            <dd class="whitespace-nowrap font-semibold">{{ $franja['horas'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <x-button variant="pedido" size="lg" :href="$enlaces['whatsapp']" target="_blank" rel="noopener" class="!rounded-full">
                    <x-brand-icon name="whatsapp" class="h-5 w-5" />
                    Pedir domicilio
                </x-button>
                <x-button variant="contorno" size="lg" :href="$enlaces['como_llegar']" target="_blank" rel="noopener" icon="navigation" class="!rounded-full">
                    Cómo llegar
                </x-button>
                @if ($enlaces['rappi'])
                    <x-button variant="contorno" size="lg" :href="$enlaces['rappi']" target="_blank" rel="noopener" icon="bike" class="!rounded-full">
                        Pedir en Rappi
                    </x-button>
                @endif
            </div>
        </div>

        <div class="lg:col-span-7">
            <div class="mapa relative aspect-[4/3] overflow-hidden rounded-[1.75rem] sm:aspect-[16/11]">
                <iframe src="{{ $enlaces['mapa'] }}" title="Mapa: {{ $negocio['nombre'] }}, {{ $negocio['direccion'] }}, {{ $negocio['barrio'] }}"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen
                        class="absolute inset-0 h-full w-full border-0"></iframe>
                <div class="pointer-events-none absolute left-1/2 top-[2%] h-[30%] w-[22%] -translate-x-1/2"
                     data-bag-anchor="ubicacion" data-bag-mode="pin" data-bag-rot="0.12,0.35,0"></div>
            </div>
            <a href="{{ $enlaces['google'] }}" target="_blank" rel="noopener" class="enlace mt-4 inline-flex items-center gap-2">
                <x-brand-icon name="googlemaps" class="h-4 w-4" />
                Ver en Google Maps
                <x-icon name="arrow-up-right" class="h-4 w-4" />
            </a>
        </div>
    </div>
</section>
