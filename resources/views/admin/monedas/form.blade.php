@extends('layouts.app')

@section('title', $moneda->exists ? 'Editar moneda' : 'Nueva moneda')

@section('content')
    <x-manzer.page-header
        :title="$moneda->exists ? 'Editar moneda' : 'Nueva moneda'"
        description="Completa los datos de la moneda."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.monedas.index') }}">
                Volver
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <div class="card">
        <form
            action="{{ $moneda->exists ? route('admin.monedas.update', $moneda) : route('admin.monedas.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($moneda->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <x-manzer.form-group
                    label="Código"
                    name="codigo"
                    type="text"
                    :value="old('codigo', $moneda->codigo)"
                    :required="true"
                    icon="hash"
                    placeholder="COP"
                    help="Código ISO 4217 (3 letras)."
                    maxlength="3"
                    style="text-transform: uppercase"
                />

                <x-manzer.form-group
                    label="Nombre"
                    name="nombre"
                    type="text"
                    :value="old('nombre', $moneda->nombre)"
                    :required="true"
                    icon="card-text"
                    placeholder="Peso colombiano"
                />

                <x-manzer.form-group
                    label="Símbolo"
                    name="simbolo"
                    type="text"
                    :value="old('simbolo', $moneda->simbolo)"
                    :required="true"
                    icon="currency-dollar"
                    placeholder="$"
                />
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Predeterminada"
                    name="es_predeterminada"
                    type="checkbox"
                    :value="old('es_predeterminada', $moneda->es_predeterminada ?? false)"
                    placeholder="Es la moneda predeterminada del sistema"
                />

                <x-manzer.form-group
                    label="Activa"
                    name="activa"
                    type="checkbox"
                    :value="old('activa', $moneda->exists ? $moneda->activa : true)"
                    placeholder="Disponible para su uso"
                />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.monedas.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    {{ $moneda->exists ? 'Guardar cambios' : 'Crear moneda' }}
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
