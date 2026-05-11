@extends('layouts.app')

@section('header', 'Tipos de productos mercado')

@section('content')
    <x-page-header
        title="Tipos"
        subtitle="Configura los tipos de productos del mercado"
        icon="settings"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="arrow-left"
                :href="route('productos-mercado.index')"
            >
                Volver a productos
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1">
            <x-card>
                <x-slot:header>
                    <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                        <x-icon name="plus" class="w-4 h-4 text-primary-600 dark:text-primary-300" />
                        Nuevo tipo
                    </h2>
                </x-slot:header>

                <form action="{{ route('productos-mercado.tipos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <x-input
                        label="Nombre"
                        name="nombre"
                        :value="old('nombre')"
                        placeholder="Ej. Verduras"
                        required
                    />
                    <x-button type="submit" variant="primary" icon="save" class="w-full">
                        Crear tipo
                    </x-button>
                </form>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card padding="!p-0" clip="true">
                <div class="px-6 py-4 border-b border-cream-200 dark:border-cream-800">
                    <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50">
                        Tipos registrados
                        <span class="text-sm font-normal text-cream-600 dark:text-cream-400 ms-2">({{ $tipos->count() }})</span>
                    </h2>
                </div>

                @if ($tipos->isEmpty())
                    <div class="px-6 py-12 text-center text-sm text-cream-500">
                        No hay tipos registrados. Crea el primero con el formulario.
                    </div>
                @else
                    <ul class="divide-y divide-cream-200 dark:divide-cream-800">
                        @foreach ($tipos as $tipo)
                            <li class="px-6 py-3 flex items-center gap-3">
                                <form
                                    action="{{ route('productos-mercado.tipos.update', $tipo) }}"
                                    method="POST"
                                    class="flex-1 flex items-center gap-2"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input
                                        type="text"
                                        name="nombre"
                                        value="{{ $tipo->nombre }}"
                                        required
                                        maxlength="100"
                                        class="flex-1 rounded-xl border-cream-300 bg-white px-3 py-2 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100 transition-all"
                                    />
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 transition-colors shadow-soft"
                                        title="Guardar cambios"
                                    >
                                        <x-icon name="save" class="w-4 h-4" />
                                    </button>
                                </form>

                                <span class="inline-flex items-center font-semibold rounded-full bg-cream-200 text-cream-800 dark:bg-cream-800 dark:text-cream-200 text-xs px-2.5 py-1 min-w-[3rem] justify-center">
                                    {{ $tipo->productos_count }}
                                </span>

                                <form
                                    action="{{ route('productos-mercado.tipos.destroy', $tipo) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar el tipo &quot;{{ $tipo->nombre }}&quot;? Esto solo funciona si no tiene productos asociados.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 active:bg-red-200 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors"
                                        title="Eliminar tipo"
                                    >
                                        <x-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            <p class="mt-3 text-xs text-cream-600 dark:text-cream-400">
                <x-icon name="info" class="w-3.5 h-3.5 inline" />
                El número entre paréntesis es la cantidad de productos asociados. Un tipo no se puede eliminar si tiene productos.
            </p>
        </div>
    </div>
@endsection
