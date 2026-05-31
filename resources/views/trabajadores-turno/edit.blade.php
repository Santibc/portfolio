@extends('layouts.app')

@section('header', 'Editar trabajador de turno')

@section('content')
    <x-page-header
        title="Editar trabajador"
        :subtitle="$trabajador->nombre"
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

    <form action="{{ route('trabajadores-turno.update', $trabajador) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PATCH')

        <x-card>
            <div class="space-y-5">
                <x-input
                    label="Nombre"
                    name="nombre"
                    :value="old('nombre', $trabajador->nombre)"
                    required
                />

                <x-input-currency
                    label="Valor de turno (por defecto)"
                    name="valor_turno_default"
                    :value="old('valor_turno_default', $trabajador->valor_turno_default)"
                    hint="Es solo una sugerencia: al registrar el gasto se puede cambiar."
                    required
                />

                <x-input-currency
                    label="Valor ahorro (por defecto)"
                    name="valor_ahorro_default"
                    :value="old('valor_ahorro_default', $trabajador->valor_ahorro_default)"
                    hint="Sugerencia del ahorro al pagar el turno. Puede cambiarse al registrar el gasto."
                    required
                />

                <input type="hidden" name="activo" value="0" />
                <x-toggle
                    name="activo"
                    label="Activo"
                    description="Si está apagado, no aparece en el formulario de gastos."
                    :checked="(bool) old('activo', $trabajador->activo)"
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
                        Guardar cambios
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
