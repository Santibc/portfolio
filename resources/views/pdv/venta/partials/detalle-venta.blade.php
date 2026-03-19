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
    @if($venta->notas)
    <div class="mt-3 p-3 bg-light rounded">
        <strong><i class="bi bi-chat-text me-1"></i>Observaciones:</strong>
        <p class="mb-0 mt-1">{{ $venta->notas }}</p>
    </div>
    @endif
</div>
<div class="modal-footer">
    <a href="{{ route('pdv.ventas.ticket', $venta->id) }}" class="btn btn-sm btn-outline-danger" target="_blank">
        <i class="bi bi-printer me-1"></i>Imprimir Ticket
    </a>
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>
