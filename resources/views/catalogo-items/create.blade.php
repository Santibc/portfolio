@extends('layouts.app')

@section('title', 'Nuevo Item')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Nuevo Item" description="Registrar un nuevo item en el catalogo">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-arrow-left"
                href="{{ route('recepcion.items.index') }}">Volver</x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('recepcion.items.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Codigo"
                                    name="codigo"
                                    type="text"
                                    icon="bi bi-upc"
                                    :required="true"
                                    placeholder="Ej: CORTE-001" />
                            </div>
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Categoria"
                                    name="categoria"
                                    type="select"
                                    icon="bi bi-tag"
                                    :required="true"
                                    :options="['' => 'Seleccione...'] + $categorias" />
                            </div>
                        </div>

                        <x-sinden.form-group
                            label="Descripcion"
                            name="descripcion"
                            type="textarea"
                            icon="bi bi-card-text"
                            :required="true"
                            :rows="3"
                            placeholder="Descripcion del item o servicio" />

                        <div class="row">
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Precio Unitario (COP)"
                                    name="precio_unitario"
                                    type="number"
                                    icon="bi bi-currency-dollar"
                                    :required="true"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00" />
                            </div>
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Porcentaje IVA (%)"
                                    name="porcentaje_iva"
                                    type="number"
                                    icon="bi bi-percent"
                                    :required="true"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    value="19.00"
                                    placeholder="19.00" />
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <x-sinden.button variant="primary" type="submit" icon="bi bi-check-lg">
                                Guardar Item
                            </x-sinden.button>
                            <x-sinden.button variant="outline" href="{{ route('recepcion.items.index') }}">
                                Cancelar
                            </x-sinden.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Informacion</h6>
                    <p class="text-muted small mb-2">Los campos <strong>codigo</strong>, <strong>descripcion</strong>, <strong>precio</strong> y <strong>categoria</strong> son obligatorios.</p>
                    <p class="text-muted small mb-2">El <strong>codigo</strong> debe ser unico (Ej: CORTE-001, MAT-010).</p>
                    <p class="text-muted small mb-2">El <strong>precio unitario</strong> se ingresa sin IVA.</p>
                    <p class="text-muted small mb-0">El <strong>IVA</strong> por defecto es 19%. Modifiquelo si aplica una tasa diferente.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
