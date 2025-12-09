<x-app-layout>
    <x-slot name="header">{{ $estadistica->exists ? 'Editar Estadística' : 'Nueva Estadística' }}</x-slot>

    <div class="container py-4">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> {{ $estadistica->exists ? 'Editar' : 'Crear' }} Estadística</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ $estadistica->exists ? route('admin.landing.estadisticas.update', $estadistica->id) : route('admin.landing.estadisticas.store') }}">
                            @csrf
                            @if($estadistica->exists)
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Número <span class="text-danger">*</span></label>
                                <input type="text" name="numero" class="form-control" required
                                       value="{{ old('numero', $estadistica->numero) }}"
                                       placeholder="Ej: 500+">
                                <small class="text-muted">Puede incluir símbolos como +, K, etc.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Etiqueta <span class="text-danger">*</span></label>
                                <input type="text" name="etiqueta" class="form-control" required
                                       value="{{ old('etiqueta', $estadistica->etiqueta) }}"
                                       placeholder="Ej: Emprendedores">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" name="orden" class="form-control"
                                       value="{{ old('orden', $estadistica->orden ?? 0) }}">
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                                           {{ old('activo', $estadistica->activo ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        Activo (visible en el sitio)
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.landing.estadisticas.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> {{ $estadistica->exists ? 'Actualizar' : 'Crear' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
