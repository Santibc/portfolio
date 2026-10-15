@php
    use App\Enums\TextoLanding as T;
@endphp
<section id="galeria" class="relative py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-8">
        <div class="flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-end">
            <div class="max-w-2xl">
                <p class="eyebrow">Galería</p>
                <h2 data-split class="titulo-seccion mt-5">{{ $textos->html(T::GaleriaTitulo) }}</h2>
            </div>
            <p data-anim class="max-w-md text-cream-700 dark:text-cream-300">{{ $textos->texto(T::GaleriaTexto) }}</p>
        </div>

        <div class="mt-14 columns-2 gap-4 sm:gap-6 lg:columns-3">
            @foreach ($galeria as $foto)
                <div data-anim class="mb-4 break-inside-avoid sm:mb-6">
                    @if ($foto->hasImagen())
                        @php $medidas = $foto->dimensionesImagen(); @endphp
                        <figure class="group overflow-hidden rounded-[1.75rem] bg-cream-200 dark:bg-cream-900">
                            <img src="{{ $foto->imagen_url }}"
                                 @if ($foto->imagen_srcset) srcset="{{ $foto->imagen_srcset }}" sizes="(min-width: 1024px) 26rem, 48vw" @endif
                                 @if ($medidas) width="{{ $medidas[0] }}" height="{{ $medidas[1] }}" @endif
                                 loading="lazy" decoding="async" alt="{{ $foto->titulo }}"
                                 class="h-auto w-full transition-transform duration-[1200ms] ease-out group-hover:scale-105">
                        </figure>
                    @else
                        {{-- Espacio reservado hasta que suban la foto desde el panel --}}
                        <x-landing.foto-pendiente :texto="$foto->titulo" class="aspect-[4/3] rounded-[1.75rem]" />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
