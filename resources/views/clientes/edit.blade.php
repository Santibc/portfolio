@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Editar Cliente" description="Modificar informacion del cliente">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-arrow-left"
                href="{{ route('recepcion.clientes.index') }}">Volver</x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('recepcion.clientes.update', $cliente) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <x-sinden.form-group
                            label="Nombre"
                            name="nombre"
                            type="text"
                            icon="bi bi-person"
                            :required="true"
                            :value="$cliente->nombre"
                            placeholder="Nombre completo del cliente" />

                        <x-sinden.form-group
                            label="Cedula/NIT"
                            name="cedula"
                            type="text"
                            icon="bi bi-card-text"
                            :value="$cliente->cedula"
                            placeholder="Numero de cedula o NIT" />

                        <x-sinden.form-group
                            label="Correo Electronico"
                            name="correo"
                            type="email"
                            icon="bi bi-envelope"
                            :value="$cliente->correo"
                            placeholder="correo@ejemplo.com" />

                        <div class="row">
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Celular Principal (WhatsApp)"
                                    name="celular_1"
                                    type="text"
                                    icon="bi bi-phone"
                                    :value="$cliente->celular_1"
                                    placeholder="300 123 4567" />
                            </div>
                            <div class="col-md-6">
                                <x-sinden.form-group
                                    label="Celular Secundario"
                                    name="celular_2"
                                    type="text"
                                    icon="bi bi-phone"
                                    :value="$cliente->celular_2"
                                    placeholder="310 987 6543" />
                            </div>
                        </div>

                        <x-sinden.form-group
                            label="Direccion"
                            name="direccion"
                            type="textarea"
                            icon="bi bi-geo-alt"
                            :rows="3"
                            :value="$cliente->direccion"
                            placeholder="Direccion completa" />

                        <div class="d-flex gap-2 mt-4">
                            <x-sinden.button variant="primary" type="submit" icon="bi bi-check-lg">
                                Actualizar Cliente
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
                    <p class="text-muted small mb-2">
                        <strong>Estado:</strong>
                        @if($cliente->activo)
                            <span class="status-badge success">ACTIVO</span>
                        @else
                            <span class="status-badge danger">INACTIVO</span>
                        @endif
                    </p>
                    <p class="text-muted small mb-0">
                        <strong>Registrado:</strong> {{ $cliente->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
