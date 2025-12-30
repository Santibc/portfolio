@extends('cliente.layout')

@section('title', 'Mi Perfil')

@section('header')
    <h2 class="mb-0"><i class="bi bi-person"></i> Mi Perfil</h2>
    <p class="text-muted mb-0">Administra tu información personal</p>
@endsection

@section('content')
<div class="row">
    {{-- Datos Personales --}}
    <div class="col-lg-8">
        <div class="content-card">
            <h5 class="mb-4"><i class="bi bi-person-badge text-primary"></i> Datos Personales</h5>

            <form action="{{ route('cliente.perfil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono', $user->telefono) }}" placeholder="+57 300 123 4567">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                               value="{{ old('fecha_nacimiento', $user->fecha_nacimiento?->format('Y-m-d')) }}">
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Te enviaremos un regalo especial en tu cumpleaños</small>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Cambiar Contraseña --}}
        <div class="content-card">
            <h5 class="mb-4"><i class="bi bi-lock text-primary"></i> Cambiar Contraseña</h5>

            <form action="{{ route('cliente.perfil.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Contraseña actual</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-lock"></i> Cambiar contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Info Lateral --}}
    <div class="col-lg-4">
        <div class="content-card text-center">
            <div class="mb-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=FF00C1&color=fff"
                     class="rounded-circle" width="120" height="120" alt="Avatar">
            </div>
            <h5 class="mb-1">{{ $user->name }}</h5>
            <p class="text-muted mb-3">{{ $user->email }}</p>

            <div class="d-grid gap-2">
                <a href="{{ route('cliente.compras') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-bag"></i> Ver mis pedidos
                </a>
                <a href="{{ route('cliente.direcciones') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-geo-alt"></i> Mis direcciones
                </a>
                <a href="{{ route('cliente.puntos') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-star"></i> Mis puntos
                </a>
            </div>
        </div>

        <div class="content-card">
            <h6 class="text-muted mb-3">Información de la cuenta</h6>
            <ul class="list-unstyled mb-0">
                <li class="mb-2">
                    <small class="text-muted">Miembro desde</small><br>
                    <strong>{{ $user->created_at->format('d M Y') }}</strong>
                </li>
                @if($user->fecha_nacimiento)
                <li class="mb-2">
                    <small class="text-muted">Cumpleaños</small><br>
                    <strong>{{ $user->fecha_nacimiento->format('d M') }}</strong>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection
