@extends('layouts.app')

@section('header', 'Menú por día')

@php
    // IDs (string) de todos los items activos — para el botón "Seleccionar todos".
    $allIds = $items->pluck('id')->map(fn ($i) => (string) $i)->values();
@endphp

@section('content')
<div x-data='{
        activeDay: {{ $dias->first()->id ?? 1 }},
        sel: @json($seleccion),
        count(d) { return (this.sel[d] || []).length; },
        toggleAll(d, ids) { this.sel[d] = (this.sel[d] || []).length === ids.length ? [] : [...ids]; },
     }'>

    <x-page-header
        title="Menú por día"
        subtitle="Configura qué items del menú salen en la caja cada día de la semana"
        icon="calendar"
    />

    <div class="mb-5">
        <x-alert variant="info" title="¿Cómo funciona?">
            Para cada día elige qué items se ofrecen. Si un día <strong>no tiene ningún item seleccionado</strong>,
            en la caja saldrán <strong>todos</strong> los items activos. La caja siempre usa el menú del
            <strong>día actual</strong>, sin importar cuándo se abrió el turno.
        </x-alert>
    </div>

    @if ($items->isEmpty())
        <x-empty-state
            icon="utensils-crossed"
            title="No hay items de menú activos"
            description="Crea items en el módulo Menú antes de configurar el menú por día."
        />
    @else
        {{-- Selector de día --}}
        <div class="flex flex-wrap items-center gap-2 mb-5">
            @foreach ($dias as $dia)
                <button type="button" @click="activeDay = {{ $dia->id }}"
                        :class="activeDay === {{ $dia->id }}
                            ? 'bg-primary-500 text-white border-primary-500 shadow-soft'
                            : 'bg-white text-cream-800 border-cream-300 hover:bg-cream-100 dark:bg-cream-900/40 dark:text-cream-200 dark:border-cream-700 dark:hover:bg-cream-800'"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-sm font-semibold border transition-all">
                    {{ $dia->nombre }}
                    <span class="inline-flex items-center justify-center min-w-[1.5rem] px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="activeDay === {{ $dia->id }}
                              ? 'bg-white/25 text-white'
                              : (count({{ $dia->id }}) > 0 ? 'bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200' : 'bg-cream-200 text-cream-600 dark:bg-cream-800 dark:text-cream-400')"
                          x-text="count({{ $dia->id }}) > 0 ? count({{ $dia->id }}) : 'Todos'"></span>
                </button>
            @endforeach
        </div>

        {{-- Un formulario por día (se muestra solo el día activo) --}}
        @foreach ($dias as $dia)
            <form method="POST" action="{{ route('menu-dia.update', $dia->id) }}"
                  x-show="activeDay === {{ $dia->id }}" x-cloak>
                @csrf
                @method('PUT')

                <x-card>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold text-cream-900 dark:text-cream-50">{{ $dia->nombre }}</h3>
                            <x-badge variant="primary">
                                <span x-text="count({{ $dia->id }}) > 0 ? count({{ $dia->id }}) + ' seleccionados' : 'Sin configurar → salen todos'"></span>
                            </x-badge>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="toggleAll({{ $dia->id }}, @js($allIds))"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100">
                                <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                <span x-text="count({{ $dia->id }}) === {{ $allIds->count() }} ? 'Quitar todos' : 'Seleccionar todos'"></span>
                            </button>
                            <button type="button" @click="sel[{{ $dia->id }}] = []"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-cream-600 hover:text-cream-900 dark:text-cream-400 dark:hover:text-cream-100">
                                <x-icon name="x" class="w-3.5 h-3.5" /> Limpiar
                            </button>
                        </div>
                    </div>

                    @foreach ($tipos as $tipo)
                        @php $itemsTipo = $items->where('tipo_id', $tipo->id); @endphp
                        @continue($itemsTipo->isEmpty())
                        <div class="mb-5">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-cream-500 mb-2">{{ $tipo->nombre }}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5">
                                @foreach ($itemsTipo as $item)
                                    @php $val = (string) $item->id; @endphp
                                    <label
                                        :class="(sel[{{ $dia->id }}] || []).includes('{{ $val }}')
                                            ? 'border-primary-500 ring-2 ring-primary-500/30 bg-primary-50 dark:bg-primary-900/30'
                                            : 'border-cream-200 dark:border-cream-800 bg-white dark:bg-cream-900/40 hover:border-primary-400'"
                                        class="relative cursor-pointer rounded-xl border p-2.5 flex items-center gap-2.5 transition-all">
                                        <input type="checkbox" name="items[]" value="{{ $val }}"
                                               x-model="sel[{{ $dia->id }}]" class="sr-only">

                                        <div class="shrink-0 w-11 h-11 rounded-lg bg-cream-100 dark:bg-cream-800 overflow-hidden flex items-center justify-center">
                                            @if ($item->hasImagen())
                                                <img src="{{ $item->imagen_url }}" alt="{{ $item->nombre }}" class="w-full h-full object-cover">
                                            @else
                                                <x-icon name="utensils-crossed" class="w-5 h-5 text-cream-400 dark:text-cream-600" />
                                            @endif
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-cream-900 dark:text-cream-50 truncate">{{ $item->nombre }}</p>
                                            <p class="text-xs text-primary-700 dark:text-primary-300 font-semibold">{{ $item->precio_formateado }}</p>
                                        </div>

                                        <span class="shrink-0 text-primary-600 dark:text-primary-300"
                                              x-show="(sel[{{ $dia->id }}] || []).includes('{{ $val }}')" x-cloak>
                                            <x-icon name="check-circle" class="w-5 h-5" />
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <x-slot:footer>
                        <div class="flex items-center justify-end">
                            <x-button type="submit" variant="primary" icon="save">
                                Guardar {{ $dia->nombre }}
                            </x-button>
                        </div>
                    </x-slot:footer>
                </x-card>
            </form>
        @endforeach
    @endif
</div>
@endsection
