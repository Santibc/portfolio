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

    @php $metodoDefault = old('metodo_pago_id', $registro->metodo_pago_id); @endphp

    <div class="max-w-md mx-auto"
         x-data="{ cantidad: {{ (float) $registro->cantidad }}, valor: {{ (int) $registro->valor }}, metodoPago: {{ $metodoDefault ? (int) $metodoDefault : 'null' }} }"
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
                        :value="old('cantidad', (float) $registro->cantidad)"
                        required
                        inputmode="decimal"
                        min="0.01"
                        step="any"
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

                    @if ($metodos->isNotEmpty())
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-cream-700 dark:text-cream-300">Método de pago</label>
                            <input type="hidden" name="metodo_pago_id" :value="metodoPago">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($metodos as $m)
                                    <button type="button" @click="metodoPago = {{ $m->id }}"
                                            :class="metodoPago == {{ $m->id }}
                                                ? 'bg-primary-500 border-primary-500 text-white shadow-sm'
                                                : 'bg-white border-cream-300 text-cream-700 hover:border-primary-400 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-200 dark:hover:border-primary-500'"
                                            class="px-4 py-2 rounded-xl border text-sm font-semibold transition-colors active:scale-95">
                                        {{ $m->nombre }}
                                    </button>
                                @endforeach
                            </div>
                            @error('metodo_pago_id')
                                <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <x-textarea
                        label="Observación (opcional)"
                        name="observacion"
                        :value="old('observacion', $registro->observacion)"
                        rows="2"
                        maxlength="500"
                        placeholder="Nota sobre esta compra (marca, calidad, proveedor, etc.)"
                    />
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
