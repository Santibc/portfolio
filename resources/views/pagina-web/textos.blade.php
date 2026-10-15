@extends('layouts.app')

@section('title', 'Página web · Textos')
@section('header', 'Página web')

@section('content')
    @include('pagina-web._encabezado', ['subtitulo' => 'Todos los textos de la página, sección por sección'])

    <x-alert variant="info" title="Palabras en cursiva dorada" class="mb-6">
        En los títulos, escribe entre <strong>*asteriscos*</strong> la palabra que quieres resaltar.
        Por ejemplo: <code class="rounded bg-white/60 px-1.5 py-0.5 dark:bg-black/20">Pan *dorado*, horneado con amor</code>
    </x-alert>

    <form method="POST" action="{{ route('pagina-web.textos.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid items-start gap-6 lg:grid-cols-2">
            @foreach ($secciones as $seccion => $claves)
                <x-card>
                    <x-slot:header>
                        <h2 class="font-semibold text-cream-900 dark:text-cream-50">{{ $seccion }}</h2>
                    </x-slot:header>

                    <div class="space-y-4">
                        @foreach ($claves as $clave)
                            @php
                                $nombre = 'textos['.$clave->value.']';
                                $valor = old('textos.'.$clave->value, $textos->texto($clave));
                            @endphp
                            @if ($clave->multilinea())
                                <x-textarea :label="$clave->etiqueta()" :name="$nombre" :value="$valor" :hint="$clave->ayuda()"
                                            :rows="$clave === \App\Enums\TextoLanding::Historia ? 8 : 3" :maxlength="$clave->maximo()" required />
                            @else
                                <x-input :label="$clave->etiqueta()" :name="$nombre" :value="$valor" :hint="$clave->ayuda()"
                                         :maxlength="$clave->maximo()" required />
                            @endif
                        @endforeach

                        @if (in_array(\App\Enums\TextoLanding::Historia, $claves, true))
                            @include('pagina-web._cifras')
                        @endif

                        @if (in_array(\App\Enums\TextoLanding::ValoresTitulo, $claves, true))
                            @include('pagina-web._valores')
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="sticky bottom-4 z-10 flex justify-end">
            <x-button type="submit" icon="save" class="shadow-soft-lg">Guardar textos</x-button>
        </div>
    </form>
@endsection
