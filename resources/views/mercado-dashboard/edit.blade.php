@extends('layouts.app')

@section('header', 'Editar registro')

@section('content')
    <x-page-header
        title="Editar registro"
        :subtitle="$registro->producto->nombre"
        icon="edit"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('mercado-dashboard.index')">
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-md mx-auto"
         x-data="{ cantidad: {{ (int) $registro->cantidad }}, valor: {{ (int) $registro->valor }} }"
         x-on:currency-changed="valor = $event.detail">

        <x-card padding="p-4">
            <div class="flex items-center gap-3">
                @if ($registro->producto->hasImagen())
                    <img src="{{ $registro->producto->imagen_url }}" alt="{{ $registro->producto->nombre }}"
                         class="w-16 h-16 rounded-xl object-cover border border-cream-200 dark:border-cream-700">
                @else
                    <div class="w-16 h-16 rounded-xl bg-cream-100 dark:bg-cream-900 flex items-center justify-center text-cream-400">
                        <x-icon name="image" class="w-8 h-8" />
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-bold text-cream-900 dark:text-cream-50 truncate">{{ $registro->producto->nombre }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if ($registro->producto->tipo)
                            <x-badge>{{ $registro->producto->tipo->nombre }}</x-badge>
                        @endif
                        <span class="text-xs text-cream-600 dark:text-cream-400">{{ $registro->producto->unidad_empaque }}</span>
                    </div>
                    <p class="text-[11px] text-cream-500 dark:text-cream-400 mt-1">
                        Registrado el {{ $registro->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        </x-card>

        <form action="{{ route('mercado-dashboard.update', $registro) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PATCH')

            <x-card>
                <div class="space-y-5">
                    <x-input
                        label="Cantidad ({{ $registro->producto->unidad_empaque }})"
                        name="cantidad"
                        type="number"
                        :value="old('cantidad', $registro->cantidad)"
                        required
                        inputmode="numeric"
                        min="1"
                        step="1"
                        autofocus
                        x-model.number="cantidad"
                        class="text-2xl py-4 font-semibold text-center"
                    />

                    <x-input-currency
                        label="Valor total (COP)"
                        name="valor"
                        :value="old('valor', $registro->valor)"
                        required
                        class="!text-2xl !py-4 !font-semibold !pl-10 text-center"
                    />

                    <p class="text-sm text-cream-600 dark:text-cream-400 text-center"
                       x-show="cantidad > 0 && valor > 0" x-cloak>
                        Unitario:
                        <span class="font-semibold text-cream-900 dark:text-cream-50"
                              x-text="'$ ' + (Math.round(valor / cantidad)).toLocaleString('es-CO')"></span>
                    </p>
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-2">
                <x-button variant="ghost" :href="route('mercado-dashboard.index')">
                    Cancelar
                </x-button>
                <x-button type="submit" variant="primary" icon="save">
                    Guardar cambios
                </x-button>
            </div>
        </form>
    </div>
@endsection
