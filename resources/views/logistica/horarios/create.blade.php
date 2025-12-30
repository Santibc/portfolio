<x-app-layout>
    <x-slot name="header">Nuevo Horario de Entrega</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-clock text-warning me-2"></i>Nuevo Horario
                        </h4>
                        <a href="{{ route('logistica.horarios.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('logistica.horarios.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="zona_cobertura_id" class="form-label">Zona <span class="text-danger">*</span></label>
                                <select class="form-select @error('zona_cobertura_id') is-invalid @enderror"
                                        id="zona_cobertura_id" name="zona_cobertura_id" required>
                                    <option value="">Seleccionar zona...</option>
                                    @foreach($zonas as $zona)
                                        <option value="{{ $zona->id }}" {{ old('zona_cobertura_id', $zonaId) == $zona->id ? 'selected' : '' }}>
                                            {{ $zona->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('zona_cobertura_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="dia_semana" class="form-label">Dia de la Semana <span class="text-danger">*</span></label>
                                <select class="form-select @error('dia_semana') is-invalid @enderror"
                                        id="dia_semana" name="dia_semana" required>
                                    <option value="">Seleccionar dia...</option>
                                    <option value="lunes" {{ old('dia_semana') == 'lunes' ? 'selected' : '' }}>Lunes</option>
                                    <option value="martes" {{ old('dia_semana') == 'martes' ? 'selected' : '' }}>Martes</option>
                                    <option value="miercoles" {{ old('dia_semana') == 'miercoles' ? 'selected' : '' }}>Miercoles</option>
                                    <option value="jueves" {{ old('dia_semana') == 'jueves' ? 'selected' : '' }}>Jueves</option>
                                    <option value="viernes" {{ old('dia_semana') == 'viernes' ? 'selected' : '' }}>Viernes</option>
                                    <option value="sabado" {{ old('dia_semana') == 'sabado' ? 'selected' : '' }}>Sabado</option>
                                    <option value="domingo" {{ old('dia_semana') == 'domingo' ? 'selected' : '' }}>Domingo</option>
                                </select>
                                @error('dia_semana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre del Horario</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre') }}"
                                       placeholder="Ej: Manana, Tarde, Noche">
                                <small class="text-muted">Nombre amigable para mostrar al cliente</small>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="capacidad_pedidos" class="form-label">Capacidad de Pedidos <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacidad_pedidos') is-invalid @enderror"
                                       id="capacidad_pedidos" name="capacidad_pedidos"
                                       value="{{ old('capacidad_pedidos', 10) }}"
                                       min="1" required>
                                <small class="text-muted">Maximo de pedidos que puedes atender en este horario</small>
                                @error('capacidad_pedidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hora_inicio" class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror"
                                       id="hora_inicio" name="hora_inicio"
                                       value="{{ old('hora_inicio', '08:00') }}" required>
                                @error('hora_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hora_fin" class="form-label">Hora de Fin <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('hora_fin') is-invalid @enderror"
                                       id="hora_fin" name="hora_fin"
                                       value="{{ old('hora_fin', '12:00') }}" required>
                                @error('hora_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Horario Activo</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Horario
                            </button>
                            <a href="{{ route('logistica.horarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tip de horarios predefinidos --}}
            <div class="alert alert-info mt-4">
                <h6 class="alert-heading"><i class="bi bi-lightbulb me-2"></i>Sugerencia</h6>
                <p class="mb-0">
                    Puedes crear multiples horarios por dia. Por ejemplo:
                    <strong>Manana (8:00-12:00)</strong>,
                    <strong>Tarde (12:00-18:00)</strong>,
                    <strong>Noche (18:00-21:00)</strong>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
