<x-app-layout>
    <x-slot name="header">Editar Horario</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-clock text-warning me-2"></i>Editar Horario
                        </h4>
                        <a href="{{ route('logistica.horarios.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('logistica.horarios.update', $horario->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="zona_cobertura_id" class="form-label">Zona <span class="text-danger">*</span></label>
                                <select class="form-select @error('zona_cobertura_id') is-invalid @enderror"
                                        id="zona_cobertura_id" name="zona_cobertura_id" required>
                                    @foreach($zonas as $zona)
                                        <option value="{{ $zona->id }}" {{ old('zona_cobertura_id', $horario->zona_cobertura_id) == $zona->id ? 'selected' : '' }}>
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
                                    <option value="lunes" {{ old('dia_semana', $horario->dia_semana) == 'lunes' ? 'selected' : '' }}>Lunes</option>
                                    <option value="martes" {{ old('dia_semana', $horario->dia_semana) == 'martes' ? 'selected' : '' }}>Martes</option>
                                    <option value="miercoles" {{ old('dia_semana', $horario->dia_semana) == 'miercoles' ? 'selected' : '' }}>Miercoles</option>
                                    <option value="jueves" {{ old('dia_semana', $horario->dia_semana) == 'jueves' ? 'selected' : '' }}>Jueves</option>
                                    <option value="viernes" {{ old('dia_semana', $horario->dia_semana) == 'viernes' ? 'selected' : '' }}>Viernes</option>
                                    <option value="sabado" {{ old('dia_semana', $horario->dia_semana) == 'sabado' ? 'selected' : '' }}>Sabado</option>
                                    <option value="domingo" {{ old('dia_semana', $horario->dia_semana) == 'domingo' ? 'selected' : '' }}>Domingo</option>
                                </select>
                                @error('dia_semana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre del Horario</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre', $horario->nombre) }}"
                                       placeholder="Ej: Manana, Tarde, Noche">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="capacidad_pedidos" class="form-label">Capacidad de Pedidos <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacidad_pedidos') is-invalid @enderror"
                                       id="capacidad_pedidos" name="capacidad_pedidos"
                                       value="{{ old('capacidad_pedidos', $horario->capacidad_pedidos) }}"
                                       min="1" required>
                                @error('capacidad_pedidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hora_inicio" class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror"
                                       id="hora_inicio" name="hora_inicio"
                                       value="{{ old('hora_inicio', \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i')) }}" required>
                                @error('hora_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hora_fin" class="form-label">Hora de Fin <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('hora_fin') is-invalid @enderror"
                                       id="hora_fin" name="hora_fin"
                                       value="{{ old('hora_fin', \Carbon\Carbon::parse($horario->hora_fin)->format('H:i')) }}" required>
                                @error('hora_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                           {{ old('activo', $horario->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Horario Activo</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Horario
                            </button>
                            <a href="{{ route('logistica.horarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
