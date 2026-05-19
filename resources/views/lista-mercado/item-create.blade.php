@extends('layouts.app')

@section('header', 'Registrar item')

@section('content')
    @php
        $p        = $item->producto;
        $backUrl  = route('lista-mercado.tipo', $item->tipo_producto_mercado_id);
        $sugerida = (int) $item->cantidad_sugerida;
    @endphp

    <x-page-header :title="$p?->nombre ?? '—'" subtitle="Cantidad sugerida pre-llenada · puedes ajustarla" icon="shopping-cart">
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="$backUrl">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-md mx-auto"
         x-data="{ cantidad: {{ (int) old('cantidad', $sugerida) }}, valor: 0 }"
         x-on:currency-changed="valor = $event.detail">

        <x-card padding="p-0" clip>
            @if ($p && $p->hasImagen())
                <button type="button"
                        onclick="previewProductoImagen('{{ $p->imagen_url }}', '{{ addslashes($p->nombre) }}')"
                        class="block w-full aspect-square bg-cream-100 dark:bg-cream-900">
                    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}"
                         class="w-full h-full object-cover">
                </button>
            @else
                <div class="w-full aspect-square bg-cream-100 dark:bg-cream-900 flex items-center justify-center text-cream-400 dark:text-cream-600">
                    <x-icon name="image" class="w-16 h-16" />
                </div>
            @endif

            <div class="p-5 space-y-2">
                <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50">{{ $p?->nombre ?? '—' }}</h2>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    @if ($item->tipo)
                        <x-badge>{{ $item->tipo->nombre }}</x-badge>
                    @endif
                    @if ($p)
                        <span class="text-cream-600 dark:text-cream-400">{{ $p->unidad_empaque }}</span>
                    @endif
                    <x-badge variant="accent" icon="list">
                        Sugerido: {{ $sugerida }}
                    </x-badge>
                </div>
            </div>
        </x-card>

        <form action="{{ route('lista-mercado.item.store', $item) }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <x-card>
                <div class="space-y-5">
                    <x-input
                        label="Cantidad ({{ $p?->unidad_empaque }})"
                        name="cantidad"
                        type="number"
                        :value="old('cantidad', $sugerida)"
                        placeholder="0"
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
                </div>
            </x-card>

            <div class="sticky bottom-0 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3
                        bg-cream-50/95 dark:bg-surface-dark/95 backdrop-blur
                        border-t border-cream-200 dark:border-cream-800
                        flex gap-2">
                <x-button type="submit" variant="primary" size="lg" icon="check"
                          class="flex-1 justify-center">
                    Registrar
                </x-button>
            </div>
        </form>

        <form action="{{ route('lista-mercado.item.saltar', $item) }}" method="POST" class="mt-3"
              onsubmit="event.preventDefault(); return swalConfirm(this, {title: '¿Saltar este producto?', text: 'Quedará como no comprado en este mercado.', icon: 'question', confirmButtonText: 'Sí, saltar', confirmButtonColor: '#75605a'});">
            @csrf
            <x-button type="submit" variant="ghost" size="md" icon="skip-forward"
                      class="w-full justify-center text-cream-700 dark:text-cream-300">
                No lo compré (saltar)
            </x-button>
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
