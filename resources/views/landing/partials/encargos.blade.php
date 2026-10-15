<section class="relative py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div data-anim class="relative isolate overflow-hidden rounded-[2.5rem] bg-cream-950 px-7 py-16 sm:px-14 sm:py-20 lg:px-20 lg:py-24">
            <img data-parallax="8"
                 src="{{ asset('images/landing/encargos-900.webp') }}"
                 srcset="{{ asset('images/landing/encargos-900.webp') }} 900w, {{ asset('images/landing/encargos-1600.webp') }} 1600w"
                 sizes="(min-width: 1280px) 80rem, 100vw" width="1600" height="1067" loading="lazy" decoding="async"
                 alt="Porción de torta de frambuesas"
                 class="absolute inset-x-0 -top-[10%] -z-10 h-[120%] w-full object-cover object-right">
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-cream-950 via-cream-950/85 to-cream-950/5"></div>

            <div class="max-w-xl">
                <p class="eyebrow !text-gold-300">Encargos</p>
                <h2 class="mt-5 font-serif leading-[1.04] text-white" style="font-size: clamp(2.2rem, 1.4rem + 2.8vw, 4rem)">
                    ¿Una celebración especial? <em class="font-normal italic text-gold-300">Hacemos tu torta</em> a la medida.
                </h2>
                <p class="mt-6 text-lg leading-relaxed text-cream-200">
                    Cumpleaños, primeras comuniones, bautizos, matrimonios o simplemente porque sí. Cuéntanos la ocasión, el sabor y
                    las porciones, y te ayudamos a diseñarla.
                </p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ $negocio->whatsappUrl('Hola, El Pan Dorado DC. Quisiera cotizar una torta por encargo.') }}"
                       target="_blank" rel="noopener"
                       class="btn-landing bg-gold-300 text-cream-950 hover:-translate-y-0.5 hover:bg-white">
                        @include('landing.partials.icono-whatsapp', ['clase' => 'h-5 w-5'])
                        Cotiza tu torta
                    </a>
                    <a href="{{ $negocio->telUrl() }}" class="btn-landing border border-white/25 text-white hover:border-white">
                        <x-icon name="phone" class="h-4 w-4" /> {{ $negocio->telefonoVisible() }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
