{{--
    Editor de componentes de inventario de un item de menú.
    Espera: $productosInventario (Collection de ProductoMercado con controla_inventario) y
            $componentesIniciales (array de ['nombre', 'cantidad', 'productos' => [ids]]).
--}}
@php
    $componentesAlpine = collect($componentesIniciales)->values()->map(fn ($c, $i) => [
        'key'       => $i + 1,
        'nombre'    => (string) ($c['nombre'] ?? ''),
        'cantidad'  => (string) ($c['cantidad'] ?? '1'),
        'productos' => array_map('strval', (array) ($c['productos'] ?? [])),
    ])->all();

    $erroresComponentes = collect($errors->getMessages())
        ->filter(fn ($msgs, $key) => str_starts_with($key, 'componentes'))
        ->flatten()
        ->unique()
        ->values();
@endphp

<div class="pt-5 border-t border-cream-200 dark:border-cream-800"
     x-data="{
         componentes: @js($componentesAlpine),
         siguiente: {{ count($componentesAlpine) + 1 }},
         agregar() {
             this.componentes.push({ key: this.siguiente++, nombre: '', cantidad: '1', productos: [] });
         },
         quitar(i) { this.componentes.splice(i, 1); },
     }">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
        <div>
            <h3 class="text-sm font-semibold text-cream-900 dark:text-cream-100 flex items-center gap-2">
                <x-icon name="package" class="w-4 h-4 text-primary-500 dark:text-primary-300" />
                Descuenta de inventario
            </h3>
            <p class="text-xs text-cream-600 dark:text-cream-400 mt-0.5">
                Qué productos de mercado se restan del inventario al vender este item. Si un componente tiene
                varios productos, el cajero elige cuál al vender (ej. "Sabor de Doritos": normales o picantes).
            </p>
        </div>
        @if ($productosInventario->isNotEmpty())
            <x-button type="button" variant="secondary" size="sm" icon="plus" @click="agregar()">
                Agregar componente
            </x-button>
        @endif
    </div>

    @if ($erroresComponentes->isNotEmpty())
        <x-alert variant="danger" class="mb-3">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($erroresComponentes as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    @if ($productosInventario->isEmpty())
        <x-alert variant="info">
            Aún no hay productos de mercado que controlen inventario. Actívalo en
            <a href="{{ route('productos-mercado.index') }}" class="font-semibold underline">Productos de mercado</a>
            para poder vincularlos aquí.
        </x-alert>
    @else
        <p x-show="componentes.length === 0" class="text-sm text-cream-600 dark:text-cream-400 italic">
            Este item no descuenta inventario (ej. un plato preparado). Agrega un componente si vende algo de mercado.
        </p>

        <div class="space-y-3">
            <template x-for="(comp, i) in componentes" :key="comp.key">
                <div class="rounded-xl border border-cream-200 dark:border-cream-700 bg-cream-50/60 dark:bg-cream-900/30 p-4 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-7">
                            <x-input label="Nombre del componente" placeholder="Ej. Sabor de Doritos"
                                     x-bind:name="`componentes[${i}][nombre]`" x-model="comp.nombre" maxlength="100" />
                        </div>
                        <div class="sm:col-span-3">
                            <x-input label="Cantidad por venta" type="number" step="0.01" min="0.01"
                                     x-bind:name="`componentes[${i}][cantidad]`" x-model="comp.cantidad" />
                        </div>
                        <div class="sm:col-span-2 flex sm:justify-end">
                            <x-button type="button" variant="ghost" size="sm" icon="trash-2" @click="quitar(i)"
                                      class="text-rose-600 dark:text-rose-400">
                                Quitar
                            </x-button>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-cream-800 dark:text-cream-200 mb-2">Productos</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($productosInventario as $producto)
                                <div class="rounded-lg border border-cream-200 dark:border-cream-700 bg-white dark:bg-cream-900/50 px-3 py-2">
                                    <x-checkbox
                                        :value="$producto->id"
                                        :label="$producto->nombre"
                                        :description="$producto->unidad_empaque"
                                        x-bind:name="`componentes[${i}][productos][]`"
                                        x-model="comp.productos"
                                    />
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs"
                           :class="comp.productos.length > 1 ? 'text-primary-700 dark:text-primary-300' : 'text-cream-600 dark:text-cream-400'"
                           x-text="comp.productos.length === 0
                               ? 'Elige al menos un producto.'
                               : (comp.productos.length === 1
                                   ? 'Se descuenta siempre este producto.'
                                   : 'El cajero elegirá uno de estos ' + comp.productos.length + ' productos al vender.')"></p>
                    </div>
                </div>
            </template>
        </div>
    @endif
</div>
