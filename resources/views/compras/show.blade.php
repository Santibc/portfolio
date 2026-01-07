<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Pedido #{{ $compra->numero_compra }}</h2>
            <a href="{{ route('compras') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </x-slot>

    <style>
        .status-badge {
            font-size: 0.875rem;
            padding: 0.375rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-pendiente { background: #fef3c7; color: #92400e; }
        .status-procesando { background: #dbeafe; color: #1e40af; }
        .status-pagada { background: #d1fae5; color: #065f46; }
        .status-enviada { background: #e0e7ff; color: #3730a3; }
        .status-entregada { background: #d1fae5; color: #065f46; }
        .status-cancelada { background: #fee2e2; color: #991b1b; }
        .status-reembolsada { background: #f3f4f6; color: #374151; }

        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 1.5rem;
            width: 2px;
            background: #e5e7eb;
        }

        .timeline-item {
            position: relative;
            padding-left: 4rem;
            padding-bottom: 2rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-icon {
            position: absolute;
            left: 0.75rem;
            width: 3rem;
            height: 3rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .timeline-icon.primary { border-color: #3730a3; color: #3730a3; }
        .timeline-icon.success { border-color: #10b981; color: #10b981; }
        .timeline-icon.danger { border-color: #ef4444; color: #ef4444; }
        .timeline-icon.info { border-color: #3b82f6; color: #3b82f6; }

        .info-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            height: 100%;
        }

        .info-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            color: #111827;
            font-weight: 500;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-right: 1rem;
        }

        .floristeria-card {
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
            border: 1px solid #f9a8d4;
        }

        .repartidor-card {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #93c5fd;
        }

        .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .text-pink { color: #ec4899; }

        @media print {
            .no-print { display: none !important; }
            .print-full { width: 100% !important; max-width: 100% !important; }
            body { font-size: 12px; }
            .info-card { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header con estado y acciones --}}
            <div class="bg-white shadow-sm rounded-4 p-6 mb-6 no-print">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <span class="status-badge status-{{ $compra->estado }}">
                                {{ ucfirst($compra->estado) }}
                            </span>
                            <span class="text-muted">
                                Creada {{ $compra->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        @if(in_array($compra->estado, ['pagada', 'enviada']))
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalRepartidor">
                                <i class="bi bi-person-badge"></i> Asignar Repartidor
                            </button>
                        @endif
                        @if(in_array($compra->estado, ['pendiente', 'procesando', 'pagada', 'enviada']))
                            <button class="btn btn-outline-success" onclick="actualizarEnvio({{ $compra->id }})">
                                <i class="bi bi-truck"></i> Actualizar Envio
                            </button>
                        @endif
                        <button class="btn btn-outline-primary" onclick="verTimeline()">
                            <i class="bi bi-clock-history"></i> Timeline
                        </button>
                        <a href="{{ route('compras.imprimir', $compra) }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="bi bi-printer"></i> Imprimir
                        </a>
                    </div>
                </div>
            </div>

            {{-- Alerta de fecha de entrega --}}
            @if($compra->fecha_entrega_deseada)
                @php
                    $esHoy = $compra->fecha_entrega_deseada->isToday();
                    $esPasada = $compra->fecha_entrega_deseada->isPast() && !$esHoy;
                @endphp
                @if($esHoy && in_array($compra->estado, ['pagada', 'enviada']))
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Entrega programada para HOY</strong>
                        @if($compra->horario_entrega)
                            - Horario: {{ $compra->horario_entrega }}
                        @endif
                    </div>
                @elseif($esPasada && in_array($compra->estado, ['pagada', 'enviada']))
                    <div class="alert alert-danger mb-4">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong>Entrega ATRASADA</strong> - Fecha programada: {{ $compra->fecha_entrega_deseada->format('d/m/Y') }}
                    </div>
                @endif
            @endif

            <div class="row g-4 mb-6">
                {{-- Informacion del Cliente que compra --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5 class="mb-4">
                            <i class="bi bi-person me-2"></i>Quien Compra
                        </h5>

                        <div class="mb-3">
                            <div class="info-label">Nombre</div>
                            <div class="info-value">{{ $compra->nombre_cliente }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Email</div>
                            <div class="info-value">
                                <a href="mailto:{{ $compra->email_cliente }}">{{ $compra->email_cliente }}</a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Telefono</div>
                            <div class="info-value">
                                <a href="tel:{{ $compra->telefono_cliente }}">{{ $compra->telefono_cliente }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informacion del Destinatario (Floristeria) --}}
                <div class="col-lg-4">
                    <div class="info-card floristeria-card">
                        <h5 class="mb-4">
                            <i class="bi bi-gift me-2 text-pink"></i>Destinatario
                            @if($compra->es_sorpresa)
                                <span class="badge bg-danger ms-2">SORPRESA</span>
                            @endif
                        </h5>

                        <div class="mb-3">
                            <div class="info-label">Nombre</div>
                            <div class="info-value">{{ $compra->nombre_destinatario ?: $compra->nombre_cliente }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Telefono</div>
                            <div class="info-value">
                                <a href="tel:{{ $compra->telefono_destinatario ?: $compra->telefono_cliente }}">
                                    {{ $compra->telefono_destinatario ?: $compra->telefono_cliente }}
                                </a>
                            </div>
                        </div>

                        @if($compra->mensaje_tarjeta)
                            <div class="mb-3">
                                <div class="info-label">Mensaje de la Tarjeta</div>
                                <div class="p-3 bg-white rounded border" style="font-style: italic;">
                                    "{{ $compra->mensaje_tarjeta }}"
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Informacion de Entrega --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5 class="mb-4">
                            <i class="bi bi-geo-alt me-2"></i>Entrega
                        </h5>

                        <div class="mb-3">
                            <div class="info-label">Direccion</div>
                            <div class="info-value">{{ $compra->direccion_envio }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Ciudad</div>
                            <div class="info-value">
                                {{ $compra->ciudad->nombre }}, {{ $compra->ciudad->departamento->nombre }}
                            </div>
                        </div>

                        @if($compra->fecha_entrega_deseada)
                            <div class="mb-3">
                                <div class="info-label">Fecha de Entrega</div>
                                <div class="info-value">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $compra->fecha_entrega_deseada->format('d/m/Y') }}
                                    @if($compra->horario_entrega)
                                        <br><small class="text-muted">{{ $compra->horario_entrega }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($compra->instrucciones_entrega)
                            <div class="mb-3">
                                <div class="info-label">Instrucciones</div>
                                <div class="info-value text-muted small">{{ $compra->instrucciones_entrega }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-6">
                {{-- Repartidor Asignado --}}
                <div class="col-lg-4">
                    <div class="info-card repartidor-card">
                        <h5 class="mb-4">
                            <i class="bi bi-person-badge me-2"></i>Repartidor
                        </h5>

                        @if($compra->repartidor)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle bg-primary text-white me-3">
                                    {{ strtoupper(substr($compra->repartidor->nombre, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="info-value">{{ $compra->repartidor->nombre }}</div>
                                    <small class="text-muted">
                                        @if($compra->repartidor->vehiculo)
                                            {{ ucfirst($compra->repartidor->vehiculo) }}
                                            @if($compra->repartidor->placa) - {{ $compra->repartidor->placa }} @endif
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="mb-2">
                                <a href="tel:{{ $compra->repartidor->telefono }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-telephone"></i> {{ $compra->repartidor->telefono }}
                                </a>
                            </div>
                            @if($compra->asignado_repartidor_at)
                                <small class="text-muted">
                                    Asignado: {{ $compra->asignado_repartidor_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-person-x display-6 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Sin repartidor asignado</p>
                                @if(in_array($compra->estado, ['pagada', 'enviada']))
                                    <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalRepartidor">
                                        <i class="bi bi-plus"></i> Asignar
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Informacion de Pago --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5 class="mb-4">
                            <i class="bi bi-credit-card me-2"></i>Pago
                        </h5>

                        <div class="mb-3">
                            <div class="info-label">Metodo de pago</div>
                            <div class="info-value">
                                {{ $compra->transaccionAprobada ? ucfirst($compra->transaccionAprobada->metodo_pago ?? 'WebPay') : 'Sin pago' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Estado del pago</div>
                            <div class="info-value">
                                @if($compra->transaccionAprobada)
                                    <span class="badge bg-success">Aprobado</span>
                                @else
                                    <span class="badge bg-danger">Pendiente</span>
                                @endif
                            </div>
                        </div>

                        @if($compra->transaccionAprobada)
                            <div class="mb-3">
                                <div class="info-label">ID Transaccion</div>
                                <div class="info-value text-truncate small">
                                    {{ $compra->transaccionAprobada->referencia_transaccion }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Fecha de pago</div>
                                <div class="info-value">
                                    {{ $compra->transaccionAprobada->fecha_procesamiento?->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Informacion de Envio/Transportadora --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5 class="mb-4">
                            <i class="bi bi-box-seam me-2"></i>Envio
                        </h5>

                        @if($compra->envio && $compra->envio->transportadora)
                            <div class="mb-3">
                                <div class="info-label">Transportadora</div>
                                <div class="info-value">{{ $compra->envio->transportadora }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Numero de guia</div>
                                <div class="info-value">{{ $compra->envio->numero_guia }}</div>
                            </div>

                            @if($compra->envio->fecha_entrega_estimada)
                                <div class="mb-3">
                                    <div class="info-label">Entrega estimada</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($compra->envio->fecha_entrega_estimada)->format('d/m/Y') }}
                                    </div>
                                </div>
                            @endif

                            @if($compra->envio->url_seguimiento)
                                <a href="{{ $compra->envio->url_seguimiento }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-seam"></i> Rastrear envio
                                </a>
                            @endif
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-box display-6 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Sin informacion de envio</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Productos --}}
            <div class="bg-white shadow-sm rounded-4 p-6 mb-6">
                <h5 class="mb-4">
                    <i class="bi bi-box me-2"></i>Productos ({{ $compra->items->count() }})
                </h5>

                @foreach($compra->items as $item)
                    <div class="product-item">
                        @if($item->producto)
                            <img src="{{ $item->producto->url_imagen_principal }}"
                                 alt="{{ $item->nombre_producto }}"
                                 class="product-image">
                        @else
                            {{-- Imagen para ramos personalizados --}}
                            <div class="product-image d-flex align-items-center justify-content-center"
                                 style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%); border-radius: 8px;">
                                <i class="bi bi-flower1" style="font-size: 2rem; color: #e91e63;"></i>
                            </div>
                        @endif

                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $item->nombre_producto }}</div>
                            @if($item->info_variante)
                                <div class="text-muted small">{{ $item->info_variante }}</div>
                            @endif
                            <div class="text-muted small">Ref: {{ $item->referencia_producto }}</div>
                        </div>

                        <div class="text-center" style="min-width: 100px;">
                            <div class="text-muted small">Cantidad</div>
                            <div class="fw-medium">{{ $item->cantidad }}</div>
                        </div>

                        <div class="text-center" style="min-width: 120px;">
                            <div class="text-muted small">Precio unitario</div>
                            <div class="fw-medium">${{ number_format($item->precio_unitario, 0, ',', '.') }}</div>
                        </div>

                        <div class="text-end" style="min-width: 120px;">
                            <div class="text-muted small">Subtotal</div>
                            <div class="fw-bold">${{ number_format($item->precio_total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endforeach

                {{-- Totales --}}
                <div class="border-top pt-4 mt-4">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>${{ number_format($compra->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($compra->descuento_total > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Descuento</span>
                                    <span>-${{ number_format($compra->descuento_total, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($compra->costo_envio > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Costo de envio</span>
                                    <span>${{ number_format($compra->costo_envio, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($compra->impuestos > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Impuestos</span>
                                    <span>${{ number_format($compra->impuestos, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                                <span>Total</span>
                                <span>${{ number_format($compra->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notas --}}
            @if($compra->notas)
                <div class="bg-white shadow-sm rounded-4 p-6">
                    <h5 class="mb-3">
                        <i class="bi bi-chat-left-text me-2"></i>Notas
                    </h5>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $compra->notas }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Asignar Repartidor --}}
    <div class="modal fade" id="modalRepartidor" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar Repartidor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($repartidores->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-person-x display-4 text-muted"></i>
                            <p class="mt-3">No hay repartidores registrados</p>
                            <a href="{{ route('repartidores.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Crear Repartidor
                            </a>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($repartidores as $repartidor)
                                <button type="button" class="list-group-item list-group-item-action d-flex align-items-center"
                                        onclick="asignarRepartidor({{ $repartidor->id }})">
                                    <div class="avatar-circle bg-primary text-white me-3" style="width: 40px; height: 40px; font-size: 0.875rem;">
                                        {{ strtoupper(substr($repartidor->nombre, 0, 2)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">{{ $repartidor->nombre }}</div>
                                        <small class="text-muted">
                                            {{ $repartidor->telefono }}
                                            @if($repartidor->vehiculo)
                                                | {{ ucfirst($repartidor->vehiculo) }}
                                            @endif
                                        </small>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Timeline --}}
    <div class="modal fade" id="modalTimeline" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Timeline del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="timeline" id="timelineContent">
                        <li class="timeline-item">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal actualizar envio --}}
    <div class="modal fade" id="modalEnvio" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEnvio">
                    <div class="modal-header">
                        <h5 class="modal-title">Actualizar Informacion de Envio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="compraIdEnvio">

                        <div class="mb-3">
                            <label class="form-label">Transportadora *</label>
                            <input type="text" class="form-control" name="transportadora" required
                                   value="{{ $compra->envio->transportadora ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Numero de guia *</label>
                            <input type="text" class="form-control" name="numero_guia" required
                                   value="{{ $compra->envio->numero_guia ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL de seguimiento</label>
                            <input type="url" class="form-control" name="url_seguimiento"
                                   value="{{ $compra->envio->url_seguimiento ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha estimada de entrega</label>
                            <input type="date" class="form-control" name="fecha_entrega_estimada"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   value="{{ $compra->envio->fecha_entrega_estimada ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnActualizarEnvio">
                            <span class="btn-text">
                                <i class="bi bi-truck me-2"></i>Actualizar Envio
                            </span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Enviando correo...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function verTimeline() {
            const modal = new bootstrap.Modal(document.getElementById('modalTimeline'));
            modal.show();

            $.ajax({
                url: "{{ route('compras.timeline', $compra) }}",
                success: function(timeline) {
                    let html = '';

                    timeline.forEach(function(evento) {
                        const fecha = new Date(evento.fecha);
                        html += `
                            <li class="timeline-item">
                                <div class="timeline-icon ${evento.color}">
                                    <i class="bi ${evento.icono}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">${evento.titulo}</h6>
                                    <p class="text-muted mb-1">${evento.descripcion}</p>
                                    <small class="text-muted">
                                        ${fecha.toLocaleDateString()} ${fecha.toLocaleTimeString()}
                                    </small>
                                </div>
                            </li>
                        `;
                    });

                    $('#timelineContent').html(html);
                },
                error: function() {
                    $('#timelineContent').html('<li class="text-danger">Error al cargar el timeline</li>');
                }
            });
        }

        function actualizarEnvio(compraId) {
            $('#compraIdEnvio').val(compraId);
            const modal = new bootstrap.Modal(document.getElementById('modalEnvio'));
            modal.show();
        }

        function asignarRepartidor(repartidorId) {
            $.ajax({
                url: "{{ route('compras.asignar-repartidor', $compra) }}",
                method: 'POST',
                data: { repartidor_id: repartidorId },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalRepartidor').modal('hide');
                        Swal.fire({
                            title: 'Repartidor Asignado',
                            text: `${response.repartidor.nombre} ha sido asignado a esta entrega`,
                            icon: 'success',
                            confirmButtonText: 'Entendido'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al asignar repartidor',
                        icon: 'error'
                    });
                }
            });
        }

        // Submit envio
        $('#formEnvio').on('submit', function(e) {
            e.preventDefault();

            const compraId = $('#compraIdEnvio').val() || {{ $compra->id }};
            const data = $(this).serialize();
            const submitBtn = $('#btnActualizarEnvio');

            submitBtn.prop('disabled', true);
            submitBtn.find('.btn-text').addClass('d-none');
            submitBtn.find('.btn-loading').removeClass('d-none');

            $.ajax({
                url: `/compras/${compraId}/actualizar-envio`,
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        submitBtn.find('.btn-loading').addClass('d-none');
                        submitBtn.find('.btn-text').removeClass('d-none');
                        submitBtn.prop('disabled', false);

                        $('#modalEnvio').modal('hide');

                        Swal.fire({
                            title: 'Envio Actualizado!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Entendido',
                            timer: 5000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    submitBtn.find('.btn-loading').addClass('d-none');
                    submitBtn.find('.btn-text').removeClass('d-none');
                    submitBtn.prop('disabled', false);

                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al actualizar el envio',
                        icon: 'error'
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
