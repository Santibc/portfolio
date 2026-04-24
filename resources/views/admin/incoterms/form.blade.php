@extends('layouts.app')

@section('title', $incoterm->exists ? 'Editar incoterm' : 'Nuevo incoterm')

@section('content')
    <x-manzer.page-header
        :title="$incoterm->exists ? 'Editar incoterm' : 'Nuevo incoterm'"
        description="Código y descripción del término de comercio."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.incoterms.index') }}">
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
            action="{{ $incoterm->exists ? route('admin.incoterms.update', $incoterm) : route('admin.incoterms.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @if ($incoterm->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <x-manzer.form-group
                    label="Código"
                    name="codigo"
                    type="text"
                    :value="old('codigo', $incoterm->codigo)"
                    :required="true"
                    icon="hash"
                    placeholder="FOB"
                    help="Máximo 4 caracteres."
                    maxlength="4"
                    style="text-transform: uppercase"
                />

                <div class="md:col-span-2">
                    <x-manzer.form-group
                        label="Descripción"
                        name="descripcion"
                        type="text"
                        :value="old('descripcion', $incoterm->descripcion)"
                        :required="true"
                        icon="card-text"
                        placeholder="Free On Board — entregado a bordo del buque"
                        maxlength="180"
                    />
                </div>
            </div>

            <div>
                <x-manzer.form-group
                    label="Activo"
                    name="activo"
                    type="checkbox"
                    :value="old('activo', $incoterm->exists ? $incoterm->activo : true)"
                    placeholder="Disponible para su uso"
                />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.incoterms.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    {{ $incoterm->exists ? 'Guardar cambios' : 'Crear incoterm' }}
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
