@extends('layouts.app')

@section('title', $impuesto->exists ? 'Editar impuesto' : 'Nuevo impuesto')

@section('content')
    <x-manzer.page-header
        :title="$impuesto->exists ? 'Editar impuesto' : 'Nuevo impuesto'"
        description="Completa los datos del impuesto o retención."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.impuestos.index') }}">
                Volver
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <div class="card">
        <form
            action="{{ $impuesto->exists ? route('admin.impuestos.update', $impuesto) : route('admin.impuestos.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($impuesto->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Nombre"
                    name="nombre"
                    type="text"
                    :value="old('nombre', $impuesto->nombre)"
                    :required="true"
                    icon="card-text"
                    placeholder="IVA 19%"
                />

                <x-manzer.form-group
                    label="Porcentaje"
                    name="porcentaje"
                    type="number"
                    :value="old('porcentaje', $impuesto->porcentaje)"
                    :required="true"
                    icon="percent"
                    placeholder="19.00"
                    step="0.01"
                    min="0"
                    max="100"
                />

                <x-manzer.form-group
                    label="Tipo"
                    name="tipo"
                    type="select"
                    :value="old('tipo', $impuesto->tipo ?? 'iva')"
                    :required="true"
                    icon="tags"
                    :options="['iva' => 'IVA', 'retencion' => 'Retención', 'otro' => 'Otro']"
                />

                <x-manzer.form-group
                    label="Código Siigo"
                    name="codigo_siigo"
                    type="text"
                    :value="old('codigo_siigo', $impuesto->codigo_siigo)"
                    icon="upc"
                    placeholder="Opcional"
                    help="Código asociado al tax en Siigo."
                />
            </div>

            <div>
                <x-manzer.form-group
                    label="Activo"
                    name="activo"
                    type="checkbox"
                    :value="old('activo', $impuesto->exists ? $impuesto->activo : true)"
                    placeholder="Disponible para su uso"
                />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.impuestos.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    {{ $impuesto->exists ? 'Guardar cambios' : 'Crear impuesto' }}
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
