@extends('layouts.app')

@section('title', 'Configuración de Alertas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Configuración de Alertas</h1>
            <p class="text-muted mb-0">Define los días de antelación para cada tipo de alerta</p>
        </div>
        <a href="{{ route('alertas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver a Alertas
        </a>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-sliders text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Configuraciones</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['activas'] }}</h3>
                            <small class="text-muted">Alertas Activas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-pause-circle text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['inactivas'] }}</h3>
                            <small class="text-muted">Alertas Inactivas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info alert-sm py-2 mb-4 small">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Días de antelación:</strong> Cuántos días antes de la caducidad se genera la alerta.
    </div>

    <!-- Tabla de Configuraciones -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tipo de Alerta</th>
                            <th style="width: 200px;">Días de Antelación</th>
                            <th style="width: 120px;">Estado</th>
                            <th class="text-end pe-4" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($configuraciones as $config)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                        <i class="bi {{ $config->tipo_icono ?? 'bi-bell' }} text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $config->tipo_label }}</h6>
                                        <small class="text-muted">{{ $config->tipo }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form class="form-dias-antelacion d-flex gap-2" data-id="{{ $config->id }}">
                                    <input type="number" name="dias_antelacion" class="form-control form-control-sm"
                                           value="{{ $config->dias_antelacion }}" min="1" max="365" style="width: 80px;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-activa" type="checkbox"
                                           data-id="{{ $config->id }}"
                                           {{ $config->activa ? 'checked' : '' }}>
                                    <label class="form-check-label small">
                                        {{ $config->activa ? 'Activa' : 'Inactiva' }}
                                    </label>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge {{ $config->activa ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $config->dias_antelacion }} días
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-sliders fs-1 d-block mb-2"></i>
                                <p class="mb-0">No hay configuraciones de alertas</p>
                                <small>Ejecuta el seeder para crear las configuraciones iniciales</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Leyenda de tipos -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent">
            <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Tipos de Alertas</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <h6 class="text-muted small mb-2">Trabajadores</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><i class="bi bi-mortarboard me-1"></i> Formaciones</li>
                        <li><i class="bi bi-file-earmark me-1"></i> Documentos de trabajador</li>
                        <li><i class="bi bi-heart-pulse me-1"></i> Aptos médicos</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted small mb-2">EPIs</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><i class="bi bi-shield-check me-1"></i> Caducidad de EPIs</li>
                        <li><i class="bi bi-clipboard-check me-1"></i> Revisiones de EPIs</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted small mb-2">Vehículos</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><i class="bi bi-card-checklist me-1"></i> ITV</li>
                        <li><i class="bi bi-shield me-1"></i> Seguro de vehículo</li>
                        <li><i class="bi bi-file-earmark me-1"></i> Documentos de vehículo</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted small mb-2">Contratos</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><i class="bi bi-file-earmark-text me-1"></i> Vencimiento de contratos</li>
                        <li><i class="bi bi-safe me-1"></i> Liberación de garantías</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted small mb-2">Subcontratas</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><i class="bi bi-file-earmark-ruled me-1"></i> Documentos CAE</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted small mb-2">Empresa</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><i class="bi bi-building me-1"></i> Caducidades generales (ISO, RC, etc.)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar días de antelación
    document.querySelectorAll('.form-dias-antelacion').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const dias = this.querySelector('input[name="dias_antelacion"]').value;

            fetch(`{{ url('alertas/configuracion') }}/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ dias_antelacion: dias })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Actualizado',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al actualizar',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        });
    });

    // Toggle activar/desactivar
    document.querySelectorAll('.toggle-activa').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const label = this.nextElementSibling;

            fetch(`{{ url('alertas/configuracion') }}/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    label.textContent = data.activa ? 'Activa' : 'Inactiva';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.activa ? 'Activada' : 'Desactivada',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            })
            .catch(error => {
                // Revertir el toggle
                this.checked = !this.checked;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al cambiar estado',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        });
    });
});
</script>
@endpush
@endsection
