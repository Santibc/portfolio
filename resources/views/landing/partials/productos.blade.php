<section id="productos" class="relative bg-cream-100 py-24 sm:py-32 dark:bg-cream-950/60">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-end">
            <div class="max-w-2xl">
                <p class="eyebrow">Lo que horneamos</p>
                <h2 data-split class="titulo-seccion mt-5">Hecho a mano, <em>todos</em> los días</h2>
            </div>
            <p data-anim class="max-w-md text-cream-700 dark:text-cream-300">
                Del pan de la mañana a la torta de tu celebración. Elige lo que se te antoje y pídelo por WhatsApp: te lo tenemos listo.
            </p>
        </div>

        {{-- La columna del medio baja un poco en escritorio para un ritmo editorial --}}
        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
            @foreach ($negocio->productos as $producto)
                <div @class(['lg:pt-20' => $loop->index % 3 === 1])>
                    <article data-anim class="group relative aspect-[4/5] overflow-hidden rounded-[2rem] bg-cream-900 shadow-soft">
                        <img src="{{ asset('images/landing/'.$producto['imagen'].'-600.webp') }}"
                             srcset="{{ asset('images/landing/'.$producto['imagen'].'-600.webp') }} 600w, {{ asset('images/landing/'.$producto['imagen'].'-1000.webp') }} 1000w"
                             sizes="(min-width: 1024px) 26rem, (min-width: 640px) 45vw, 90vw"
                             width="1000" height="1250" loading="lazy" decoding="async"
                             alt="{{ $producto['titulo'] }}"
                             class="absolute inset-0 h-full w-full object-cover transition-transform duration-[1400ms] ease-out group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-cream-950/90 via-cream-950/20 to-transparent"></div>

                        <div class="absolute inset-x-0 bottom-0 p-7">
                            <span class="font-serif text-sm italic text-gold-300">0{{ $loop->iteration }}</span>
                            <h3 class="mt-1 font-serif text-3xl text-white">{{ $producto['titulo'] }}</h3>
                            <p class="mt-2 max-w-xs text-sm leading-relaxed text-cream-100/85">{{ $producto['descripcion'] }}</p>
                            <a href="{{ $negocio->whatsappUrl('Hola, El Pan Dorado DC. Quisiera pedir: '.$producto['titulo'].'.') }}"
                               target="_blank" rel="noopener"
                               class="mt-5 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition-colors hover:bg-white hover:text-cream-950">
                                Pedir <span class="sr-only">{{ $producto['titulo'] }}</span>
                                <x-icon name="arrow-up-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
