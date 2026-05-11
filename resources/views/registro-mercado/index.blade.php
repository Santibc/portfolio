@extends('layouts.app')

@section('header', 'Registrar Mercado')

@section('content')
    <x-page-header
        title="Registrar Mercado"
        subtitle="Toca un producto para registrar cantidad y valor"
        icon="shopping-cart"
    />

    <form action="{{ route('registro-mercado.index') }}" method="GET"
          class="sticky top-16 z-30 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3
                 bg-cream-50/95 dark:bg-surface-dark/95 backdrop-blur
                 border-b border-cream-200 dark:border-cream-800 mb-4">
        <x-select
            name="tipo_id"
            :value="$tipoId"
            placeholder="Todos los tipos"
            :options="$tipos->pluck('nombre', 'id')"
            x-data
            x-on:change="$el.form.submit()"
            aria-label="Filtrar por tipo"
        />
    </form>

    @if ($productos->isEmpty())
        <x-empty-state
            icon="shopping-basket"
            title="No hay productos"
            :description="$tipoId
                ? 'No hay productos activos para este tipo. Cambia el filtro o crea uno nuevo en Productos Mercado.'
                : 'Aún no hay productos activos. Ve a Productos Mercado para crear el primero.'"
        />
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @foreach ($productos as $p)
                <a href="{{ route('registro-mercado.create', ['producto' => $p->id] + ($tipoId ? ['tipo_id' => $tipoId] : [])) }}"
                   class="group flex flex-col rounded-xl overflow-hidden bg-white dark:bg-cream-900/40
                          border border-cream-200 dark:border-cream-800
                          hover:border-primary-400 hover:shadow-md dark:hover:border-primary-500
                          active:scale-[0.98] transition-all">
                    <div class="aspect-square w-full bg-cream-100 dark:bg-cream-900">
                        @if ($p->hasImagen())
                            <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}"
                                 class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-cream-400 dark:text-cream-600">
                                <x-icon name="image" class="w-8 h-8" />
                            </div>
                        @endif
                    </div>
                    <div class="p-2 flex flex-col gap-1">
                        <p class="text-xs sm:text-sm font-semibold text-cream-900 dark:text-cream-50 line-clamp-2 leading-snug">
                            {{ $p->nombre }}
                        </p>
                        <div class="flex flex-wrap items-center gap-1">
                            @if ($p->tipo)
                                <span class="inline-flex items-center font-semibold rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-200 text-[10px] px-1.5 py-0.5">
                                    {{ $p->tipo->nombre }}
                                </span>
                            @endif
                            <span class="text-[10px] text-cream-600 dark:text-cream-400 truncate">
                                {{ $p->unidad_empaque }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
