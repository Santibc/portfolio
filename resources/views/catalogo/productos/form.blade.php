@extends('layouts.app')

@section('title', $producto->exists ? 'Editar ' . $producto->referencia : 'Nuevo producto')

@section('content')
    <div class="container-fluid py-4">
        <x-manzer.page-header
            :title="$producto->exists ? 'Editar ' . $producto->referencia : 'Nuevo producto'"
            description="Define referencia, precio base y características del producto."
        >
            <x-slot name="actions">
                <x-manzer.button
                    variant="secondary"
                    icon="arrow-left"
                    href="{{ route('catalogos.productos.index') }}"
                >
                    Volver
                </x-manzer.button>
            </x-slot>
        </x-manzer.page-header>

        @if (session('success'))
            <div class="mb-4">
                <x-manzer.alert type="success" :message="session('success')" dismissible />
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4">
                <x-manzer.alert type="error" :message="session('error')" dismissible />
            </div>
        @endif

        <form
            action="{{ $producto->exists ? route('catalogos.productos.update', $producto) : route('catalogos.productos.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf
            @if ($producto->exists)
                @method('PUT')
            @endif

            <div class="card p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-manzer.form-group
                        label="Referencia"
                        name="referencia"
                        icon="upc"
                        placeholder="REF-001"
                        required
                        :value="$producto->referencia"
                        :attributes="new \Illuminate\View\ComponentAttributeBag(array_filter([
                            'oninput' => !$producto->exists ? 'this.value=this.value.toUpperCase()' : null,
                            'readonly' => $producto->exists && $producto->referencia ? true : null,
                        ]))"
                    />

                    <x-manzer.form-group
                        label="Descripción"
                        name="descripcion"
                        icon="card-text"
                        placeholder="Descripción del producto"
                        required
                        :value="$producto->descripcion"
                    />

                    <x-manzer.form-group
                        label="Color"
                        name="color"
                        icon="tag"
                        placeholder="p.ej. Azul marino"
                        :value="$producto->color ?? ''"
                    />

                    <x-manzer.form-group
                        label="Composición"
                        name="composicion"
                        icon="card-text"
                        placeholder="p.ej. 100% algodón"
                        :value="$producto->composicion ?? ''"
                    />

                    <x-manzer.form-group
                        label="Código PA"
                        name="codigo_pa"
                        icon="hash"
                        placeholder="6104.44"
                        help="Partida arancelaria, p.ej. 6104.44"
                        :value="$producto->codigo_pa ?? ''"
                    />

                    <x-manzer.form-group
                        label="Unidad de medida"
                        name="unidad_medida"
                        icon="box-seam"
                        placeholder="Und"
                        :value="$producto->unidad_medida ?? 'Und'"
                    />

                    <x-manzer.form-group
                        label="Precio unitario"
                        name="precio_unitario"
                        type="number"
                        icon="cash-coin"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        required
                        :value="$producto->precio_unitario ?? ''"
                    />

                    <x-manzer.form-group
                        label="Moneda"
                        name="moneda_id"
                        type="select"
                        icon="currency-exchange"
                        required
                        :value="$producto->moneda_id ?? ''"
                        :options="$monedas->pluck('codigo', 'id')->toArray()"
                    />

                    <div>
                        <label for="impuesto_id" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <i class="bi bi-percent mr-1"></i>Impuesto (IVA)
                        </label>
                        <select id="impuesto_id" name="impuesto_id" class="input {{ $errors->has('impuesto_id') ? 'ring-red-500 focus:ring-red-500' : '' }}">
                            <option value="" @selected(old('impuesto_id', $producto->impuesto_id) === null || old('impuesto_id', $producto->impuesto_id) === '')>Sin impuesto</option>
                            @foreach ($impuestos as $impuesto)
                                <option value="{{ $impuesto->id }}" @selected((string) old('impuesto_id', $producto->impuesto_id) === (string) $impuesto->id)>
                                    {{ $impuesto->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('impuesto_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="imagen" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <i class="bi bi-image mr-1"></i>Imagen
                        </label>
                        <input
                            type="file"
                            id="imagen"
                            name="imagen"
                            accept="image/*"
                            class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:text-zinc-300 dark:file:bg-zinc-800 dark:file:text-zinc-200"
                        >
                        @error('imagen')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        @if ($producto->exists && $producto->imagen_url)
                            <div class="mt-3">
                                <p class="mb-1 text-xs font-medium text-zinc-600 dark:text-zinc-400">Imagen actual</p>
                                <img
                                    src="{{ $producto->imagen_url }}"
                                    alt="{{ $producto->referencia }}"
                                    class="h-24 w-24 rounded-lg border border-zinc-200 object-cover"
                                >
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 sm:flex-row sm:items-center sm:gap-8">
                            <label class="inline-flex items-center gap-2">
                                <input
                                    type="hidden"
                                    name="es_prenda"
                                    value="0"
                                >
                                <input
                                    type="checkbox"
                                    id="es_prenda"
                                    name="es_prenda"
                                    value="1"
                                    @checked(old('es_prenda', $producto->exists ? $producto->es_prenda : true))
                                    class="h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500"
                                >
                                <span class="text-sm">Es una prenda (se desglosa por tallas en la factura)</span>
                            </label>

                            <label class="inline-flex items-center gap-2">
                                <input
                                    type="hidden"
                                    name="activo"
                                    value="0"
                                >
                                <input
                                    type="checkbox"
                                    id="activo"
                                    name="activo"
                                    value="1"
                                    @checked(old('activo', $producto->exists ? $producto->activo : true))
                                    class="h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500"
                                >
                                <span class="text-sm">Activo</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a
                    href="{{ route('catalogos.productos.index') }}"
                    class="btn-secondary"
                >
                    Cancelar
                </a>
                <x-manzer.button
                    type="submit"
                    variant="primary"
                    icon="check-lg"
                >
                    Guardar producto
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
