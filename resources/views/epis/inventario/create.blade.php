@extends('layouts.app')

@section('title', 'Nuevo EPI')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo EPI</h1>
            <p class="text-muted mb-0">Registra una nueva unidad de Equipo de Proteccion Individual</p>
        </div>
        <a href="{{ route('epi-inventario.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('epi-inventario.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Tipo de EPI -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Tipo de EPI</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Seleccionar tipo de EPI <span class="text-danger">*</span></label>
                                <select name="epi_catalogo_id" class="form-select @error('epi_catalogo_id') is-invalid @enderror" required id="epi_catalogo_id">
                                    <option value="">Seleccionar...</option>
                                    @php
                                        $currentCategoria = null;
                                    @endphp
                                    @foreach($catalogos as $catalogo)
                                        @if($catalogo->categoria !== $currentCategoria)
                                            @if($currentCategoria !== null)
                                                </optgroup>
                                            @endif
                                            <optgroup label="{{ $catalogo->categoria ?? 'Sin categoria' }}">
                                            @php $currentCategoria = $catalogo->categoria; @endphp
                                        @endif
                                        <option value="{{ $catalogo->id }}"
                                                data-tiene-caducidad="{{ $catalogo->tiene_caducidad ? '1' : '0' }}"
                                                {{ old('epi_catalogo_id') == $catalogo->id ? 'selected' : '' }}>
                                            {{ $catalogo->nombre }}
                                        </option>
                                    @endforeach
                                    @if($currentCategoria !== null)
                                        </optgroup>
                                    @endif
                                </select>
                                @error('epi_catalogo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Identificacion -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-upc me-2"></i>Identificacion</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Numero de Serie</label>
                                <input type="text" name="numero_serie" class="form-control @error('numero_serie') is-invalid @enderror"
                                       value="{{ old('numero_serie') }}" placeholder="Ej: SN-2025-001">
                                @error('numero_serie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opcional. Numero de serie del fabricante o codigo interno.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Fechas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Compra</label>
                                <input type="date" name="fecha_compra" class="form-control @error('fecha_compra') is-invalid @enderror"
                                       value="{{ old('fecha_compra') }}">
                                @error('fecha_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="fecha_caducidad_container">
                                <label class="form-label">Fecha de Caducidad</label>
                                <input type="date" name="fecha_caducidad" class="form-control @error('fecha_caducidad') is-invalid @enderror"
                                       value="{{ old('fecha_caducidad') }}">
                                @error('fecha_caducidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Fecha de caducidad indicada por el fabricante.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos Economicos -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Datos Economicos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Coste de adquisicion</label>
                                <div class="input-group">
                                    <input type="number" name="coste" step="0.01" min="0"
                                           class="form-control @error('coste') is-invalid @enderror"
                                           value="{{ old('coste') }}" placeholder="0.00">
                                    <span class="input-group-text">€</span>
                                </div>
                                @error('coste')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Acciones -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-check2-square me-2"></i>Acciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar EPI
                            </button>
                            <a href="{{ route('epi-inventario.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="notas" class="form-control" rows="4"
                                  placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectCatalogo = document.getElementById('epi_catalogo_id');
    const caducidadContainer = document.getElementById('fecha_caducidad_container');

    function toggleCaducidad() {
        const selectedOption = selectCatalogo.options[selectCatalogo.selectedIndex];
        const tieneCaducidad = selectedOption.getAttribute('data-tiene-caducidad') === '1';

        if (tieneCaducidad) {
            caducidadContainer.style.display = '';
        } else {
            caducidadContainer.style.display = 'none';
        }
    }

    selectCatalogo.addEventListener('change', toggleCaducidad);
    toggleCaducidad();
});
</script>
@endpush
@endsection
