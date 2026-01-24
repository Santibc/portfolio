@extends('layouts.app')

@section('title', 'Nuevo Tipo de EPI')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Tipo de EPI</h1>
            <p class="text-muted mb-0">Registra un nuevo tipo de Equipo de Proteccion Individual</p>
        </div>
        <a href="{{ route('epi-catalogo.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <form action="{{ route('epi-catalogo.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Datos Basicos -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Datos del EPI</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nombre del EPI <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre') }}" placeholder="Ej: Casco de seguridad, Arnes anticaidas..." required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Categoria</label>
                                <input type="text" name="categoria" class="form-control @error('categoria') is-invalid @enderror"
                                       value="{{ old('categoria') }}" list="categorias-list"
                                       placeholder="Ej: Proteccion de la cabeza, Proteccion contra caidas...">
                                <datalist id="categorias-list">
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat }}">
                                    @endforeach
                                </datalist>
                                @error('categoria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Puedes seleccionar una categoria existente o escribir una nueva</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuracion -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Configuracion</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="tiene_caducidad" value="0">
                                    <input class="form-check-input" type="checkbox" name="tiene_caducidad" value="1"
                                           id="tiene_caducidad" {{ old('tiene_caducidad') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tiene_caducidad">
                                        <strong>Tiene fecha de caducidad</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Marcar si este tipo de EPI tiene fecha de caducidad (ej: cascos, arneses)
                                </small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="requiere_revision" value="0">
                                    <input class="form-check-input" type="checkbox" name="requiere_revision" value="1"
                                           id="requiere_revision" {{ old('requiere_revision') ? 'checked' : '' }}
                                           onchange="togglePeriodicidad()">
                                    <label class="form-check-label" for="requiere_revision">
                                        <strong>Requiere revision periodica</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Marcar si este tipo de EPI necesita revisiones periodicas
                                </small>
                            </div>

                            <div class="col-md-6" id="periodicidad-container" style="{{ old('requiere_revision') ? '' : 'display: none;' }}">
                                <label class="form-label">Periodicidad de revision (meses) <span class="text-danger">*</span></label>
                                <input type="number" name="periodicidad_revision_meses"
                                       class="form-control @error('periodicidad_revision_meses') is-invalid @enderror"
                                       value="{{ old('periodicidad_revision_meses', 12) }}" min="1" max="60"
                                       placeholder="Ej: 12">
                                @error('periodicidad_revision_meses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Cada cuantos meses debe revisarse este tipo de EPI</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Acciones -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-check2-square me-2"></i>Acciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Guardar Tipo de EPI
                            </button>
                            <a href="{{ route('epi-catalogo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ayuda -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ayuda</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">
                            <strong>Fecha de caducidad:</strong> Algunos EPIs como cascos o arneses tienen fecha de caducidad indicada por el fabricante.
                        </p>
                        <p class="small text-muted mb-0">
                            <strong>Revision periodica:</strong> Algunos EPIs requieren inspecciones periodicas para verificar su estado (ej: equipos anticaidas cada 12 meses).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePeriodicidad() {
    const checkbox = document.getElementById('requiere_revision');
    const container = document.getElementById('periodicidad-container');

    if (checkbox.checked) {
        container.style.display = '';
    } else {
        container.style.display = 'none';
    }
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
    togglePeriodicidad();
});
</script>
@endpush
@endsection
