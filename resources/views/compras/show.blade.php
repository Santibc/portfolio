<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Detalle de Compra #{{ $compra->numero_compra }}</h2>
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
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header con estado y acciones --}}
            <div class="bg-white shadow-sm rounded-4 p-6 mb-6">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3">
                            <span class="status-badge status-{{ $compra->estado }}">
                                {{ ucfirst($compra->estado) }}
                            </span>
                            <span class="text-muted">
                                Creada {{ $compra->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-outline-primary" onclick="verTimeline()">
                            <i class="bi bi-clock-history"></i> Timeline
                        </button>
                        <button class="btn btn-outline-secondary" onclick="imprimirCompra()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-6">
                {{-- Información del Cliente --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5 class="mb-4">
                            <i class="bi bi-person me-2"></i>Información del Cliente
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
                            <div class="info-label">Teléfono</div>
                            <div class="info-value">
                                <a href="tel:{{ $compra->telefono_cliente }}">{{ $compra->telefono_cliente }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Información de Envío --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5 class="mb-4">
                            <i class="bi bi-geo-alt me-2"></i>Información de Envío
                        </h5>
                        
                        <div class="mb-3">
                            <div class="info-label">Dirección</div>
                            <div class="info-value">{{ $compra->direccion_envio }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Ciudad</div>
                            <div class="info-value">
                                {{ $compra->ciudad->nombre }}, {{ $compra->ciudad->departamento->nombre }}
                            </div>
                        </div>
                        
                        @if($compra->envio)
                            <hr>
                            <div class="mb-3">
                                <div class="info-label">Transportadora</div>
                                <div class="info-value">{{ $compra->envio->transportadora }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="info-label">Número de guía</div>
                                <div class="info-value">{{ $compra->envio->numero_guia }}</div>
                            </div>
                            
                            @if($compra->envio->url_seguimiento)
                                <a href="{{ $compra->envio->url_seguimiento }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-seam"></i> Rastrear envío
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Información de Pago --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h5 class="mb-4">
                            <i class="bi bi-credit-card me-2"></i>Información de Pago
                        </h5>
                        
                        <div class="mb-3">
                            <div class="info-label">Método de pago</div>
                            <div class="info-value">
                                {{ $compra->transaccionAprobada ? ucfirst($compra->transaccionAprobada->metodo_pago ?? 'Wompi') : 'Sin pago' }}
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
                                <div class="info-label">ID Transacción</div>
                                <div class="info-value text-truncate">
                                    {{ $compra->transaccionAprobada->referencia_transaccion }}
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="info-label">Fecha de pago</div>
                                <div class="info-value">
                                    {{ $compra->transaccionAprobada->fecha_procesamiento->format('d/m/Y H:i') }}
                                </div>
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
                        <img src="{{ $item->producto->url_imagen_principal }}" 
                             alt="{{ $item->nombre_producto }}" 
                             class="product-image">
                        
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
                            @if($compra->costo_envio > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Costo de envío</span>
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

    {{-- Modal Timeline --}}
    <div class="modal fade" id="modalTimeline" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Timeline de la Compra</h5>
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

    @push('scripts')
    <script>
        function verTimeline() {
            const modal = new bootstrap.Modal(document.getElementById('modalTimeline'));
            modal.show();
            
            // Cargar timeline
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

        function imprimirCompra() {
            window.print();
        }
    </script>
    @endpush
</x-app-layout>