@extends('layouts.app')

@section('header', 'Editar producto mercado')

@section('content')
    <x-page-header
        title="Editar producto"
        :subtitle="$producto->nombre"
        icon="shopping-basket"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="arrow-left"
                :href="route('productos-mercado.index')"
            >
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('productos-mercado.update', $producto) }}" method="POST" enctype="multipart/form-data" class="max-w-2xl">
        @csrf
        @method('PATCH')

        <x-card>
            <div class="space-y-5">
                <x-input
                    label="Nombre"
                    name="nombre"
                    :value="old('nombre', $producto->nombre)"
                    required
                />

                <x-input
                    label="Unidad de empaque"
                    name="unidad_empaque"
                    :value="old('unidad_empaque', $producto->unidad_empaque)"
                    placeholder="Ej. kg, unidad, caja x12"
                    hint="Cómo se vende o empaca este producto"
                    required
                />

                <x-select
                    label="Tipo"
                    name="tipo_id"
                    :options="$tipos"
                    :value="old('tipo_id', $producto->tipo_id)"
                    placeholder="Selecciona un tipo..."
                    tomselect
                    required
                />

                <div>
                    <label class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">
                        Imagen
                    </label>

                    @if ($producto->hasImagen())
                        <div class="mb-3 flex items-center gap-3">
                            <img
                                src="{{ $producto->imagen_url }}"
                                alt="{{ $producto->nombre }}"
                                class="w-20 h-20 rounded-xl object-cover border border-cream-200 dark:border-cream-700"
                            />
                            <span class="text-xs text-cream-600 dark:text-cream-400">Imagen actual</span>
                        </div>
                    @endif

                    <input
                        type="file"
                        name="imagen"
                        id="imagen"
                        accept="image/*"
                        class="block w-full text-sm text-cream-700 dark:text-cream-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-800 hover:file:bg-primary-200 dark:file:bg-primary-900/40 dark:file:text-primary-200 dark:hover:file:bg-primary-900/60 cursor-pointer"
                    />
                    @error('imagen')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $message }}
                        </p>
                    @else
                        <p class="mt-1.5 text-xs text-cream-600 dark:text-cream-400">
                            {{ $producto->hasImagen() ? 'Sube una imagen nueva para reemplazar la actual.' : 'JPG, PNG, WEBP o GIF. Máx 2 MB.' }}
                        </p>
                    @enderror
                </div>

                <input type="hidden" name="activo" value="0" />
                <x-toggle
                    name="activo"
                    label="Activo"
                    description="Si está apagado, el producto queda oculto sin borrarse"
                    :checked="(bool) old('activo', $producto->activo)"
                />

                <input type="hidden" name="controla_inventario" value="0" />
                <x-toggle
                    name="controla_inventario"
                    label="Controla inventario"
                    description="Las compras suman stock y las ventas de caja vinculadas lo descuentan (ej. gaseosas, Doritos). Registra las compras en la unidad en que se vende."
                    :checked="(bool) old('controla_inventario', $producto->controla_inventario)"
                />
                @error('controla_inventario')
                    <p class="-mt-2 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                        <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $message }}
                    </p>
                @enderror
                @if ($stock !== null)
                    <p class="-mt-2 pl-14 text-sm text-cream-700 dark:text-cream-300 flex flex-wrap items-center gap-2">
                        <x-icon name="package" class="w-4 h-4 text-primary-500 dark:text-primary-300" />
                        Stock actual:
                        <strong class="text-cream-900 dark:text-cream-50">{{ \App\Models\ProductoMercado::formatearCantidad($stock) }} {{ $producto->unidad_empaque }}</strong>
                        <a href="{{ route('inventario.show', $producto) }}" class="font-medium text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 underline underline-offset-2">
                            Ver kardex / ajustar
                        </a>
                    </p>
                @endif
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <x-button
                        variant="ghost"
                        :href="route('productos-mercado.index')"
                    >
                        Cancelar
                    </x-button>
                    <x-button type="submit" variant="primary" icon="save">
                        Guardar cambios
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
