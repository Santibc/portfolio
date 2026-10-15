@php
    $pasos = [
        ['titulo' => 'Ingredientes seleccionados', 'texto' => 'Harinas de calidad, mantequilla, huevos frescos y nada de afanes. Todo empieza por lo que ponemos en la mesa de trabajo.', 'imagen' => 'paso-ingredientes', 'alt' => 'Harina y huevos frescos en un recipiente'],
        ['titulo' => 'Amasado a mano', 'texto' => 'Cada masa se trabaja con paciencia y respeta su tiempo de reposo, para lograr una miga suave y aireada.', 'imagen' => 'paso-amasado', 'alt' => 'Manos amasando una masa de pan sobre harina'],
        ['titulo' => 'Horneado lento', 'texto' => 'Temperatura y tiempo justos para esa corteza dorada que cruje al partirla. Horneamos varias veces al día.', 'imagen' => 'paso-horneado', 'alt' => 'Panadero sacando pan del horno'],
        ['titulo' => 'Directo a tu mesa', 'texto' => 'Del horno a la vitrina y de la vitrina a tu casa, todavía calientico. Así de sencillo, así de rico.', 'imagen' => 'paso-mesa', 'alt' => 'Croissants recién horneados servidos con café'],
    ];
@endphp
{{-- En escritorio landing.js fija la sección y desplaza la pista en horizontal; sin JS (o con
     movimiento reducido) la pista se recorre con scroll horizontal y en móvil se apila. --}}
<section id="proceso" data-horizontal class="relative overflow-hidden bg-cream-950 py-24 text-cream-50 sm:py-32 lg:flex lg:min-h-screen lg:flex-col lg:justify-center lg:py-24">
    <div aria-hidden="true" class="pointer-events-none absolute -right-40 top-0 h-[30rem] w-[30rem] rounded-full bg-primary-700/25 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-7xl px-5 sm:px-8">
        <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
            <div>
                <p class="eyebrow !text-gold-300">Del horno a tu mesa</p>
                <h2 data-split class="titulo-seccion mt-5 !text-cream-50">Así nace <em class="!text-gold-300">cada</em> pan</h2>
            </div>
            <p data-anim class="max-w-sm text-cream-300">Sin atajos: buenos ingredientes, manos expertas y el tiempo que cada masa necesita.</p>
        </div>
    </div>

    <div data-pista-marco class="relative mt-14 lg:overflow-x-auto">
        <ol data-pista class="flex flex-col gap-6 px-5 sm:px-8 lg:w-max lg:flex-row lg:gap-8 lg:px-[max(2rem,calc((100vw-80rem)/2+2rem))]">
            @foreach ($pasos as $paso)
                <li class="group lg:w-[26rem] lg:shrink-0 xl:w-[29rem]">
                    <article class="h-full overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.04]">
                        <div class="relative aspect-[3/2] overflow-hidden">
                            <img src="{{ asset('images/landing/'.$paso['imagen'].'-600.webp') }}"
                                 srcset="{{ asset('images/landing/'.$paso['imagen'].'-600.webp') }} 600w, {{ asset('images/landing/'.$paso['imagen'].'-1000.webp') }} 1000w"
                                 sizes="(min-width: 1024px) 29rem, 90vw" width="1000" height="1250" loading="lazy" decoding="async"
                                 alt="{{ $paso['alt'] }}"
                                 class="h-full w-full object-cover transition-transform duration-[1400ms] ease-out group-hover:scale-105">
                            <span class="absolute left-5 top-4 font-serif text-6xl italic text-white/90 [text-shadow:0_2px_20px_rgb(0_0_0/0.45)]">0{{ $loop->iteration }}</span>
                        </div>
                        <div class="p-7">
                            <h3 class="font-serif text-3xl text-cream-50">{{ $paso['titulo'] }}</h3>
                            <p class="mt-3 leading-relaxed text-cream-300">{{ $paso['texto'] }}</p>
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
