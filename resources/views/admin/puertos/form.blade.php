@extends('layouts.app')

@section('title', $puerto->exists ? 'Editar puerto' : 'Nuevo puerto')

@section('content')
    <x-manzer.page-header
        :title="$puerto->exists ? 'Editar puerto' : 'Nuevo puerto'"
        description="Nombre del puerto y país al que pertenece."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.puertos.index') }}">
                Volver
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <div class="card">
        <form
            action="{{ $puerto->exists ? route('admin.puertos.update', $puerto) : route('admin.puertos.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($puerto->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Nombre"
                    name="nombre"
                    type="text"
                    :value="old('nombre', $puerto->nombre)"
                    :required="true"
                    icon="geo-alt"
                    placeholder="Puerto de Buenaventura"
                />

                <x-manzer.form-group
                    label="País"
                    name="pais"
                    type="text"
                    :value="old('pais', $puerto->exists ? $puerto->pais : 'Colombia')"
                    :required="true"
                    icon="flag"
                    placeholder="Colombia"
                />
            </div>

            <div>
                <x-manzer.form-group
                    label="Activo"
                    name="activo"
                    type="checkbox"
                    :value="old('activo', $puerto->exists ? $puerto->activo : true)"
                    placeholder="Disponible para su uso"
                />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.puertos.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    {{ $puerto->exists ? 'Guardar cambios' : 'Crear puerto' }}
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
