<div class="modal-header">
    <h6 class="modal-title fw-bold"><i class="bi bi-receipt me-2"></i>{{ $venta->numero_venta }}</h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-4">
            <small class="text-muted d-block">Cliente</small>
            <strong>{{ $venta->nombre_cliente ?: ($venta->cliente ? ($venta->cliente->razon_social ?: $venta->cliente->nombre_contacto) : 'Consumidor Final') }}</strong>
        </div>
        <div class="col-md-4">
            <small class="text-muted d-block">Caja</small>
            <strong>{{ $venta->caja->nombre ?? '-' }}</strong>
        </div>
        <div class="col-md-4">
            <small class="text-muted d-block">Cajero</small>
            <strong>{{ $venta->usuario->name ?? '-' }}</strong>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <small class="text-muted d-block">Fecha</small>
            <strong>{{ $venta->created_at->format('d/m/Y h:i A') }}</strong>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Metodo</small>
            <strong>{{ ucfirst($venta->metodo_pago ?? '-') }}</strong>
            @if($venta->tipo_transferencia)
                <small class="text-muted">({{ $venta->tipo_transferencia }})</small>
            @endif
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Estado</small>
            @if($venta->estado === 'completada')
                <span class="badge bg-success">Completada</span>
            @elseif($venta->estado === 'anulada')
                <span class="badge bg-danger">Anulada</span>
            @else
                <span class="badge bg-secondary">{{ ucfirst($venta->estado) }}</span>
            @endif
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Lista de Precios</small>
            <strong>{{ $venta->listaPrecio->nombre ?? '-' }}</strong>
        </div>
    </div>

    @if($venta->esta_anulada)
        <div class="alert alert-danger py-2 mb-3">
            <strong>Anulada por:</strong> {{ $venta->anulador->name ?? '-' }} |
            <strong>Fecha:</strong> {{ $venta->anulada_en?->format('d/m/Y h:i A') }} |
            <strong>Motivo:</strong> {{ $venta->motivo_anulacion }}
        </div>
    @endif

    @if($venta->metodo_pago === 'efectivo' && $venta->monto_recibido)
    <div class="row mb-3">
        <div class="col-md-4">
            <small class="text-muted d-block">Monto Recibido</small>
            <strong>${{ number_format($venta->monto_recibido, 0) }}</strong>
        </div>
        <div class="col-md-4">
            <small class="text-muted d-block">Cambio</small>
            <strong>${{ number_format($venta->cambio, 0) }}</strong>
        </div>
    </div>
    @endif

    @if($venta->metodo_pago === 'mixto')
    <div class="row mb-3">
        <div class="col-md-3">
            <small class="text-muted d-block">Efectivo</small>
            <strong>${{ number_format($venta->monto_efectivo ?? 0, 0) }}</strong>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Transferencia</small>
            <strong>${{ number_format($venta->monto_transferencia ?? 0, 0) }}</strong>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Tipo</small>
            <strong>{{ $venta->tipo_transferencia ?? '-' }}</strong>
        </div>
        @if($venta->monto_recibido && $venta->cambio > 0)
        <div class="col-md-3">
            <small class="text-muted d-block">Cambio</small>
            <strong>${{ number_format($venta->cambio, 0) }}</strong>
        </div>
        @endif
    </div>
    @endif

    <hr>

    {{-- Items table --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-end">P. Unit.</th>
                    <th class="text-center">Desc.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->items as $item)
                <tr>
                    <td>
                        {{ $item->producto->nombre ?? '-' }}
                        <small class="text-muted d-block">{{ $item->producto->referencia ?? '' }}</small>
                    </td>
                    <td>{{ $item->variante->nombre_variante ?? '-' }}</td>
                    <td class="text-center">{{ $item->cantidad }}</td>
                    <td class="text-end">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="text-center">
                        @if(($item->descuento_porcentaje ?? 0) > 0)
                            <span class="badge bg-warning text-dark">{{ number_format($item->descuento_porcentaje, 1) }}%</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end fw-semibold">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="row justify-content-end">
        <div class="col-md-5">
            <table class="table table-sm mb-0">
                <tr><td>Subtotal:</td><td class="text-end">${{ number_format($venta->subtotal, 2) }}</td></tr>
                @if(($venta->descuento_global ?? 0) > 0)
                <tr><td>Descuento global:</td><td class="text-end text-danger">-${{ number_format($venta->descuento_global, 2) }}</td></tr>
                @endif
                @if(($venta->iva ?? 0) > 0)
                <tr><td>IVA:</td><td class="text-end">${{ number_format($venta->iva, 2) }}</td></tr>
                @endif
                <tr class="fw-bold" style="font-size:1.1em;"><td>TOTAL:</td><td class="text-end">${{ number_format($venta->total, 2) }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Comprobante de pago --}}
    @if($venta->comprobante_pago)
    <div class="mt-3 p-3 bg-light rounded">
        <strong><i class="bi bi-paperclip me-1"></i>Comprobante de pago:</strong>
        <a href="{{ asset($venta->comprobante_pago) }}" target="_blank" class="ms-2">
            <i class="bi bi-file-earmark me-1"></i>Ver comprobante
        </a>
    </div>
    @endif

    {{-- Observaciones --}}
    @php
        $notasLimpias = $venta->notas ? preg_replace('/\s*\[PREFACTURA_ORIGINAL:.*?\]/', '', $venta->notas) : '';
        $notasLimpias = trim($notasLimpias);
    @endphp
    @if($notasLimpias)
    <div class="mt-3 p-3 bg-light rounded">
        <strong><i class="bi bi-chat-text me-1"></i>Observaciones:</strong>
        <p class="mb-0 mt-1">{{ $notasLimpias }}</p>
    </div>
    @endif

    {{-- Prefactura comparison --}}
    @if($venta->prefactura_id)
        @php
            $prefacturaOrig = \App\Models\Prefactura::with('items.producto', 'items.variante', 'usuarioCreador')->find($venta->prefactura_id);
        @endphp
        @if($prefacturaOrig)
        <div class="mt-3 p-3 border rounded" style="border-color: var(--miracle-lilac) !important;">
            <h6 class="fw-bold mb-2">
                <i class="bi bi-receipt me-2"></i>Origen: Prefactura {{ $prefacturaOrig->numero_prefactura }}
                <small class="text-muted fw-normal">— Creada por {{ $prefacturaOrig->usuarioCreador->name ?? '-' }} ({{ $prefacturaOrig->created_at->format('d/m/Y h:i A') }})</small>
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="font-size: 0.85em;">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant. Original</th>
                            <th class="text-center">Cant. Final</th>
                            <th class="text-end">Precio Original</th>
                            <th class="text-end">Precio Final</th>
                            <th class="text-center">Cambio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $itemsVenta = $venta->items->keyBy(function($i) {
                                return $i->producto_id . '-' . ($i->variante_producto_id ?? 'null');
                            });
                            $itemsPref = $prefacturaOrig->items->keyBy(function($i) {
                                return $i->producto_id . '-' . ($i->variante_producto_id ?? 'null');
                            });
                            $allKeys = $itemsPref->keys()->merge($itemsVenta->keys())->unique();
                        @endphp
                        @foreach($allKeys as $key)
                            @php
                                $orig = $itemsPref->get($key);
                                $final = $itemsVenta->get($key);
                                $nombre = ($orig ? $orig->producto->nombre : $final->producto->nombre) ?? '-';
                                $ref = ($orig ? $orig->producto->referencia : $final->producto->referencia) ?? '';
                                $cantOrig = $orig ? $orig->cantidad : 0;
                                $cantFinal = $final ? $final->cantidad : 0;
                                $precioOrig = $orig ? $orig->precio_unitario : 0;
                                $precioFinal = $final ? $final->precio_unitario : 0;
                                $cambio = ($cantOrig != $cantFinal || $precioOrig != $precioFinal);
                                $eliminado = !$final;
                                $agregado = !$orig;
                            @endphp
                            <tr class="{{ $eliminado ? 'table-danger' : ($agregado ? 'table-success' : ($cambio ? 'table-warning' : '')) }}">
                                <td>{{ $ref }} {{ $nombre }}</td>
                                <td class="text-center">{{ $eliminado ? $cantOrig : ($orig ? $cantOrig : '-') }}</td>
                                <td class="text-center">{{ $agregado ? $cantFinal : ($final ? $cantFinal : '-') }}</td>
                                <td class="text-end">{{ $orig ? '$'.number_format($precioOrig, 0) : '-' }}</td>
                                <td class="text-end">{{ $final ? '$'.number_format($precioFinal, 0) : '-' }}</td>
                                <td class="text-center">
                                    @if($eliminado)
                                        <span class="badge bg-danger">Eliminado</span>
                                    @elseif($agregado)
                                        <span class="badge bg-success">Agregado</span>
                                    @elseif($cambio)
                                        <span class="badge bg-warning text-dark">Modificado</span>
                                    @else
                                        <span class="badge bg-secondary">Sin cambio</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif
    {{-- Factura Electrónica SIIGO --}}
    @if($venta->facturaSiigo)
    <div class="mt-3 p-3 border rounded" style="border-color: var(--miracle-lilac) !important;">
        <h6 class="fw-bold mb-2">
            <i class="bi bi-receipt-cutoff me-2"></i>Factura Electrónica
            {!! $venta->facturaSiigo->estado_badge !!}
            @if($venta->facturaSiigo->tipo_documento === 'consumidor_final')
                <span class="badge bg-light text-dark border">Consumidor Final</span>
            @endif
        </h6>
        <div class="row g-2">
            @if($venta->facturaSiigo->numero_factura)
            <div class="col-md-4">
                <small class="text-muted d-block">N° Factura</small>
                <strong>{{ $venta->facturaSiigo->numero_factura }}</strong>
            </div>
            @endif
            @if($venta->facturaSiigo->cufe)
            <div class="col-12">
                <small class="text-muted d-block">CUFE</small>
                <code class="text-break" style="font-size: 0.75em;">{{ $venta->facturaSiigo->cufe }}</code>
            </div>
            @endif
            @if($venta->facturaSiigo->email_destino)
            <div class="col-md-6">
                <small class="text-muted d-block">Email</small>
                <span>{{ $venta->facturaSiigo->email_destino }}
                    @if($venta->facturaSiigo->estado_envio_email === 'enviado')
                        <i class="bi bi-check-circle text-success" title="Enviado"></i>
                    @elseif($venta->facturaSiigo->estado_envio_email === 'error')
                        <i class="bi bi-x-circle text-danger" title="Error de envío"></i>
                    @endif
                </span>
            </div>
            @endif
            @if($venta->facturaSiigo->errores)
            <div class="col-12">
                <div class="alert alert-danger py-1 mb-0 mt-1">
                    <small><strong>Error:</strong> {{ $venta->facturaSiigo->errores }}</small>
                </div>
            </div>
            @endif
        </div>
        <div class="mt-2 d-flex gap-2 flex-wrap">
            @if($venta->facturaSiigo->siigo_invoice_id)
                <a href="{{ route('pdv.ventas.factura.pdf', $venta->id) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                    <i class="bi bi-file-pdf me-1"></i>Descargar PDF
                </a>
                <button class="btn btn-sm btn-outline-primary" onclick="reenviarEmailFactura({{ $venta->id }})">
                    <i class="bi bi-envelope me-1"></i>Reenviar Email
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="consultarEstadoFactura({{ $venta->id }})">
                    <i class="bi bi-arrow-repeat me-1"></i>Actualizar Estado
                </button>
            @endif
            @if($venta->facturaSiigo->puedeReintentar())
                <button class="btn btn-sm btn-outline-warning" onclick="reintentarFactura({{ $venta->id }})">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar ({{ $venta->facturaSiigo->intentos }}/{{ \App\Models\ConfiguracionPdv::obtener('siigo_max_reintentos', 3) }})
                </button>
            @endif
        </div>
    </div>
    @endif

    {{-- Notas Crédito con reintento --}}
    @if($venta->facturaSiigo && $venta->facturaSiigo->notasCredito->count() > 0)
    <div class="mt-2 p-3 border rounded" style="border-color: #dc3545 !important; border-style: dashed !important;">
        <h6 class="fw-bold mb-2"><i class="bi bi-arrow-return-left me-2"></i>Notas Crédito</h6>
        @foreach($venta->facturaSiigo->notasCredito as $nc)
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            {!! $nc->estado_badge !!}
            <strong>{{ $nc->numero_factura ?? 'Sin número' }}</strong>
            <small class="text-muted">{{ $nc->created_at->format('d/m/Y h:i A') }}</small>
            @if($nc->errores)
                <span class="text-danger" style="font-size:0.8em;"><i class="bi bi-exclamation-triangle me-1"></i>{{ $nc->errores }}</span>
            @endif
            @if($nc->puedeReintentar())
                <button class="btn btn-sm btn-outline-warning py-0 px-2" onclick="reintentarNotaCredito({{ $venta->id }}, {{ $nc->id }})" style="font-size:0.8em;">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar NC ({{ $nc->intentos }}/{{ \App\Models\ConfiguracionPdv::obtener('siigo_max_reintentos', 3) }})
                </button>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- Logs SIIGO expandible --}}
    @if(isset($siigoLogs) && $siigoLogs->count() > 0)
    <div class="mt-2">
        <div class="border rounded" style="border-color: #6c757d !important;">
            <button class="btn btn-sm w-100 text-start d-flex align-items-center justify-content-between py-2 px-3"
                    type="button" data-bs-toggle="collapse" data-bs-target="#siigoLogsCollapse"
                    aria-expanded="false" style="background: #f8f9fa;">
                <span>
                    <i class="bi bi-terminal me-2"></i>
                    <strong>Logs SIIGO</strong>
                    <span class="badge bg-secondary ms-1">{{ $siigoLogs->count() }}</span>
                </span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div class="collapse" id="siigoLogsCollapse">
                <div class="p-2" style="max-height: 400px; overflow-y: auto;">
                    @foreach($siigoLogs as $log)
                    @php
                        $borderColor = $log->exitoso ? '#198754' : '#dc3545';
                        $bgColor = $log->exitoso ? '#f0fdf4' : '#fef2f2';
                        $iconClass = $log->exitoso ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger';
                    @endphp
                    <div class="mb-2 rounded border" style="border-color: {{ $borderColor }} !important; background: {{ $bgColor }}; font-size: 0.8em;">
                        {{-- Log header --}}
                        <div class="d-flex align-items-center justify-content-between px-2 py-1" style="border-bottom: 1px solid {{ $borderColor }};">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi {{ $iconClass }}"></i>
                                <strong>{{ $log->method }}</strong>
                                <code class="text-dark">{{ $log->endpoint }}</code>
                                @if($log->response_code)
                                    <span class="badge {{ $log->exitoso ? 'bg-success' : 'bg-danger' }}">{{ $log->response_code }}</span>
                                @endif
                            </div>
                            <div class="text-muted d-flex align-items-center gap-2">
                                @if($log->duracion_ms)
                                    <span>{{ $log->duracion_ms }}ms</span>
                                @endif
                                <span>{{ $log->created_at->format('h:i:s A') }}</span>
                            </div>
                        </div>
                        {{-- Error message --}}
                        @if($log->error_mensaje)
                        <div class="px-2 py-1 text-danger" style="border-bottom: 1px dashed {{ $borderColor }};">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $log->error_mensaje }}
                        </div>
                        @endif
                        {{-- Expandable request/response --}}
                        <div class="px-2 py-1">
                            @if($log->request_body)
                            <details class="mb-1">
                                <summary class="fw-semibold" style="cursor:pointer;"><i class="bi bi-arrow-up-circle me-1"></i>Request</summary>
                                <pre class="mb-0 mt-1 p-2 bg-white rounded border" style="font-size: 0.85em; max-height: 200px; overflow: auto; white-space: pre-wrap;">{{ json_encode($log->request_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                            @endif
                            @if($log->response_body)
                            <details>
                                <summary class="fw-semibold" style="cursor:pointer;"><i class="bi bi-arrow-down-circle me-1"></i>Response</summary>
                                <pre class="mb-0 mt-1 p-2 bg-white rounded border" style="font-size: 0.85em; max-height: 200px; overflow: auto; white-space: pre-wrap;">{{ json_encode($log->response_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route('pdv.ventas.ticket', $venta->id) }}" class="btn btn-sm btn-outline-danger" target="_blank">
        <i class="bi bi-printer me-1"></i>Imprimir Ticket
    </a>
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>
