@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('auditoria.index') }}">Auditoría</a></li>
            <li class="breadcrumb-item active">Registro #{{ $auditoria->id }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detalle de Registro de Auditoría</h1>
            <p class="text-muted mb-0">Información completa del registro #{{ $auditoria->id }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver al listado
            </a>
            @if($urlRegistro)
            <a href="{{ $urlRegistro }}" class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-up-right me-2"></i>Ver registro original
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información General -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Información General
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="ps-0" style="width: 140px;">ID Registro:</th>
                                <td><code>#{{ $auditoria->id }}</code></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Fecha/Hora:</th>
                                <td>
                                    {{ $auditoria->created_at ? $auditoria->created_at->format('d/m/Y H:i:s') : '-' }}
                                    @if($auditoria->created_at)
                                    <br><small class="text-muted">{{ $auditoria->created_at->diffForHumans() }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">Usuario:</th>
                                <td>
                                    @if($auditoria->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr($auditoria->user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $auditoria->user->name }}</span>
                                            <br><small class="text-muted">{{ $auditoria->user->email }}</small>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-muted">Sistema</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">Acción:</th>
                                <td>
                                    @php
                                        $badgeColor = $accionColores[$auditoria->accion] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} fs-6">
                                        @switch($auditoria->accion)
                                            @case('crear')
                                                <i class="bi bi-plus-circle me-1"></i>
                                                @break
                                            @case('editar')
                                                <i class="bi bi-pencil me-1"></i>
                                                @break
                                            @case('eliminar')
                                                <i class="bi bi-trash me-1"></i>
                                                @break
                                            @case('login')
                                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                                @break
                                            @case('logout')
                                                <i class="bi bi-box-arrow-right me-1"></i>
                                                @break
                                            @default
                                                <i class="bi bi-activity me-1"></i>
                                        @endswitch
                                        {{ ucfirst($auditoria->accion) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">Módulo:</th>
                                <td>
                                    <span class="badge bg-secondary fs-6">
                                        {{ $tablaLabels[$auditoria->tabla] ?? ucfirst(str_replace('_', ' ', $auditoria->tabla)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">ID del Registro:</th>
                                <td>
                                    @if($auditoria->registro_id)
                                    <code class="fs-5">#{{ $auditoria->registro_id }}</code>
                                    @else
                                    <span class="text-muted">No aplica</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">Dirección IP:</th>
                                <td>
                                    @if($auditoria->ip_address)
                                    <span class="font-monospace">{{ $auditoria->ip_address }}</span>
                                    @else
                                    <span class="text-muted">No disponible</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="ps-0">User Agent:</th>
                                <td>
                                    @if($auditoria->user_agent)
                                    <small class="text-muted text-break">{{ Str::limit($auditoria->user_agent, 100) }}</small>
                                    @else
                                    <span class="text-muted">No disponible</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Datos Comparativos -->
        <div class="col-lg-7 mb-4">
            @if($auditoria->datos_anteriores || $auditoria->datos_nuevos)
            <div class="row">
                <!-- Datos Anteriores -->
                @if($auditoria->datos_anteriores)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm border-start border-danger border-3">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0 text-danger">
                                <i class="bi bi-dash-circle me-2"></i>Datos Anteriores
                            </h5>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded mb-0" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($auditoria->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Datos Nuevos -->
                @if($auditoria->datos_nuevos)
                <div class="col-12">
                    <div class="card border-0 shadow-sm border-start border-success border-3">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0 text-success">
                                <i class="bi bi-plus-circle me-2"></i>Datos Nuevos
                            </h5>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded mb-0" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($auditoria->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-center text-center text-muted py-5">
                    <div>
                        <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                        <p class="mb-0">No hay datos registrados para esta acción</p>
                        <small>Algunas acciones como login/logout no almacenan datos detallados</small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Comparación de Cambios (si es edición) -->
    @if($auditoria->accion === 'editar' && $auditoria->datos_anteriores && $auditoria->datos_nuevos)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5 class="card-title mb-0">
                <i class="bi bi-arrow-left-right me-2"></i>Resumen de Cambios
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Campo</th>
                            <th>Valor Anterior</th>
                            <th></th>
                            <th>Valor Nuevo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $anteriores = $auditoria->datos_anteriores ?? [];
                            $nuevos = $auditoria->datos_nuevos ?? [];
                            $todosLosCampos = array_unique(array_merge(array_keys($anteriores), array_keys($nuevos)));
                            $camposIgnorados = ['created_at', 'updated_at', 'deleted_at'];
                        @endphp
                        @foreach($todosLosCampos as $campo)
                            @if(!in_array($campo, $camposIgnorados))
                            @php
                                $valorAnterior = $anteriores[$campo] ?? null;
                                $valorNuevo = $nuevos[$campo] ?? null;
                                $cambio = $valorAnterior !== $valorNuevo;
                            @endphp
                            @if($cambio)
                            <tr>
                                <td><code>{{ $campo }}</code></td>
                                <td>
                                    @if(is_array($valorAnterior))
                                        <small class="text-muted">[Array]</small>
                                    @elseif(is_null($valorAnterior))
                                        <span class="text-muted fst-italic">null</span>
                                    @elseif($valorAnterior === '')
                                        <span class="text-muted fst-italic">vacío</span>
                                    @else
                                        <span class="text-danger">{{ Str::limit((string)$valorAnterior, 50) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </td>
                                <td>
                                    @if(is_array($valorNuevo))
                                        <small class="text-muted">[Array]</small>
                                    @elseif(is_null($valorNuevo))
                                        <span class="text-muted fst-italic">null</span>
                                    @elseif($valorNuevo === '')
                                        <span class="text-muted fst-italic">vacío</span>
                                    @else
                                        <span class="text-success fw-medium">{{ Str::limit((string)$valorNuevo, 50) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
