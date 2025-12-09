<x-app-layout>
    <x-slot name="header">{{ $valor->exists ? 'Editar Valor' : 'Nuevo Valor' }}</x-slot>

    <div class="container py-4">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-heart"></i> {{ $valor->exists ? 'Editar' : 'Crear' }} Valor</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ $valor->exists ? route('admin.landing.valores.update', $valor->id) : route('admin.landing.valores.store') }}">
                            @csrf
                            @if($valor->exists)
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Icono (clase Bootstrap Icons) <span class="text-danger">*</span></label>
                                <input type="text" name="icono" class="form-control" required
                                       value="{{ old('icono', $valor->icono) }}"
                                       placeholder="bi bi-people-fill">
                                <small class="text-muted">
                                    Usar clases de <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>
                                </small>
                                @if($valor->icono)
                                    <div class="mt-2">
                                        <span class="me-2">Vista previa:</span>
                                        <i class="{{ $valor->icono }} fs-3"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" class="form-control" required
                                       value="{{ old('titulo', $valor->titulo) }}"
                                       placeholder="Ej: Comunidad">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción <span class="text-danger">*</span></label>
                                <textarea name="descripcion" class="form-control" rows="3" required
                                          placeholder="Ej: Creemos en el poder de la colaboración...">{{ old('descripcion', $valor->descripcion) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" name="orden" class="form-control"
                                       value="{{ old('orden', $valor->orden ?? 0) }}">
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                                           {{ old('activo', $valor->activo ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        Activo (visible en el sitio)
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.landing.valores.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> {{ $valor->exists ? 'Actualizar' : 'Crear' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
