<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-file-text fs-4"></i>
            <span>Detalle de Pedido</span>
            <span class="badge bg-primary ms-2">{{ $solicitud->numero_solicitud }}</span>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            {{-- Alertas de sesión --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if(session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif

            {{-- Navegación --}}
            <div class="mb-4">
                <a href="{{ route('portal.historial') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver al historial
                </a>
                <a href="{{ route('portal.pedido.seguimiento', $solicitud->id) }}" class="btn btn-outline-info ms-2">
                    <i class="bi bi-geo-alt me-1"></i> Ver seguimiento
                </a>
            </div>

            <div class="row">
                {{-- Información del pedido --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Información del Pedido
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Número de Solicitud:</strong><br>
                                        {{ $solicitud->numero_solicitud }}
                                    </p>
                                    <p class="mb-2">
                                        <strong>Fecha de Solicitud:</strong><br>
                                        {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    @if($solicitud->notas_cliente)
                                        <p class="mb-2">
                                            <strong>Notas:</strong><br>
                                            {{ $solicitud->notas_cliente }}
                                        </p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Estado:</strong><br>
                                        <span class="badge bg-{{ $solicitud->color_estado }}">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Estado de Pago:</strong><br>
                                        <span class="badge bg-{{ $solicitud->color_estado_pago }}">
                                            {{ $solicitud->etiqueta_estado_pago }}
                                        </span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Estado de Envío:</strong><br>
                                        <span class="badge bg-{{ $solicitud->color_estado_envio }}">
                                            <i class="bi {{ $solicitud->icono_estado_envio }} me-1"></i>
                                            {{ $solicitud->etiqueta_estado_envio }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Items del pedido --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-cart me-2"></i>
                                Productos ({{ $solicitud->items->count() }} items)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Variante</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio Unit.</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($solicitud->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item->producto && $item->producto->imagenPrincipal)
                                                            <img src="{{ asset($item->producto->imagenPrincipal->ruta_imagen) }}"
                                                                 alt="{{ $item->producto->nombre }}"
                                                                 class="rounded me-2"
                                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item->producto->nombre ?? 'Producto eliminado' }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $item->producto->referencia ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($item->varianteProducto)
                                                        {{ $item->varianteProducto->referencia_variante ?? '' }}
                                                        @if($item->varianteProducto->color)
                                                            - {{ $item->varianteProducto->color }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->cantidad }}</td>
                                                <td class="text-end">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                                                <td class="text-end">${{ number_format($item->precio_total, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen y acciones --}}
                <div class="col-lg-4">
                    {{-- Resumen de totales --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-calculator me-2"></i>
                                Resumen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>${{ number_format($solicitud->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($solicitud->valor_flete > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Flete:</span>
                                    <span>${{ number_format($solicitud->valor_flete, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($solicitud->descuento_total > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Descuento:</span>
                                    <span>-${{ number_format($solicitud->descuento_total, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total:</span>
                                <span>${{ number_format($solicitud->monto_total, 0, ',', '.') }}</span>
                            </div>

                            @if($solicitud->monto_pagado > 0)
                                <hr>
                                <div class="d-flex justify-content-between text-success">
                                    <span>Pagado:</span>
                                    <span>${{ number_format($solicitud->monto_pagado, 0, ',', '.') }}</span>
                                </div>
                                @if($solicitud->saldo_pendiente > 0)
                                    <div class="d-flex justify-content-between text-danger">
                                        <span>Saldo pendiente:</span>
                                        <span>${{ number_format($solicitud->saldo_pendiente, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Información de envío --}}
                    @if($solicitud->estaDespachado())
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-truck me-2"></i>
                                    Información de Envío
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($solicitud->transportadora)
                                    <p class="mb-2">
                                        <strong>Transportadora:</strong><br>
                                        {{ $solicitud->transportadora }}
                                    </p>
                                @endif
                                @if($solicitud->numero_guia)
                                    <p class="mb-2">
                                        <strong>Número de Guía:</strong><br>
                                        <code>{{ $solicitud->numero_guia }}</code>
                                    </p>
                                @endif
                                @if($solicitud->despachado_en)
                                    <p class="mb-2">
                                        <strong>Fecha de Despacho:</strong><br>
                                        {{ $solicitud->despachado_en->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                                @if($solicitud->entregado_en)
                                    <p class="mb-0">
                                        <strong>Fecha de Entrega:</strong><br>
                                        {{ $solicitud->entregado_en->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Facturación --}}
                    @if($solicitud->tieneFactura())
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-receipt me-2"></i>
                                    Facturación
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">
                                    <strong>Número de Factura:</strong><br>
                                    {{ $solicitud->numero_factura }}
                                </p>
                                @if($solicitud->facturada_en)
                                    <p class="mb-0">
                                        <strong>Fecha de Factura:</strong><br>
                                        {{ $solicitud->facturada_en->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Acciones de descarga --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-download me-2"></i>
                                Descargas
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($solicitud->puedeDescargarGuia())
                                <a href="{{ route('portal.pedido.guia', $solicitud->id) }}"
                                   class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-file-earmark-arrow-down me-2"></i>
                                    Descargar Guía de Envío
                                </a>
                            @else
                                <button class="btn btn-outline-secondary w-100 mb-2" disabled>
                                    <i class="bi bi-file-earmark me-2"></i>
                                    Guía no disponible
                                </button>
                            @endif

                            @if($solicitud->puedeDescargarFactura())
                                <a href="{{ route('portal.pedido.factura', $solicitud->id) }}"
                                   class="btn btn-warning w-100">
                                    <i class="bi bi-receipt me-2"></i>
                                    Descargar Factura
                                </a>
                            @else
                                <button class="btn btn-outline-secondary w-100" disabled>
                                    <i class="bi bi-receipt me-2"></i>
                                    Factura no disponible
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
