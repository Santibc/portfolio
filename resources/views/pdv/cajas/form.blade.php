<x-app-layout>
    @section('title', ($caja ? 'Editar' : 'Nueva') . ' Caja')

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('pdv.cajas.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold mb-0">{{ $caja ? 'Editar' : 'Nueva' }} Caja</h4>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('pdv.cajas.guardar') }}" method="POST">
                            @csrf
                            @if($caja)
                                <input type="hidden" name="id" value="{{ $caja->id }}">
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                           value="{{ old('nombre', $caja->nombre ?? '') }}" placeholder="Ej: Centro de Experiencia" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                                           value="{{ old('codigo', $caja->codigo ?? '') }}" placeholder="Ej: CEXP" required>
                                    @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ubicación <span class="text-danger">*</span></label>
                                    <select name="ubicacion_id" class="form-select @error('ubicacion_id') is-invalid @enderror" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($ubicaciones as $ub)
                                            <option value="{{ $ub->id }}" {{ old('ubicacion_id', $caja->ubicacion_id ?? '') == $ub->id ? 'selected' : '' }}>
                                                {{ $ub->nombre }} ({{ $ub->tipo }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ubicacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Cajero Asignado</label>
                                    <select name="cajero_asignado_id" class="form-select">
                                        <option value="">Sin asignar</option>
                                        @foreach($usuarios as $u)
                                            <option value="{{ $u->id }}" {{ old('cajero_asignado_id', $caja->cajero_asignado_id ?? '') == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }} ({{ $u->getRoleNames()->first() }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('pdv.cajas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn text-white" style="background: var(--miracle-pink);">
                                    <i class="bi bi-check-lg me-1"></i>{{ $caja ? 'Actualizar' : 'Crear' }} Caja
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
