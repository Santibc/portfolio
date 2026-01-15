@extends('layouts.app')

@section('title', 'Configuración de Puntos')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-gear"></i> Configuración del Sistema de Puntos
            </h1>
            <p class="text-muted mb-0">Personaliza las reglas de acumulación y canje de puntos</p>
        </div>
        <a href="{{ route('admin.puntos.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.puntos.configuracion.guardar') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Configuración General -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-toggles"></i> Configuración General
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="sistema_activo" id="sistema_activo" value="1" {{ $config->sistema_activo ? 'checked' : '' }}>
                            <label class="form-check-label" for="sistema_activo">
                                <strong>Sistema de Puntos Activo</strong>
                                <br>
                                <small class="text-muted">Activar o desactivar todo el sistema de puntos</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="referidos_activo" id="referidos_activo" value="1" {{ $config->referidos_activo ? 'checked' : '' }}>
                            <label class="form-check-label" for="referidos_activo">
                                <strong>Sistema de Referidos Activo</strong>
                                <br>
                                <small class="text-muted">Permitir que los clientes refieran a otros</small>
                            </label>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-clock"></i> Expiración de Puntos (meses)
                            </label>
                            <input type="number" name="meses_expiracion" class="form-control" value="{{ $config->meses_expiracion }}" min="1" required>
                            <small class="text-muted">Los puntos ganados expirarán después de este período</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acumulación de Puntos -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-arrow-up-circle text-success"></i> Acumulación de Puntos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-cart"></i> Puntos por Monto de Compra
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">1 punto cada $</span>
                                <input type="number" name="puntos_por_monto" class="form-control" value="{{ $config->puntos_por_monto }}" min="1" required>
                            </div>
                            <small class="text-muted">Ej: 100 = 1 punto por cada $100 gastados</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-person-plus text-success"></i> Puntos por Registro
                            </label>
                            <input type="number" name="puntos_registro" class="form-control" value="{{ $config->puntos_registro }}" min="0" required>
                            <small class="text-muted">Puntos de bienvenida al crear cuenta</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-bag-check text-info"></i> Puntos por Primera Compra
                            </label>
                            <input type="number" name="puntos_primera_compra" class="form-control" value="{{ $config->puntos_primera_compra }}" min="0" required>
                            <small class="text-muted">Bonus extra en la primera compra</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-people text-warning"></i> Puntos por Referir un Amigo
                            </label>
                            <input type="number" name="puntos_referir" class="form-control" value="{{ $config->puntos_referir }}" min="0" required>
                            <small class="text-muted">Puntos para quien invita a alguien</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-person-check text-primary"></i> Puntos por Ser Referido
                            </label>
                            <input type="number" name="puntos_ser_referido" class="form-control" value="{{ $config->puntos_ser_referido }}" min="0" required>
                            <small class="text-muted">Puntos para quien es invitado</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canje de Puntos -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-arrow-down-circle text-danger"></i> Canje de Puntos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-cash text-success"></i> Tasa de Conversión
                                </label>
                                <div class="input-group">
                                    <input type="number" name="puntos_por_peso" class="form-control" value="{{ $config->puntos_por_peso }}" min="1" required>
                                    <span class="input-group-text">puntos = $1</span>
                                </div>
                                <small class="text-muted">Ej: 100 puntos = $1 de descuento</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-arrow-down"></i> Canje Mínimo
                                </label>
                                <div class="input-group">
                                    <input type="number" name="canje_minimo" class="form-control" value="{{ $config->canje_minimo }}" min="1" required>
                                    <span class="input-group-text">puntos</span>
                                </div>
                                <small class="text-muted">Cantidad mínima para poder canjear</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-arrow-up"></i> Canje Máximo
                                </label>
                                <div class="input-group">
                                    <input type="number" name="canje_maximo" class="form-control" value="{{ $config->canje_maximo }}" min="1" placeholder="Sin límite">
                                    <span class="input-group-text">puntos</span>
                                </div>
                                <small class="text-muted">Máximo por compra (vacío = sin límite)</small>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            <strong>Ejemplo con configuración actual:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Cliente compra por $10.000 → Gana {{ floor(10000 / $config->puntos_por_monto) }} puntos</li>
                                <li>Cliente canjea {{ $config->canje_minimo }} puntos → Obtiene ${{ floor($config->canje_minimo / $config->puntos_por_peso) }} de descuento</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.puntos.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Guardar Configuración
            </button>
        </div>
    </form>
</div>
@endsection
