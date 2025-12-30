<x-app-layout>
    <x-slot name="header">Nueva Zona de Cobertura</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>Nueva Zona
                        </h4>
                        <a href="{{ route('logistica.zonas.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('logistica.zonas.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nombre" class="form-label">Nombre de la Zona <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre') }}"
                                       placeholder="Ej: Zona Norte, Centro, Sector Premium" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" class="form-control @error('orden') is-invalid @enderror"
                                       id="orden" name="orden" value="{{ old('orden', 0) }}" min="0">
                                @error('orden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                          id="descripcion" name="descripcion" rows="2"
                                          placeholder="Descripción breve de la zona...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Ciudades Incluidas <span class="text-danger">*</span></label>
                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                    <div class="row g-2">
                                        @foreach($ciudades as $ciudad)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="ciudades_ids[]" value="{{ $ciudad->id }}"
                                                           id="ciudad_{{ $ciudad->id }}"
                                                           {{ in_array($ciudad->id, old('ciudades_ids', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="ciudad_{{ $ciudad->id }}">
                                                        {{ $ciudad->nombre }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('ciudades_ids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Selecciona las ciudades donde se realizan entregas en esta zona.</small>
                            </div>

                            <div class="col-md-6">
                                <label for="codigo_postal_desde" class="form-label">Código Postal Desde</label>
                                <input type="text" class="form-control @error('codigo_postal_desde') is-invalid @enderror"
                                       id="codigo_postal_desde" name="codigo_postal_desde" value="{{ old('codigo_postal_desde') }}"
                                       placeholder="Ej: 110001">
                                @error('codigo_postal_desde')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="codigo_postal_hasta" class="form-label">Código Postal Hasta</label>
                                <input type="text" class="form-control @error('codigo_postal_hasta') is-invalid @enderror"
                                       id="codigo_postal_hasta" name="codigo_postal_hasta" value="{{ old('codigo_postal_hasta') }}"
                                       placeholder="Ej: 110999">
                                @error('codigo_postal_hasta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Zona Activa</label>
                                </div>
                                <small class="text-muted">Las zonas inactivas no aparecerán en el checkout.</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Zona
                            </button>
                            <a href="{{ route('logistica.zonas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
