<x-app-layout>
    <x-slot name="header">Nueva Regla de Capacidad</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-calendar-event text-danger me-2"></i>Nueva Regla
                        </h4>
                        <a href="{{ route('logistica.capacidad.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('logistica.capacidad.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                                       id="fecha" name="fecha" value="{{ old('fecha') }}"
                                       min="{{ date('Y-m-d') }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="capacidad_total_dia" class="form-label">Pedidos por Dia <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacidad_total_dia') is-invalid @enderror"
                                       id="capacidad_total_dia" name="capacidad_total_dia"
                                       value="{{ old('capacidad_total_dia', 50) }}"
                                       min="1" required>
                                @error('capacidad_total_dia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="capacidad_por_hora" class="form-label">Pedidos por Hora <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacidad_por_hora') is-invalid @enderror"
                                       id="capacidad_por_hora" name="capacidad_por_hora"
                                       value="{{ old('capacidad_por_hora', 5) }}"
                                       min="1" required>
                                @error('capacidad_por_hora')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea class="form-control @error('notas') is-invalid @enderror"
                                          id="notas" name="notas" rows="2"
                                          placeholder="Ej: Dia de San Valentin - alta demanda esperada">{{ old('notas') }}</textarea>
                                @error('notas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Regla Activa</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Regla
                            </button>
                            <a href="{{ route('logistica.capacidad.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning mt-4">
                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Importante</h6>
                <p class="mb-0">
                    Esta regla limitara la cantidad de pedidos que se pueden recibir para entrega en la fecha seleccionada.
                    Los clientes veran un mensaje cuando la capacidad este llena.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
