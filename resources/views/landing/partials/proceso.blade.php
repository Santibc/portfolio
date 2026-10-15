@php
    use App\Enums\TextoLanding as T;
@endphp
{{-- En escritorio landing.js fija la sección y desplaza la pista en horizontal; sin JS (o con
     movimiento reducido) la pista se recorre con scroll horizontal y en móvil se apila. --}}
<section id="proceso" data-horizontal class="relative overflow-hidden bg-cream-950 py-24 text-cream-50 sm:py-32 lg:flex lg:min-h-screen lg:flex-col lg:justify-center lg:py-24">
    <div aria-hidden="true" class="pointer-events-none absolute -right-40 top-0 h-[30rem] w-[30rem] rounded-full bg-primary-700/25 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-7xl px-5 sm:px-8">
        <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
            <div>
                <p class="eyebrow !text-gold-300">Del horno a tu mesa</p>
                <h2 data-split class="titulo-seccion em-oro mt-5 !text-cream-50">{{ $textos->html(T::ProcesoTitulo) }}</h2>
            </div>
            <p data-anim class="max-w-sm text-cream-300">{{ $textos->texto(T::ProcesoTexto) }}</p>
        </div>
    </div>

    <div data-pista-marco class="relative mt-14 lg:overflow-x-auto">
        <ol data-pista class="flex flex-col gap-6 px-5 sm:px-8 lg:w-max lg:flex-row lg:gap-8 lg:px-[max(2rem,calc((100vw-80rem)/2+2rem))]">
            @foreach ($pasos as $paso)
                <li class="group lg:w-[26rem] lg:shrink-0 xl:w-[29rem]">
                    <article class="h-full overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.04]">
                        <div class="relative aspect-[3/2] overflow-hidden bg-cream-900">
                            @if ($paso->hasImagen())
                                <img src="{{ $paso->imagen_url }}"
                                     @if ($paso->imagen_srcset) srcset="{{ $paso->imagen_srcset }}" sizes="(min-width: 1024px) 29rem, 90vw" @endif
                                     loading="lazy" decoding="async" alt="{{ $paso->titulo }}"
                                     class="h-full w-full object-cover transition-transform duration-[1400ms] ease-out group-hover:scale-105">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-primary-800 to-cream-950"></div>
                            @endif
                            <span class="absolute left-5 top-4 font-serif text-6xl italic text-white/90 [text-shadow:0_2px_20px_rgb(0_0_0/0.45)]">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="p-7">
                            <h3 class="font-serif text-3xl text-cream-50">{{ $paso->titulo }}</h3>
                            <p class="mt-3 leading-relaxed text-cream-300">{{ $paso->texto }}</p>
                        </div>
                    </article>
                </li>
            @endforeach
        </ol>
    </div>

    <div class="relative mx-auto mt-12 hidden w-full max-w-7xl px-8 lg:block" aria-hidden="true">
        <div class="h-px bg-white/15">
            <div data-progreso class="h-px origin-left scale-x-0 bg-gold-300"></div>
        </div>
    </div>
</section>
