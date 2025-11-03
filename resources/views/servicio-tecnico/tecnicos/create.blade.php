<x-app-layout>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>
                        <i class="bi bi-person-gear me-2"></i>
                        {{ isset($tecnico) ? 'Editar Técnico' : 'Nuevo Técnico' }}
                    </h2>
                    <p class="text-muted">{{ isset($tecnico) ? 'Actualizar información del técnico' : 'Registrar nuevo técnico en el sistema' }}</p>
                </div>
                <a href="{{ route('st.tecnicos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Errores en el formulario</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ isset($tecnico) ? route('st.tecnicos.update', $tecnico->id) : route('st.tecnicos.store') }}" method="POST">
        @csrf
        @if(isset($tecnico))
            @method('PUT')
        @endif

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Información Personal</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('codigo') is-invalid @enderror"
                               id="codigo"
                               name="codigo"
                               value="{{ old('codigo', $tecnico->codigo ?? '') }}"
                               maxlength="20"
                               placeholder="Ej: TEC001"
                               required>
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Código único del técnico</small>
                    </div>

                    <div class="col-md-8">
                        <label for="nombre_completo" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('nombre_completo') is-invalid @enderror"
                               id="nombre_completo"
                               name="nombre_completo"
                               value="{{ old('nombre_completo', $tecnico->nombre_completo ?? '') }}"
                               required>
                        @error('nombre_completo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="documento" class="form-label">Documento de Identidad <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('documento') is-invalid @enderror"
                               id="documento"
                               name="documento"
                               value="{{ old('documento', $tecnico->documento ?? '') }}"
                               maxlength="50"
                               required>
                        @error('documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $tecnico->email ?? '') }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('telefono') is-invalid @enderror"
                               id="telefono"
                               name="telefono"
                               value="{{ old('telefono', $tecnico->telefono ?? '') }}"
                               maxlength="20"
                               required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="celular" class="form-label">Celular <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('celular') is-invalid @enderror"
                               id="celular"
                               name="celular"
                               value="{{ old('celular', $tecnico->celular ?? '') }}"
                               maxlength="20"
                               required>
                        @error('celular')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-tools me-2"></i>Información Profesional</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="especialidad" class="form-label">Especialidad</label>
                        <input type="text"
                               class="form-control @error('especialidad') is-invalid @enderror"
                               id="especialidad"
                               name="especialidad"
                               value="{{ old('especialidad', $tecnico->especialidad ?? '') }}"
                               placeholder="Ej: Cámaras de seguridad, CCTV, Control de acceso">
                        @error('especialidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                        <input type="date"
                               class="form-control @error('fecha_ingreso') is-invalid @enderror"
                               id="fecha_ingreso"
                               name="fecha_ingreso"
                               value="{{ old('fecha_ingreso', isset($tecnico) && $tecnico->fecha_ingreso ? $tecnico->fecha_ingreso->format('Y-m-d') : '') }}">
                        @error('fecha_ingreso')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="certificaciones" class="form-label">Certificaciones</label>
                        <textarea class="form-control @error('certificaciones') is-invalid @enderror"
                                  id="certificaciones"
                                  name="certificaciones"
                                  rows="3"
                                  placeholder="Listado de certificaciones y cursos realizados...">{{ old('certificaciones', $tecnico->certificaciones ?? '') }}</textarea>
                        @error('certificaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(isset($tecnico))
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="activo"
                                       name="activo"
                                       value="1"
                                       {{ old('activo', $tecnico->activo ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Técnico Activo
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('st.tecnicos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>{{ isset($tecnico) ? 'Actualizar' : 'Guardar' }} Técnico
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
</x-app-layout>
