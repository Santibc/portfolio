@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">
            <i class="fas fa-user-circle"></i>
            Mi Perfil
        </h1>
        <p class="page-subtitle">Gestiona tu información personal y preferencias</p>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        {{-- Columna izquierda: Foto de perfil --}}
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-camera me-2"></i>Foto de Perfil
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        @if($user->hasProfilePhoto())
                            <img src="{{ $user->profile_photo_url }}"
                                 alt="Foto de perfil"
                                 class="rounded-circle"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #e5e7eb;">
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 4px solid #e5e7eb;">
                                <span class="text-white" style="font-size: 3rem; font-weight: 600;">{{ $user->initials }}</span>
                            </div>
                        @endif
                    </div>

                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge bg-primary">{{ $user->roles->first()->name ?? 'Usuario' }}</span>

                    <hr class="my-4">

                    {{-- Formulario subir foto --}}
                    <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Cambiar foto</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                            @error('profile_photo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-upload me-2"></i>Subir Foto
                        </button>
                    </form>

                    @if($user->hasProfilePhoto())
                        <form action="{{ route('profile.photo.destroy') }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('¿Eliminar foto de perfil?')">
                                <i class="fas fa-trash me-2"></i>Eliminar Foto
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna derecha: Formularios --}}
        <div class="col-lg-8">
            {{-- Información del perfil --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Información del Perfil
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Actualiza la información de perfil y la dirección de correo electrónico de tu cuenta.</p>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-muted small">
                                        Tu dirección de correo electrónico no ha sido verificada.
                                        <button form="send-verification" class="btn btn-link btn-sm p-0">
                                            Reenviar correo de verificación
                                        </button>
                                    </p>

                                    @if (session('status') === 'verification-link-sent')
                                        <p class="text-success small">
                                            Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>

                        @if (session('status') === 'profile-updated')
                            <span class="text-success ms-3" x-data="{ show: true }" x-show="show" x-transition
                                  x-init="setTimeout(() => show = false, 2000)">
                                <i class="fas fa-check me-1"></i>Guardado
                            </span>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Cambiar contraseña --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lock me-2"></i>Actualizar Contraseña
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Asegúrate de que tu cuenta use una contraseña larga y aleatoria para mantenerte seguro.</p>

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-key me-2"></i>Cambiar Contraseña
                        </button>

                        @if (session('status') === 'password-updated')
                            <span class="text-success ms-3" x-data="{ show: true }" x-show="show" x-transition
                                  x-init="setTimeout(() => show = false, 2000)">
                                <i class="fas fa-check me-1"></i>Guardado
                            </span>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Eliminar cuenta --}}
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Zona de Peligro
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Eliminar Cuenta</h6>
                    <p class="text-muted mb-3">Una vez eliminada tu cuenta, todos tus recursos y datos se eliminarán permanentemente. Antes de eliminarla, descarga cualquier dato o información que desees conservar.</p>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-2"></i>Eliminar Cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal eliminar cuenta --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">¿Eliminar cuenta?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>Una vez eliminada tu cuenta, todos tus recursos y datos se eliminarán permanentemente. Ingresa tu contraseña para confirmar.</p>

                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="delete_password" name="password" placeholder="Tu contraseña">
                        @error('password', 'userDeletion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        modal.show();
    });
</script>
@endpush
@endif
@endsection
