@extends('layouts.app')

@section('title', 'Emails de Cumpleaños')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Configuración de Emails de Cumpleaños</h1>
            <p class="text-muted mb-0">Personaliza el email automático que se envía a los trabajadores en su cumpleaños</p>
        </div>
        <a href="{{ route('alertas.configuracion.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Config. Alertas
        </a>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-envelope-check text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['enviados_hoy'] }}</h3>
                            <small class="text-muted">Enviados Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['enviados_mes'] }}</h3>
                            <small class="text-muted">Enviados Este Mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-graph-up text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['enviados_anio'] }}</h3>
                            <small class="text-muted">Enviados Este Año</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['fallidos_mes'] }}</h3>
                            <small class="text-muted">Fallidos Este Mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Toggle Activar/Desactivar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Envío automático de emails de cumpleaños</h6>
                        <small class="text-muted">Se ejecuta diariamente a las {{ $config->hora_envio }}h</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toggleActiva"
                               {{ $config->activa ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                        <label class="form-check-label ms-2 fw-semibold" for="toggleActiva" id="toggleLabel">
                            {{ $config->activa ? 'Activado' : 'Desactivado' }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Formulario Plantilla -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Plantilla del Email</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cumpleanos.configuracion.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="asunto" class="form-label fw-semibold">Asunto del email</label>
                            <input type="text" class="form-control @error('asunto') is-invalid @enderror"
                                   id="asunto" name="asunto" value="{{ old('asunto', $config->asunto) }}"
                                   placeholder="Ej: ¡Feliz Cumpleaños, {nombre}!">
                            @error('asunto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cuerpo" class="form-label fw-semibold">Cuerpo del email (HTML)</label>
                            <textarea class="form-control @error('cuerpo') is-invalid @enderror"
                                      id="cuerpo" name="cuerpo" rows="10"
                                      placeholder="Escribe el contenido del email...">{{ old('cuerpo', $config->cuerpo) }}</textarea>
                            @error('cuerpo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Puedes usar HTML para dar formato. El header y footer corporativos se añaden automáticamente.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="hora_envio" class="form-label fw-semibold">Hora de envío</label>
                            <input type="time" class="form-control @error('hora_envio') is-invalid @enderror"
                                   id="hora_envio" name="hora_envio" value="{{ old('hora_envio', $config->hora_envio) }}"
                                   style="max-width: 150px;">
                            @error('hora_envio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Guardar Plantilla
                        </button>
                    </form>
                </div>
            </div>

            <!-- Adjunto -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-paperclip me-2"></i>Archivo Adjunto</h6>
                </div>
                <div class="card-body">
                    @if($config->adjunto_path)
                        <div class="d-flex align-items-center justify-content-between bg-light rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-center">
                                @php
                                    $ext = pathinfo($config->adjunto_nombre_original, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                                @endphp
                                @if($isImage)
                                    <img src="{{ asset($config->adjunto_path) }}" alt="Preview"
                                         class="rounded me-3" style="max-height: 60px; max-width: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-danger bg-opacity-10 rounded-3 p-2 me-3">
                                        <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $config->adjunto_nombre_original }}</p>
                                    <small class="text-muted">Se adjuntará a cada email de cumpleaños</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnEliminarAdjunto">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('cumpleanos.configuracion.adjunto.subir') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" class="form-control @error('adjunto') is-invalid @enderror"
                                   name="adjunto" accept=".jpg,.jpeg,.png,.gif,.pdf">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-upload me-1"></i>Subir
                            </button>
                        </div>
                        @error('adjunto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formatos: JPG, PNG, GIF, PDF. Máximo 5MB.</div>
                    </form>
                </div>
            </div>

            <!-- Enviar Prueba -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-send me-2"></i>Enviar Email de Prueba</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cumpleanos.configuracion.prueba') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="email" class="form-control @error('email_prueba') is-invalid @enderror"
                                   name="email_prueba" placeholder="correo@ejemplo.com"
                                   value="{{ old('email_prueba') }}">
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-send me-1"></i>Enviar Prueba
                            </button>
                        </div>
                        @error('email_prueba')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Se enviará un email de prueba con datos ficticios a la dirección indicada.</div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Placeholders -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-braces me-2"></i>Placeholders Disponibles</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Usa estos marcadores en el asunto y cuerpo del email. Se reemplazarán automáticamente con los datos del trabajador.</p>
                    <table class="table table-sm mb-0">
                        <tbody>
                            @foreach($placeholders as $placeholder => $descripcion)
                            <tr>
                                <td><code>{{ $placeholder }}</code></td>
                                <td class="small text-muted">{{ $descripcion }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Log Reciente -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Últimos Envíos</h6>
                </div>
                <div class="card-body p-0">
                    @if($logReciente->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($logReciente as $log)
                            <div class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="mb-0 small fw-semibold text-truncate" style="max-width: 200px;">
                                            {{ $log->destinatario_email }}
                                        </p>
                                        <small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($log->estado === 'enviado')
                                        <span class="badge bg-success-subtle text-success">Enviado</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger" title="{{ $log->error_message }}">Fallido</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-envelope fs-2 d-block mb-2 opacity-50"></i>
                            <small>No hay envíos registrados</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle activar/desactivar
    const toggleActiva = document.getElementById('toggleActiva');
    const toggleLabel = document.getElementById('toggleLabel');

    if (toggleActiva) {
        toggleActiva.addEventListener('change', function() {
            fetch('{{ route('cumpleanos.configuracion.toggle') }}', {
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
                    toggleLabel.textContent = data.activa ? 'Activado' : 'Desactivado';
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            })
            .catch(error => {
                toggleActiva.checked = !toggleActiva.checked;
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
    }

    // Eliminar adjunto
    const btnEliminar = document.getElementById('btnEliminarAdjunto');
    if (btnEliminar) {
        btnEliminar.addEventListener('click', function() {
            Swal.fire({
                title: '¿Eliminar adjunto?',
                text: 'El archivo adjunto será eliminado permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('cumpleanos.configuracion.adjunto.eliminar') }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Adjunto eliminado',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al eliminar',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                }
            });
        });
    }
});
</script>
@endpush
@endsection
