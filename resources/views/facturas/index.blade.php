@extends('layouts.app')

@section('title', 'Gestión de Facturas')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Facturas</h1>
            <p class="text-muted mb-0">Emisión y seguimiento de facturas</p>
        </div>
        <div>
            <a href="{{ route('facturas.export.excel', request()->query()) }}" class="btn btn-outline-success me-2">
                <i class="bi bi-file-earmark-excel me-2"></i>Exportar Excel
            </a>
            <a href="{{ route('facturas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Nueva Factura
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

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-receipt text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['total'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Total Facturado</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-clock text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['pendiente'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Pendiente de Cobro</small>
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
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['cobrado'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Cobrado</small>
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
                            <i class="bi bi-calendar-month text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ number_format($stats['este_mes'], 2, ',', '.') }} €</h3>
                            <small class="text-muted">Este Mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('facturas.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" placeholder="Nº o cliente..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre_comercial }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="emitida" {{ request('estado') == 'emitida' ? 'selected' : '' }}>Emitida</option>
                            <option value="enviada" {{ request('estado') == 'enviada' ? 'selected' : '' }}>Enviada</option>
                            <option value="cobrada" {{ request('estado') == 'cobrada' ? 'selected' : '' }}>Cobrada</option>
                            <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de facturas --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Obra</th>
                        <th>Fecha</th>
                        <th class="text-end">Base</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas as $factura)
                    <tr>
                        <td>
                            <a href="{{ route('facturas.show', $factura) }}" class="text-decoration-none fw-semibold">
                                {{ $factura->numero ?? 'Borrador #' . $factura->id }}
                            </a>
                        </td>
                        <td>{{ $factura->cliente?->nombre_comercial ?? '-' }}</td>
                        <td>
                            @if($factura->obra)
                                <a href="{{ route('obras.show', $factura->obra) }}" class="text-decoration-none">
                                    {{ $factura->obra->codigo }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                        <td class="text-end">{{ number_format($factura->base_imponible, 2, ',', '.') }} €</td>
                        <td class="text-end fw-semibold">{{ number_format($factura->total, 2, ',', '.') }} €</td>
                        <td class="text-center">
                            @switch($factura->estado)
                                @case('borrador')
                                    <span class="badge bg-secondary">Borrador</span>
                                    @break
                                @case('emitida')
                                    <span class="badge bg-primary">Emitida</span>
                                    @break
                                @case('enviada')
                                    <span class="badge bg-info">Enviada</span>
                                    @break
                                @case('cobrada')
                                    <span class="badge bg-success">Cobrada</span>
                                    @break
                                @case('anulada')
                                    <span class="badge bg-danger">Anulada</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="text-end">
                            {{-- Acciones según estado --}}
                            @if($factura->estado == 'borrador')
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="emitirFactura({{ $factura->id }})" title="Emitir factura">
                                    <i class="bi bi-send"></i>
                                </button>
                                <a href="{{ route('facturas.edit', $factura) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @elseif($factura->estado == 'emitida')
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="marcarEnviada({{ $factura->id }})" title="Marcar como enviada (sin correo)">
                                    <i class="bi bi-check2-circle"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="enviarFactura({{ $factura->id }})" title="Enviar por email">
                                    <i class="bi bi-envelope"></i>
                                </button>
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Ver PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            @elseif($factura->estado == 'enviada')
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="cobrarFactura({{ $factura->id }})" title="Marcar como cobrada">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Ver PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            @elseif($factura->estado == 'cobrada')
                                <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Ver PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            @endif

                            <a href="{{ route('facturas.show', $factura) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>

                            @if($factura->estado == 'borrador' || auth()->user()->hasRole('Administrador'))
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFactura({{ $factura->id }}, '{{ $factura->numero ?? 'Borrador #' . $factura->id }}')" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-3"></i>
                                <p class="mb-0">No hay facturas registradas.</p>
                                <a href="{{ route('facturas.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-lg me-2"></i>Crear primera factura
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($facturas->hasPages())
        <div class="card-footer bg-white">
            {{ $facturas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function emitirFactura(id) {
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
                    const response = await fetch(`{{ url('facturas') }}/${id}/emitir`, {
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

    async function enviarFactura(id) {
        try {
            const emailsResponse = await fetch(`{{ url('facturas') }}/${id}/emails-cliente`, { headers: { 'Accept': 'application/json' } });
            const emailsData = await emailsResponse.json();
            if (!emailsData.success) { Swal.fire('Error', emailsData.message, 'error'); return; }
            const availableEmails = emailsData.emails;
            if (availableEmails.length === 0) { Swal.fire('Error', 'El cliente no tiene emails configurados.', 'error'); return; }
            const emailCheckboxesHtml = availableEmails.map((emailObj, index) => {
                const checked = emailObj.por_defecto || index === 0 ? 'checked' : '';
                const badgeHtml = emailObj.por_defecto ? '<span class="badge bg-info ms-2">Por defecto</span>' : '';
                return `<div class="form-check text-start mb-2"><input class="form-check-input email-checkbox" type="checkbox" value="${emailObj.email}" id="email-${index}" ${checked}><label class="form-check-label" for="email-${index}">${emailObj.label} ${badgeHtml}</label></div>`;
            }).join('');
            const result = await Swal.fire({
                title: 'Enviar Factura por Email',
                html: `<div class="text-start"><p class="text-muted mb-3">Seleccione los destinatarios:</p><div class="mb-3 p-3 bg-light rounded">${emailCheckboxesHtml}</div><div class="mb-3"><label class="form-label fw-semibold"><i class="bi bi-plus-circle me-1"></i>Emails adicionales (separados por comas)</label><textarea id="emails-custom" class="form-control" rows="2" placeholder="email1@ejemplo.com, email2@ejemplo.com"></textarea><small class="text-muted">Puede añadir otros emails separados por comas</small></div><div class="alert alert-info mt-3 mb-0"><i class="bi bi-info-circle me-2"></i><small>El PDF se adjuntará automáticamente.</small></div></div>`,
                width: 600, showCancelButton: true, confirmButtonText: '<i class="bi bi-send me-2"></i>Enviar', cancelButtonText: 'Cancelar', confirmButtonColor: '#0d6efd',
                preConfirm: () => {
                    const selectedEmails = Array.from(document.querySelectorAll('.email-checkbox:checked')).map(cb => cb.value);
                    const customEmailsText = document.getElementById('emails-custom').value.trim();
                    let customEmails = [];
                    if (customEmailsText) {
                        customEmails = customEmailsText.split(',').map(email => email.trim().toLowerCase()).filter(email => email.length > 0);
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        for (const email of customEmails) { if (!emailRegex.test(email)) { Swal.showValidationMessage(`Email inválido: ${email}`); return false; } }
                    }
                    const allEmails = [...new Set([...selectedEmails, ...customEmails])];
                    if (allEmails.length === 0) { Swal.showValidationMessage('Debe seleccionar al menos un destinatario'); return false; }
                    if (allEmails.length > 10) { Swal.showValidationMessage('No puede enviar a más de 10 destinatarios'); return false; }
                    return allEmails;
                }
            });
            if (result.isConfirmed && result.value) {
                const emailsToSend = result.value;
                Swal.fire({ title: 'Enviando...', html: `Enviando factura a ${emailsToSend.length} destinatario(s)...`, allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                const sendResponse = await fetch(`{{ url('facturas') }}/${id}/enviar`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
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
                } else { Swal.fire('Error', sendData.message, 'error'); }
            }
        } catch (error) { console.error('Error:', error); Swal.fire('Error', 'Error de conexión al servidor', 'error'); }
    }

    function marcarEnviada(id) {
        Swal.fire({
            title: '¿Marcar como enviada?',
            text: 'La factura quedará como enviada (sin enviar ningún correo). Después podrás marcarla como cobrada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, marcar enviada',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('facturas') }}/${id}/marcar-enviada`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Hecho', text: data.message, timer: 1500, showConfirmButton: false })
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            }
        });
    }

    function cobrarFactura(id) {
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
                    const response = await fetch(`{{ url('facturas') }}/${id}/cobrar`, {
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

    function eliminarFactura(id, numero) {
        Swal.fire({
            title: '¿Eliminar factura?',
            html: `Se eliminará la factura: <strong>${numero}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('facturas') }}/${id}`, {
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
