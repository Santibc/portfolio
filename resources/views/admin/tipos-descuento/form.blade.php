@extends('layouts.app')

@section('title', $tipo->exists ? 'Editar tipo de descuento' : 'Nuevo tipo de descuento')

@section('content')
    <x-manzer.page-header
        :title="$tipo->exists ? 'Editar tipo de descuento' : 'Nuevo tipo de descuento'"
        description="Configura el alcance y la modalidad del descuento."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.tipos-descuento.index') }}">
                Volver
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <div class="card">
        <form
            action="{{ $tipo->exists ? route('admin.tipos-descuento.update', $tipo) : route('admin.tipos-descuento.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($tipo->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Nombre"
                    name="nombre"
                    type="text"
                    :value="old('nombre', $tipo->nombre)"
                    :required="true"
                    icon="card-text"
                    placeholder="Descuento pronto pago"
                />

                <x-manzer.form-group
                    label="Alcance"
                    name="alcance"
                    type="select"
                    :value="old('alcance', $tipo->alcance ?? 'linea')"
                    :required="true"
                    icon="arrows-angle-contract"
                    :options="['linea' => 'Por línea', 'global' => 'Global']"
                    help="¿Se aplica por ítem o sobre el total?"
                />

                <x-manzer.form-group
                    label="Modalidad"
                    name="modalidad"
                    type="select"
                    :value="old('modalidad', $tipo->modalidad ?? 'porcentaje')"
                    :required="true"
                    icon="percent"
                    :options="['porcentaje' => 'Porcentaje', 'valor_fijo' => 'Valor fijo']"
                />

                <x-manzer.form-group
                    label="Activo"
                    name="activo"
                    type="checkbox"
                    :value="old('activo', $tipo->exists ? $tipo->activo : true)"
                    placeholder="Disponible para su uso"
                />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.tipos-descuento.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    {{ $tipo->exists ? 'Guardar cambios' : 'Crear tipo' }}
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
