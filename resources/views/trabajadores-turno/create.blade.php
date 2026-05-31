@extends('layouts.app')

@section('header', 'Nuevo trabajador de turno')

@section('content')
    <x-page-header
        title="Nuevo trabajador de turno"
        subtitle="Registra una persona que recibe pago por turno"
        icon="users"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="arrow-left"
                :href="route('trabajadores-turno.index')"
            >
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('trabajadores-turno.store') }}" method="POST" class="max-w-2xl">
        @csrf

        <x-card>
            <div class="space-y-5">
                <x-input
                    label="Nombre"
                    name="nombre"
                    :value="old('nombre')"
                    placeholder="Ej. Maritza"
                    required
                />

                <x-input-currency
                    label="Valor de turno (por defecto)"
                    name="valor_turno_default"
                    :value="old('valor_turno_default')"
                    hint="Es solo una sugerencia: al registrar el gasto se puede cambiar."
                    required
                />

                <x-input-currency
                    label="Valor ahorro (por defecto)"
                    name="valor_ahorro_default"
                    :value="old('valor_ahorro_default')"
                    hint="Sugerencia del ahorro al pagar el turno. Puede cambiarse al registrar el gasto."
                    required
                />

                <input type="hidden" name="activo" value="0" />
                <x-toggle
                    name="activo"
                    label="Activo"
                    description="Si está apagado, no aparece en el formulario de gastos."
                    :checked="old('activo', '1') == '1'"
                />
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <x-button
                        variant="ghost"
                        :href="route('trabajadores-turno.index')"
                    >
                        Cancelar
                    </x-button>
                    <x-button type="submit" variant="primary" icon="save">
                        Guardar
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
