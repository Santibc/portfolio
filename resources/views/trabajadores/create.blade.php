@extends('layouts.app')

@section('title', 'Nuevo Trabajador')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('trabajadores.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-1">Nuevo Trabajador</h1>
                <p class="text-muted mb-0">Registrar un nuevo trabajador en el sistema</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('trabajadores.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Tipo de relación -->
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Relación <span class="text-danger">*</span></label>
                        <select name="tipo_relacion" id="tipoRelacion" class="form-select @error('tipo_relacion') is-invalid @enderror" required onchange="toggleSubcontrata()">
                            <option value="propio" {{ old('tipo_relacion') === 'propio' ? 'selected' : '' }}>Propio</option>
                            <option value="subcontrata" {{ old('tipo_relacion') === 'subcontrata' ? 'selected' : '' }}>Subcontrata</option>
                        </select>
                        @error('tipo_relacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Subcontrata -->
                    <div class="col-md-6" id="subcontrataContainer" style="{{ old('tipo_relacion') === 'subcontrata' ? '' : 'display: none;' }}">
                        <label class="form-label">Subcontrata <span class="text-danger">*</span></label>
                        <select name="subcontrata_id" id="subcontrataId" class="form-select @error('subcontrata_id') is-invalid @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($subcontratas as $subcontrata)
                                <option value="{{ $subcontrata->id }}" {{ old('subcontrata_id') == $subcontrata->id ? 'selected' : '' }}>
                                    {{ $subcontrata->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('subcontrata_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Datos personales -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-muted fw-semibold">DATOS PERSONALES</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror"
                               value="{{ old('apellidos') }}" required>
                        @error('apellidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">DNI <span class="text-danger">*</span></label>
                        <input type="text" name="dni" class="form-control @error('dni') is-invalid @enderror"
                               value="{{ old('dni') }}" required placeholder="12345678A">
                        @error('dni')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono') }}" placeholder="600000000">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                               value="{{ old('fecha_nacimiento') }}">
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                               value="{{ old('direccion') }}">
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Datos laborales -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-muted fw-semibold">DATOS LABORALES</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fecha Alta <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_alta" class="form-control @error('fecha_alta') is-invalid @enderror"
                               value="{{ old('fecha_alta', date('Y-m-d')) }}" required>
                        @error('fecha_alta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Categoría Convenio</label>
                        <input type="text" name="categoria_convenio" class="form-control @error('categoria_convenio') is-invalid @enderror"
                               value="{{ old('categoria_convenio') }}" placeholder="Oficial 1ª, Peón...">
                        @error('categoria_convenio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Cuadrilla</label>
                        <select name="cuadrilla_id" class="form-select @error('cuadrilla_id') is-invalid @enderror">
                            <option value="">Sin asignar</option>
                            @foreach($cuadrillas as $cuadrilla)
                                <option value="{{ $cuadrilla->id }}" {{ old('cuadrilla_id') == $cuadrilla->id ? 'selected' : '' }}>
                                    {{ $cuadrilla->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('cuadrilla_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Datos económicos -->
                    <div class="col-12" id="datosEconomicosHeader" style="{{ old('tipo_relacion') === 'subcontrata' ? 'display: none;' : '' }}">
                        <hr class="my-2">
                        <h6 class="text-muted fw-semibold">DATOS ECONÓMICOS</h6>
                    </div>

                    <div class="col-md-3" id="salarioContainer" style="{{ old('tipo_relacion') === 'subcontrata' ? 'display: none;' : '' }}">
                        <label class="form-label">Salario Bruto Mensual</label>
                        <div class="input-group">
                            <input type="number" name="salario_bruto_mensual" class="form-control @error('salario_bruto_mensual') is-invalid @enderror"
                                   value="{{ old('salario_bruto_mensual') }}" step="0.01" min="0">
                            <span class="input-group-text">€</span>
                            @error('salario_bruto_mensual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3" id="costeDiaContainer" style="{{ old('tipo_relacion') === 'subcontrata' ? 'display: none;' : '' }}">
                        <label class="form-label">Coste Empresa/Día</label>
                        <div class="input-group">
                            <input type="number" name="coste_empresa_dia" class="form-control @error('coste_empresa_dia') is-invalid @enderror"
                                   value="{{ old('coste_empresa_dia') }}" step="0.01" min="0">
                            <span class="input-group-text">€</span>
                            @error('coste_empresa_dia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3" id="costeHoraContainer" style="{{ old('tipo_relacion') === 'subcontrata' ? 'display: none;' : '' }}">
                        <label class="form-label">Coste/Hora</label>
                        <div class="input-group">
                            <input type="number" name="coste_hora" class="form-control @error('coste_hora') is-invalid @enderror"
                                   value="{{ old('coste_hora') }}" step="0.01" min="0">
                            <span class="input-group-text">€</span>
                            @error('coste_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3" id="vacacionesContainer" style="{{ old('tipo_relacion') === 'subcontrata' ? 'display: none;' : '' }}">
                        <label class="form-label">Vacaciones Anuales</label>
                        <div class="input-group">
                            <input type="number" name="vacaciones_anuales" class="form-control @error('vacaciones_anuales') is-invalid @enderror"
                                   value="{{ old('vacaciones_anuales', 22) }}" min="0">
                            <span class="input-group-text">días</span>
                            @error('vacaciones_anuales')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('trabajadores.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear Trabajador</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSubcontrata() {
    const tipoRelacion = document.getElementById('tipoRelacion').value;
    const subcontrataContainer = document.getElementById('subcontrataContainer');
    const datosEconomicosHeader = document.getElementById('datosEconomicosHeader');
    const salarioContainer = document.getElementById('salarioContainer');
    const costeDiaContainer = document.getElementById('costeDiaContainer');
    const costeHoraContainer = document.getElementById('costeHoraContainer');
    const vacacionesContainer = document.getElementById('vacacionesContainer');

    if (tipoRelacion === 'subcontrata') {
        subcontrataContainer.style.display = 'block';
        datosEconomicosHeader.style.display = 'none';
        salarioContainer.style.display = 'none';
        costeDiaContainer.style.display = 'none';
        costeHoraContainer.style.display = 'none';
        vacacionesContainer.style.display = 'none';
    } else {
        subcontrataContainer.style.display = 'none';
        datosEconomicosHeader.style.display = 'block';
        salarioContainer.style.display = 'block';
        costeDiaContainer.style.display = 'block';
        costeHoraContainer.style.display = 'block';
        vacacionesContainer.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleSubcontrata();
});
</script>
@endpush
@endsection
