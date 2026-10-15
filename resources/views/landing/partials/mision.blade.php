<section class="relative overflow-hidden bg-cream-100 py-24 sm:py-32 dark:bg-cream-950/60">
    <div aria-hidden="true" class="pointer-events-none absolute -right-40 -top-40 h-[30rem] w-[30rem] rounded-full bg-gold-300/25 blur-3xl dark:bg-primary-800/20"></div>

    <div class="relative mx-auto grid max-w-7xl gap-6 px-5 sm:px-8 lg:grid-cols-2 lg:gap-8">
        @foreach (['Nuestra misión' => $negocio->mision, 'Nuestra visión' => $negocio->vision] as $titulo => $texto)
            <article data-anim class="relative rounded-[2rem] border border-cream-900/10 bg-white/70 p-9 backdrop-blur sm:p-14 dark:border-white/10 dark:bg-cream-900/40">
                <span aria-hidden="true" class="absolute right-8 top-2 font-serif text-[9rem] leading-none text-gold-500/40 dark:text-gold-300/20">&ldquo;</span>
                <h2 class="eyebrow">{{ $titulo }}</h2>
                <p class="relative mt-6 font-serif text-2xl leading-snug text-cream-950 sm:text-3xl dark:text-cream-50">{{ $texto }}</p>
            </article>
        @endforeach
    </div>
</section>
