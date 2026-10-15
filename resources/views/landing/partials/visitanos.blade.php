<section id="visitanos" class="relative bg-cream-100 py-24 sm:py-32 dark:bg-cream-950/60">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 sm:px-8 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <p class="eyebrow">Visítanos</p>
            <h2 data-split class="titulo-seccion mt-5">Te esperamos con el <em>horno</em> encendido</h2>

            <ul class="mt-10 space-y-7">
                <li data-anim class="flex gap-4">
                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-600 dark:bg-primary-900/60 dark:text-primary-200">
                        <x-icon name="map-pin" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-cream-600 dark:text-cream-400">Dirección</p>
                        <p class="mt-1 text-lg text-cream-950 dark:text-cream-50">{{ $negocio->direccion }}</p>
                        <p class="text-cream-700 dark:text-cream-300">Barrio {{ $negocio->barrio }}, {{ $negocio->ciudad }}</p>
                    </div>
                </li>
                <li data-anim class="flex gap-4">
                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-600 dark:bg-primary-900/60 dark:text-primary-200">
                        <x-icon name="phone" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-cream-600 dark:text-cream-400">Teléfono y WhatsApp</p>
                        <a href="{{ $negocio->telUrl() }}" class="mt-1 inline-block text-lg text-cream-950 transition-colors hover:text-primary-600 dark:text-cream-50 dark:hover:text-primary-300">{{ $negocio->telefonoVisible() }}</a>
                    </div>
                </li>
                <li data-anim class="flex gap-4">
                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-600 dark:bg-primary-900/60 dark:text-primary-200">
                        <x-icon name="clock" class="h-5 w-5" />
                    </span>
                    <div class="flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-cream-600 dark:text-cream-400">Horario</p>
                        <dl class="mt-1 space-y-1">
                            @foreach ($negocio->horario as $franja)
                                <div class="flex flex-wrap justify-between gap-x-6 text-cream-950 dark:text-cream-50">
                                    <dt>{{ $franja['dias'] }}</dt>
                                    <dd class="text-cream-700 dark:text-cream-300">{{ $franja['horas'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </li>
            </ul>

            <div data-anim class="mt-10 flex flex-wrap gap-3">
                <a href="{{ $negocio->comoLlegarUrl() }}" target="_blank" rel="noopener" class="btn-landing-primario">
                    <x-icon name="navigation" class="h-4 w-4" /> Cómo llegar
                </a>
                <a href="{{ $negocio->whatsappUrl() }}" target="_blank" rel="noopener" class="btn-landing-secundario">
                    @include('landing.partials.icono-whatsapp', ['clase' => 'h-4 w-4']) Escríbenos
                </a>
            </div>
        </div>

        <div data-anim class="lg:col-span-7">
            <div class="relative h-[24rem] overflow-hidden rounded-[2rem] border border-cream-900/10 bg-cream-200 shadow-soft-lg sm:h-[30rem] lg:h-full lg:min-h-[34rem] dark:border-white/10 dark:bg-cream-900">
                <iframe src="{{ $negocio->mapaEmbedUrl() }}"
                        title="Mapa de {{ $negocio->nombre }} en {{ $negocio->direccionCompleta() }}"
                        class="absolute inset-0 h-full w-full border-0"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>
