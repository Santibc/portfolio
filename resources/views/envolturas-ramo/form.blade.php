<x-app-layout>
    <x-slot name="header">
        {{ $envoltura->exists ? 'Editar Envoltura' : 'Nueva Envoltura' }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Errores de validación --}}
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-xl font-semibold mb-0">
                            <i class="bi bi-box text-success"></i>
                            {{ $envoltura->exists ? 'Editar: ' . $envoltura->nombre : 'Agregar Nueva Envoltura' }}
                        </h4>
                        <a href="{{ route('envolturas-ramo.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('envolturas-ramo.guardar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($envoltura->exists)
                            <input type="hidden" name="envoltura_id" value="{{ $envoltura->id }}">
                        @endif

                        <div class="row">
                            {{-- Columna izquierda: Datos básicos --}}
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informacion Basica</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre de la Envoltura *</label>
                                            <input type="text"
                                                   class="form-control @error('nombre') is-invalid @enderror"
                                                   id="nombre"
                                                   name="nombre"
                                                   value="{{ old('nombre', $envoltura->nombre) }}"
                                                   placeholder="Ej: Papel Kraft, Celofan, Tela Premium"
                                                   required>
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripcion</label>
                                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                                      id="descripcion"
                                                      name="descripcion"
                                                      rows="3"
                                                      placeholder="Descripcion de la envoltura...">{{ old('descripcion', $envoltura->descripcion) }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="precio_adicional" class="form-label">Precio Adicional *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">+$</span>
                                                    <input type="number"
                                                           class="form-control @error('precio_adicional') is-invalid @enderror"
                                                           id="precio_adicional"
                                                           name="precio_adicional"
                                                           value="{{ old('precio_adicional', $envoltura->precio_adicional) ?? 0 }}"
                                                           min="0"
                                                           step="100"
                                                           required>
                                                </div>
                                                <small class="text-muted">Colocar 0 si esta incluido en el precio base</small>
                                                @error('precio_adicional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="icono" class="form-label">Icono (Bootstrap Icons)</label>
                                                <input type="text"
                                                       class="form-control @error('icono') is-invalid @enderror"
                                                       id="icono"
                                                       name="icono"
                                                       value="{{ old('icono', $envoltura->icono) }}"
                                                       placeholder="Ej: bi-box, bi-gift, bi-bag">
                                                <small class="text-muted">
                                                    Ver iconos en <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Columna derecha: Imagen y opciones --}}
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-image"></i> Imagen</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div id="imagen-preview" class="mb-3">
                                            @if($envoltura->imagen)
                                                <img src="{{ asset($envoltura->imagen) }}"
                                                     class="img-fluid rounded"
                                                     style="max-height: 200px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                     style="height: 200px;">
                                                    <div class="text-center text-muted">
                                                        <i class="bi bi-box" style="font-size: 4rem;"></i>
                                                        <p class="mb-0 mt-2">Sin imagen</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <input type="file"
                                               class="form-control @error('imagen') is-invalid @enderror"
                                               id="imagen"
                                               name="imagen"
                                               accept="image/*">
                                        <small class="text-muted d-block mt-1">JPG, PNG, GIF, WebP. Max 2MB</small>
                                        @error('imagen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if($envoltura->imagen)
                                            <div class="form-check mt-3">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="eliminar_imagen"
                                                       name="eliminar_imagen"
                                                       value="1">
                                                <label class="form-check-label text-danger" for="eliminar_imagen">
                                                    <i class="bi bi-trash"></i> Eliminar imagen actual
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-gear"></i> Opciones</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="orden" class="form-label">Orden de Aparicion</label>
                                            <input type="number"
                                                   class="form-control @error('orden') is-invalid @enderror"
                                                   id="orden"
                                                   name="orden"
                                                   value="{{ old('orden', $envoltura->orden) ?? 0 }}"
                                                   min="0">
                                            <small class="text-muted">Menor numero = aparece primero</small>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   id="activo"
                                                   name="activo"
                                                   value="1"
                                                   {{ old('activo', $envoltura->activo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="activo">
                                                <strong>Envoltura Activa</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted">Si esta desactivada, no aparecera en "Arma tu Ramo"</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('envolturas-ramo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> {{ $envoltura->exists ? 'Actualizar' : 'Guardar' }} Envoltura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Preview de imagen
        const imagenInput = document.getElementById('imagen');
        const imagenPreview = document.getElementById('imagen-preview');

        imagenInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagenPreview.innerHTML = `
                        <img src="${e.target.result}"
                             class="img-fluid rounded"
                             style="max-height: 200px; object-fit: cover;">
                    `;
                }

                reader.readAsDataURL(this.files[0]);

                const eliminarCheckbox = document.getElementById('eliminar_imagen');
                if (eliminarCheckbox) {
                    eliminarCheckbox.checked = false;
                }
            }
        });

        const eliminarCheckbox = document.getElementById('eliminar_imagen');
        if (eliminarCheckbox) {
            eliminarCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    imagenPreview.innerHTML = `
                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                             style="height: 200px;">
                            <div class="text-center text-muted">
                                <i class="bi bi-box" style="font-size: 4rem;"></i>
                                <p class="mb-0 mt-2">Sin imagen</p>
                            </div>
                        </div>
                    `;
                }
            });
        }
    });
    </script>
    @endpush
</x-app-layout>
