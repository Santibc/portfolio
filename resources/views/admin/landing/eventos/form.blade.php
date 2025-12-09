<x-app-layout>
    <x-slot name="header">{{ $evento->exists ? 'Editar Evento' : 'Nuevo Evento' }}</x-slot>

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

        <form method="POST" action="{{ $evento->exists ? route('admin.landing.eventos.update', $evento->id) : route('admin.landing.eventos.store') }}" enctype="multipart/form-data">
            @csrf
            @if($evento->exists)
                @method('PUT')
            @endif

            {{-- Información Básica --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control" required
                                   value="{{ old('titulo', $evento->titulo) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control"
                                   value="{{ old('slug', $evento->slug) }}"
                                   placeholder="auto-generado">
                            <small class="text-muted">Se genera automáticamente del título</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Extracto</label>
                            <textarea name="extracto" class="form-control" rows="2">{{ old('extracto', $evento->extracto) }}</textarea>
                            <small class="text-muted">Resumen corto para las tarjetas</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Contenido Completo (HTML permitido)</label>
                            <textarea name="contenido" class="form-control" rows="6">{{ old('contenido', $evento->contenido) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Imagen --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-image"></i> Imagen Principal</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            @if($evento->imagen)
                                <div class="mb-2">
                                    <img src="{{ asset($evento->imagen) }}" style="max-height: 150px;" class="rounded">
                                </div>
                            @endif
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detalles del Evento --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar"></i> Detalles del Evento</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Todos estos campos son opcionales. Solo se mostrarán si tienen valor.</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="text" name="fecha" class="form-control"
                                   value="{{ old('fecha', $evento->fecha) }}"
                                   placeholder="Ej: 15-17 Marzo, 2025">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hora</label>
                            <input type="text" name="hora" class="form-control"
                                   value="{{ old('hora', $evento->hora) }}"
                                   placeholder="Ej: 9:00 AM - 8:00 PM">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="ubicacion" class="form-control"
                                   value="{{ old('ubicacion', $evento->ubicacion) }}"
                                   placeholder="Ej: Corferias, Bogotá">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="text" name="precio" class="form-control"
                                   value="{{ old('precio', $evento->precio) }}"
                                   placeholder="Ej: Desde $150.000 COP">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="form-select" required>
                                <option value="proximo" {{ old('estado', $evento->estado) == 'proximo' ? 'selected' : '' }}>Próximo</option>
                                <option value="activo" {{ old('estado', $evento->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="finalizado" {{ old('estado', $evento->estado) == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" class="form-control"
                                   value="{{ old('orden', $evento->orden ?? 0) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Incluye --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> ¿Qué incluye?</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Lista de elementos incluidos. Dejar vacío para no mostrar esta sección.</p>
                    <div id="incluye-container">
                        @php
                            $incluye = old('incluye', $evento->incluye ?? ['']);
                            if (empty($incluye)) $incluye = [''];
                        @endphp
                        @foreach($incluye as $index => $item)
                        <div class="incluye-item mb-2">
                            <div class="input-group">
                                <input type="text" name="incluye[]" class="form-control" value="{{ $item }}" placeholder="Ej: Stand de 3x3 metros">
                                <button type="button" class="btn btn-outline-danger eliminar-incluye" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="agregar-incluye" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="bi bi-plus"></i> Agregar Item
                    </button>
                </div>
            </div>

            {{-- Galería --}}
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-images"></i> Galería de Imágenes</h5>
                </div>
                <div class="card-body">
                    @if($evento->galeria && count($evento->galeria) > 0)
                        <div class="row mb-3">
                            @foreach($evento->galeria as $index => $img)
                            <div class="col-md-3 mb-3">
                                <div class="position-relative">
                                    <img src="{{ asset($img) }}" class="img-fluid rounded">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="eliminar_galeria[]" value="{{ $index }}" id="eliminar_{{ $index }}">
                                        <label class="form-check-label text-danger" for="eliminar_{{ $index }}">
                                            Eliminar
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                    <label class="form-label">Agregar nuevas imágenes</label>
                    <input type="file" name="galeria[]" class="form-control" accept="image/*" multiple>
                    <small class="text-muted">Puedes seleccionar múltiples imágenes</small>
                </div>
            </div>

            {{-- Estado --}}
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                               {{ old('activo', $evento->activo ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">
                            Evento activo (visible en el sitio)
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.landing.eventos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> {{ $evento->exists ? 'Actualizar' : 'Crear' }} Evento
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#agregar-incluye').click(function() {
                const newItem = `
                    <div class="incluye-item mb-2">
                        <div class="input-group">
                            <input type="text" name="incluye[]" class="form-control" placeholder="Ej: Stand de 3x3 metros">
                            <button type="button" class="btn btn-outline-danger eliminar-incluye" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#incluye-container').append(newItem);
            });

            $(document).on('click', '.eliminar-incluye', function() {
                if ($('.incluye-item').length > 1) {
                    $(this).closest('.incluye-item').remove();
                } else {
                    $(this).closest('.incluye-item').find('input').val('');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
