@extends('layouts.app')

@section('title', $tipo->exists ? 'Editar tipo de pago' : 'Nuevo tipo de pago')

@section('content')
    <x-manzer.page-header
        :title="$tipo->exists ? 'Editar tipo de pago' : 'Nuevo tipo de pago'"
        description="Define el nombre, los días de crédito y el código Siigo."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.tipos-pago.index') }}">
                Volver
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    @if (session('error'))
        <div class="mb-4">
            <x-manzer.alert type="error" :message="session('error')" dismissible />
        </div>
    @endif

    <div class="card">
        <form
            action="{{ $tipo->exists ? route('admin.tipos-pago.update', $tipo) : route('admin.tipos-pago.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($tipo->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <x-manzer.form-group
                    label="Nombre"
                    name="nombre"
                    type="text"
                    :value="old('nombre', $tipo->nombre)"
                    :required="true"
                    icon="card-text"
                    placeholder="Contado"
                />

                <x-manzer.form-group
                    label="Días crédito"
                    name="dias_credito"
                    type="number"
                    :value="old('dias_credito', $tipo->exists ? $tipo->dias_credito : 0)"
                    :required="true"
                    icon="calendar-week"
                    placeholder="0"
                    min="0"
                    help="0 significa pago al contado."
                />

                <x-manzer.form-group
                    label="Código Siigo"
                    name="codigo_siigo"
                    type="text"
                    :value="old('codigo_siigo', $tipo->codigo_siigo)"
                    icon="upc"
                    placeholder="Opcional"
                />
            </div>

            <div>
                <x-manzer.form-group
                    label="Activo"
                    name="activo"
                    type="checkbox"
                    :value="old('activo', $tipo->exists ? $tipo->activo : true)"
                    placeholder="Disponible para su uso"
                />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.tipos-pago.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    {{ $tipo->exists ? 'Guardar cambios' : 'Crear tipo' }}
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
