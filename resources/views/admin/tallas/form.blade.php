@extends('layouts.app')

@section('title', $talla->exists ? 'Editar talla' : 'Nueva talla')

@section('content')
    <x-manzer.page-header
        :title="$talla->exists ? 'Editar talla' : 'Nueva talla'"
        description="Nombre de la talla y orden en que aparece en las líneas de factura."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.tallas.index') }}">
                Volver
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <div class="card">
        <form
            action="{{ $talla->exists ? route('admin.tallas.update', $talla) : route('admin.tallas.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($talla->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Nombre"
                    name="nombre"
                    type="text"
                    :value="old('nombre', $talla->nombre)"
                    :required="true"
                    icon="rulers"
                    placeholder="M"
                    help="Se muestra como columna en las líneas de factura (ej: XS, S, M, L)."
                />

                <x-manzer.form-group
                    label="Orden"
                    name="orden"
                    type="number"
                    :value="old('orden', $talla->exists ? $talla->orden : 0)"
                    icon="sort-numeric-down"
                    placeholder="0"
                    min="0"
                    help="Define el orden de las columnas de talla."
                />
            </div>

            <div>
                <x-manzer.form-group
                    label="Activa"
                    name="activo"
                    type="checkbox"
                    :value="old('activo', $talla->exists ? $talla->activo : true)"
                    placeholder="Disponible para su uso"
                />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.tallas.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    {{ $talla->exists ? 'Guardar cambios' : 'Crear talla' }}
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
