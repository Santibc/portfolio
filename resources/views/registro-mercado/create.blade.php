@extends('layouts.app')

@section('header', 'Registrar')

@section('content')
    @php $backUrl = route('registro-mercado.index', $tipoId ? ['tipo_id' => $tipoId] : []); @endphp

    <x-page-header :title="$producto->nombre" subtitle="Registrar cantidad y valor" icon="shopping-cart">
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="$backUrl">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    @php $metodoDefault = old('metodo_pago_id', $metodos->first()?->id); @endphp

    <div class="max-w-md mx-auto"
         x-data="{ cantidad: 0, valor: 0, metodoPago: {{ $metodoDefault ? (int) $metodoDefault : 'null' }} }"
         x-on:currency-changed="valor = $event.detail">

        <x-card padding="p-0" clip>
            @if ($producto->hasImagen())
                <button type="button"
                        onclick="previewProductoImagen('{{ $producto->imagen_url }}', '{{ addslashes($producto->nombre) }}')"
                        class="block w-full aspect-square bg-cream-100 dark:bg-cream-900">
                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}"
                         class="w-full h-full object-cover">
                </button>
            @else
                <div class="w-full aspect-square bg-cream-100 dark:bg-cream-900 flex items-center justify-center text-cream-400 dark:text-cream-600">
                    <x-icon name="image" class="w-16 h-16" />
                </div>
            @endif

            <div class="p-5 space-y-2">
                <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50">{{ $producto->nombre }}</h2>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    @if ($producto->tipo)
                        <x-badge>{{ $producto->tipo->nombre }}</x-badge>
                    @endif
                    <span class="text-cream-600 dark:text-cream-400">{{ $producto->unidad_empaque }}</span>
                </div>
            </div>
        </x-card>

        <form action="{{ route('registro-mercado.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="producto_mercado_id" value="{{ $producto->id }}">
            @if ($tipoId)
                <input type="hidden" name="tipo_id" value="{{ $tipoId }}">
            @endif

            <x-card>
                <div class="space-y-5">
                    <x-input
                        label="Cantidad ({{ $producto->unidad_empaque }})"
                        name="cantidad"
                        type="number"
                        :value="old('cantidad')"
                        placeholder="0"
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
                        :value="old('valor')"
                        placeholder="0"
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

                    <div class="pt-1 border-t border-cream-200 dark:border-cream-800">
                        @if ($turnoActivo)
                            <x-checkbox
                                name="vincular_caja"
                                value="1"
                                :checked="(bool) old('vincular_caja', false)"
                                label="Vincular con la caja abierta"
                                description="Este gasto se descontará del turno de caja abierto y aparecerá en su dashboard."
                            />
                            @error('vincular_caja')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        @else
                            <div class="flex items-start gap-2 rounded-lg bg-cream-100 dark:bg-cream-900/40 px-3 py-2 text-xs text-cream-600 dark:text-cream-400">
                                <x-icon name="info" class="w-4 h-4 shrink-0 mt-0.5" />
                                <span>No hay una caja abierta, así que este registro no se vinculará a ningún turno.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>

            <div class="sticky bottom-0 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3
                        bg-cream-50/95 dark:bg-surface-dark/95 backdrop-blur
                        border-t border-cream-200 dark:border-cream-800">
                <x-button type="submit" variant="primary" size="lg" icon="check"
                          class="w-full justify-center">
                    Registrar
                </x-button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    window.previewProductoImagen = window.previewProductoImagen || function (url, nombre) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: nombre,
            imageUrl: url,
            imageAlt: nombre,
            showConfirmButton: false,
            showCloseButton: true,
            background: isDark ? '#1a1610' : '#fffdfa',
            color: isDark ? '#fbf5e9' : '#3e2723',
            customClass: {
                popup: 'rounded-2xl',
                image: 'rounded-xl max-h-[70vh] w-auto',
            },
        });
    };
</script>
@endpush
