{{-- Listado genérico de Productos / Proceso / Galería ($vista lo arma cada controlador) --}}
@extends('layouts.app')

@section('title', 'Página web · '.$vista['titulo'])
@section('header', 'Página web')

@section('content')
    @include('pagina-web._encabezado', ['subtitulo' => $vista['descripcion']])

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-cream-600 dark:text-cream-400">
            {{ $elementos->count() }} {{ $elementos->count() === 1 ? $vista['singular'] : $vista['singular'].'s' }} · aparecen en la página en este orden
        </p>
        <x-button icon="plus" :href="route($vista['ruta'].'.create')">Agregar {{ $vista['singular'] }}</x-button>
    </div>

    @if ($elementos->isEmpty())
        <x-card>
            <x-empty-state :icon="$vista['icono']" title="Todavía no hay nada aquí"
                           description="Esta sección no aparece en la página hasta que agregues el primero.">
                <x-slot:actions>
                    <x-button icon="plus" :href="route($vista['ruta'].'.create')">Agregar {{ $vista['singular'] }}</x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-card>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($elementos as $elemento)
                <x-card padding="p-0" clip class="flex flex-col">
                    <div class="relative {{ $vista['proporcion'] }} bg-cream-100 dark:bg-cream-900">
                        @if ($elemento->hasImagen())
                            <img src="{{ $elemento->imagen_url }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 text-cream-500 dark:text-cream-400">
                                <x-icon name="camera" class="w-7 h-7" />
                                <span class="text-xs">Sin foto</span>
                            </div>
                        @endif
                        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-semibold text-cream-800 shadow-soft dark:bg-cream-950/85 dark:text-cream-100">
                            #{{ $loop->iteration }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col gap-2 p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold leading-snug text-cream-900 dark:text-cream-50">{{ $elemento->titulo }}</h3>
                            @if (isset($elemento->activo) && ! $elemento->activo)
                                <x-badge variant="neutral" size="sm">Oculto</x-badge>
                            @elseif (! $elemento->hasImagen())
                                <x-badge variant="warning" size="sm">Sin foto</x-badge>
                            @endif
                        </div>

                        @if ($detalle = $elemento->descripcion ?? $elemento->texto)
                            <p class="line-clamp-3 text-sm text-cream-600 dark:text-cream-400">{{ $detalle }}</p>
                        @endif

                        <div class="mt-auto flex items-center gap-2 pt-2">
                            <x-button variant="secondary" size="sm" icon="edit" :href="route($vista['ruta'].'.edit', $elemento)">Editar</x-button>
                            <form method="POST" action="{{ route($vista['ruta'].'.destroy', $elemento) }}"
                                  onsubmit="event.preventDefault(); return swalConfirm(this, {title: @js('¿Eliminar «'.$elemento->titulo.'»?'), text: 'Se quita de la página web junto con su foto.', icon: 'warning', confirmButtonText: 'Sí, eliminar', confirmButtonColor: '#e11d48'});">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="ghost" size="sm" icon="trash-2" class="!text-rose-600 dark:!text-rose-400">Eliminar</x-button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
