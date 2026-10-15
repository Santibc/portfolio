@php
    // `foto` null = espacio reservado para las fotos reales del local.
    $galeria = [
        ['foto' => 'galeria-local', 'anchos' => [600, 1200], 'alto' => 447, 'alt' => 'Panadería con estantes de baguettes y canastas de pan'],
        ['foto' => null, 'texto' => 'Fachada del local'],
        ['foto' => 'galeria-vitrina', 'anchos' => [600, 1200], 'alto' => 401, 'alt' => 'Vitrina con hojaldres y panes dorados'],
        ['foto' => 'galeria-canela', 'anchos' => [600, 900], 'alto' => 800, 'alt' => 'Rollos de canela recién horneados'],
        ['foto' => null, 'texto' => 'Nuestro equipo'],
        ['foto' => 'galeria-baguettes', 'anchos' => [600, 1200], 'alto' => 400, 'alt' => 'Baguettes enharinadas sobre una mesa oscura'],
        ['foto' => 'galeria-tajado', 'anchos' => [600, 1200], 'alto' => 400, 'alt' => 'Pan artesanal tajado sobre una tabla de madera'],
        ['foto' => null, 'texto' => 'Interior del local'],
    ];
@endphp
<section id="galeria" class="relative py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-end">
            <div class="max-w-2xl">
                <p class="eyebrow">Galería</p>
                <h2 data-split class="titulo-seccion mt-5">Un vistazo a <em>nuestra</em> vitrina</h2>
            </div>
            <p data-anim class="max-w-md text-cream-700 dark:text-cream-300">
                Muy pronto, fotos de nuestro local, de nuestro equipo y de todo lo que sale del horno cada día.
            </p>
        </div>

        <div class="mt-14 columns-2 gap-4 sm:gap-6 lg:columns-3">
            @foreach ($galeria as $item)
                <div data-anim class="mb-4 break-inside-avoid sm:mb-6">
                    @if ($item['foto'])
                        <figure class="group overflow-hidden rounded-[1.75rem] bg-cream-200 dark:bg-cream-900">
                            <img src="{{ asset('images/landing/'.$item['foto'].'-600.webp') }}"
                                 srcset="{{ asset('images/landing/'.$item['foto'].'-'.$item['anchos'][0].'.webp') }} {{ $item['anchos'][0] }}w, {{ asset('images/landing/'.$item['foto'].'-'.$item['anchos'][1].'.webp') }} {{ $item['anchos'][1] }}w"
                                 sizes="(min-width: 1024px) 26rem, 48vw" width="600" height="{{ $item['alto'] }}"
                                 loading="lazy" decoding="async" alt="{{ $item['alt'] }}"
                                 class="h-auto w-full transition-transform duration-[1200ms] ease-out group-hover:scale-105">
                        </figure>
                    @else
                        <x-landing.foto-pendiente :texto="$item['texto']" class="aspect-[4/3] rounded-[1.75rem]" />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
