@extends('layouts.app')

@section('title', $cliente->nombre_comercial)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-{{ $cliente->tipo === 'publico' ? 'info' : 'warning' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                 style="width: 60px; height: 60px;">
                <i class="bi bi-{{ $cliente->tipo === 'publico' ? 'bank' : 'briefcase' }} fs-4"></i>
            </div>
            <div>
                <h1 class="h3 mb-1">{{ $cliente->nombre_comercial }}</h1>
                <p class="text-muted mb-0">
                    @if($cliente->tipo === 'publico')
                        <span class="badge bg-info-subtle text-info me-2">Público</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning me-2">Privado</span>
                    @endif
                    @if($cliente->cif)
                        <span class="me-2">CIF: {{ $cliente->cif }}</span>
                    @endif
                    @if($cliente->activo)
                        <span class="badge bg-success-subtle text-success">Activo</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            @can('editar_clientes')
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endcan
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ $stats['total_obras'] }}</h3>
                    <small class="text-muted">Total Obras</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success">{{ $stats['obras_activas'] }}</h3>
                    <small class="text-muted">Obras Activas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info">{{ $stats['total_facturas'] }}</h3>
                    <small class="text-muted">Facturas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-warning">{{ $stats['facturas_pendientes'] }}</h3>
                    <small class="text-muted">Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-primary">{{ number_format($stats['importe_facturado'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Facturado</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger">{{ number_format($stats['importe_pendiente'], 0, ',', '.') }}€</h3>
                    <small class="text-muted">Por Cobrar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda - Información del Cliente -->
        <div class="col-lg-4">
            <!-- Datos Generales -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Datos de la Empresa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        @if($cliente->razon_social)
                        <tr>
                            <td class="text-muted" style="width: 40%">Razón Social</td>
                            <td>{{ $cliente->razon_social }}</td>
                        </tr>
                        @endif
                        @if($cliente->cif)
                        <tr>
                            <td class="text-muted">CIF</td>
                            <td>{{ $cliente->cif }}</td>
                        </tr>
                        @endif
                        @if($cliente->telefono)
                        <tr>
                            <td class="text-muted">Teléfono</td>
                            <td><a href="tel:{{ $cliente->telefono }}">{{ $cliente->telefono }}</a></td>
                        </tr>
                        @endif
                        @if($cliente->email)
                        <tr>
                            <td class="text-muted">Email</td>
                            <td><a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Dirección -->
            @if($cliente->direccion || $cliente->ciudad || $cliente->provincia)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Dirección</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        @if($cliente->direccion){{ $cliente->direccion }}<br>@endif
                        @if($cliente->codigo_postal || $cliente->ciudad)
                            {{ $cliente->codigo_postal }} {{ $cliente->ciudad }}<br>
                        @endif
                        @if($cliente->provincia){{ $cliente->provincia }}@endif
                        @if($cliente->pais && $cliente->pais !== 'España'), {{ $cliente->pais }}@endif
                    </p>
                </div>
            </div>
            @endif

            <!-- Persona de Contacto -->
            @if($cliente->persona_contacto)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Persona de Contacto</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Nombre</td>
                            <td>{{ $cliente->persona_contacto }}</td>
                        </tr>
                        @if($cliente->telefono_contacto)
                        <tr>
                            <td class="text-muted">Teléfono</td>
                            <td><a href="tel:{{ $cliente->telefono_contacto }}">{{ $cliente->telefono_contacto }}</a></td>
                        </tr>
                        @endif
                        @if($cliente->email_contacto)
                        <tr>
                            <td class="text-muted">Email</td>
                            <td><a href="mailto:{{ $cliente->email_contacto }}">{{ $cliente->email_contacto }}</a></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            <!-- Condiciones Comerciales -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Condiciones Comerciales</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Pago</td>
                            <td>{{ $cliente->condiciones_pago ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Retención</td>
                            <td>{{ number_format($cliente->retencion_porcentaje ?? 0, 2, ',', '.') }}%</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Notas -->
            @if($cliente->notas)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $cliente->notas }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Derecha - Tabs con contenido -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm content-tabs-card">
                <div class="card-header bg-transparent tabs-header">
                    <ul class="nav nav-pills" id="clienteTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="obras-tab" data-bs-toggle="tab" data-bs-target="#obras"
                                    type="button" role="tab">
                                <i class="bi bi-building me-1"></i>Obras
                                @if($cliente->obras->count() > 0)
                                    <span class="tab-badge">{{ $cliente->obras->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="facturas-tab" data-bs-toggle="tab" data-bs-target="#facturas"
                                    type="button" role="tab">
                                <i class="bi bi-receipt me-1"></i>Facturas
                                @if($cliente->facturas->count() > 0)
                                    <span class="tab-badge">{{ $cliente->facturas->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="interacciones-tab" data-bs-toggle="tab" data-bs-target="#interacciones"
                                    type="button" role="tab">
                                <i class="bi bi-chat-dots me-1"></i>Interacciones
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tabs-content">
                    <div class="tab-content" id="clienteTabsContent">
                        <!-- Tab Obras -->
                        <div class="tab-pane fade show active" id="obras" role="tabpanel">
                            @if($cliente->obras->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Estado</th>
                                                <th>Presupuesto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cliente->obras as $obra)
                                            <tr>
                                                <td><code>{{ $obra->codigo }}</code></td>
                                                <td>{{ $obra->nombre }}</td>
                                                <td>
                                                    @php
                                                        $estadoColors = [
                                                            'presentada' => 'secondary',
                                                            'aprobada' => 'info',
                                                            'en_curso' => 'primary',
                                                            'pausada' => 'warning',
                                                            'finalizada' => 'success',
                                                            'cancelada' => 'danger',
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $estadoColors[$obra->estado] ?? 'secondary' }}-subtle text-{{ $estadoColors[$obra->estado] ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $obra->estado)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $obra->presupuesto ? number_format($obra->presupuesto, 2, ',', '.') . ' €' : '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay obras registradas para este cliente</p>
                                </div>
                            @endif
                        </div>

                        <!-- Tab Facturas -->
                        <div class="tab-pane fade" id="facturas" role="tabpanel">
                            @if($cliente->facturas->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Número</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cliente->facturas as $factura)
                                            <tr>
                                                <td><code>{{ $factura->numero }}</code></td>
                                                <td>{{ $factura->fecha_emision?->format('d/m/Y') }}</td>
                                                <td>{{ number_format($factura->total, 2, ',', '.') }} €</td>
                                                <td>
                                                    @php
                                                        $estadoFacturaColors = [
                                                            'borrador' => 'secondary',
                                                            'emitida' => 'info',
                                                            'enviada' => 'primary',
                                                            'cobrada' => 'success',
                                                            'anulada' => 'danger',
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $estadoFacturaColors[$factura->estado] ?? 'secondary' }}-subtle text-{{ $estadoFacturaColors[$factura->estado] ?? 'secondary' }}">
                                                        {{ ucfirst($factura->estado) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay facturas registradas para este cliente</p>
                                </div>
                            @endif
                        </div>

                        <!-- Tab Interacciones -->
                        <div class="tab-pane fade" id="interacciones" role="tabpanel">
                            <!-- Botón Nueva Interacción -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nuevaInteraccionModal">
                                    <i class="bi bi-plus-lg me-1"></i>Nueva Interacción
                                </button>
                            </div>

                            @if($cliente->interacciones->count() > 0)
                                <div class="timeline">
                                    @foreach($cliente->interacciones as $interaccion)
                                    <div class="d-flex mb-3">
                                        <div class="me-3">
                                            @php
                                                $tipoIcons = [
                                                    'llamada' => 'telephone',
                                                    'email' => 'envelope',
                                                    'reunion' => 'people',
                                                    'visita' => 'geo-alt',
                                                    'otro' => 'chat-dots',
                                                ];
                                            @endphp
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-{{ $tipoIcons[$interaccion->tipo] ?? 'chat-dots' }} text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong class="text-capitalize">{{ $interaccion->tipo }}</strong>
                                                    <small class="text-muted ms-2">{{ $interaccion->fecha?->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                            <p class="mb-1">{{ $interaccion->descripcion }}</p>
                                            @if($interaccion->proximo_paso)
                                                <small class="text-info">
                                                    <i class="bi bi-arrow-right-circle me-1"></i>
                                                    Próximo: {{ $interaccion->proximo_paso }}
                                                    @if($interaccion->fecha_proximo_contacto)
                                                        ({{ $interaccion->fecha_proximo_contacto->format('d/m/Y') }})
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay interacciones registradas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Interacción -->
<div class="modal fade" id="nuevaInteraccionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('clientes.interacciones.store', $cliente) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Interacción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select" required>
                                <option value="llamada">Llamada</option>
                                <option value="email">Email</option>
                                <option value="reunion">Reunión</option>
                                <option value="visita">Visita</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" class="form-control" required
                                   value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea name="descripcion" class="form-control" rows="3" required
                                      placeholder="Describe el contenido de la interacción..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Próximo Paso</label>
                            <input type="text" name="proximo_paso" class="form-control"
                                   placeholder="¿Qué acción realizar a continuación?">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Fecha Próximo Contacto</label>
                            <input type="date" name="fecha_proximo_contacto" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Interacción</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Content Tabs - Fix for CSS conflicts */
.content-tabs-card .tab-content,
#clienteTabsContent.tab-content {
    display: block !important;
}

#clienteTabsContent .tab-pane {
    display: none !important;
}

#clienteTabsContent .tab-pane.active {
    display: block !important;
}

.tabs-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, rgba(74, 124, 89, 0.05) 0%, rgba(74, 124, 89, 0.1) 100%);
    border-bottom: 1px solid #e5e7eb;
}

.tabs-header .nav-pills {
    gap: 0.5rem;
}

.tabs-header .nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 10px;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.2s;
    background: transparent;
}

.tabs-header .nav-link:hover {
    background: rgba(74, 124, 89, 0.1);
    color: #4A7C59;
}

.tabs-header .nav-link.active {
    background: #4A7C59;
    color: white;
}

.tab-badge {
    background: rgba(0, 0, 0, 0.1);
    padding: 0.15rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.nav-link.active .tab-badge {
    background: rgba(255, 255, 255, 0.2);
}

.tabs-content {
    padding: 1.5rem;
    min-height: 300px;
    background: #fff;
}
</style>
@endpush

@push('scripts')
<script>
// Inicializar tabs manualmente
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#clienteTabs .nav-link');
    const tabPanes = document.querySelectorAll('#clienteTabsContent .tab-pane');

    tabButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Remover active de todos los botones
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Agregar active al botón clickeado
            this.classList.add('active');

            // Obtener el target
            const targetId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);

            // Ocultar todos los panes
            tabPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Mostrar el pane objetivo
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });
});
</script>
@endpush
@endsection
