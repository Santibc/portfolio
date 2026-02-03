@extends('layouts.app')

@section('title', 'Contrato: ' . $contrato->codigo)

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center mb-1">
                <h1 class="h3 mb-0 me-3">{{ $contrato->codigo }}</h1>
                <span class="badge bg-{{ $contrato->estado_badge }} fs-6">
                    {{ \App\Models\Contrato::ESTADOS[$contrato->estado] }}
                </span>
            </div>
            <p class="text-muted mb-0">{{ $contrato->titulo }}</p>
        </div>
        <div>
            @role('Administrador|Contabilidad')
            <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            @endrole
            <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    {{-- Alertas de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Contenido principal --}}
        <div class="col-lg-8">
            {{-- Información del contrato --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Información del Contrato</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tipo de Contrato</label>
                            <p class="mb-0 fw-semibold">{{ $contrato->tipo->nombre ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Código</label>
                            <p class="mb-0 fw-semibold">{{ $contrato->codigo }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Título</label>
                            <p class="mb-0">{{ $contrato->titulo }}</p>
                        </div>
                        @if($contrato->descripcion)
                        <div class="col-12">
                            <label class="form-label text-muted small">Descripción</label>
                            <p class="mb-0">{{ $contrato->descripcion }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Parte contratante --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Parte Contratante</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Tipo</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $contrato->cliente_id ? 'success' : ($contrato->subcontrata_id ? 'info' : 'secondary') }}">
                                    {{ $contrato->parte_tipo }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-muted small">Nombre</label>
                            <p class="mb-0 fw-semibold">
                                @if($contrato->cliente)
                                    <a href="{{ route('clientes.show', $contrato->cliente) }}" class="text-decoration-none">
                                        {{ $contrato->parte_nombre }}
                                    </a>
                                @elseif($contrato->subcontrata)
                                    <a href="{{ route('subcontratas.show', $contrato->subcontrata) }}" class="text-decoration-none">
                                        {{ $contrato->parte_nombre }}
                                    </a>
                                @else
                                    {{ $contrato->parte_nombre }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Fechas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Firma</label>
                            <p class="mb-0">{{ $contrato->fecha_firma ? $contrato->fecha_firma->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Inicio</label>
                            <p class="mb-0">{{ $contrato->fecha_inicio ? $contrato->fecha_inicio->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Fin</label>
                            <p class="mb-0">
                                {{ $contrato->fecha_fin ? $contrato->fecha_fin->format('d/m/Y') : '-' }}
                                @if($contrato->proximo_a_vencer)
                                    <span class="badge bg-warning text-dark ms-2">
                                        <i class="bi bi-exclamation-triangle"></i> Vence en {{ $contrato->dias_para_vencer }} días
                                    </span>
                                @elseif($contrato->vencido && $contrato->estado == 'activo')
                                    <span class="badge bg-danger ms-2">
                                        <i class="bi bi-x-circle"></i> Vencido
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información económica --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-currency-euro me-2"></i>Información Económica</h5>
                </div>
                <div class="card-body">
                    <div class="bg-light rounded p-4">
                        <div class="row">
                            <div class="col-md-4 text-center border-end">
                                <label class="form-label text-muted small d-block">Base Imponible</label>
                                <span class="fs-4 fw-bold">{{ number_format($contrato->importe ?? 0, 2, ',', '.') }} €</span>
                            </div>
                            <div class="col-md-4 text-center border-end">
                                <label class="form-label text-muted small d-block">IVA ({{ $contrato->iva_porcentaje }}%)</label>
                                <span class="fs-4">{{ number_format($contrato->importe_iva, 2, ',', '.') }} €</span>
                            </div>
                            <div class="col-md-4 text-center">
                                <label class="form-label text-muted small d-block">Total</label>
                                <span class="fs-4 fw-bold text-success">{{ number_format($contrato->importe_total, 2, ',', '.') }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Retención de garantía --}}
            @if($contrato->tiene_retencion)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Retención de Garantía</h5>
                    <span class="badge bg-{{ $contrato->garantia_badge }} fs-6">
                        {{ \App\Models\Contrato::ESTADOS_GARANTIA[$contrato->estado_garantia] ?? 'Pendiente' }}
                    </span>
                </div>
                <div class="card-body">
                    {{-- Información general --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Porcentaje Retención</label>
                            <p class="mb-0 fw-semibold">{{ $contrato->retencion_porcentaje }}%</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Importe Retenido</label>
                            <p class="mb-0 fw-semibold text-warning">{{ number_format($contrato->importe_retenido, 2, ',', '.') }} €</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Liberación Prevista</label>
                            <p class="mb-0">{{ $contrato->fecha_liberacion_garantia ? $contrato->fecha_liberacion_garantia->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Liberación Completa</label>
                            <p class="mb-0">
                                @if($contrato->fecha_liberacion_real)
                                    <span class="text-success fw-semibold">{{ $contrato->fecha_liberacion_real->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-muted">Pendiente</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Barra de progreso --}}
                    @if($contrato->porcentaje_total_liberado > 0)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Progreso de liberación</span>
                            <span class="fw-semibold">{{ $contrato->porcentaje_total_liberado }}% liberado</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ $contrato->porcentaje_total_liberado }}%"
                                 aria-valuenow="{{ $contrato->porcentaje_total_liberado }}"
                                 aria-valuemin="0" aria-valuemax="100">
                                {{ $contrato->porcentaje_total_liberado }}%
                            </div>
                        </div>
                        <div class="row mt-3 text-center">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Liberado</small>
                                <span class="text-success fw-bold">{{ number_format($contrato->importe_total_liberado, 2, ',', '.') }} €</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Pendiente</small>
                                <span class="text-warning fw-bold">{{ number_format($contrato->importe_pendiente_liberar, 2, ',', '.') }} €</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">% Restante</small>
                                <span class="fw-bold">{{ $contrato->porcentaje_pendiente_liberar }}%</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Historial de liberaciones --}}
                    @if($contrato->liberaciones->count() > 0)
                    <div class="border-top pt-4 mb-4">
                        <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>Historial de Liberaciones</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="text-end">Porcentaje</th>
                                        <th class="text-end">Importe</th>
                                        <th>Usuario</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contrato->liberaciones as $liberacion)
                                    <tr>
                                        <td>{{ $liberacion->fecha_liberacion->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ $liberacion->porcentaje_liberado }}%</span>
                                        </td>
                                        <td class="text-end">{{ number_format($liberacion->importe_liberado, 2, ',', '.') }} €</td>
                                        <td>{{ $liberacion->usuario->name ?? 'Sistema' }}</td>
                                        <td><small>{{ $liberacion->notas ?? '-' }}</small></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Botón de liberar garantía --}}
                    @if($contrato->porcentaje_pendiente_liberar > 0)
                        @role('Administrador|Contabilidad')
                        <div class="pt-3 border-top">
                            <button type="button" class="btn btn-success" onclick="liberarGarantia()">
                                <i class="bi bi-unlock me-2"></i>Liberar Garantía
                                @if($contrato->porcentaje_total_liberado > 0)
                                    ({{ $contrato->porcentaje_pendiente_liberar }}% restante)
                                @endif
                            </button>
                        </div>
                        @endrole
                    @endif
                </div>
            </div>
            @endif

            {{-- Obras vinculadas --}}
            @if($contrato->obras->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Obras Vinculadas</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contrato->obras as $obra)
                            <tr>
                                <td><a href="{{ route('obras.show', $obra) }}">{{ $obra->codigo }}</a></td>
                                <td>{{ Str::limit($obra->nombre, 40) }}</td>
                                <td>
                                    <span class="badge bg-{{ $obra->estado_badge ?? 'secondary' }}">
                                        {{ ucfirst($obra->estado) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('obras.show', $obra) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Notas --}}
            @if($contrato->notas)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>Notas</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $contrato->notas }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Estado y acciones --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <span class="badge bg-{{ $contrato->estado_badge }} fs-5 px-4 py-2">
                            {{ \App\Models\Contrato::ESTADOS[$contrato->estado] }}
                        </span>
                    </div>

                    @role('Administrador|Contabilidad')
                    <div class="d-grid gap-2">
                        @if($contrato->estado == 'borrador')
                            <button type="button" class="btn btn-success" onclick="activarContrato()">
                                <i class="bi bi-check-circle me-2"></i>Activar Contrato
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="cancelarContrato()">
                                <i class="bi bi-x-circle me-2"></i>Cancelar Contrato
                            </button>
                        @elseif($contrato->estado == 'activo')
                            <button type="button" class="btn btn-outline-warning" onclick="marcarVencido()">
                                <i class="bi bi-clock-history me-2"></i>Marcar como Vencido
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="cancelarContrato()">
                                <i class="bi bi-x-circle me-2"></i>Cancelar Contrato
                            </button>
                        @elseif($contrato->estado == 'vencido')
                            <button type="button" class="btn btn-outline-success" onclick="reactivarContrato()">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Reactivar Contrato
                            </button>
                        @endif
                    </div>
                    @endrole
                </div>
            </div>

            {{-- Documento --}}
            @if($contrato->documento_path)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-pdf me-2"></i>Documento</h6>
                </div>
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 3rem;"></i>
                    <p class="text-muted small mt-2 mb-3">Documento del contrato</p>
                    <a href="{{ asset($contrato->documento_path) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i>Ver Documento
                    </a>
                </div>
            </div>
            @endif

            {{-- Opciones --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Opciones</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Renovación automática</span>
                        @if($contrato->renovacion_automatica)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Preaviso vencimiento</span>
                        <span>{{ $contrato->dias_preaviso_vencimiento ?? 30 }} días</span>
                    </div>
                </div>
            </div>

            {{-- Información del registro --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Registro</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Creado</span>
                        <span>{{ $contrato->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Actualizado</span>
                        <span>{{ $contrato->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function activarContrato() {
        Swal.fire({
            title: '¿Activar contrato?',
            text: 'El contrato pasará de Borrador a Activo.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                await cambiarEstado('activar');
            }
        });
    }

    function cancelarContrato() {
        Swal.fire({
            title: '¿Cancelar contrato?',
            text: 'El contrato se marcará como Cancelado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No',
        }).then(async (result) => {
            if (result.isConfirmed) {
                await cambiarEstado('cancelar');
            }
        });
    }

    function marcarVencido() {
        Swal.fire({
            title: '¿Marcar como vencido?',
            text: 'El contrato se marcará como Vencido.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, marcar vencido',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                await cambiarEstado('marcar-vencido');
            }
        });
    }

    function reactivarContrato() {
        Swal.fire({
            title: '¿Reactivar contrato?',
            text: 'El contrato volverá al estado Activo.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                await cambiarEstado('reactivar');
            }
        });
    }

    async function cambiarEstado(accion) {
        try {
            const response = await fetch(`{{ url('contratos/' . $contrato->id) }}/${accion}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => window.location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    }

    function liberarGarantia() {
        const porcentajePendiente = {{ $contrato->porcentaje_pendiente_liberar ?? 100 }};
        const importeRetenido = {{ $contrato->importe_retenido ?? 0 }};
        const importePendiente = {{ $contrato->importe_pendiente_liberar ?? $contrato->importe_retenido ?? 0 }};

        Swal.fire({
            title: 'Liberar Garantía',
            html: `
                <div class="text-start">
                    <div class="alert alert-info mb-3">
                        <strong>Información de la garantía:</strong><br>
                        <small>Importe total retenido: <strong>${new Intl.NumberFormat('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(importeRetenido)} €</strong></small><br>
                        <small>Pendiente de liberar: <strong>${new Intl.NumberFormat('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(importePendiente)} €</strong> (${porcentajePendiente}%)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Porcentaje a liberar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" id="porcentajeLiberacion" class="form-control"
                                   value="${porcentajePendiente}" min="1" max="${porcentajePendiente}" step="1">
                            <span class="input-group-text">%</span>
                        </div>
                        <input type="range" class="form-range mt-2" id="porcentajeSlider"
                               min="1" max="${porcentajePendiente}" step="1" value="${porcentajePendiente}">
                        <small class="text-muted">Importe a liberar: <span id="importeCalculado" class="fw-bold">0.00</span> €</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha de liberación <span class="text-danger">*</span></label>
                        <input type="date" id="fechaLiberacion" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas / Motivo</label>
                        <textarea id="notasLiberacion" class="form-control" rows="3"
                                  placeholder="Ej: Primera fase completada, liberación según contrato, etc."></textarea>
                    </div>
                </div>
            `,
            width: '600px',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Liberar',
            cancelButtonText: 'Cancelar',
            didOpen: () => {
                const inputPorcentaje = document.getElementById('porcentajeLiberacion');
                const slider = document.getElementById('porcentajeSlider');
                const spanImporte = document.getElementById('importeCalculado');

                function actualizarImporte() {
                    const porcentaje = parseInt(inputPorcentaje.value) || 0;
                    const importe = (importeRetenido * porcentaje / 100);
                    spanImporte.textContent = new Intl.NumberFormat('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(importe);
                }

                inputPorcentaje.addEventListener('input', (e) => {
                    slider.value = e.target.value;
                    actualizarImporte();
                });

                slider.addEventListener('input', (e) => {
                    inputPorcentaje.value = e.target.value;
                    actualizarImporte();
                });

                actualizarImporte();
            },
            preConfirm: () => {
                const porcentaje = parseInt(document.getElementById('porcentajeLiberacion').value);
                const fecha = document.getElementById('fechaLiberacion').value;
                const notas = document.getElementById('notasLiberacion').value;

                if (!porcentaje || porcentaje <= 0) {
                    Swal.showValidationMessage('Debe especificar un porcentaje válido');
                    return false;
                }
                if (porcentaje > porcentajePendiente) {
                    Swal.showValidationMessage(`Solo puede liberar hasta ${porcentajePendiente}%`);
                    return false;
                }
                if (!fecha) {
                    Swal.showValidationMessage('Debe especificar una fecha');
                    return false;
                }

                return { porcentaje, fecha, notas };
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch('{{ route('contratos.liberar-garantia', $contrato) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            porcentaje: result.value.porcentaje,
                            fecha_liberacion: result.value.fecha,
                            notas: result.value.notas || null,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Garantía liberada',
                            html: `
                                <p><strong>Porcentaje liberado:</strong> ${result.value.porcentaje}%</p>
                                <p><strong>Importe liberado:</strong> ${data.importe_liberado} €</p>
                                <p><strong>Total liberado:</strong> ${data.porcentaje_total}%</p>
                                ${data.porcentaje_pendiente > 0 ? `<p><strong>Restante:</strong> ${data.porcentaje_pendiente}%</p>` : '<p class="text-success">¡Garantía liberada completamente!</p>'}
                            `,
                            timer: 3000,
                            showConfirmButton: true,
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            }
        });
    }
</script>
@endpush
