@extends('layouts.app')

@section('title', 'Recordatorios de Fichaje')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Recordatorios de Fichaje</h1>
            <p class="text-muted mb-0">Avisa a los trabajadores (email + dentro del CRM) para que fichen</p>
        </div>
        <a href="{{ route('fichajes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Volver a fichajes</a>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card border-0 shadow-sm" style="max-width: 660px;">
        <div class="card-body">
            <form action="{{ route('fichajes.configuracion.guardar') }}" method="POST">
                @csrf
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ $config->activo ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="activo">Activar recordatorios automáticos</label>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Hora de recordatorio de ENTRADA</label>
                        <input type="time" name="hora_entrada" class="form-control" value="{{ $config->horaEntradaCorta() }}" required>
                        <small class="text-muted">Se avisa a quien aún no fichó la entrada.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora de recordatorio de SALIDA</label>
                        <input type="time" name="hora_salida" class="form-control" value="{{ $config->horaSalidaCorta() }}" required>
                        <small class="text-muted">Se avisa a quien fichó entrada pero no salida.</small>
                    </div>
                </div>

                <div class="alert alert-info mt-3 small">
                    <i class="bi bi-info-circle me-1"></i>El aviso llega por <strong>email</strong> y como <strong>notificación dentro del CRM</strong> (campanita) al trabajador.
                    Requiere que el trabajador tenga usuario con email. La automatización diaria necesita el cron de Laravel activo en el servidor.
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar configuración</button>
            </form>
        </div>
    </div>
</div>
@endsection
