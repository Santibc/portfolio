<x-app-layout>
    <x-slot name="header">{{ isset($cliente) ? 'Editar' : 'Nuevo' }} Cliente - Servicio Técnico</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('st.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('st.clientes.index') }}">Clientes</a></li>
                    <li class="breadcrumb-item active">{{ isset($cliente) ? 'Editar' : 'Nuevo' }}</li>
                </ol>
            </nav>

    <form action="{{ isset($cliente) ? route('st.clientes.update', $cliente) : route('st.clientes.store') }}" method="POST">
        @csrf
        @if(isset($cliente))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Información del Cliente</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                                <select name="tipo_cliente" id="tipoCliente" class="form-select" required>
                                    <option value="particular" {{ (isset($cliente) && $cliente->tipo_cliente == 'particular') ? 'selected' : 'selected' }}>Particular</option>
                                    <option value="empresa" {{ (isset($cliente) && $cliente->tipo_cliente == 'empresa') ? 'selected' : '' }}>Empresa</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-select" required>
                                    <option value="CC" {{ (isset($cliente) && $cliente->tipo_documento == 'CC') ? 'selected' : 'selected' }}>Cédula de Ciudadanía</option>
                                    <option value="NIT" {{ (isset($cliente) && $cliente->tipo_documento == 'NIT') ? 'selected' : '' }}>NIT</option>
                                    <option value="CE" {{ (isset($cliente) && $cliente->tipo_documento == 'CE') ? 'selected' : '' }}>Cédula de Extranjería</option>
                                    <option value="Pasaporte" {{ (isset($cliente) && $cliente->tipo_documento == 'Pasaporte') ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento" class="form-control" value="{{ $cliente->numero_documento ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre_completo" class="form-control" value="{{ $cliente->nombre_completo ?? '' }}" required>
                            </div>
                        </div>

                        <div class="row mb-3" id="razonSocialRow" style="display: none;">
                            <div class="col-md-12">
                                <label class="form-label">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control" value="{{ $cliente->razon_social ?? '' }}">
                                <small class="text-muted">Solo para empresas</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Celular <span class="text-danger">*</span></label>
                                <input type="text" name="celular" class="form-control" value="{{ $cliente->celular ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ $cliente->telefono ?? '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $cliente->email ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <textarea name="direccion" class="form-control" rows="2">{{ $cliente->direccion ?? '' }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $cliente->ciudad_texto ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Departamento</label>
                                <input type="text" name="departamento" class="form-control" value="{{ old('departamento', $cliente->departamento_texto ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3">{{ $cliente->observaciones ?? '' }}</textarea>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" {{ (isset($cliente) && $cliente->activo) || !isset($cliente) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Cliente Activo
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Ayuda</h6>
                    </div>
                    <div class="card-body">
                        <p class="small"><i class="bi bi-info-circle me-1"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>
                        <p class="small"><i class="bi bi-building me-1"></i> Si es empresa, ingrese la razón social.</p>
                        <p class="small"><i class="bi bi-phone me-1"></i> El celular es obligatorio para notificaciones.</p>
                        <p class="small"><i class="bi bi-envelope me-1"></i> El email es opcional pero recomendado.</p>
                    </div>
                </div>

                @if(isset($cliente))
                    <div class="card shadow mt-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Estadísticas</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Equipos Registrados:</strong> {{ $cliente->equipos()->count() }}</p>
                            <p class="mb-0"><strong>Órdenes de Servicio:</strong> {{ $cliente->ordenesServicio()->count() }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4 mb-4">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>{{ isset($cliente) ? 'Actualizar' : 'Guardar' }} Cliente
                </button>
                <a href="{{ route('st.clientes.index') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Mostrar/ocultar razón social según tipo de cliente
    function toggleRazonSocial() {
        const tipoCliente = $('#tipoCliente').val();
        if (tipoCliente === 'empresa') {
            $('#razonSocialRow').show();
        } else {
            $('#razonSocialRow').hide();
        }
    }

    toggleRazonSocial();
    $('#tipoCliente').on('change', toggleRazonSocial);
});
</script>
@endpush
        </div>
    </div>
</x-app-layout>
