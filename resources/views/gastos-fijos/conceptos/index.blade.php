@extends('layouts.app')

@section('header', 'Conceptos de gastos fijos')

@section('content')
    <x-page-header
        title="Conceptos"
        subtitle="Catálogo de conceptos para los gastos fijos (arriendo, servicios...)"
        icon="settings"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('gastos-fijos.index')">
                Volver a gastos fijos
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1">
            <x-card>
                <x-slot:header>
                    <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                        <x-icon name="plus" class="w-4 h-4 text-primary-600 dark:text-primary-300" />
                        Nuevo concepto
                    </h2>
                </x-slot:header>

                <form action="{{ route('gastos-fijos.conceptos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <x-input
                        label="Nombre"
                        name="nombre"
                        :value="old('nombre')"
                        placeholder="Ej. Vigilancia"
                        required
                    />
                    <x-input
                        label="Orden"
                        name="orden"
                        type="number"
                        :value="old('orden', 0)"
                        min="0"
                        hint="Posición en las listas (menor primero)."
                    />
                    <x-button type="submit" variant="primary" icon="save" class="w-full">
                        Crear concepto
                    </x-button>
                </form>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card padding="!p-0" clip="true">
                <div class="px-6 py-4 border-b border-cream-200 dark:border-cream-800">
                    <h2 class="text-base font-semibold text-cream-900 dark:text-cream-50">
                        Conceptos registrados
                        <span class="text-sm font-normal text-cream-600 dark:text-cream-400 ms-2">({{ $conceptos->count() }})</span>
                    </h2>
                </div>

                @if ($conceptos->isEmpty())
                    <div class="px-6 py-12 text-center text-sm text-cream-500">
                        No hay conceptos registrados. Crea el primero con el formulario.
                    </div>
                @else
                    <ul class="divide-y divide-cream-200 dark:divide-cream-800">
                        @foreach ($conceptos as $concepto)
                            <li class="px-6 py-3 flex flex-wrap items-center gap-2">
                                <form
                                    action="{{ route('gastos-fijos.conceptos.update', $concepto) }}"
                                    method="POST"
                                    class="flex-1 min-w-[12rem] flex items-center gap-2"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input
                                        type="text"
                                        name="nombre"
                                        value="{{ $concepto->nombre }}"
                                        required
                                        maxlength="100"
                                        class="flex-1 rounded-xl border-cream-300 bg-white px-3 py-2 text-sm text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100 transition-all"
                                    />
                                    <input
                                        type="number"
                                        name="orden"
                                        value="{{ $concepto->orden }}"
                                        min="0"
                                        title="Orden"
                                        class="w-16 rounded-xl border-cream-300 bg-white px-2 py-2 text-sm text-center text-cream-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700 dark:text-cream-100 transition-all"
                                    />
                                    <label class="inline-flex items-center gap-1.5 text-xs text-cream-600 dark:text-cream-400 select-none">
                                        <input type="hidden" name="activo" value="0">
                                        <input type="checkbox" name="activo" value="1" @checked($concepto->activo)
                                               class="rounded border-cream-300 text-primary-600 focus:ring-primary-500/30 dark:bg-cream-900/40 dark:border-cream-700">
                                        Activo
                                    </label>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-primary-500 text-white hover:bg-primary-600 active:bg-primary-700 transition-colors shadow-soft"
                                        title="Guardar cambios"
                                    >
                                        <x-icon name="save" class="w-4 h-4" />
                                    </button>
                                </form>

                                <span class="inline-flex items-center font-semibold rounded-full bg-cream-200 text-cream-800 dark:bg-cream-800 dark:text-cream-200 text-xs px-2.5 py-1 min-w-[3rem] justify-center" title="Gastos asociados">
                                    {{ $concepto->gastos_fijos_count }}
                                </span>

                                <form
                                    action="{{ route('gastos-fijos.conceptos.destroy', $concepto) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar el concepto &quot;{{ $concepto->nombre }}&quot;? Solo funciona si no tiene gastos asociados.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 active:bg-red-200 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors"
                                        title="Eliminar concepto"
                                    >
                                        <x-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <p class="px-6 py-3 text-xs text-cream-600 dark:text-cream-400 border-t border-cream-200 dark:border-cream-800">
                    <x-icon name="info" class="w-3.5 h-3.5 inline" />
                    El número es la cantidad de gastos asociados. Un concepto no se puede eliminar si tiene gastos; desactívalo para ocultarlo del formulario.
                </p>
            </x-card>
        </div>
    </div>
@endsection
