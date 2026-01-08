@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.tamanos-ramo.index') }}">Tamaños de Ramo</a></li>
                <li class="breadcrumb-item active">{{ isset($tamanoRamo) ? 'Editar' : 'Crear' }}</li>
            </ol>
        </nav>
        <h2>{{ isset($tamanoRamo) ? 'Editar Tamaño de Ramo' : 'Nuevo Tamaño de Ramo' }}</h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ isset($tamanoRamo) ? route('admin.tamanos-ramo.update', $tamanoRamo) : route('admin.tamanos-ramo.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="formTamanoRamo">
                        @csrf
                        @if(isset($tamanoRamo))
                            @method('PUT')
                        @endif

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre del Tamaño <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre"
                                   name="nombre"
                                   value="{{ old('nombre', $tamanoRamo->nombre ?? '') }}"
                                   placeholder="Ej: Pequeño, Mediano, Grande"
                                   required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Este es el nombre que verán los clientes</small>
                        </div>

                        <!-- Cantidad de Flores -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cantidad_flores_min" class="form-label">
                                    Cantidad Mínima de Flores <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('cantidad_flores_min') is-invalid @enderror"
                                       id="cantidad_flores_min"
                                       name="cantidad_flores_min"
                                       value="{{ old('cantidad_flores_min', $tamanoRamo->cantidad_flores_min ?? 6) }}"
                                       min="1"
                                       required>
                                @error('cantidad_flores_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cantidad_flores_max" class="form-label">
                                    Cantidad Máxima de Flores <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('cantidad_flores_max') is-invalid @enderror"
                                       id="cantidad_flores_max"
                                       name="cantidad_flores_max"
                                       value="{{ old('cantidad_flores_max', $tamanoRamo->cantidad_flores_max ?? 12) }}"
                                       min="1"
                                       required>
                                @error('cantidad_flores_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Precio Base -->
                        <div class="mb-3">
                            <label for="precio_base" class="form-label">
                                Precio Base <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('precio_base') is-invalid @enderror"
                                       id="precio_base"
                                       name="precio_base"
                                       value="{{ old('precio_base', isset($tamanoRamo) ? number_format($tamanoRamo->precio_base, 2, '.', '') : 0) }}"
                                       min="0"
                                       step="0.01"
                                       required>
                                @error('precio_base')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Precio de la envoltura y preparación (sin incluir flores)</small>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                      id="descripcion"
                                      name="descripcion"
                                      rows="3"
                                      placeholder="Descripción opcional del tamaño...">{{ old('descripcion', $tamanoRamo->descripcion ?? '') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Máximo 500 caracteres</small>
                        </div>

                        <!-- Imagen -->
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen del Tamaño</label>
                            <input type="file"
                                   class="form-control @error('imagen') is-invalid @enderror"
                                   id="imagen"
                                   name="imagen"
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="previewImage(event)">
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">JPG, PNG o WEBP. Máximo 2MB. Recomendado: 800x600px</small>

                            @if(isset($tamanoRamo) && $tamanoRamo->imagen)
                                <div class="mt-3">
                                    <label class="form-label">Imagen Actual:</label>
                                    <div>
                                        <img src="{{ $tamanoRamo->imagen_url }}"
                                             alt="{{ $tamanoRamo->nombre }}"
                                             id="imagenActual"
                                             class="img-thumbnail"
                                             style="max-width: 300px;">
                                    </div>
                                </div>
                            @endif

                            <div id="previewContainer" class="mt-3" style="display: none;">
                                <label class="form-label">Vista Previa:</label>
                                <div>
                                    <img id="imagenPreview"
                                         class="img-thumbnail"
                                         style="max-width: 300px;">
                                </div>
                            </div>
                        </div>

                        <!-- Orden -->
                        <div class="mb-3">
                            <label for="orden" class="form-label">Orden de Visualización</label>
                            <input type="number"
                                   class="form-control @error('orden') is-invalid @enderror"
                                   id="orden"
                                   name="orden"
                                   value="{{ old('orden', $tamanoRamo->orden ?? 0) }}"
                                   min="0">
                            @error('orden')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Los tamaños se ordenarán de menor a mayor número</small>
                        </div>

                        <!-- Estado Activo -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="activo"
                                       name="activo"
                                       {{ old('activo', $tamanoRamo->activo ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    <strong>Tamaño Activo</strong>
                                    <br><small class="text-muted">Solo los tamaños activos aparecerán en "Arma tu Ramo"</small>
                                </label>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.tamanos-ramo.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ isset($tamanoRamo) ? 'Actualizar' : 'Crear' }} Tamaño
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel lateral con información -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle me-2"></i>Información
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">¿Qué es un tamaño de ramo?</h6>
                    <p class="small">Los tamaños de ramo son las opciones predefinidas que los clientes pueden elegir al crear su ramo personalizado en "Arma tu Ramo".</p>

                    <h6 class="fw-bold mt-3">Componentes del precio:</h6>
                    <ul class="small">
                        <li><strong>Precio Base:</strong> Costo de envoltura y preparación</li>
                        <li><strong>Precio Flores:</strong> Se suma según cantidad seleccionada</li>
                        <li><strong>Adicionales:</strong> Chocolates, peluches, etc.</li>
                    </ul>

                    <h6 class="fw-bold mt-3">Ejemplos comunes:</h6>
                    <table class="table table-sm small">
                        <thead>
                            <tr>
                                <th>Tamaño</th>
                                <th>Flores</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Pequeño</td>
                                <td>6 - 12</td>
                            </tr>
                            <tr>
                                <td>Mediano</td>
                                <td>12 - 24</td>
                            </tr>
                            <tr>
                                <td>Grande</td>
                                <td>24 - 50</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i>Consejo</h6>
                    <p class="small mb-0">
                        Configura rangos que no se solapen entre tamaños para evitar confusión.
                        Por ejemplo: 6-12, 13-24, 25-50 flores.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagenPreview').src = e.target.result;
            document.getElementById('previewContainer').style.display = 'block';

            // Ocultar imagen actual si existe
            const imagenActual = document.getElementById('imagenActual');
            if (imagenActual) {
                imagenActual.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
}

// Validación del formulario
document.getElementById('formTamanoRamo').addEventListener('submit', function(e) {
    const min = parseInt(document.getElementById('cantidad_flores_min').value);
    const max = parseInt(document.getElementById('cantidad_flores_max').value);

    if (max < min) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Error en las cantidades',
            text: 'La cantidad máxima debe ser mayor o igual a la cantidad mínima',
        });
        return false;
    }
});
</script>
@endpush
