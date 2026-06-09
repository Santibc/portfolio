@extends('layouts.app')

@section('title', 'Factura ' . ($factura->numero ?? 'Borrador #' . $factura->id))

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                Factura {{ $factura->numero ?? 'Borrador #' . $factura->id }}
            </h1>
            <p class="text-muted mb-0">
                {{ $factura->cliente?->nombre_comercial }}
                @if($factura->obra)
                    - {{ $factura->obra->codigo }}
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Columna principal --}}
        <div class="col-lg-8">
            {{-- Datos de la factura --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Datos de la Factura</h5>
                    @switch($factura->estado)
                        @case('borrador')
                            <span class="badge bg-secondary fs-6">Borrador</span>
                            @break
                        @case('emitida')
                            <span class="badge bg-primary fs-6">Emitida</span>
                            @break
                        @case('enviada')
                            <span class="badge bg-info fs-6">Enviada</span>
                            @break
                        @case('cobrada')
                            <span class="badge bg-success fs-6">Cobrada</span>
                            @break
                        @case('anulada')
                            <span class="badge bg-danger fs-6">Anulada</span>
                            @break
                    @endswitch
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Número</label>
                            <p class="mb-0 fw-semibold fs-5">{{ $factura->numero ?? 'Sin asignar' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha Emisión</label>
                            <p class="mb-0">{{ $factura->fecha_emision->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha Vencimiento</label>
                            <p class="mb-0">{{ $factura->fecha_vencimiento?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Cliente</label>
                            <p class="mb-0">
                                <a href="{{ route('clientes.show', $factura->cliente) }}" class="text-decoration-none">
                                    {{ $factura->cliente?->nombre_comercial }}
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Obra</label>
                            <p class="mb-0">
                                @if($factura->obra)
                                    <a href="{{ route('obras.show', $factura->obra) }}" class="text-decoration-none">
                                        {{ $factura->obra->codigo }} - {{ $factura->obra->nombre }}
                                    </a>
                                @else
                                    <span class="text-muted">Sin obra asociada</span>
                                @endif
                            </p>
                        </div>
                        @if($factura->estado == 'cobrada' && $factura->fecha_cobro)
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Cobro</label>
                            <p class="mb-0 text-success fw-semibold">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ $factura->fecha_cobro->format('d/m/Y') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Líneas de factura --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>Líneas de Factura</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Dto %</th>
                                    <th class="text-end">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentGrupo = null;
                                    $lineasArray = $factura->lineas->values();
                                @endphp

                                @foreach($lineasArray as $index => $linea)
                                    @php
                                        $nextLinea = $lineasArray[$index + 1] ?? null;
                                        $isNewGroup = ($linea->grupo && $linea->grupo !== $currentGrupo);
                                        $isLastInGroup = ($linea->grupo && (!$nextLinea || $nextLinea->grupo !== $linea->grupo));
                                    @endphp

                                    {{-- Cabecera de grupo --}}
                                    @if($isNewGroup)
                                        @php $currentGrupo = $linea->grupo; @endphp
                                        <tr style="background-color: #e8f5e9;">
                                            <td colspan="5" class="fw-bold">
                                                <i class="bi bi-folder-fill text-success me-2"></i>{{ $currentGrupo }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($factura->lineas->where('grupo', $currentGrupo)->sum('importe'), 2, ',', '.') }} €
                                            </td>
                                        </tr>
                                    @endif

                                    @if(!$linea->grupo && $currentGrupo)
                                        @php $currentGrupo = null; @endphp
                                    @endif

                                    {{-- Línea normal --}}
                                    <tr>
                                        <td @if($linea->grupo) style="padding-left: 30px;" @endif>{{ $linea->concepto }}</td>
                                        <td class="text-muted">{{ $linea->descripcion ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($linea->cantidad, 2, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($linea->precio_unitario, 2, ',', '.') }} €</td>
                                        <td class="text-end">{{ number_format($linea->descuento_porcentaje, 2, ',', '.') }}%</td>
                                        <td class="text-end fw-semibold">{{ number_format($linea->importe, 2, ',', '.') }} €</td>
                                    </tr>

                                    {{-- Subtotal de grupo --}}
                                    @if($linea->grupo && $isLastInGroup)
                                        @php $currentGrupo = null; @endphp
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Notas --}}
            @if($factura->notas)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Notas</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $factura->notas }}</p>
                </div>
            </div>
            @endif

            {{-- Pie de página PDF --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-file-pdf me-2"></i>Pie de Página PDF</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted small">
                        {{ $factura->footer_text ?? \App\Models\Factura::DEFAULT_FOOTER_TEXT }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Columna lateral --}}
        <div class="col-lg-4">
            {{-- Totales --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Totales</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Base Imponible:</span>
                        <span class="fw-semibold">{{ number_format($factura->base_imponible, 2, ',', '.') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>IVA ({{ number_format($factura->iva_porcentaje, 0) }}%):</span>
                        <span>{{ number_format($factura->iva_importe, 2, ',', '.') }} €</span>
                    </div>
                    @if($factura->retencion_porcentaje > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Retención ({{ number_format($factura->retencion_porcentaje, 0) }}%):</span>
                        <span class="text-danger">-{{ number_format($factura->retencion_importe, 2, ',', '.') }} €</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fs-5 fw-bold">TOTAL:</span>
                        <span class="fs-5 fw-bold text-primary">{{ number_format($factura->total, 2, ',', '.') }} €</span>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(in_array($factura->estado, ['emitida','enviada','cobrada']))
                            <button type="button" class="btn btn-outline-primary" onclick="editarNumeroFactura()">
                                <i class="bi bi-hash me-2"></i>Editar Número
                            </button>
                        @endif
                        @switch($factura->estado)
                            @case('borrador')
                                <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>Editar Factura
                                </a>
                                <button type="button" class="btn btn-success" onclick="emitirFactura()">
                                    <i class="bi bi-send me-2"></i>Emitir Factura
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="eliminarFactura()">
                                    <i class="bi bi-trash me-2"></i>Eliminar
                                </button>
                                @break

                            @case('emitida')
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-secondary" target="_blank">
                                    <i class="bi bi-file-pdf me-2"></i>Ver PDF
                                </a>
                                <button type="button" class="btn btn-info text-white" onclick="enviarFactura()">
                                    <i class="bi bi-envelope me-2"></i>Marcar como Enviada
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="anularFactura()">
                                    <i class="bi bi-x-circle me-2"></i>Anular
                                </button>
                                @break

                            @case('enviada')
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-secondary" target="_blank">
                                    <i class="bi bi-file-pdf me-2"></i>Ver PDF
                                </a>
                                <button type="button" class="btn btn-success" onclick="cobrarFactura()">
                                    <i class="bi bi-check-circle me-2"></i>Marcar como Cobrada
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="anularFactura()">
                                    <i class="bi bi-x-circle me-2"></i>Anular
                                </button>
                                @break

                            @case('cobrada')
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-secondary" target="_blank">
                                    <i class="bi bi-file-pdf me-2"></i>Ver PDF
                                </a>
                                <a href="{{ route('facturas.descargar-pdf', $factura) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-download me-2"></i>Descargar PDF
                                </a>
                                @break

                            @case('anulada')
                                @if($factura->pdf_path)
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-secondary" target="_blank">
                                    <i class="bi bi-file-pdf me-2"></i>Ver PDF
                                </a>
                                @endif
                                <div class="alert alert-danger mb-0 mt-2">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Esta factura ha sido anulada.
                                </div>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            {{-- Información del cliente --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Cliente</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-1">{{ $factura->cliente?->nombre_comercial }}</h6>
                    @if($factura->cliente?->cif)
                        <p class="text-muted small mb-1">CIF: {{ $factura->cliente->cif }}</p>
                    @endif
                    @if($factura->cliente?->direccion)
                        <p class="text-muted small mb-1">{{ $factura->cliente->direccion }}</p>
                    @endif
                    @if($factura->cliente?->email)
                        <p class="text-muted small mb-0">
                            <i class="bi bi-envelope me-1"></i>{{ $factura->cliente->email }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function emitirFactura() {
        Swal.fire({
            title: '¿Emitir factura?',
            text: 'Se generará el número de factura. Esta acción no se puede deshacer.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, emitir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ route('facturas.emitir', $factura) }}`, {
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
                            title: 'Factura emitida',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
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

    async function enviarFactura() {
        try {
            const facturaId = {{ $factura->id }};
            const emailsResponse = await fetch(`{{ url('facturas') }}/${facturaId}/emails-cliente`, {
                headers: { 'Accept': 'application/json' },
            });
            const emailsData = await emailsResponse.json();
            if (!emailsData.success) {
                Swal.fire('Error', emailsData.message, 'error');
                return;
            }
            const availableEmails = emailsData.emails;
            if (availableEmails.length === 0) {
                Swal.fire('Error', 'El cliente no tiene emails configurados.', 'error');
                return;
            }
            const emailCheckboxesHtml = availableEmails.map((emailObj, index) => {
                const checked = emailObj.por_defecto || index === 0 ? 'checked' : '';
                const badgeHtml = emailObj.por_defecto ? '<span class="badge bg-info ms-2">Por defecto</span>' : '';
                return `<div class="form-check text-start mb-2"><input class="form-check-input email-checkbox" type="checkbox" value="${emailObj.email}" id="email-${index}" ${checked}><label class="form-check-label" for="email-${index}">${emailObj.label} ${badgeHtml}</label></div>`;
            }).join('');
            const result = await Swal.fire({
                title: 'Enviar Factura por Email',
                html: `<div class="text-start"><p class="text-muted mb-3">Seleccione los destinatarios:</p><div class="mb-3 p-3 bg-light rounded">${emailCheckboxesHtml}</div><div class="mb-3"><label class="form-label fw-semibold"><i class="bi bi-plus-circle me-1"></i>Emails adicionales (separados por comas)</label><textarea id="emails-custom" class="form-control" rows="2" placeholder="email1@ejemplo.com, email2@ejemplo.com"></textarea><small class="text-muted">Puede añadir otros emails separados por comas</small></div><div class="alert alert-info mt-3 mb-0"><i class="bi bi-info-circle me-2"></i><small>El PDF se adjuntará automáticamente.</small></div></div>`,
                width: 600,
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-send me-2"></i>Enviar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd',
                preConfirm: () => {
                    const selectedEmails = Array.from(document.querySelectorAll('.email-checkbox:checked')).map(cb => cb.value);
                    const customEmailsText = document.getElementById('emails-custom').value.trim();
                    let customEmails = [];
                    if (customEmailsText) {
                        customEmails = customEmailsText.split(',').map(email => email.trim().toLowerCase()).filter(email => email.length > 0);
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        for (const email of customEmails) {
                            if (!emailRegex.test(email)) {
                                Swal.showValidationMessage(`Email inválido: ${email}`);
                                return false;
                            }
                        }
                    }
                    const allEmails = [...new Set([...selectedEmails, ...customEmails])];
                    if (allEmails.length === 0) {
                        Swal.showValidationMessage('Debe seleccionar al menos un destinatario');
                        return false;
                    }
                    if (allEmails.length > 10) {
                        Swal.showValidationMessage('No puede enviar a más de 10 destinatarios');
                        return false;
                    }
                    return allEmails;
                }
            });
            if (result.isConfirmed && result.value) {
                const emailsToSend = result.value;
                Swal.fire({
                    title: 'Enviando...',
                    html: `Enviando factura a ${emailsToSend.length} destinatario(s)...`,
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                const sendResponse = await fetch('{{ route("facturas.enviar", $factura) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ emails: emailsToSend }),
                });
                const sendData = await sendResponse.json();
                if (sendData.success) {
                    let icon = sendData.warning ? 'warning' : 'success';
                    let title = sendData.warning ? 'Enviado con advertencias' : '¡Enviado!';
                    let detailsHtml = '';
                    if (sendData.detalles) {
                        if (sendData.detalles.enviados?.length > 0) {
                            detailsHtml += '<div class="text-start mt-2"><strong>Enviados:</strong><ul>';
                            sendData.detalles.enviados.forEach(email => { detailsHtml += `<li class="text-success">${email}</li>`; });
                            detailsHtml += '</ul></div>';
                        }
                        if (sendData.detalles.fallidos?.length > 0) {
                            detailsHtml += '<div class="text-start mt-2"><strong>Fallidos:</strong><ul>';
                            sendData.detalles.fallidos.forEach(item => { detailsHtml += `<li class="text-danger">${item.email}: ${item.error}</li>`; });
                            detailsHtml += '</ul></div>';
                        }
                    }
                    Swal.fire({ icon: icon, title: title, html: sendData.message + detailsHtml, confirmButtonText: 'Aceptar' }).then(() => window.location.reload());
                } else {
                    Swal.fire('Error', sendData.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión al servidor', 'error');
        }
    }

    function cobrarFactura() {
        Swal.fire({
            title: 'Marcar como cobrada',
            html: '<input type="date" id="fechaCobro" class="form-control" value="{{ date('Y-m-d') }}">',
            showCancelButton: true,
            confirmButtonText: 'Marcar Cobrada',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
        }).then(async (result) => {
            if (result.isConfirmed) {
                const fechaCobro = document.getElementById('fechaCobro').value;
                try {
                    const response = await fetch(`{{ route('facturas.cobrar', $factura) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ fecha_cobro: fechaCobro }),
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
        });
    }

    function editarNumeroFactura() {
        Swal.fire({
            title: 'Editar número de factura',
            html: `
                <div class="mb-2 text-start">
                    <label class="form-label mb-1">Serie</label>
                    <input id="serieInput" class="form-control" value="{{ $factura->serie }}" maxlength="20">
                </div>
                <div class="text-start">
                    <label class="form-label mb-1">Número</label>
                    <input id="numeroInput" class="form-control" value="{{ $factura->numero }}" maxlength="50" placeholder="Ej: F-2026-00001">
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ route('facturas.numero', $factura) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            serie: document.getElementById('serieInput').value,
                            numero: document.getElementById('numeroInput').value,
                        }),
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Éxito', text: data.message, timer: 1500, showConfirmButton: false }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            }
        });
    }

    function anularFactura() {
        Swal.fire({
            title: '¿Anular factura?',
            text: 'Esta acción marcará la factura como anulada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ route('facturas.anular', $factura) }}`, {
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
                            title: 'Factura anulada',
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
        });
    }

    function eliminarFactura() {
        Swal.fire({
            title: '¿Eliminar factura?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ route('facturas.destroy', $factura) }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = '{{ route('facturas.index') }}';
                        });
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
