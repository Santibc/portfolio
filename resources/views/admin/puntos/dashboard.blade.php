@extends('layouts.app')

@section('title', 'Dashboard Puntos y Referidos')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-star-fill text-warning"></i> Puntos y Referidos
            </h1>
            <p class="text-muted mb-0">Dashboard de estadísticas y configuración</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.puntos.movimientos') }}" class="btn btn-outline-primary">
                <i class="bi bi-list-ul"></i> Ver Movimientos
            </a>
            <a href="{{ route('admin.puntos.referidos') }}" class="btn btn-outline-info">
                <i class="bi bi-people"></i> Ver Referidos
            </a>
            <a href="{{ route('admin.puntos.configuracion') }}" class="btn btn-primary">
                <i class="bi bi-gear"></i> Configurar
            </a>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 small">Puntos en Circulación</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_puntos_circulacion']) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-arrow-up-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Puntos disponibles para canjear
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 small">Puntos Canjeados</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_puntos_canjeados']) }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="bi bi-arrow-down-circle text-danger fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Total de puntos utilizados
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 small">Total Referidos</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_referidos']) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Clientes registrados con código
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1 small">Puntos por Referidos</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['puntos_por_referidos']) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="bi bi-gift text-primary fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Puntos otorgados por referir
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Top 10 Usuarios -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy text-warning"></i> Top 10 Usuarios con Más Puntos
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Usuario</th>
                                    <th class="text-end">Ganados</th>
                                    <th class="text-end">Canjeados</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topUsuarios as $index => $usuario)
                                <tr>
                                    <td class="text-center">
                                        @if($index < 3)
                                            <span class="badge bg-warning">{{ $index + 1 }}</span>
                                        @else
                                            <span class="text-muted">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $usuario->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $usuario->email }}</small>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success">+{{ number_format($usuario->puntos_ganados) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">-{{ number_format($usuario->puntos_canjeados) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format($usuario->saldo) }}</strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No hay usuarios con puntos aún
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuración Actual -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-gear"></i> Configuración Actual
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Sistema de Puntos</span>
                            @if($config->sistema_activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Sistema de Referidos</span>
                            @if($config->referidos_activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <h6 class="small text-muted mb-2">Acumulación</h6>
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-2">
                            <i class="bi bi-cart text-primary"></i>
                            <strong>Por compra:</strong> 1 punto cada ${{ number_format($config->puntos_por_monto) }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-person-plus text-success"></i>
                            <strong>Registro:</strong> {{ $config->puntos_registro }} puntos
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-bag-check text-info"></i>
                            <strong>Primera compra:</strong> {{ $config->puntos_primera_compra }} puntos
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-people text-warning"></i>
                            <strong>Por referir:</strong> {{ $config->puntos_referir }} puntos
                        </li>
                    </ul>

                    <h6 class="small text-muted mb-2">Canje</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <i class="bi bi-cash text-success"></i>
                            <strong>Tasa:</strong> {{ $config->puntos_por_peso }} puntos = $1
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-arrow-down-circle text-danger"></i>
                            <strong>Mínimo:</strong> {{ $config->canje_minimo }} puntos
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock text-muted"></i>
                            <strong>Expiración:</strong> {{ $config->meses_expiracion }} meses
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('admin.puntos.configuracion') }}" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-pencil"></i> Modificar Configuración
                    </a>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning"></i> Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.puntos.movimientos') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list-ul"></i> Ver Todos los Movimientos
                        </a>
                        <a href="{{ route('admin.puntos.referidos') }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-diagram-3"></i> Análisis de Referidos
                        </a>
                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#ajustarPuntosModal">
                            <i class="bi bi-plus-circle"></i> Ajustar Puntos Manual
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajustar Puntos -->
<div class="modal fade" id="ajustarPuntosModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAjustarPuntos" onsubmit="ajustarPuntos(event)">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i> Ajustar Puntos Manualmente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Usuario *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Seleccione un usuario...</option>
                            @foreach($topUsuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }} ({{ $usuario->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Puntos *</label>
                        <input type="number" name="puntos" class="form-control" required placeholder="Ej: 100 (positivo suma, negativo resta)">
                        <small class="text-muted">Usa números positivos para sumar, negativos para restar</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Concepto *</label>
                        <textarea name="concepto" class="form-control" rows="3" required placeholder="Motivo del ajuste..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Ajustar Puntos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function ajustarPuntos(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    fetch('{{ route("admin.puntos.ajustar") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                confirmButtonColor: '#1a1a1a'
            }).then(() => {
                location.reload();
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al ajustar los puntos',
            confirmButtonColor: '#1a1a1a'
        });
    });
}
</script>
@endpush
@endsection
