@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Nuevo Cliente" description="Registrar un nuevo cliente en el sistema">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-arrow-left"
                href="{{ route('recepcion.clientes.index') }}">Volver</x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('recepcion.clientes.store') }}" method="POST">
                        @csrf

                        <x-sinden.form-group
                            label="Nombre"
                            name="nombre"
                            type="text"
                            icon="bi bi-person"
                            :required="true"
                            placeholder="Nombre completo del cliente" />

                        <x-sinden.form-group
                            label="Cedula/NIT"
                            name="cedula"
                            type="text"
                            icon="bi bi-card-text"
                            placeholder="Numero de cedula o NIT" />

                        <x-sinden.form-group
                            label="Correo Electronico"
                            name="correo"
                            type="email"
                            icon="bi bi-envelope"
                            placeholder="correo@ejemplo.com" />

                        <div class="row">
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Celular Principal (WhatsApp)"
                                    name="celular_1"
                                    type="text"
                                    icon="bi bi-phone"
                                    placeholder="300 123 4567" />
                            </div>
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Celular Secundario"
                                    name="celular_2"
                                    type="text"
                                    icon="bi bi-phone"
                                    placeholder="310 987 6543" />
                            </div>
                        </div>

                        <x-sinden.form-group
                            label="Direccion"
                            name="direccion"
                            type="textarea"
                            icon="bi bi-geo-alt"
                            :rows="3"
                            placeholder="Direccion completa" />

                        <div class="d-flex gap-2 mt-4">
                            <x-sinden.button variant="primary" type="submit" icon="bi bi-check-lg">
                                Guardar Cliente
                            </x-sinden.button>
                            <x-sinden.button variant="outline" href="{{ route('recepcion.clientes.index') }}">
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
                    <p class="text-muted small mb-2">Solo el <strong>nombre</strong> es obligatorio.</p>
                    <p class="text-muted small mb-2">El correo se usara para enviar notificaciones futuras.</p>
                    <p class="text-muted small mb-0">Los celulares se usan para contacto y busqueda rapida de clientes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
