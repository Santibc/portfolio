{{-- Así se arma: con 3D la escena se fija y el paquete se abre y se llena paso a paso --}}
<section id="como-se-arma" class="relative" aria-labelledby="titulo-arma">
    <div class="arma-escena contenedor grid items-center gap-x-12 gap-y-4 py-16 lg:grid-cols-12 lg:py-10" data-arma-escena>
        <div class="arma-texto sobre-3d order-2 lg:order-1 lg:col-span-7">
            <h2 id="titulo-arma" class="titulo-seccion">Así se arma</h2>
            <p class="rotulo texto-tenue mt-3 text-sm">Modo de preparación · {{ count($pasos) }} pasos</p>

            <ol class="pasos mt-5" data-pasos>
                @foreach ($pasos as $i => $paso)
                    <li class="paso -mx-3 grid grid-cols-[auto_minmax(0,1fr)] items-center gap-x-4 px-3 py-3 sm:grid-cols-[auto_auto_minmax(0,1fr)_auto] sm:py-3.5" data-paso="{{ $i }}">
                        <span class="paso__num grid h-10 w-10 shrink-0 place-items-center rounded-full font-brand text-xl leading-none" aria-hidden="true">{{ $i + 1 }}</span>
                        @if (file_exists(public_path($paso['imagen'])))
                            <img src="{{ asset($paso['imagen']) }}" alt="" width="480" height="480" loading="lazy" decoding="async"
                                 class="hidden h-16 w-16 rounded-2xl object-cover ring-2 ring-black/10 dark:ring-white/25 sm:block">
                        @else
                            <span class="hidden sm:block" aria-hidden="true"></span>
                        @endif
                        <div>
                            <h3 class="text-lg font-extrabold leading-tight sm:text-xl">{{ $paso['titulo'] }}</h3>
                            {{-- En celular, con el pin activo, solo el paso actual muestra este detalle --}}
                            <p class="paso__detalle texto-suave mt-0.5 text-[0.95rem] leading-snug">
                                {{ $paso['descripcion'] }}
                                <span class="rotulo texto-tenue mt-1 block text-[0.7rem] sm:hidden">{{ $paso['medida'] }}</span>
                            </p>
                        </div>
                        <p class="rotulo texto-tenue hidden max-w-[9rem] text-right text-xs leading-tight sm:block">{{ $paso['medida'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="order-1 h-[24vh] sm:h-[30vh] lg:order-2 lg:col-span-5 lg:mt-20 lg:h-[60vh]" data-bag-anchor="arma" data-bag-mode="arma" data-bag-rot="0.2,-0.25,0.04"></div>
    </div>
</section>
