@php
    use App\Enums\TextoLanding as T;

    $fotoHero = $fotos->get('hero');
    $fotoMini = $fotos->get('hero_mini');
@endphp
<section id="inicio" class="relative overflow-hidden pb-20 pt-28 sm:pt-32 lg:pb-28 lg:pt-40">
    {{-- Halos cálidos de fondo --}}
    <div aria-hidden="true" class="pointer-events-none absolute -right-48 -top-24 h-[38rem] w-[38rem] rounded-full bg-gold-300/30 blur-3xl dark:bg-primary-700/20"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -left-56 bottom-0 h-[30rem] w-[30rem] rounded-full bg-accent-200/40 blur-3xl dark:bg-accent-900/20"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-16 px-5 sm:px-8 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-7 xl:col-span-6">
            <p class="eyebrow" data-anim-hero="fade">{{ $textos->texto(T::HeroEtiqueta) }}</p>

            <h1 data-hero-titulo data-anim-hero="titulo"
                class="em-dorado mt-6 font-serif font-normal leading-[0.98] tracking-tight text-cream-950 dark:text-cream-50"
                style="font-size: clamp(2.7rem, 1.4rem + 4vw, 5.3rem)">
                {{ $textos->html(T::HeroTitulo) }}
            </h1>

            <p data-anim-hero="fade" class="mt-7 max-w-xl text-base leading-relaxed text-cream-700 sm:text-lg dark:text-cream-300">
                {{ $textos->texto(T::HeroTexto) }}
            </p>

            <div data-anim-hero="fade" class="mt-9 flex flex-wrap items-center gap-3">
                <a href="{{ $negocio->whatsappUrl() }}" target="_blank" rel="noopener" class="btn-landing-primario">
                    @include('landing.partials.icono-whatsapp', ['clase' => 'h-5 w-5'])
                    Pide por WhatsApp
                </a>
                @if ($productos->isNotEmpty())
                    <a href="#productos" class="btn-landing-secundario">
                        Ver productos <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @endif
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
                    @if ($fotoHero?->hasImagen())
                        <img data-parallax="5" src="{{ $fotoHero->imagen_url }}"
                             @if ($fotoHero->imagen_srcset) srcset="{{ $fotoHero->imagen_srcset }}" sizes="(min-width: 1024px) 30rem, 90vw" @endif
                             fetchpriority="high" decoding="async" alt="{{ $fotoHero->textoAlternativo() }}"
                             class="absolute inset-x-0 -top-[6%] h-[112%] w-full object-cover">
                    @else
                        <x-landing.foto-pendiente texto="Foto principal" class="absolute inset-0 border-0" />
                    @endif
                </div>

                {{-- Tarjeta flotante --}}
                <div data-anim-hero="pop" class="absolute -left-3 bottom-8 flex items-center gap-3 rounded-2xl bg-white/90 p-2.5 pr-5 shadow-soft-lg backdrop-blur sm:-left-12 sm:bottom-12 dark:bg-cream-900/90">
                    @if ($fotoMini?->hasImagen())
                        <img src="{{ $fotoMini->imagen_url }}" alt="" class="h-14 w-14 rounded-xl object-cover">
                    @else
                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/60 dark:text-primary-200">
                            <x-icon name="croissant" class="h-6 w-6" />
                        </span>
                    @endif
                    <div>
                        <p class="font-serif text-xl leading-tight text-cream-950 dark:text-cream-50">{{ $textos->texto(T::HeroTarjetaTitulo) }}</p>
                        <p class="text-xs text-cream-600 dark:text-cream-400">{{ $textos->texto(T::HeroTarjetaTexto) }}</p>
                    </div>
                </div>

                {{-- Sello giratorio con el lema y el nombre --}}
                <div data-anim-hero="pop" class="absolute -right-2 -top-8 h-28 w-28 sm:-right-10 sm:h-36 sm:w-36">
                    <svg class="sello-giro absolute inset-0 h-full w-full text-primary-600 dark:text-gold-300" viewBox="0 0 200 200" aria-hidden="true">
                        <defs>
                            <path id="circulo-sello" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0" />
                        </defs>
                        <text fill="currentColor" font-size="15" font-weight="600" letter-spacing="2">
                            <textPath href="#circulo-sello" textLength="500" lengthAdjust="spacing">{{ mb_strtoupper($negocio->lema.' · '.$negocio->nombre.' ·') }}</textPath>
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
