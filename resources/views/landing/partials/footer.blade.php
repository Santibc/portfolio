<footer class="relative overflow-hidden bg-cream-950 text-cream-200">
    <div class="mx-auto max-w-7xl px-5 pb-10 pt-20 sm:px-8">
        <div class="grid gap-12 border-b border-white/10 pb-14 sm:grid-cols-2 lg:grid-cols-12">
            <div class="sm:col-span-2 lg:col-span-4">
                <img src="{{ asset('images/logo-horizontal-dark.png') }}" alt="{{ $negocio->nombre }}" width="720" height="129" class="h-12 w-auto" loading="lazy">
                <p class="mt-6 max-w-sm leading-relaxed text-cream-300">
                    Pan artesanal y pastelería hechos con {{ mb_strtolower($negocio->lema) }}, en el barrio {{ $negocio->barrio }} de Bogotá.
                </p>
                @if ($negocio->redes)
                    <ul class="mt-6 flex flex-wrap gap-3">
                        @foreach ($negocio->redes as $red => $url)
                            <li><a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex rounded-full border border-white/15 px-4 py-2 text-sm capitalize transition-colors hover:border-gold-300 hover:text-gold-300">{{ $red }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <nav class="lg:col-span-2" aria-label="Secciones">
                <h2 class="text-[11px] font-semibold uppercase tracking-[0.28em] text-gold-300">Explora</h2>
                <ul class="mt-5 space-y-3">
                    @foreach ($enlaces as $ancla => $texto)
                        <li><a href="#{{ $ancla }}" class="transition-colors hover:text-gold-300">{{ $texto }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div class="lg:col-span-3">
                <h2 class="text-[11px] font-semibold uppercase tracking-[0.28em] text-gold-300">Contacto</h2>
                <ul class="mt-5 space-y-3">
                    <li>{{ $negocio->direccion }}<br><span class="text-cream-400">Barrio {{ $negocio->barrio }}, {{ $negocio->ciudad }}</span></li>
                    <li><a href="{{ $negocio->telUrl() }}" class="transition-colors hover:text-gold-300">{{ $negocio->telefonoVisible() }}</a></li>
                    <li><a href="{{ $negocio->whatsappUrl() }}" target="_blank" rel="noopener" class="transition-colors hover:text-gold-300">Pedidos por WhatsApp</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-[11px] font-semibold uppercase tracking-[0.28em] text-gold-300">Horario</h2>
                <ul class="mt-5 space-y-3">
                    @foreach ($negocio->horario as $franja)
                        <li>{{ $franja['dias'] }}<br><span class="whitespace-nowrap text-cream-400">{{ $franja['horas'] }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="flex flex-col gap-4 pt-8 text-sm text-cream-400 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $negocio->nombreLargo }}.</p>
            <div class="flex items-center gap-6">
                <span>Hecho con cariño en Bogotá</span>
                @auth
                    <a href="{{ route('dashboard') }}" class="transition-colors hover:text-gold-300">Ir al panel</a>
                @else
                    <a href="{{ route('login') }}" class="transition-colors hover:text-gold-300">Ingresar</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Nombre gigante decorativo --}}
    <p aria-hidden="true" class="pointer-events-none -mb-[2vw] select-none whitespace-nowrap text-center font-serif leading-none text-white/[0.045]" style="font-size: 15.5vw">El Pan Dorado</p>
</footer>
