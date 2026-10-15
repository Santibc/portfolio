<section id="inicio" class="relative overflow-hidden pb-20 pt-28 sm:pt-32 lg:pb-28 lg:pt-40">
    {{-- Halos cálidos de fondo --}}
    <div aria-hidden="true" class="pointer-events-none absolute -right-48 -top-24 h-[38rem] w-[38rem] rounded-full bg-gold-300/30 blur-3xl dark:bg-primary-700/20"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -left-56 bottom-0 h-[30rem] w-[30rem] rounded-full bg-accent-200/40 blur-3xl dark:bg-accent-900/20"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-16 px-5 sm:px-8 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-7 xl:col-span-6">
            <p class="eyebrow" data-anim-hero="fade">Panadería &amp; Pastelería · {{ $negocio->barrio }}, Bogotá</p>

            <h1 data-hero-titulo data-anim-hero="titulo"
                class="mt-6 font-serif font-normal leading-[0.98] tracking-tight text-cream-950 dark:text-cream-50"
                style="font-size: clamp(2.7rem, 1.4rem + 4vw, 5.3rem)">
                Pan <em class="font-normal italic text-primary-500 dark:text-primary-300">dorado</em>, horneado con amor cada mañana.
            </h1>

            <p data-anim-hero="fade" class="mt-7 max-w-xl text-base leading-relaxed text-cream-700 sm:text-lg dark:text-cream-300">
                Pan artesanal, pastelería y tortas por encargo, hechos a mano en nuestro horno del barrio {{ $negocio->barrio }}.
                Escríbenos por WhatsApp y te lo tenemos listo.
            </p>

            <div data-anim-hero="fade" class="mt-9 flex flex-wrap items-center gap-3">
                <a href="{{ $negocio->whatsappUrl() }}" target="_blank" rel="noopener" class="btn-landing-primario">
                    @include('landing.partials.icono-whatsapp', ['clase' => 'h-5 w-5'])
                    Pide por WhatsApp
                </a>
                <a href="#productos" class="btn-landing-secundario">
                    Ver productos <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <dl data-anim-hero="fade" class="mt-12 grid max-w-lg grid-cols-3 gap-6 border-t border-cream-900/10 pt-7 dark:border-white/10">
                @foreach (['Horneado' => 'Diario', 'Recetas' => 'De casa', 'Pedidos' => 'WhatsApp'] as $termino => $valor)
                    <div class="flex flex-col-reverse gap-1">
                        <dt class="text-[11px] uppercase tracking-[0.2em] text-cream-600 dark:text-cream-400">{{ $termino }}</dt>
                        <dd class="font-serif text-2xl text-cream-950 sm:text-3xl dark:text-cream-50">{{ $valor }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="relative lg:col-span-5 xl:col-start-8">
            <div class="relative mx-auto max-w-[23rem] sm:max-w-[27rem] lg:max-w-[30rem]">
                {{-- Arco dorado desplazado detrás de la foto --}}
                <div aria-hidden="true" class="arco absolute inset-0 translate-x-4 translate-y-4 border border-gold-500/70 dark:border-gold-300/30"></div>

                <div data-anim-hero="arco" class="arco relative aspect-[4/5] overflow-hidden bg-cream-200 shadow-[0_40px_80px_-30px_rgb(52_37_24/0.55)] dark:bg-cream-900">
                    <img data-parallax="5"
                         src="{{ asset('images/landing/hero-640.webp') }}"
                         srcset="{{ asset('images/landing/hero-640.webp') }} 640w, {{ asset('images/landing/hero-1200.webp') }} 1200w"
                         sizes="(min-width: 1024px) 30rem, 90vw"
                         width="1200" height="1500" fetchpriority="high" decoding="async"
                         alt="Panes artesanales recién horneados junto a espigas de trigo"
                         class="absolute inset-x-0 -top-[6%] h-[112%] w-full object-cover">
                </div>

                {{-- Tarjeta flotante --}}
                <div data-anim-hero="pop" class="absolute -left-3 bottom-8 flex items-center gap-3 rounded-2xl bg-white/90 p-2.5 pr-5 shadow-soft-lg backdrop-blur sm:-left-12 sm:bottom-12 dark:bg-cream-900/90">
                    <img src="{{ asset('images/landing/hero-croissant-320.webp') }}" width="320" height="320" alt="" class="h-14 w-14 rounded-xl object-cover">
                    <div>
                        <p class="font-serif text-xl leading-tight text-cream-950 dark:text-cream-50">Recién horneado</p>
                        <p class="text-xs text-cream-600 dark:text-cream-400">Varias horneadas al día</p>
                    </div>
                </div>

                {{-- Sello giratorio con el lema del logo --}}
                <div data-anim-hero="pop" class="absolute -right-2 -top-8 h-28 w-28 sm:-right-10 sm:h-36 sm:w-36">
                    <svg class="sello-giro absolute inset-0 h-full w-full text-primary-600 dark:text-gold-300" viewBox="0 0 200 200" aria-hidden="true">
                        <defs>
                            <path id="circulo-sello" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0" />
                        </defs>
                        <text fill="currentColor" font-size="15" font-weight="600" letter-spacing="2">
                            <textPath href="#circulo-sello" textLength="500" lengthAdjust="spacing">CALIDAD &amp; SERVICIO · EL PAN DORADO DC ·</textPath>
                        </text>
                    </svg>
                    <div class="absolute inset-[21%] grid place-items-center rounded-full bg-cream-50 shadow-soft dark:bg-cream-950">
                        <img src="{{ asset('images/logo-emblema.png') }}" alt="" class="w-[80%] dark:hidden">
                        <img src="{{ asset('images/logo-emblema-dark.png') }}" alt="" class="hidden w-[80%] dark:block">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
