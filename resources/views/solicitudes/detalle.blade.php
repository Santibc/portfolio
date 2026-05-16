<x-app-layout>
    <x-slot name="header">Cotización {{ $solicitud->numero_solicitud }}</x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">

            {{-- Breadcrumb + Volver --}}
            <div class="row mb-4">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('solicitudes') }}">Solicitudes</a></li>
                            <li class="breadcrumb-item active">{{ $solicitud->numero_solicitud }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Info Cliente --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                            <h6 class="mb-0" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                                <i class="bi bi-person me-2"></i>Información del Cliente
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted d-block">Cliente</small>
                                <strong>{{ $solicitud->cliente->nombre_contacto }}</strong>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Email</small>
                                <span>{{ $solicitud->cliente->email }}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Teléfono</small>
                                <span>{{ $solicitud->cliente->telefono }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Lista de Precios</small>
                                <span>{{ $solicitud->cliente->listaPrecio?->nombre ?? 'Sin lista' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Cotización --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                            <h6 class="mb-0" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                                <i class="bi bi-file-earmark-text me-2"></i>Información de la Cotización
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Número</small>
                                    <code class="fs-6">{{ $solicitud->numero_solicitud }}</code>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Fecha</small>
                                    <span>{{ $solicitud->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Estado</small>
                                    <span class="badge bg-{{ $solicitud->color_estado }} fs-6">
                                        {{ ucfirst($solicitud->estado) }}
                                    </span>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Origen</small>
                                    <span>{{ $solicitud->enlaceAcceso ? 'Enlace de Acceso' : 'Tienda a Tienda' }}</span>
                                </div>

                                @if($solicitud->estado === 'aplicada' && $solicitud->aplicadaPor)
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Aplicada por</small>
                                        <span>{{ $solicitud->aplicadaPor->name }}</span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Fecha aplicación</small>
                                        <span>{{ $solicitud->aplicada_en->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif

                                @if($solicitud->estado === 'rechazada' && $solicitud->rechazadaPor)
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Rechazada por</small>
                                        <span>{{ $solicitud->rechazadaPor->name }}</span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Fecha rechazo</small>
                                        <span>{{ $solicitud->rechazada_en->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif

                                @if($solicitud->forma_pago_factura)
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Forma de Pago</small>
                                        <span>{{ $solicitud->forma_pago_factura }}</span>
                                    </div>
                                @endif

                                @if($solicitud->fecha_vencimiento)
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Fecha Vencimiento</small>
                                        <span>{{ $solicitud->fecha_vencimiento->format('d/m/Y') }}</span>
                                    </div>
                                @endif

                                {{-- Reserva de Stock --}}
                                @if($solicitud->tiene_reserva_stock && $solicitud->reserva_expira_en)
                                    <div class="col-12 mb-2">
                                        <small class="text-muted d-block">Reserva Stock</small>
                                        @if($solicitud->reserva_expira_en->isPast())
                                            <span class="badge bg-danger">Expirada</span>
                                        @else
                                            <span class="badge bg-info">Activa</span>
                                        @endif
                                        — Expira: {{ $solicitud->reserva_expira_en->format('d/m/Y H:i') }}
                                    </div>
                                @elseif($solicitud->reserva_liberada_en)
                                    <div class="col-12 mb-2">
                                        <small class="text-muted d-block">Reserva Stock</small>
                                        <span class="badge bg-secondary">Liberada</span>
                                        — {{ $solicitud->reserva_liberada_en->format('d/m/Y H:i') }}
                                        @php
                                            $reservaLiberada = $solicitud->reservas->first(fn($r) => $r->liberada_por !== null);
                                        @endphp
                                        @if($reservaLiberada && $reservaLiberada->liberadaPor)
                                            por <strong>{{ $reservaLiberada->liberadaPor->name }}</strong>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notas del cliente --}}
            @if($solicitud->notas_cliente)
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h6 style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-chat-left-text me-2"></i>Notas del Cliente
                            <small class="text-muted">— {{ $solicitud->cliente->nombre_contacto }}</small>
                        </h6>
                        <div class="alert alert-info mb-0">{!! nl2br(e($solicitud->notas_cliente)) !!}</div>
                    </div>
                </div>
            @endif

            {{-- Observaciones del vendedor --}}
            @if($solicitud->observaciones_vendedor)
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h6 style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-chat-right-text me-2"></i>Observaciones del Vendedor
                            <small class="text-muted">— {{ $solicitud->createdBy?->name ?? 'Vendedor' }}</small>
                        </h6>
                        <div class="alert alert-warning mb-0">{!! nl2br(e($solicitud->observaciones_vendedor)) !!}</div>
                    </div>
                </div>
            @endif

            {{-- Productos Cotizados --}}
            <div class="card shadow mb-4">
                <div class="card-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                    <h6 class="mb-0" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                        <i class="bi bi-box-seam me-2"></i>Productos Cotizados
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Referencia</th>
                                    <th>Producto</th>
                                    <th>Variante</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitud->items as $item)
                                    <tr>
                                        <td><code>{{ $item->referencia_producto }}</code></td>
                                        <td>{{ $item->nombre_producto }}</td>
                                        <td>{{ $item->info_variante ?: '-' }}</td>
                                        <td class="text-center">{{ $item->cantidad }}</td>
                                        <td class="text-end">${{ number_format($item->precio_unitario, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->precio_total, 2) }}</td>
                                        <td>
                                            @if($item->observacion)
                                                <small class="text-muted">{{ $item->observacion }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $subtotal = $solicitud->items->sum('precio_total');
                                    $flete = $solicitud->valor_flete ?? 0;
                                    $descuento = $solicitud->descuento_total ?? 0;
                                    $porcentajeIva = $solicitud->porcentaje_iva ?? 0;
                                    $valorIva = $solicitud->valor_iva ?? 0;
                                    $totalConIva = $solicitud->monto_total + $valorIva;
                                    $aplicaFleteCliente = (bool) ($solicitud->cliente->aplica_flete ?? false);
                                @endphp
                                <tr>
                                    <th colspan="5" class="text-end">Subtotal:</th>
                                    <th class="text-end">${{ number_format($subtotal, 2) }}</th>
                                    <td></td>
                                </tr>
                                @if($flete > 0)
                                    <tr>
                                        <td colspan="5" class="text-end">Flete:</td>
                                        <td class="text-end">${{ number_format($flete, 2) }}</td>
                                        <td></td>
                                    </tr>
                                @elseif($aplicaFleteCliente)
                                    <tr>
                                        <td colspan="5" class="text-end">Flete:</td>
                                        <td class="text-end"><span class="badge bg-info">Aplica flete</span></td>
                                        <td></td>
                                    </tr>
                                @endif
                                @if($descuento > 0)
                                    <tr>
                                        <td colspan="5" class="text-end">Descuento:</td>
                                        <td class="text-end text-danger">-${{ number_format($descuento, 2) }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                                @if($porcentajeIva > 0 && $valorIva > 0)
                                    <tr>
                                        <td colspan="5" class="text-end">IVA ({{ number_format($porcentajeIva, 0) }}%):</td>
                                        <td class="text-end">${{ number_format($valorIva, 2) }}</td>
                                        <td></td>
                                    </tr>
                                @endif
                                <tr style="background-color: var(--miracle-lilac-light);">
                                    <th colspan="5" class="text-end" style="color: var(--miracle-dark);">Total:</th>
                                    <th class="text-end" style="color: var(--miracle-dark); font-size: 1.1rem;">${{ number_format($totalConIva, 2) }}</th>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Observaciones del admin --}}
            @if($solicitud->estado === 'aplicada' && $solicitud->observaciones_admin)
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h6 style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-shield-check me-2"></i>Observaciones del Administrador
                            <small class="text-muted">— {{ $solicitud->aplicadaPor?->name ?? 'Administrador' }}</small>
                        </h6>
                        <div class="alert alert-secondary mb-0">{!! nl2br(e($solicitud->observaciones_admin)) !!}</div>
                    </div>
                </div>
            @endif

            {{-- Motivo de rechazo --}}
            @if($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h6 style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-x-octagon me-2"></i>Motivo del Rechazo
                            <small class="text-muted">— {{ $solicitud->rechazadaPor?->name ?? 'Administrador' }}</small>
                        </h6>
                        <div class="alert alert-warning mb-0">{!! nl2br(e($solicitud->motivo_rechazo)) !!}</div>
                    </div>
                </div>
            @endif

            {{-- Historial de Pagos --}}
            @if($solicitud->pagos->count() > 0)
                @php
                    $metodosPago = \App\Models\SolicitudCotizacion::METODOS_PAGO;
                @endphp
                <div class="card shadow mb-4">
                    <div class="card-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                        <h6 class="mb-0" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-clock-history me-2"></i>Historial de Pagos
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- Resumen de pago --}}
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <div class="p-3 rounded" style="background-color: var(--miracle-lilac-light);">
                                    <small class="text-muted d-block">Total con IVA</small>
                                    <strong class="fs-5" style="color: var(--miracle-dark);">$ {{ number_format($solicitud->monto_total_con_iva, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="p-3 rounded" style="background-color: #e8f5e9;">
                                    <small class="text-muted d-block">Pagado (aprobado)</small>
                                    <strong class="text-success fs-5">$ {{ number_format($solicitud->monto_pagado, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="p-3 rounded" style="background-color: #ffebee;">
                                    <small class="text-muted d-block">Saldo Pendiente</small>
                                    <strong class="text-danger fs-5">$ {{ number_format($solicitud->saldo_pendiente, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Info forma de pago / crédito --}}
                        @if($solicitud->forma_pago_factura)
                            @php
                                $esCredito = str_contains($solicitud->forma_pago_factura, 'Crédito');
                                $esMixto = str_contains($solicitud->forma_pago_factura, 'Mixto');
                            @endphp
                            <div class="alert {{ $esCredito ? 'alert-info' : 'alert-light' }} py-2 mb-3">
                                <div class="row text-center">
                                    <div class="col-md-{{ $solicitud->fecha_vencimiento ? '4' : '12' }}">
                                        <small class="text-muted d-block">Forma de Pago</small>
                                        <strong>{{ $solicitud->forma_pago_factura }}</strong>
                                    </div>
                                    @if($esMixto && $solicitud->monto_credito)
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Valor a Crédito</small>
                                            <strong class="text-info">$ {{ number_format($solicitud->monto_credito, 0, ',', '.') }}</strong>
                                        </div>
                                    @endif
                                    @if($solicitud->fecha_vencimiento)
                                        @php
                                            $diasRestantes = now()->diffInDays($solicitud->fecha_vencimiento, false);
                                            $colorDias = $diasRestantes < 0 ? 'text-danger' : ($diasRestantes <= 7 ? 'text-warning' : 'text-success');
                                        @endphp
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Fecha de Vencimiento</small>
                                            <strong>{{ $solicitud->fecha_vencimiento->format('d/m/Y') }}</strong>
                                            <span class="{{ $colorDias }} small">
                                                @if($diasRestantes < 0)
                                                    (Vencido hace {{ abs((int) $diasRestantes) }} días)
                                                @else
                                                    ({{ (int) $diasRestantes }} días restantes)
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Alerta de pagos pendientes de aprobación --}}
                        @php
                            $pagosPendientesAprobacion = $solicitud->pagos->where('estado', 'pendiente');
                        @endphp
                        @if($pagosPendientesAprobacion->count() > 0)
                            <div class="alert alert-warning py-2 text-center small mb-3">
                                <i class="bi bi-clock me-1"></i>
                                {{ $pagosPendientesAprobacion->count() }} pago(s) pendiente(s) de aprobación por
                                <strong>$ {{ number_format($pagosPendientesAprobacion->sum('monto'), 0, ',', '.') }}</strong>
                            </div>
                        @endif

                        {{-- Tabla de pagos --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Registrado por</th>
                                        <th>Estado</th>
                                        <th>Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($solicitud->pagos->sortBy('created_at') as $index => $pago)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                                            <td><strong>$ {{ number_format($pago->monto, 0, ',', '.') }}</strong></td>
                                            <td>{{ $metodosPago[$pago->metodo_pago] ?? $pago->metodo_pago }}</td>
                                            <td>{{ $pago->registradoPor?->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $pago->color_estado }}">{{ $pago->etiqueta_estado }}</span>
                                                @if($pago->estaAprobado() && $pago->aprobadoPor)
                                                    <br><small class="text-muted">por {{ $pago->aprobadoPor->name }}</small>
                                                @endif
                                                @if($pago->estaRechazado() && $pago->aprobadoPor)
                                                    <br><small class="text-muted">por {{ $pago->aprobadoPor->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pago->comprobante)
                                                    @php
                                                        $comprobantes = is_string($pago->comprobante) ? [$pago->comprobante] : (is_array($pago->comprobante) ? $pago->comprobante : []);
                                                    @endphp
                                                    @foreach($comprobantes as $idx => $comp)
                                                        <a href="/solicitudes/{{ $solicitud->id }}/pagos/{{ $pago->id }}/comprobante?index={{ $idx }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                            <i class="bi bi-download me-1"></i> {{ count($comprobantes) > 1 ? 'Archivo ' . ($idx + 1) : 'Descargar' }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($pago->notas)
                                            <tr>
                                                <td colspan="7" class="py-1 px-3 bg-light border-0" style="border-top: none !important;">
                                                    <small><i class="bi bi-chat-text me-1 text-primary"></i><strong>Nota:</strong> {{ $pago->notas }}</small>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Badge estado de pago --}}
                        <div class="text-end">
                            @if($solicitud->color_estado_pago === 'pink')
                                <span class="badge fs-6" style="background-color:#FF84D5;color:#fff;">
                            @else
                                <span class="badge bg-{{ $solicitud->color_estado_pago }} fs-6">
                            @endif
                                Estado de Pago: {{ $solicitud->etiqueta_estado_pago }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Descuento de Stock --}}
            @if($solicitud->estado === 'aplicada')
                <div class="card shadow mb-4">
                    <div class="card-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                        <h6 class="mb-0" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-box-seam me-2"></i>Descuento de Stock
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($solicitud->stock_descontado)
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Stock descontado</strong> por <strong>{{ $solicitud->stockDescontadoPor?->name ?? 'Sistema' }}</strong>
                                el {{ $solicitud->stock_descontado_en->format('d/m/Y H:i') }}
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Stock pendiente de descontar.</strong>
                                <span class="text-muted">El stock de los productos no ha sido descontado del inventario.</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Información de Envío --}}
            @if($solicitud->estado === 'aplicada')
                <div class="card shadow mb-4">
                    <div class="card-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                        <h6 class="mb-0" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-truck me-2"></i>Información de Envío
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <small class="text-muted d-block">Estado de Envío</small>
                                <span class="badge bg-{{ $solicitud->color_estado_envio }}">
                                    <i class="bi {{ $solicitud->icono_estado_envio }} me-1"></i>{{ $solicitud->etiqueta_estado_envio }}
                                </span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <small class="text-muted d-block">Transportadora</small>
                                <span>{{ $solicitud->transportadora ? e($solicitud->transportadora) : '-' }}</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <small class="text-muted d-block">Número de Guía</small>
                                @if($solicitud->numero_guia)
                                    <code>{{ $solicitud->numero_guia }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                            @if($solicitud->despachado_en)
                                <div class="col-md-3 mb-2">
                                    <small class="text-muted d-block">Despachado</small>
                                    @php
                                        $despachadoPor = $solicitud->despachado_por ? \App\Models\User::find($solicitud->despachado_por)?->name : null;
                                    @endphp
                                    <span>{{ $despachadoPor ? $despachadoPor . ' — ' : '' }}{{ $solicitud->despachado_en->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                            @if($solicitud->entregado_en)
                                <div class="col-md-3 mb-2">
                                    <small class="text-muted d-block">Entregado</small>
                                    <span>{{ $solicitud->entregado_en->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        @if($solicitud->archivo_guia)
                            @php
                                $esImagen = preg_match('/\.(jpg|jpeg|png|webp)$/i', $solicitud->archivo_guia);
                            @endphp
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1">Archivo de Guía:</small>
                                @if($esImagen)
                                    <img src="/{{ e($solicitud->archivo_guia) }}" class="img-thumbnail" style="max-height: 200px;">
                                    <a href="/{{ e($solicitud->archivo_guia) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-eye me-1"></i>Ver completa
                                    </a>
                                @else
                                    <a href="/{{ e($solicitud->archivo_guia) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Ver PDF de guía
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Historial de Cambios --}}
            @if($solicitud->historialEstados && $solicitud->historialEstados->count() > 0)
                @php
                    $iconosTipo = [
                        'estado' => 'bi-flag-fill text-warning',
                        'envio' => 'bi-truck text-info',
                        'pago' => 'bi-credit-card text-success',
                    ];
                    $etiquetasTipo = [
                        'estado' => 'Estado',
                        'envio' => 'Envío',
                        'pago' => 'Pago',
                    ];
                @endphp
                <div class="card shadow mb-4">
                    <div class="card-header" style="background-color: var(--miracle-lilac-light); border-bottom: 2px solid var(--miracle-pink);">
                        <h6 class="mb-0" style="font-family: 'Comfortaa', cursive; color: var(--miracle-dark);">
                            <i class="bi bi-clock-history me-2"></i>Historial de Cambios
                        </h6>
                    </div>
                    <div class="card-body">
                        <div style="max-height: 400px; overflow-y: auto;">
                            @foreach($solicitud->historialEstados as $h)
                                @php
                                    $icono = $iconosTipo[$h->tipo_cambio] ?? 'bi-circle text-secondary';
                                    $etiquetaTipo = $etiquetasTipo[$h->tipo_cambio] ?? $h->tipo_cambio;
                                    $usuario = $h->usuario?->name ?? 'Sistema';
                                @endphp
                                <div class="d-flex align-items-start mb-2 small border-start border-2 ps-3">
                                    <div class="flex-grow-1">
                                        <i class="bi {{ $icono }} me-1"></i>
                                        <span class="badge bg-light text-dark me-1">{{ $etiquetaTipo }}</span>
                                        <strong>{{ e($usuario) }}</strong> cambió de
                                        <span class="badge bg-secondary">{{ $h->estado_anterior ?? '-' }}</span>
                                        a <span class="badge bg-primary">{{ $h->estado_nuevo }}</span>

                                        @php $datos = $h->datos_adicionales; @endphp
                                        @if(!empty($datos))
                                            @php
                                                $detalles = [];
                                                if (!empty($datos['transportadora'])) $detalles[] = 'Transportadora: ' . e($datos['transportadora']);
                                                if (!empty($datos['numero_guia'])) $detalles[] = 'Guía: ' . e($datos['numero_guia']);
                                                if (!empty($datos['archivo_guia'])) {
                                                    $esImg = preg_match('/\.(jpg|jpeg|png|webp)$/i', $datos['archivo_guia']);
                                                    if ($esImg) {
                                                        $detalles[] = '<a href="/' . e($datos['archivo_guia']) . '" target="_blank"><i class="bi bi-image me-1"></i>Ver imagen</a>';
                                                    } else {
                                                        $detalles[] = '<a href="/' . e($datos['archivo_guia']) . '" target="_blank"><i class="bi bi-file-pdf me-1"></i>Ver PDF</a>';
                                                    }
                                                }
                                            @endphp
                                            @if(!empty($detalles))
                                                <br><small class="text-muted">{!! implode(' | ', $detalles) !!}</small>
                                            @endif
                                        @endif

                                        @if($h->observaciones)
                                            <br><small class="text-muted"><i class="bi bi-chat-text me-1"></i>{{ e($h->observaciones) }}</small>
                                        @endif

                                        <br><small class="text-muted">{{ $h->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
