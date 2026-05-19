@extends('layouts.app')

@section('header', 'Nuevo item del menú')

@section('content')
    <x-page-header
        title="Nuevo item"
        subtitle="Registra un plato, combo o adición"
        icon="utensils-crossed"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('menu-items.index')">
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('menu-items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <x-card>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Columna izquierda: imagen --}}
                <div class="lg:col-span-4">
                    <label for="imagen" class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">
                        Imagen
                    </label>
                    <input type="file" name="imagen" id="imagen" accept="image/*"
                           class="block w-full text-sm text-cream-700 dark:text-cream-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-800 hover:file:bg-primary-200 dark:file:bg-primary-900/40 dark:file:text-primary-200 dark:hover:file:bg-primary-900/60 cursor-pointer" />
                    @error('imagen')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $message }}
                        </p>
                    @else
                        <p class="mt-1.5 text-xs text-cream-600 dark:text-cream-400">JPG, PNG, WEBP o GIF. Máx 2 MB.</p>
                    @enderror
                </div>

                {{-- Columna derecha: campos --}}
                <div class="lg:col-span-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-input
                            label="Nombre"
                            name="nombre"
                            :value="old('nombre')"
                            placeholder="Ej. Ajiaco"
                            required
                        />

                        <x-input-currency
                            label="Precio (COP)"
                            name="precio"
                            :value="old('precio')"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-select
                            label="Categoría"
                            name="tipo_id"
                            :options="$tipos"
                            :value="old('tipo_id')"
                            placeholder="Selecciona una categoría..."
                            tomselect
                            required
                        />

                        <x-input
                            label="Orden de aparición"
                            name="orden"
                            type="number"
                            :value="old('orden', 0)"
                            hint="Menor número aparece primero en el grid del POS"
                        />
                    </div>

                    <input type="hidden" name="activo" value="0" />
                    <x-toggle
                        name="activo"
                        label="Activo"
                        description="Si está apagado, el item no aparece en el catálogo del POS"
                        :checked="old('activo', '1') == '1'"
                    />
                </div>
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <x-button variant="ghost" :href="route('menu-items.index')">Cancelar</x-button>
                    <x-button type="submit" variant="primary" icon="save">Guardar</x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
