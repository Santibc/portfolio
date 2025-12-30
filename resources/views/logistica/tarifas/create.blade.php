<x-app-layout>
    <x-slot name="header">Nueva Tarifa</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-cash-coin text-success me-2"></i>Nueva Tarifa de Envio
                        </h4>
                        <a href="{{ route('logistica.tarifas.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>

                    <form action="{{ route('logistica.tarifas.store') }}" method="POST">
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
                                <label for="nombre" class="form-label">Nombre de la Tarifa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre') }}"
                                       placeholder="Ej: Estandar, Express, Mismo Dia" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="costo_base" class="form-label">Costo Base <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('costo_base') is-invalid @enderror"
                                           id="costo_base" name="costo_base" value="{{ old('costo_base', 0) }}"
                                           min="0" step="100" required>
                                </div>
                                @error('costo_base')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="minimo_compra" class="form-label">Minimo de Compra <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('minimo_compra') is-invalid @enderror"
                                           id="minimo_compra" name="minimo_compra" value="{{ old('minimo_compra', 0) }}"
                                           min="0" step="1000" required>
                                </div>
                                <small class="text-muted">Monto minimo para aplicar esta tarifa</small>
                                @error('minimo_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="tiempo_entrega_horas" class="form-label">Tiempo de Entrega <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('tiempo_entrega_horas') is-invalid @enderror"
                                           id="tiempo_entrega_horas" name="tiempo_entrega_horas"
                                           value="{{ old('tiempo_entrega_horas', 24) }}"
                                           min="1" required>
                                    <span class="input-group-text">horas</span>
                                </div>
                                @error('tiempo_entrega_horas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="costo_si_no_alcanza_minimo" class="form-label">Costo Extra si No Alcanza Minimo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('costo_si_no_alcanza_minimo') is-invalid @enderror"
                                           id="costo_si_no_alcanza_minimo" name="costo_si_no_alcanza_minimo"
                                           value="{{ old('costo_si_no_alcanza_minimo') }}"
                                           min="0" step="100">
                                </div>
                                <small class="text-muted">Se suma al costo base si la compra no alcanza el minimo</small>
                                @error('costo_si_no_alcanza_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="costo_por_km" class="form-label">Costo por Kilometro</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('costo_por_km') is-invalid @enderror"
                                           id="costo_por_km" name="costo_por_km"
                                           value="{{ old('costo_por_km') }}"
                                           min="0" step="100">
                                    <span class="input-group-text">/km</span>
                                </div>
                                <small class="text-muted">Opcional - para calculo por distancia</small>
                                @error('costo_por_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3"><i class="bi bi-gift me-2"></i>Envio Gratis</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="envio_gratis_desde"
                                                           name="envio_gratis_desde" value="1"
                                                           {{ old('envio_gratis_desde') ? 'checked' : '' }}
                                                           onchange="toggleMontoGratis()">
                                                    <label class="form-check-label" for="envio_gratis_desde">
                                                        Ofrecer envio gratis desde cierto monto
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6" id="monto_gratis_container" style="{{ old('envio_gratis_desde') ? '' : 'display: none;' }}">
                                                <label for="monto_envio_gratis" class="form-label">Monto para Envio Gratis</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control"
                                                           id="monto_envio_gratis" name="monto_envio_gratis"
                                                           value="{{ old('monto_envio_gratis') }}"
                                                           min="0" step="1000">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Tarifa Activa</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Tarifa
                            </button>
                            <a href="{{ route('logistica.tarifas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleMontoGratis() {
            const checkbox = document.getElementById('envio_gratis_desde');
            const container = document.getElementById('monto_gratis_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
    @endpush
</x-app-layout>
