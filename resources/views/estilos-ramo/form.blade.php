<x-app-layout>
    <x-slot name="header">
        {{ $estilo->exists ? 'Editar Estilo' : 'Nuevo Estilo' }}
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
                            <i class="bi bi-palette text-primary"></i>
                            {{ $estilo->exists ? 'Editar: ' . $estilo->nombre : 'Agregar Nuevo Estilo de Ramo' }}
                        </h4>
                        <a href="{{ route('estilos-ramo.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('estilos-ramo.guardar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($estilo->exists)
                            <input type="hidden" name="estilo_id" value="{{ $estilo->id }}">
                        @endif

                        <div class="row">
                            {{-- Columna izquierda: Datos básicos --}}
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informacion Basica</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8 mb-3">
                                                <label for="nombre" class="form-label">Nombre del Estilo *</label>
                                                <input type="text"
                                                       class="form-control @error('nombre') is-invalid @enderror"
                                                       id="nombre"
                                                       name="nombre"
                                                       value="{{ old('nombre', $estilo->nombre) }}"
                                                       placeholder="Ej: Romantico, Elegante, Silvestre"
                                                       required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="color_principal" class="form-label">Color Principal</label>
                                                <div class="input-group">
                                                    <input type="color"
                                                           class="form-control form-control-color"
                                                           id="color_picker"
                                                           value="{{ old('color_principal', $estilo->color_principal) ?? '#e91e63' }}"
                                                           style="width: 50px;">
                                                    <input type="text"
                                                           class="form-control @error('color_principal') is-invalid @enderror"
                                                           id="color_principal"
                                                           name="color_principal"
                                                           value="{{ old('color_principal', $estilo->color_principal) }}"
                                                           placeholder="#e91e63">
                                                </div>
                                                <small class="text-muted">Color para resaltar en la UI</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripcion</label>
                                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                                      id="descripcion"
                                                      name="descripcion"
                                                      rows="3"
                                                      placeholder="Descripcion del estilo...">{{ old('descripcion', $estilo->descripcion) }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="icono" class="form-label">Icono (Bootstrap Icons)</label>
                                            <input type="text"
                                                   class="form-control @error('icono') is-invalid @enderror"
                                                   id="icono"
                                                   name="icono"
                                                   value="{{ old('icono', $estilo->icono) }}"
                                                   placeholder="Ej: bi-heart, bi-star, bi-flower2">
                                            <small class="text-muted">
                                                Ver iconos disponibles en <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Precio y Rango de Flores</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="precio_base" class="form-label">Precio Base *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number"
                                                           class="form-control @error('precio_base') is-invalid @enderror"
                                                           id="precio_base"
                                                           name="precio_base"
                                                           value="{{ old('precio_base', $estilo->precio_base) }}"
                                                           min="0"
                                                           step="100"
                                                           required>
                                                </div>
                                                <small class="text-muted">Precio base del estilo (sin flores)</small>
                                                @error('precio_base')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="flores_minimo" class="form-label">Minimo de Flores *</label>
                                                <input type="number"
                                                       class="form-control @error('flores_minimo') is-invalid @enderror"
                                                       id="flores_minimo"
                                                       name="flores_minimo"
                                                       value="{{ old('flores_minimo', $estilo->flores_minimo) ?? 5 }}"
                                                       min="1"
                                                       required>
                                                @error('flores_minimo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="flores_maximo" class="form-label">Maximo de Flores *</label>
                                                <input type="number"
                                                       class="form-control @error('flores_maximo') is-invalid @enderror"
                                                       id="flores_maximo"
                                                       name="flores_maximo"
                                                       value="{{ old('flores_maximo', $estilo->flores_maximo) ?? 30 }}"
                                                       min="1"
                                                       required>
                                                @error('flores_maximo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle"></i>
                                            El rango de flores determina cuantas flores minimas y maximas puede seleccionar el cliente para este estilo.
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
                                            @if($estilo->imagen)
                                                <img src="{{ asset($estilo->imagen) }}"
                                                     class="img-fluid rounded"
                                                     style="max-height: 200px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                     style="height: 200px;">
                                                    <div class="text-center text-muted">
                                                        <i class="bi bi-palette" style="font-size: 4rem;"></i>
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

                                        @if($estilo->imagen)
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
                                                   value="{{ old('orden', $estilo->orden) ?? 0 }}"
                                                   min="0">
                                            <small class="text-muted">Menor numero = aparece primero</small>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   id="activo"
                                                   name="activo"
                                                   value="1"
                                                   {{ old('activo', $estilo->activo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="activo">
                                                <strong>Estilo Activo</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted">Si esta desactivado, no aparecera en "Arma tu Ramo"</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('estilos-ramo.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> {{ $estilo->exists ? 'Actualizar' : 'Guardar' }} Estilo
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
        // Sincronizar color picker con input text
        const colorPicker = document.getElementById('color_picker');
        const colorInput = document.getElementById('color_principal');

        colorPicker.addEventListener('input', function() {
            colorInput.value = this.value;
        });

        colorInput.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorPicker.value = this.value;
            }
        });

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
                                <i class="bi bi-palette" style="font-size: 4rem;"></i>
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
