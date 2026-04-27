<div class="modal-header">
    <h6 class="modal-title fw-bold"><i class="bi bi-receipt me-2"></i>{{ $prefactura->numero_prefactura }}</h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row g-3 mb-3">
        <div class="col-md-4"><small class="text-muted d-block">Cliente</small><strong>{{ $prefactura->nombre_cliente_display }}</strong></div>
        <div class="col-md-4"><small class="text-muted d-block">Creada por</small><strong>{{ $prefactura->usuarioCreador->name ?? '-' }}</strong></div>
        @if($prefactura->vendedora_prefactura)
            <div class="col-md-4"><small class="text-muted d-block">Vendedora</small><strong>{{ $prefactura->vendedora_prefactura }}</strong></div>
        @endif
        <div class="col-md-4"><small class="text-muted d-block">Estado</small>{!! $prefactura->estado_badge !!}</div>
        <div class="col-md-4"><small class="text-muted d-block">Fecha</small><strong>{{ $prefactura->created_at->format('d/m/Y h:i A') }}</strong></div>
        @if($prefactura->usuarioCajero)
            <div class="col-md-4"><small class="text-muted d-block">Aceptada por</small><strong>{{ $prefactura->usuarioCajero->name }}</strong></div>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead class="table-light">
                <tr><th>Producto</th><th>Variante</th><th class="text-center">Cant.</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach($prefactura->items as $item)
                    <tr>
                        <td>{{ $item->producto->nombre ?? '-' }}</td>
                        <td>{{ $item->variante ? $item->variante->referencia_variante : '-' }}</td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-end">${{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="text-end fw-semibold">${{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-active"><td colspan="4" class="text-end fw-bold">TOTAL:</td><td class="text-end fw-bold">${{ number_format($prefactura->total, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
    @if($prefactura->observaciones)
        <div class="p-2 bg-light rounded"><strong>Observaciones:</strong> {{ $prefactura->observaciones }}</div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
</div>
