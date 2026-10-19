{{-- ¿Qué es? El paquete gira y muestra su reverso --}}
<section id="que-es" class="relative overflow-x-clip py-20 lg:py-28" aria-labelledby="titulo-que-es">
    {{-- En celular el paquete se va de cuadro mientras se lee (en una sola columna taparía el texto) --}}
    <div class="pointer-events-none absolute right-[-75vw] top-[45%] h-[30vh] w-[30vw] lg:hidden" data-bag-anchor="que-salida" data-bag-mode="park" aria-hidden="true"></div>
    <div class="contenedor grid items-center gap-x-12 gap-y-12 lg:grid-cols-12">
        <div class="hidden h-[58vh] lg:col-span-4 lg:block" data-bag-anchor="que" data-bag-mode="back" data-bag-rot="0.08,0.32,0.1"></div>

        <div class="sobre-3d lg:col-span-8">
            <div class="flex items-start gap-4">
                <h2 id="titulo-que-es" class="titulo-seccion flex-1">¿Qué es un dorilokos?</h2>
                <div class="h-[19vh] w-[36%] shrink-0 lg:hidden" data-bag-anchor="que-movil" data-bag-mode="back" data-bag-rot="0.08,0.32,0.1"></div>
            </div>

            <p class="texto-suave mt-6 max-w-[62ch] text-lg leading-relaxed sm:text-xl">
                Un paquete de Doritos abierto y cargado hasta el borde, para comer ahí mismo con tenedor. Nació en las calles de México como «dorilocos»; en Bogotá se volvió plato fuerte, y aquí le metimos la bandeja paisa: frijol, chicharrón, maduro y carne desmechada sobre tus Doritos.
            </p>
            <p class="mt-4 flex items-start gap-2.5 font-semibold">
                <x-icon name="flame" class="mt-0.5 h-5 w-5 shrink-0 text-accent-600" />
                Truco de la casa: cómelo de una, antes de que la salsa ablande lo crocante.
            </p>

            <div class="mt-12 grid gap-10 md:grid-cols-2 md:items-center">
                @include('landing.partials.corte-capas')

                @if (file_exists(public_path($referencia['imagen'])))
                    <figure class="ventana aspect-[4/5] w-full max-w-sm justify-self-center md:justify-self-end">
                        <img src="{{ asset($referencia['imagen']) }}"
                             srcset="{{ asset(str_replace('.webp', '-640.webp', $referencia['imagen'])) }} 640w, {{ asset($referencia['imagen']) }} 960w"
                             sizes="(min-width: 1024px) 24rem, (min-width: 768px) 45vw, 90vw"
                             alt="{{ $referencia['alt'] }}" width="960" height="1200" loading="lazy" decoding="async"
                             class="h-full w-full object-cover">
                        <figcaption class="letra-legal absolute bottom-3 left-3 rounded-full bg-primary-950/80 px-3 py-1.5 !text-cream-100">Foto de referencia</figcaption>
                    </figure>
                @endif
            </div>
        </div>
    </div>
</section>
