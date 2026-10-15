<section class="relative pb-24 sm:pb-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="eyebrow">Por qué elegirnos</p>
            <h2 data-split class="titulo-seccion mt-5">Calidad <em>&amp;</em> servicio en cada detalle</h2>
        </div>

        <div class="mt-16 grid gap-px overflow-hidden rounded-[2rem] border border-cream-900/10 bg-cream-900/10 sm:grid-cols-2 lg:grid-cols-4 dark:border-white/10 dark:bg-white/10">
            @foreach ($negocio->valores as $valor)
                <div data-anim class="group bg-cream-50 p-8 transition-colors duration-500 hover:bg-white lg:p-10 dark:bg-surface-dark dark:hover:bg-cream-950">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-primary-600 transition-transform duration-500 group-hover:-rotate-6 group-hover:scale-110 dark:bg-primary-900/60 dark:text-primary-200">
                        <x-icon :name="$valor['icono']" class="h-6 w-6" />
                    </span>
                    <h3 class="mt-7 font-serif text-3xl text-cream-950 dark:text-cream-50">{{ $valor['titulo'] }}</h3>
                    <p class="mt-3 leading-relaxed text-cream-700 dark:text-cream-300">{{ $valor['texto'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
