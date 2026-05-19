@extends('layouts.app')

@section('header', 'Menú')

@section('content')
<div x-data="{ tiposOpen: false }">
    <x-page-header
        title="Menú"
        subtitle="Platos, combos y adiciones disponibles para la caja"
        icon="utensils-crossed"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="settings"
                @click="tiposOpen = true"
            >
                Categorías
            </x-button>
            <x-button
                variant="primary"
                icon="plus"
                :href="route('menu-items.create')"
            >
                Nuevo item
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filtro por tipo --}}
    <div class="mb-5">
        <form method="GET" action="{{ route('menu-items.index') }}" class="flex flex-wrap items-center gap-2">
            <a href="{{ route('menu-items.index') }}"
               class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-medium border transition-all {{ $tipoId === null ? 'bg-primary-500 text-white border-primary-500 shadow-soft' : 'bg-white text-cream-800 border-cream-300 hover:bg-cream-100 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700 dark:hover:bg-cream-800' }}">
                Todos
            </a>
            @foreach ($tipos as $t)
                <a href="{{ route('menu-items.index', ['tipo_id' => $t->id]) }}"
                   class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-medium border transition-all {{ $tipoId === $t->id ? 'bg-primary-500 text-white border-primary-500 shadow-soft' : 'bg-white text-cream-800 border-cream-300 hover:bg-cream-100 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700 dark:hover:bg-cream-800' }}">
                    {{ $t->nombre }}
                </a>
            @endforeach
        </form>
    </div>

    @if ($items->isEmpty())
        <x-empty-state
            icon="utensils-crossed"
            title="Aún no hay items"
            description="Crea el primer plato, combo o adición con el botón “Nuevo item”."
        />
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach ($items as $item)
                <x-card padding="p-0" class="overflow-hidden flex flex-col h-full">
                    <div class="relative w-full pt-[100%] bg-cream-100 dark:bg-cream-900/40 overflow-hidden">
                        @if ($item->hasImagen())
                            <img src="{{ $item->imagen_url }}" alt="{{ $item->nombre }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-cream-400 dark:text-cream-600">
                                <x-icon name="image" class="w-10 h-10" />
                            </div>
                        @endif

                        @if (! $item->activo)
                            <span class="absolute top-2 left-2 inline-flex items-center font-semibold rounded-full bg-cream-900/80 text-cream-50 text-[10px] px-2 py-0.5">Inactivo</span>
                        @endif

                        <span class="absolute top-2 right-2 inline-flex items-center font-semibold rounded-full bg-primary-500/95 text-white text-[10px] px-2 py-0.5 shadow-soft">
                            {{ $item->tipo?->nombre }}
                        </span>
                    </div>

                    <div class="p-3 flex-1 flex flex-col">
                        <h3 class="font-semibold text-sm text-cream-900 dark:text-cream-50 line-clamp-2 min-h-[2.5rem]">
                            {{ $item->nombre }}
                        </h3>
                        <p class="mt-1 text-lg font-bold text-primary-700 dark:text-primary-300">
                            {{ $item->precio_formateado }}
                        </p>

                        <div class="mt-3 pt-3 border-t border-cream-200 dark:border-cream-800 flex items-center justify-between gap-2">
                            <a href="{{ route('menu-items.edit', $item) }}"
                               class="inline-flex items-center gap-1 text-xs text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium">
                                <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                            </a>
                            <form action="{{ route('menu-items.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este item del menú?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium">
                                    <x-icon name="trash-2" class="w-3.5 h-3.5" /> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif

    {{-- Modal Tipos --}}
    <div x-show="tiposOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-cream-950/60 backdrop-blur-sm"
         @keydown.escape.window="tiposOpen = false"
         @click.self="tiposOpen = false">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-lg bg-white dark:bg-surface-dark rounded-2xl shadow-soft-lg">
                <div class="flex items-center justify-between px-5 py-4 border-b border-cream-200 dark:border-cream-800">
                    <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">Categorías del menú</h3>
                    <button type="button" class="text-cream-500 hover:text-cream-800 dark:hover:text-cream-200"
                            @click="tiposOpen = false">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <ul class="divide-y divide-cream-200 dark:divide-cream-800">
                        @foreach ($tipos as $t)
                            <li class="py-2 flex items-center justify-between gap-2">
                                <form action="{{ route('menu-items.tipos.update', $t) }}" method="POST" class="flex-1 flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="nombre" value="{{ $t->nombre }}" required
                                           class="flex-1 rounded-lg border-cream-300 bg-white px-2.5 py-1.5 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-primary-500 hover:bg-primary-600 text-white text-xs font-medium">
                                        <x-icon name="save" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                                <form action="{{ route('menu-items.tipos.destroy', $t) }}" method="POST" onsubmit="return confirm('¿Eliminar la categoría {{ $t->nombre }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/40 dark:text-red-200 text-xs font-medium">
                                        <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    <form action="{{ route('menu-items.tipos.store') }}" method="POST" class="flex items-center gap-2 pt-3 border-t border-cream-200 dark:border-cream-800">
                        @csrf
                        <input type="text" name="nombre" placeholder="Nueva categoría…" required
                               class="flex-1 rounded-lg border-cream-300 bg-white px-2.5 py-1.5 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100">
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium">
                            <x-icon name="plus" class="w-3.5 h-3.5" /> Agregar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
