{{-- Encabezado común del módulo "Página web" + navegación entre sus pantallas --}}
@php
    $pantallas = [
        ['pagina-web.negocio.edit', 'pagina-web.negocio.*', 'store', 'Negocio'],
        ['pagina-web.textos.edit', 'pagina-web.textos.*', 'type', 'Textos'],
        ['pagina-web.fotos.index', 'pagina-web.fotos.*', 'image-plus', 'Fotos'],
        ['pagina-web.productos.index', 'pagina-web.productos.*', 'croissant', 'Productos'],
        ['pagina-web.proceso.index', 'pagina-web.proceso.*', 'list-ordered', 'Proceso'],
        ['pagina-web.galeria.index', 'pagina-web.galeria.*', 'images', 'Galería'],
    ];
@endphp

<x-page-header title="Página web" :subtitle="$subtitulo" icon="globe">
    <x-slot:actions>
        <x-button variant="secondary" icon="external-link" :href="route('welcome')" target="_blank" rel="noopener">
            Ver página
        </x-button>
    </x-slot:actions>
</x-page-header>

<nav class="-mx-1 mb-6 flex gap-2 overflow-x-auto px-1 pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Secciones de la página web">
    @foreach ($pantallas as [$ruta, $patron, $icono, $texto])
        @php $activa = request()->routeIs($patron); @endphp
        <a href="{{ route($ruta) }}" @if ($activa) aria-current="page" @endif
           @class([
               'inline-flex shrink-0 items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-colors',
               'bg-primary-500 text-white shadow-soft dark:bg-primary-400 dark:text-primary-950' => $activa,
               'border border-cream-300 bg-white text-cream-700 hover:border-primary-400 hover:text-primary-700 dark:border-cream-700 dark:bg-cream-900 dark:text-cream-300 dark:hover:border-primary-500 dark:hover:text-primary-200' => ! $activa,
           ])>
            <x-icon :name="$icono" class="w-4 h-4" />
            {{ $texto }}
        </a>
    @endforeach
</nav>
