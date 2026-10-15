@php
    use App\Enums\TextoLanding as T;

    $fotoEncargos = $fotos->get('encargos');
@endphp
<section class="relative py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div data-anim class="relative isolate overflow-hidden rounded-[2.5rem] bg-cream-950 px-7 py-16 sm:px-14 sm:py-20 lg:px-20 lg:py-24">
            @if ($fotoEncargos?->hasImagen())
                <img data-parallax="8" src="{{ $fotoEncargos->imagen_url }}"
                     @if ($fotoEncargos->imagen_srcset) srcset="{{ $fotoEncargos->imagen_srcset }}" sizes="(min-width: 1280px) 80rem, 100vw" @endif
                     loading="lazy" decoding="async" alt="{{ $fotoEncargos->textoAlternativo() }}"
                     class="absolute inset-x-0 -top-[10%] -z-10 h-[120%] w-full object-cover object-right">
            @else
                <div aria-hidden="true" class="absolute -right-24 -top-24 -z-10 h-[28rem] w-[28rem] rounded-full bg-primary-600/40 blur-3xl"></div>
            @endif
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-cream-950 via-cream-950/85 to-cream-950/5"></div>

            <div class="max-w-xl">
                <p class="eyebrow !text-gold-300">Encargos</p>
                <h2 class="em-oro mt-5 font-serif leading-[1.04] text-white" style="font-size: clamp(2.2rem, 1.4rem + 2.8vw, 4rem)">
                    {{ $textos->html(T::EncargosTitulo) }}
                </h2>
                <p class="mt-6 text-lg leading-relaxed text-cream-200">{{ $textos->texto(T::EncargosTexto) }}</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ $negocio->whatsappUrl('Hola, '.$negocio->nombre.'. Quisiera cotizar una torta por encargo.') }}"
                       target="_blank" rel="noopener"
                       class="btn-landing bg-gold-300 text-cream-950 hover:-translate-y-0.5 hover:bg-white">
                        @include('landing.partials.icono-whatsapp', ['clase' => 'h-5 w-5'])
                        Cotiza tu torta
                    </a>
                    <a href="{{ $negocio->telUrl() }}" class="btn-landing border border-white/25 text-white hover:border-white">
                        <x-icon name="phone" class="h-4 w-4" /> {{ $negocio->telefonoVisible() }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
