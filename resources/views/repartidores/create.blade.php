<x-app-layout>
    <x-slot name="header">Nuevo Repartidor</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-truck text-primary me-2"></i>Nuevo Repartidor
                        </h4>
                        <a href="{{ route('repartidores.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('repartidores.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Telefono <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono" name="telefono" value="{{ old('telefono') }}" required>
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="documento" class="form-label">Documento de Identidad</label>
                                <input type="text" class="form-control @error('documento') is-invalid @enderror"
                                       id="documento" name="documento" value="{{ old('documento') }}">
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="vehiculo" class="form-label">Tipo de Vehiculo</label>
                                <select class="form-select @error('vehiculo') is-invalid @enderror"
                                        id="vehiculo" name="vehiculo">
                                    <option value="">Sin vehiculo</option>
                                    <option value="moto" {{ old('vehiculo') == 'moto' ? 'selected' : '' }}>Moto</option>
                                    <option value="bicicleta" {{ old('vehiculo') == 'bicicleta' ? 'selected' : '' }}>Bicicleta</option>
                                    <option value="carro" {{ old('vehiculo') == 'carro' ? 'selected' : '' }}>Carro</option>
                                    <option value="a_pie" {{ old('vehiculo') == 'a_pie' ? 'selected' : '' }}>A pie</option>
                                </select>
                                @error('vehiculo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="placa" class="form-label">Placa</label>
                                <input type="text" class="form-control @error('placa') is-invalid @enderror"
                                       id="placa" name="placa" value="{{ old('placa') }}"
                                       placeholder="ABC-123">
                                @error('placa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea class="form-control @error('notas') is-invalid @enderror"
                                          id="notas" name="notas" rows="2"
                                          placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                                @error('notas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Repartidor Activo</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Repartidor
                            </button>
                            <a href="{{ route('repartidores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
