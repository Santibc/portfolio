@extends('layouts.app')

@section('header', 'Lista — ' . $tipo->nombre)

@section('content')
    <x-page-header
        :title="$tipo->nombre"
        subtitle="Toca un producto para registrar o saltar"
        icon="map-pin"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('lista-mercado.index')">
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @php
        $pendientesCount  = $pendientes->count();
        $registradosCount = $items->where('estado.value', 'registrado')->count();
        $saltadosCount    = $items->where('estado.value', 'saltado')->count();
        $totalCount       = $items->count();
    @endphp

    <div class="mb-4 surface-card p-4 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <x-progress
                :value="$registradosCount + $saltadosCount"
                :max="$totalCount"
                :color="$pendientesCount === 0 ? 'emerald' : 'primary'"
                :label="$registradosCount + $saltadosCount . ' de ' . $totalCount . ' procesados'"
                showValue
            />
        </div>
        <div class="flex flex-wrap gap-1.5">
            @if ($registradosCount > 0)
                <x-badge variant="success" size="sm" icon="check">{{ $registradosCount }} registrados</x-badge>
            @endif
            @if ($saltadosCount > 0)
                <x-badge variant="neutral" size="sm" icon="skip-forward">{{ $saltadosCount }} saltados</x-badge>
            @endif
            @if ($pendientesCount > 0)
                <x-badge variant="warning" size="sm" icon="clock">{{ $pendientesCount }} pendientes</x-badge>
            @endif
        </div>
    </div>

    @if ($pendientes->isEmpty())
        <x-empty-state
            icon="check-circle"
            title="¡Tipo completado!"
            description="Todos los productos de {{ $tipo->nombre }} han sido procesados."
        >
            <x-slot:actions>
                <x-button variant="primary" icon="arrow-left" :href="route('lista-mercado.index')">
                    Ver otros lugares
                </x-button>
            </x-slot:actions>
        </x-empty-state>
    @else
        <h3 class="text-sm font-semibold uppercase tracking-wider text-cream-700 dark:text-cream-300 mb-3">
            Pendientes ({{ $pendientesCount }})
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @foreach ($pendientes as $item)
                @php $p = $item->producto; @endphp
                <a href="{{ route('lista-mercado.item.create', $item) }}"
                   class="group flex flex-col rounded-xl overflow-hidden bg-white dark:bg-cream-900/40
                          border border-cream-200 dark:border-cream-800
                          hover:border-primary-400 hover:shadow-md dark:hover:border-primary-500
                          active:scale-[0.98] transition-all">
                    <div class="aspect-square w-full bg-cream-100 dark:bg-cream-900 relative">
                        @if ($p && $p->hasImagen())
                            <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}"
                                 class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-cream-400 dark:text-cream-600">
                                <x-icon name="image" class="w-8 h-8" />
                            </div>
                        @endif
                        <span class="absolute top-1.5 right-1.5 inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-primary-500 text-white shadow">
                            {{ $item->cantidad_sugerida }}
                        </span>
                    </div>
                    <div class="p-2 flex flex-col gap-1">
                        <p class="text-xs sm:text-sm font-semibold text-cream-900 dark:text-cream-50 line-clamp-2 leading-snug">
                            {{ $p?->nombre ?? '—' }}
                        </p>
                        <span class="text-[10px] text-cream-600 dark:text-cream-400 truncate">
                            {{ $p?->unidad_empaque }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if ($procesados->isNotEmpty())
        <h3 class="mt-8 text-sm font-semibold uppercase tracking-wider text-cream-700 dark:text-cream-300 mb-3">
            Procesados ({{ $procesados->count() }})
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @foreach ($procesados as $item)
                @php $p = $item->producto; @endphp
                <div class="flex flex-col rounded-xl overflow-hidden bg-cream-50 dark:bg-cream-900/20
                            border border-cream-200 dark:border-cream-800 opacity-70">
                    <div class="aspect-square w-full bg-cream-100 dark:bg-cream-900 relative">
                        @if ($p && $p->hasImagen())
                            <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}"
                                 class="w-full h-full object-cover grayscale" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-cream-400">
                                <x-icon name="image" class="w-8 h-8" />
                            </div>
                        @endif
                        <span class="absolute inset-0 flex items-center justify-center">
                            @if ($item->estado->value === 'registrado')
                                <x-icon name="check-circle" class="w-10 h-10 text-emerald-500 drop-shadow-lg" />
                            @else
                                <x-icon name="skip-forward" class="w-10 h-10 text-cream-500 drop-shadow-lg" />
                            @endif
                        </span>
                    </div>
                    <div class="p-2 flex flex-col gap-1">
                        <p class="text-xs font-semibold text-cream-800 dark:text-cream-200 line-clamp-2 leading-snug">
                            {{ $p?->nombre ?? '—' }}
                        </p>
                        <x-badge :variant="$item->estado->value === 'registrado' ? 'success' : 'neutral'" size="sm">
                            {{ $item->estado->label() }}
                        </x-badge>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
