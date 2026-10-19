{{-- Sello inferior del empaque --}}
<footer class="relative mt-6" aria-labelledby="titulo-pie">
    <div class="sello dientes-arriba h-6" aria-hidden="true" style="--diente: 8px"></div>
    <div class="bg-[color:var(--sello)]">
        <div class="contenedor grid gap-12 py-14 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <img src="{{ asset('images/landing/logo-384.webp') }}" alt="" width="128" height="128" class="h-28 w-28" loading="lazy">
                <h2 id="titulo-pie" class="mt-4 font-brand text-3xl leading-none">{{ $negocio['nombre'] }}</h2>
                <p class="texto-suave mt-2 font-semibold">{{ $negocio['lema'] }}</p>
            </div>

            <div class="lg:col-span-3">
                <h3 class="rotulo texto-tenue text-sm">Pide</h3>
                <ul class="mt-4 space-y-3 font-semibold">
                    <li>
                        <a href="{{ $enlaces['whatsapp'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 hover:underline">
                            <x-brand-icon name="whatsapp" class="h-5 w-5" /> WhatsApp {{ $negocio['whatsapp_visible'] }}
                        </a>
                    </li>
                    @if ($enlaces['rappi'])
                        <li>
                            <a href="{{ $enlaces['rappi'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 hover:underline">
                                <x-icon name="bike" class="h-5 w-5" /> Rappi
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ $enlaces['instagram'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 hover:underline">
                            <x-brand-icon name="instagram" class="h-5 w-5" /> {{ $negocio['instagram_usuario'] }}
                        </a>
                    </li>
                    @if ($enlaces['tiktok'])
                        <li>
                            <a href="{{ $enlaces['tiktok'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 hover:underline">
                                <x-brand-icon name="tiktok" class="h-5 w-5" /> TikTok
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="rotulo texto-tenue text-sm">Visítanos</h3>
                <address class="mt-4 not-italic">
                    <p class="font-semibold">{{ $negocio['direccion'] }}</p>
                    <p class="texto-suave">{{ $negocio['barrio'] }}, {{ $negocio['ciudad'] }}</p>
                </address>
                <ul class="texto-suave mt-4 space-y-1 text-[0.95rem]">
                    @foreach ($horario['dias'] as $franja)
                        <li><span class="font-semibold texto-tinta">{{ $franja['dias'] }}:</span> {{ $franja['horas'] }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2">
                @include('landing.partials.codigo-barras', ['numero' => $negocio['whatsapp_visible']])
            </div>
        </div>

        <div class="contenedor letra-legal flex flex-wrap items-center justify-between gap-4 border-t border-[color:var(--sello-estria)] py-6">
            <p>Contenido neto: 1 antojo grande. Hecho al momento en {{ $negocio['barrio'] }}, {{ $negocio['ciudad'] }}. © {{ date('Y') }} {{ $negocio['nombre'] }}.</p>
            <a href="{{ route('login') }}" class="hover:underline">Acceso del equipo</a>
        </div>
    </div>
</footer>
