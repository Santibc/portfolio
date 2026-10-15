<section id="historia" class="relative py-24 sm:py-32">
    <div class="mx-auto grid max-w-7xl items-center gap-20 px-5 sm:px-8 lg:grid-cols-2 lg:gap-24">
        {{-- Collage: foto + espacio reservado para la foto real del local --}}
        <div class="relative order-2 pb-16 lg:order-1 lg:pb-0">
            <div data-anim class="relative aspect-[4/5] w-[80%] overflow-hidden rounded-[2.5rem] bg-cream-200 shadow-soft-lg dark:bg-cream-900">
                <img data-parallax="7"
                     src="{{ asset('images/landing/historia-640.webp') }}"
                     srcset="{{ asset('images/landing/historia-640.webp') }} 640w, {{ asset('images/landing/historia-1100.webp') }} 1100w"
                     sizes="(min-width: 1024px) 30rem, 80vw" width="1100" height="1375" loading="lazy" decoding="async"
                     alt="Croissants dorados recién horneados en la vitrina"
                     class="absolute inset-x-0 -top-[8%] h-[116%] w-full object-cover">
            </div>
            <div data-anim class="absolute -bottom-2 right-0 w-[50%] lg:-bottom-12">
                <x-landing.foto-pendiente texto="Foto del local" class="aspect-[4/5] rotate-3 rounded-[2rem] shadow-soft-lg" />
            </div>
            <div aria-hidden="true" class="absolute -left-4 top-10 hidden h-24 w-24 rounded-full border border-gold-500/60 sm:block dark:border-gold-300/30"></div>
        </div>

        <div class="order-1 lg:order-2">
            <p class="eyebrow">Nuestra historia</p>
            <h2 data-split class="titulo-seccion mt-5">Una tradición que <em>huele</em> a pan recién hecho</h2>

            @foreach ($negocio->historia as $parrafo)
                <p data-anim class="mt-6 text-lg leading-relaxed text-cream-700 dark:text-cream-300">{{ $parrafo }}</p>
            @endforeach

            <div data-anim class="mt-10 flex items-center gap-5">
                <span class="h-px w-12 bg-primary-500/50 dark:bg-primary-300/50"></span>
                <div>
                    <p class="font-serif text-3xl italic text-primary-600 dark:text-primary-300">{{ $negocio->propietaria }}</p>
                    <p class="mt-1 text-[11px] uppercase tracking-[0.28em] text-cream-600 dark:text-cream-400">{{ $negocio->propietariaCargo }}</p>
                </div>
            </div>

            <dl class="mt-12 grid grid-cols-3 gap-6 border-t border-cream-900/10 pt-8 dark:border-white/10">
                @foreach ($negocio->cifras as $cifra)
                    <div data-anim class="flex flex-col-reverse justify-end gap-2">
                        <dt class="text-sm leading-snug text-cream-600 dark:text-cream-400">{{ $cifra['texto'] }}</dt>
                        <dd class="font-serif text-4xl text-cream-950 sm:text-5xl dark:text-cream-50">
                            {{ $cifra['prefijo'] }}<span data-contador="{{ $cifra['valor'] }}">{{ $cifra['valor'] }}</span><span class="text-2xl sm:text-3xl">{{ $cifra['sufijo'] }}</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</section>
