@extends('cliente.layout')

@section('title', 'Pedido ' . $compra->numero_compra)

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('cliente.compras') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="bi bi-arrow-left"></i> Volver a Mis Compras
            </a>
            <h1 class="mb-0"><i class="bi bi-receipt"></i> Pedido {{ $compra->numero_compra }}</h1>
            <p class="text-muted mb-0">Realizado el {{ $compra->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            @switch($compra->estado)
                @case('pendiente')
                    <span class="badge badge-estado badge-pendiente fs-6">
                        <i class="bi bi-clock"></i> Pendiente
                    </span>
                    @break
                @case('procesando')
                    <span class="badge badge-estado badge-pendiente fs-6">
                        <i class="bi bi-hourglass-split"></i> Procesando
                    </span>
                    @break
                @case('pagada')
                    <span class="badge badge-estado badge-pagada fs-6">
                        <i class="bi bi-check-circle"></i> Pagada
                    </span>
                    @break
                @case('enviada')
                    <span class="badge badge-estado badge-enviada fs-6">
                        <i class="bi bi-truck"></i> Enviada
                    </span>
                    @break
                @case('entregada')
                    <span class="badge badge-estado badge-entregada fs-6">
                        <i class="bi bi-box-seam"></i> Entregada
                    </span>
                    @break
                @case('cancelada')
                    <span class="badge badge-estado badge-cancelada fs-6">
                        <i class="bi bi-x-circle"></i> Cancelada
                    </span>
                    @break
            @endswitch
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Productos -->
        <div class="col-lg-8">
            <div class="content-card">
                <h5 class="mb-4"><i class="bi bi-box"></i> Productos</h5>

                @foreach($compra->items as $item)
                    <div class="d-flex align-items-start gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <img src="{{ $item->producto->url_imagen_principal ?? asset('images/no-image.png') }}"
                             alt="{{ $item->nombre_producto }}"
                             class="rounded"
                             style="width: 80px; height: 80px; object-fit: cover;">

                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->nombre_producto }}</h6>
                            @if($item->info_variante)
                                <small class="text-muted">{{ $item->info_variante }}</small>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-muted">
                                    {{ $item->cantidad }} x ${{ number_format($item->precio_unitario, 0, ',', '.') }}
                                </span>
                                <strong>${{ number_format($item->precio_total, 0, ',', '.') }}</strong>
                            </div>

                            @if($compra->puedeSerCalificada())
                                <div class="mt-2">
                                    @if(in_array($item->id, $calificacionesExistentes))
                                        <span class="badge bg-success">
                                            <i class="bi bi-check"></i> Ya calificado
                                        </span>
                                    @else
                                        <a href="{{ route('cliente.calificar', $item->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-star"></i> Calificar producto
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Información de envío -->
            @if($compra->envio)
                <div class="content-card">
                    <h5 class="mb-4"><i class="bi bi-truck"></i> Información de Envío</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Transportadora:</strong></p>
                            <p class="text-muted">{{ $compra->envio->transportadora ?? 'Por definir' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Número de guía:</strong></p>
                            <p class="text-muted">{{ $compra->envio->numero_guia ?? 'Por definir' }}</p>
                        </div>
                        @if($compra->envio->url_seguimiento)
                            <div class="col-12">
                                <a href="{{ $compra->envio->url_seguimiento }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-up-right"></i> Rastrear Envío
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Resumen -->
        <div class="col-lg-4">
            <div class="content-card">
                <h5 class="mb-4"><i class="bi bi-receipt-cutoff"></i> Resumen</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>${{ number_format($compra->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($compra->descuento_total > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Descuento</span>
                        <span>-${{ number_format($compra->descuento_total, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Envío</span>
                    <span>
                        @if($compra->costo_envio > 0)
                            ${{ number_format($compra->costo_envio, 0, ',', '.') }}
                        @else
                            Por calcular
                        @endif
                    </span>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <strong>Total</strong>
                    <strong class="fs-5">${{ number_format($compra->total, 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Dirección de envío -->
            <div class="content-card">
                <h5 class="mb-4"><i class="bi bi-geo-alt"></i> Dirección de Envío</h5>

                <p class="mb-1"><strong>{{ $compra->nombre_cliente }}</strong></p>
                <p class="mb-1 text-muted">{{ $compra->direccion_envio }}</p>
                <p class="mb-1 text-muted">{{ $compra->ciudad->nombre ?? '' }}, {{ $compra->ciudad->departamento->nombre ?? '' }}</p>
                <p class="mb-0 text-muted">{{ $compra->telefono_cliente }}</p>
            </div>

            @if($compra->notas)
                <div class="content-card">
                    <h5 class="mb-3"><i class="bi bi-chat-text"></i> Notas</h5>
                    <p class="text-muted mb-0">{{ $compra->notas }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
