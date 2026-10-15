@php
    use App\Enums\TextoLanding as T;

    $fotoHistoria = $fotos->get('historia');
    $fotoLocal = $fotos->get('local');
@endphp
<section id="historia" class="relative py-24 sm:py-32">
    <div class="mx-auto grid max-w-7xl items-center gap-20 px-5 sm:px-8 lg:grid-cols-2 lg:gap-24">
        {{-- Collage: foto grande + foto del local (o su espacio reservado) --}}
        <div class="relative order-2 pb-16 lg:order-1 lg:pb-0">
            <div data-anim class="relative aspect-[4/5] w-[80%] overflow-hidden rounded-[2.5rem] bg-cream-200 shadow-soft-lg dark:bg-cream-900">
                @if ($fotoHistoria?->hasImagen())
                    <img data-parallax="7" src="{{ $fotoHistoria->imagen_url }}"
                         @if ($fotoHistoria->imagen_srcset) srcset="{{ $fotoHistoria->imagen_srcset }}" sizes="(min-width: 1024px) 30rem, 80vw" @endif
                         loading="lazy" decoding="async" alt="{{ $fotoHistoria->textoAlternativo() }}"
                         class="absolute inset-x-0 -top-[8%] h-[116%] w-full object-cover">
                @else
                    <x-landing.foto-pendiente texto="Nuestra historia" class="absolute inset-0 border-0" />
                @endif
            </div>
            <div data-anim class="absolute -bottom-2 right-0 w-[50%] lg:-bottom-12">
                @if ($fotoLocal?->hasImagen())
                    <div class="aspect-[4/5] rotate-3 overflow-hidden rounded-[2rem] border-4 border-cream-50 shadow-soft-lg dark:border-surface-dark">
                        <img src="{{ $fotoLocal->imagen_url }}"
                             @if ($fotoLocal->imagen_srcset) srcset="{{ $fotoLocal->imagen_srcset }}" sizes="(min-width: 1024px) 18rem, 50vw" @endif
                             loading="lazy" decoding="async" alt="{{ $fotoLocal->textoAlternativo() }}" class="h-full w-full object-cover">
                    </div>
                @else
                    <x-landing.foto-pendiente texto="Foto del local" class="aspect-[4/5] rotate-3 rounded-[2rem] shadow-soft-lg" />
                @endif
            </div>
            <div aria-hidden="true" class="absolute -left-4 top-10 hidden h-24 w-24 rounded-full border border-gold-500/60 sm:block dark:border-gold-300/30"></div>
        </div>

        <div class="order-1 lg:order-2">
            <p class="eyebrow">Nuestra historia</p>
            <h2 data-split class="titulo-seccion mt-5">{{ $textos->html(T::HistoriaTitulo) }}</h2>

            @foreach ($textos->parrafos(T::Historia) as $parrafo)
                <p data-anim class="mt-6 text-lg leading-relaxed text-cream-700 dark:text-cream-300">{{ $parrafo }}</p>
            @endforeach

            @if ($negocio->propietaria)
                <div data-anim class="mt-10 flex items-center gap-5">
                    <span class="h-px w-12 bg-primary-500/50 dark:bg-primary-300/50"></span>
                    <div>
                        <p class="font-serif text-3xl italic text-primary-600 dark:text-primary-300">{{ $negocio->propietaria }}</p>
                        @if ($negocio->propietariaCargo)
                            <p class="mt-1 text-[11px] uppercase tracking-[0.28em] text-cream-600 dark:text-cream-400">{{ $negocio->propietariaCargo }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if ($cifras->isNotEmpty())
                <dl @class([
                    'mt-12 grid gap-6 border-t border-cream-900/10 pt-8 dark:border-white/10',
                    'grid-cols-2' => $cifras->count() === 2 || $cifras->count() === 4,
                    'grid-cols-3' => $cifras->count() === 3,
                    'grid-cols-1' => $cifras->count() === 1,
                ])>
                    @foreach ($cifras as $cifra)
                        <div data-anim class="flex flex-col-reverse justify-end gap-2">
                            <dt class="text-sm leading-snug text-cream-600 dark:text-cream-400">{{ $cifra->texto }}</dt>
                            <dd class="font-serif text-4xl text-cream-950 sm:text-5xl dark:text-cream-50">
                                {{ $cifra->prefijo }}<span data-contador="{{ $cifra->valor }}">{{ $cifra->valor }}</span><span class="text-2xl sm:text-3xl">{{ $cifra->sufijo_visible }}</span>
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </div>
</section>
