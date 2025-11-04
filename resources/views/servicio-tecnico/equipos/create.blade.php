<x-app-layout>
    <x-slot name="header">{{ isset($equipo) ? 'Editar Equipo' : 'Nuevo Equipo' }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="text-muted mb-4">{{ isset($equipo) ? 'Actualizar información del equipo' : 'Registrar nuevo equipo de seguridad' }}</p>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ isset($equipo) ? route('st.equipos.update', $equipo->id) : route('st.equipos.store') }}"
                  method="POST" id="equipoForm">
                @csrf
                @if(isset($equipo))
                    @method('PUT')
                @endif

                {{-- Información del Cliente --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Cliente Propietario</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="st_cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="st_cliente_id" id="st_cliente_id"
                                        class="form-select @error('st_cliente_id') is-invalid @enderror" required>
                                    <option value="">Seleccione un cliente</option>
                                    @foreach($clientes ?? [] as $cliente)
                                        <option value="{{ $cliente->id }}"
                                                {{ (old('st_cliente_id', $equipo->st_cliente_id ?? '') == $cliente->id) ? 'selected' : '' }}>
                                            {{ $cliente->nombre_completo_formateado }} - {{ $cliente->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('st_cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Información del Equipo --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Equipo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="tipo_equipo" class="form-label">Tipo de Equipo <span class="text-danger">*</span></label>
                                <select name="tipo_equipo" id="tipo_equipo"
                                        class="form-select @error('tipo_equipo') is-invalid @enderror" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="Cámara IP" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'Cámara IP' ? 'selected' : '' }}>Cámara IP</option>
                                    <option value="Cámara Análoga" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'Cámara Análoga' ? 'selected' : '' }}>Cámara Análoga</option>
                                    <option value="Cámara PTZ" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'Cámara PTZ' ? 'selected' : '' }}>Cámara PTZ</option>
                                    <option value="DVR" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'DVR' ? 'selected' : '' }}>DVR</option>
                                    <option value="NVR" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'NVR' ? 'selected' : '' }}>NVR</option>
                                    <option value="Monitor" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'Monitor' ? 'selected' : '' }}>Monitor</option>
                                    <option value="Fuente de Poder" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'Fuente de Poder' ? 'selected' : '' }}>Fuente de Poder</option>
                                    <option value="Switch POE" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'Switch POE' ? 'selected' : '' }}>Switch POE</option>
                                    <option value="Otro" {{ old('tipo_equipo', $equipo->tipo_equipo ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo_equipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" name="marca" id="marca"
                                       class="form-control @error('marca') is-invalid @enderror"
                                       value="{{ old('marca', $equipo->marca ?? '') }}"
                                       placeholder="Ej: Hikvision, Dahua, etc.">
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" name="modelo" id="modelo"
                                       class="form-control @error('modelo') is-invalid @enderror"
                                       value="{{ old('modelo', $equipo->modelo ?? '') }}"
                                       placeholder="Ej: DS-2CD2043G2-I">
                                @error('modelo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="numero_serie" class="form-label">Número de Serie <span class="text-danger">*</span></label>
                                <input type="text" name="numero_serie" id="numero_serie"
                                       class="form-control @error('numero_serie') is-invalid @enderror"
                                       value="{{ old('numero_serie', $equipo->numero_serie ?? '') }}" required
                                       placeholder="Número de serie único del equipo">
                                @error('numero_serie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="mac_address" class="form-label">Dirección MAC</label>
                                <input type="text" name="mac_address" id="mac_address"
                                       class="form-control @error('mac_address') is-invalid @enderror"
                                       value="{{ old('mac_address', $equipo->mac_address ?? '') }}"
                                       placeholder="Ej: 00:1A:2B:3C:4D:5E">
                                @error('mac_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ip_address" class="form-label">Dirección IP</label>
                                <input type="text" name="ip_address" id="ip_address"
                                       class="form-control @error('ip_address') is-invalid @enderror"
                                       value="{{ old('ip_address', $equipo->ip_address ?? '') }}"
                                       placeholder="Ej: 192.168.1.100">
                                @error('ip_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="especificaciones" class="form-label">Especificaciones Técnicas</label>
                                <textarea name="especificaciones" id="especificaciones" rows="3"
                                          class="form-control @error('especificaciones') is-invalid @enderror"
                                          placeholder="Detalles técnicos del equipo: resolución, lente, funciones especiales, etc.">{{ old('especificaciones', $equipo->especificaciones ?? '') }}</textarea>
                                @error('especificaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ubicación e Instalación --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Ubicación e Instalación</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="ubicacion_instalacion" class="form-label">Ubicación de Instalación</label>
                                <input type="text" name="ubicacion_instalacion" id="ubicacion_instalacion"
                                       class="form-control @error('ubicacion_instalacion') is-invalid @enderror"
                                       value="{{ old('ubicacion_instalacion', $equipo->ubicacion_instalacion ?? '') }}"
                                       placeholder="Ej: Entrada principal, Parqueadero, Bodega, etc.">
                                @error('ubicacion_instalacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_compra" class="form-label">Fecha de Compra</label>
                                <input type="date" name="fecha_compra" id="fecha_compra"
                                       class="form-control @error('fecha_compra') is-invalid @enderror"
                                       value="{{ old('fecha_compra', isset($equipo->fecha_compra) ? $equipo->fecha_compra->format('Y-m-d') : '') }}">
                                @error('fecha_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_instalacion" class="form-label">Fecha de Instalación</label>
                                <input type="date" name="fecha_instalacion" id="fecha_instalacion"
                                       class="form-control @error('fecha_instalacion') is-invalid @enderror"
                                       value="{{ old('fecha_instalacion', isset($equipo->fecha_instalacion) ? $equipo->fecha_instalacion->format('Y-m-d') : '') }}">
                                @error('fecha_instalacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Garantía --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Información de Garantía</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="en_garantia" id="en_garantia"
                                           value="1" {{ old('en_garantia', $equipo->en_garantia ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="en_garantia">
                                        <strong>Equipo en Garantía</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6" id="vencimiento_garantia_div" style="display: none;">
                                <label for="vencimiento_garantia" class="form-label">Fecha de Vencimiento de Garantía</label>
                                <input type="date" name="vencimiento_garantia" id="vencimiento_garantia"
                                       class="form-control @error('vencimiento_garantia') is-invalid @enderror"
                                       value="{{ old('vencimiento_garantia', isset($equipo->vencimiento_garantia) ? $equipo->vencimiento_garantia->format('Y-m-d') : '') }}">
                                @error('vencimiento_garantia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estado --}}
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Estado del Equipo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado Operacional <span class="text-danger">*</span></label>
                                <select name="estado" id="estado"
                                        class="form-select @error('estado') is-invalid @enderror" required>
                                    <option value="operativo" {{ old('estado', $equipo->estado ?? 'operativo') == 'operativo' ? 'selected' : '' }}>Operativo</option>
                                    <option value="en_reparacion" {{ old('estado', $equipo->estado ?? '') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                                    <option value="fuera_servicio" {{ old('estado', $equipo->estado ?? '') == 'fuera_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                                    <option value="en_bodega" {{ old('estado', $equipo->estado ?? '') == 'en_bodega' ? 'selected' : '' }}>En Bodega</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(isset($equipo))
                            <div class="col-md-6">
                                <label for="activo" class="form-label">Estado del Registro</label>
                                <select name="activo" id="activo" class="form-select @error('activo') is-invalid @enderror">
                                    <option value="1" {{ old('activo', $equipo->activo ?? true) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('activo', $equipo->activo ?? true) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('activo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('st.equipos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> {{ isset($equipo) ? 'Actualizar' : 'Registrar' }} Equipo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Mostrar/ocultar campo de vencimiento de garantía
    function toggleVencimientoGarantia() {
        if ($('#en_garantia').is(':checked')) {
            $('#vencimiento_garantia_div').slideDown();
            $('#vencimiento_garantia').prop('required', true);
        } else {
            $('#vencimiento_garantia_div').slideUp();
            $('#vencimiento_garantia').prop('required', false);
        }
    }

    $('#en_garantia').on('change', toggleVencimientoGarantia);

    // Inicializar al cargar
    toggleVencimientoGarantia();

    // Validación del formulario
    $('#equipoForm').on('submit', function(e) {
        let valid = true;
        let mensaje = '';

        // Validar número de serie
        if ($('#numero_serie').val().trim() === '') {
            valid = false;
            mensaje += '- El número de serie es obligatorio\n';
        }

        // Validar cliente
        if ($('#st_cliente_id').val() === '') {
            valid = false;
            mensaje += '- Debe seleccionar un cliente\n';
        }

        // Validar tipo de equipo
        if ($('#tipo_equipo').val() === '') {
            valid = false;
            mensaje += '- Debe seleccionar un tipo de equipo\n';
        }

        if (!valid) {
            e.preventDefault();
            Swal.fire({
                title: 'Campos Requeridos',
                text: mensaje,
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
        }
    });
});
</script>
@endpush
        </div>
    </div>
</x-app-layout>
