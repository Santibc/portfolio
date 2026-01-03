<x-app-layout>
    <x-slot name="header">
        {{ $flor->exists ? 'Editar Flor' : 'Nueva Flor' }}
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
                            <i class="bi bi-flower1 text-pink-500"></i>
                            {{ $flor->exists ? 'Editar: ' . $flor->nombre : 'Agregar Nueva Flor' }}
                        </h4>
                        <a href="{{ route('flores.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('flores.guardar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($flor->exists)
                            <input type="hidden" name="flor_id" value="{{ $flor->id }}">
                        @endif

                        <div class="row">
                            {{-- Columna izquierda: Datos básicos --}}
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Información Básica</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8 mb-3">
                                                <label for="nombre" class="form-label">Nombre de la Flor *</label>
                                                <input type="text"
                                                       class="form-control @error('nombre') is-invalid @enderror"
                                                       id="nombre"
                                                       name="nombre"
                                                       value="{{ old('nombre', $flor->nombre) }}"
                                                       placeholder="Ej: Rosa Roja"
                                                       required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="color" class="form-label">Color</label>
                                                <div class="input-group">
                                                    <input type="color"
                                                           class="form-control form-control-color"
                                                           id="color_picker"
                                                           value="{{ old('color', $flor->color) ?? '#e91e63' }}"
                                                           style="width: 50px;">
                                                    <input type="text"
                                                           class="form-control @error('color') is-invalid @enderror"
                                                           id="color"
                                                           name="color"
                                                           value="{{ old('color', $flor->color) }}"
                                                           placeholder="#e91e63">
                                                </div>
                                                <small class="text-muted">Color representativo de la flor</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                                      id="descripcion"
                                                      name="descripcion"
                                                      rows="3"
                                                      placeholder="Descripción breve de la flor...">{{ old('descripcion', $flor->descripcion) }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Precio y Stock</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="precio_unidad" class="form-label">Precio por Unidad *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number"
                                                           class="form-control @error('precio_unidad') is-invalid @enderror"
                                                           id="precio_unidad"
                                                           name="precio_unidad"
                                                           value="{{ old('precio_unidad', $flor->precio_unidad) }}"
                                                           min="0"
                                                           step="100"
                                                           required>
                                                </div>
                                                @error('precio_unidad')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="stock" class="form-label">Stock Disponible *</label>
                                                <input type="number"
                                                       class="form-control @error('stock') is-invalid @enderror"
                                                       id="stock"
                                                       name="stock"
                                                       value="{{ old('stock', $flor->stock) ?? 0 }}"
                                                       min="0"
                                                       required>
                                                @error('stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cantidad_minima" class="form-label">Cantidad Mínima por Pedido *</label>
                                                <input type="number"
                                                       class="form-control @error('cantidad_minima') is-invalid @enderror"
                                                       id="cantidad_minima"
                                                       name="cantidad_minima"
                                                       value="{{ old('cantidad_minima', $flor->cantidad_minima) ?? 1 }}"
                                                       min="1"
                                                       required>
                                                @error('cantidad_minima')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="cantidad_maxima" class="form-label">Cantidad Máxima por Pedido *</label>
                                                <input type="number"
                                                       class="form-control @error('cantidad_maxima') is-invalid @enderror"
                                                       id="cantidad_maxima"
                                                       name="cantidad_maxima"
                                                       value="{{ old('cantidad_maxima', $flor->cantidad_maxima) ?? 50 }}"
                                                       min="1"
                                                       required>
                                                @error('cantidad_maxima')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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
                                            @if($flor->imagen)
                                                <img src="{{ asset($flor->imagen) }}"
                                                     class="img-fluid rounded"
                                                     style="max-height: 200px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                     style="height: 200px;">
                                                    <div class="text-center text-muted">
                                                        <i class="bi bi-flower1" style="font-size: 4rem;"></i>
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
                                        <small class="text-muted d-block mt-1">JPG, PNG, GIF, WebP. Máx 2MB</small>
                                        @error('imagen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if($flor->imagen)
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
                                            <label for="orden" class="form-label">Orden de Aparición</label>
                                            <input type="number"
                                                   class="form-control @error('orden') is-invalid @enderror"
                                                   id="orden"
                                                   name="orden"
                                                   value="{{ old('orden', $flor->orden) ?? 0 }}"
                                                   min="0">
                                            <small class="text-muted">Menor número = aparece primero</small>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   id="disponible"
                                                   name="disponible"
                                                   value="1"
                                                   {{ old('disponible', $flor->disponible) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="disponible">
                                                <strong>Flor Disponible</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted">Si está desactivada, no aparecerá en "Arma tu Ramo"</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('flores.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> {{ $flor->exists ? 'Actualizar' : 'Guardar' }} Flor
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
        const colorInput = document.getElementById('color');

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

                // Desmarcar "eliminar imagen" si se selecciona una nueva
                const eliminarCheckbox = document.getElementById('eliminar_imagen');
                if (eliminarCheckbox) {
                    eliminarCheckbox.checked = false;
                }
            }
        });

        // Si se marca eliminar imagen, mostrar placeholder
        const eliminarCheckbox = document.getElementById('eliminar_imagen');
        if (eliminarCheckbox) {
            eliminarCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    imagenPreview.innerHTML = `
                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                             style="height: 200px;">
                            <div class="text-center text-muted">
                                <i class="bi bi-flower1" style="font-size: 4rem;"></i>
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
