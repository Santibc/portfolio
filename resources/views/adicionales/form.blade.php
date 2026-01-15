<x-app-layout>
    <x-slot name="header">
        {{ $adicional->exists ? 'Editar Complementario' : 'Nuevo Complementario' }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                            <i class="bi bi-box-seam text-primary"></i>
                            {{ $adicional->exists ? 'Editar: ' . $adicional->nombre : 'Agregar Producto Complementario' }}
                        </h4>
                        <a href="{{ route('adicionales.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('adicionales.guardar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($adicional->exists)
                            <input type="hidden" name="adicional_id" value="{{ $adicional->id }}">
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Información Básica</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="nombre" class="form-label">Nombre *</label>
                                                <input type="text"
                                                       class="form-control @error('nombre') is-invalid @enderror"
                                                       id="nombre"
                                                       name="nombre"
                                                       value="{{ old('nombre', $adicional->nombre) }}"
                                                       placeholder="Ej: Caja de Chocolates"
                                                       required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="tipo_complementario" class="form-label">Tipo de Complementario *</label>
                                                <select class="form-select @error('tipo_complementario') is-invalid @enderror"
                                                        id="tipo_complementario"
                                                        name="tipo_complementario"
                                                        required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="cross_selling" {{ old('tipo_complementario', $adicional->tipo_complementario ?? 'cross_selling') == 'cross_selling' ? 'selected' : '' }}>
                                                        <i class="bi bi-shop"></i> Cross-selling (General)
                                                    </option>
                                                    <option value="especifico" {{ old('tipo_complementario', $adicional->tipo_complementario) == 'especifico' ? 'selected' : '' }}>
                                                        <i class="bi bi-tag"></i> Específico (Asignado a productos)
                                                    </option>
                                                </select>
                                                <small class="text-muted">
                                                    <strong>Cross-selling:</strong> Se muestra a todos | <strong>Específico:</strong> Solo si está asignado
                                                </small>
                                                @error('tipo_complementario')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="categoria" class="form-label">Categoría *</label>
                                                <select class="form-select @error('categoria') is-invalid @enderror"
                                                        id="categoria"
                                                        name="categoria"
                                                        required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach($categorias as $key => $nombre)
                                                        <option value="{{ $key }}" {{ old('categoria', $adicional->categoria) == $key ? 'selected' : '' }}>
                                                            {{ $nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('categoria')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                                      id="descripcion"
                                                      name="descripcion"
                                                      rows="3"
                                                      placeholder="Descripción del producto...">{{ old('descripcion', $adicional->descripcion) }}</textarea>
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
                                                <label for="precio" class="form-label">Precio *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number"
                                                           class="form-control @error('precio') is-invalid @enderror"
                                                           id="precio"
                                                           name="precio"
                                                           value="{{ old('precio', $adicional->precio) }}"
                                                           min="0"
                                                           step="100"
                                                           required>
                                                </div>
                                                @error('precio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="stock" class="form-label">Stock Disponible *</label>
                                                <input type="number"
                                                       class="form-control @error('stock') is-invalid @enderror"
                                                       id="stock"
                                                       name="stock"
                                                       value="{{ old('stock', $adicional->stock) ?? 0 }}"
                                                       min="0"
                                                       required>
                                                @error('stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-image"></i> Imagen</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div id="imagen-preview" class="mb-3">
                                            @if($adicional->imagen)
                                                <img src="{{ asset($adicional->imagen) }}"
                                                     class="img-fluid rounded"
                                                     style="max-height: 180px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                     style="height: 180px;">
                                                    <div class="text-center text-muted">
                                                        <i class="bi bi-gift" style="font-size: 4rem;"></i>
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

                                        @if($adicional->imagen)
                                            <div class="form-check mt-3">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="eliminar_imagen"
                                                       name="eliminar_imagen"
                                                       value="1">
                                                <label class="form-check-label text-danger" for="eliminar_imagen">
                                                    <i class="bi bi-trash"></i> Eliminar imagen
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
                                                   class="form-control"
                                                   id="orden"
                                                   name="orden"
                                                   value="{{ old('orden', $adicional->orden) ?? 0 }}"
                                                   min="0">
                                            <small class="text-muted">Menor número = aparece primero</small>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   id="disponible"
                                                   name="disponible"
                                                   value="1"
                                                   {{ old('disponible', $adicional->disponible) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="disponible">
                                                <strong>Producto Disponible</strong>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   id="mostrar_en_checkout"
                                                   name="mostrar_en_checkout"
                                                   value="1"
                                                   {{ old('mostrar_en_checkout', $adicional->mostrar_en_checkout) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mostrar_en_checkout">
                                                <strong>Mostrar en Checkout</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted">Se ofrecerá como upsell al finalizar compra</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('adicionales.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> {{ $adicional->exists ? 'Actualizar' : 'Guardar' }}
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
        const imagenInput = document.getElementById('imagen');
        const imagenPreview = document.getElementById('imagen-preview');

        imagenInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagenPreview.innerHTML = `
                        <img src="${e.target.result}"
                             class="img-fluid rounded"
                             style="max-height: 180px; object-fit: cover;">
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
                             style="height: 180px;">
                            <div class="text-center text-muted">
                                <i class="bi bi-gift" style="font-size: 4rem;"></i>
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
