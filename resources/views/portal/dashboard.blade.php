<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-circle fs-4"></i>
            <span>Mi Portal</span>
            <span class="badge bg-info ms-2">{{ $cliente->nombre }}</span>
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

            {{-- Métricas del cliente --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">Total Cotizaciones</p>
                                    <h3 class="mb-0 fw-bold">{{ $totalCotizaciones }}</h3>
                                </div>
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="bi bi-file-text text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">Pendientes</p>
                                    <h3 class="mb-0 fw-bold">{{ $cotizacionesPendientes }}</h3>
                                </div>
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                    <i class="bi bi-clock text-warning fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">En Camino</p>
                                    <h3 class="mb-0 fw-bold">{{ $pedidosEnCamino }}</h3>
                                </div>
                                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                    <i class="bi bi-truck text-info fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">Total Comprado</p>
                                    <h3 class="mb-0 fw-bold">${{ number_format($totalComprado, 0, ',', '.') }}</h3>
                                </div>
                                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                    <i class="bi bi-currency-dollar text-success fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alertas de pedidos despachados --}}
            @if($pedidosDespachados->count() > 0)
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-truck me-2 fs-5"></i>
                    <div>
                        <strong>Tienes {{ $pedidosDespachados->count() }} pedido(s) despachado(s) recientemente.</strong>
                        Revisa el seguimiento para más detalles.
                    </div>
                </div>
            @endif

            {{-- Últimos pedidos --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Últimos Pedidos
                    </h5>
                    <a href="{{ route('portal.historial') }}" class="btn btn-sm btn-outline-primary">
                        Ver todos <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº Solicitud</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Pago</th>
                                    <th>Envío</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimosPedidos as $pedido)
                                    <tr>
                                        <td>
                                            <strong>{{ $pedido->numero_solicitud }}</strong>
                                        </td>
                                        <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                        <td>${{ number_format($pedido->monto_total, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $pedido->color_estado }}">
                                                {{ ucfirst($pedido->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $pedido->color_estado_pago }}">
                                                {{ $pedido->etiqueta_estado_pago }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $pedido->color_estado_envio }}">
                                                <i class="bi {{ $pedido->icono_estado_envio }} me-1"></i>
                                                {{ $pedido->etiqueta_estado_envio }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('portal.pedido.detalle', $pedido->id) }}"
                                                   class="btn btn-outline-primary" title="Ver detalle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('portal.pedido.seguimiento', $pedido->id) }}"
                                                   class="btn btn-outline-info" title="Seguimiento">
                                                    <i class="bi bi-geo-alt"></i>
                                                </a>
                                                @if($pedido->puedeDescargarGuia())
                                                    <a href="{{ route('portal.pedido.guia', $pedido->id) }}"
                                                       class="btn btn-outline-success" title="Descargar guía">
                                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                                    </a>
                                                @endif
                                                @if($pedido->puedeDescargarFactura())
                                                    <a href="{{ route('portal.pedido.factura', $pedido->id) }}"
                                                       class="btn btn-outline-warning" title="Descargar factura">
                                                        <i class="bi bi-receipt"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No tienes pedidos aún
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Información de contacto --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="bi bi-question-circle me-2"></i>
                        ¿Necesitas ayuda?
                    </h6>
                    <p class="text-muted mb-0">
                        Si tienes alguna consulta sobre tus pedidos, comunícate con nosotros:
                        <br>
                        <i class="bi bi-envelope me-1"></i> ventas@miraclebeauty.com
                        <br>
                        <i class="bi bi-whatsapp me-1"></i> +57 300 123 4567
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
