@extends('layouts.app')

@section('title', 'Página web · Fotos')
@section('header', 'Página web')

@section('content')
    @include('pagina-web._encabezado', ['subtitulo' => 'Las fotos fijas de la página: portada, historia, local y encargos'])

    <div class="grid items-start gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($fotos as $foto)
            @php
                // Cada formulario tiene su propia bolsa de errores (ver UpdateFotoLandingRequest).
                $bolsa = $errors->getBag(\App\Http\Requests\UpdateFotoLandingRequest::bolsaErrores($foto->slot->value));
            @endphp
            <x-card>
                <x-slot:header>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-cream-900 dark:text-cream-50">{{ $foto->slot->etiqueta() }}</h2>
                            <p class="mt-0.5 text-xs text-cream-600 dark:text-cream-400">{{ $foto->slot->descripcion() }}</p>
                        </div>
                        @if ($foto->hasImagen())
                            <x-badge variant="success" size="sm" class="shrink-0 whitespace-nowrap">Con foto</x-badge>
                        @else
                            <x-badge variant="warning" size="sm" class="shrink-0 whitespace-nowrap">Pendiente</x-badge>
                        @endif
                    </div>
                </x-slot:header>

                <form method="POST" action="{{ route('pagina-web.fotos.update', $foto) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <x-input-imagen label="" :id="'foto-'.$foto->slot->value" :actual="$foto->imagen_url ?: null"
                                    :proporcion="$foto->slot->proporcion()" :error="$bolsa->first('imagen')" />

                    <x-input label="Descripción de la foto" :id="'alt-'.$foto->slot->value" name="alt"
                             :value="$bolsa->any() ? old('alt') : $foto->alt" :placeholder="$foto->slot->altPorDefecto()"
                             :error="$bolsa->first('alt')" maxlength="150"
                             hint="Para Google y lectores de pantalla. Vacío = la sugerida." />

                    <x-button type="submit" icon="save" class="w-full justify-center">Guardar</x-button>
                </form>

                @if ($foto->hasImagen())
                    <form method="POST" action="{{ route('pagina-web.fotos.destroy', $foto) }}" class="mt-2"
                          onsubmit="event.preventDefault(); return swalConfirm(this, {title: '¿Quitar esta foto?', text: 'En la página se verá un espacio reservado hasta que subas otra.', icon: 'warning', confirmButtonText: 'Sí, quitar', confirmButtonColor: '#e11d48'});">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="ghost" size="sm" icon="trash-2" class="w-full justify-center !text-rose-600 dark:!text-rose-400">
                            Quitar foto
                        </x-button>
                    </form>
                @endif
            </x-card>
        @endforeach
    </div>
@endsection
