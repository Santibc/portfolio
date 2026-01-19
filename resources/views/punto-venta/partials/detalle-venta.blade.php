<div class="row">
    <div class="col-md-6">
        <h6 class="text-muted">Información de la Venta</h6>
        <table class="table table-sm">
            <tr>
                <th width="40%">N° Venta:</th>
                <td><strong>{{ $venta->numero_venta }}</strong></td>
            </tr>
            <tr>
                <th>Fecha:</th>
                <td>{{ $venta->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Ubicación:</th>
                <td>{{ $venta->ubicacion->nombre ?? '-' }}</td>
            </tr>
            <tr>
                <th>Vendedor:</th>
                <td>{{ $venta->usuario->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Estado:</th>
                <td>
                    @if($venta->estado === 'completada')
                        <span class="badge bg-success">Completada</span>
                    @else
                        <span class="badge bg-danger">Anulada</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="text-muted">Cliente y Pago</h6>
        <table class="table table-sm">
            <tr>
                <th width="40%">Cliente:</th>
                <td>{{ $venta->nombre_cliente_display }}</td>
            </tr>
            <tr>
                <th>Método de Pago:</th>
                <td>
                    @php
                        $colores = ['efectivo' => 'success', 'tarjeta' => 'primary', 'transferencia' => 'info', 'mixto' => 'warning'];
                    @endphp
                    <span class="badge bg-{{ $colores[$venta->metodo_pago] ?? 'secondary' }}">
                        {{ ucfirst($venta->metodo_pago) }}
                    </span>
                </td>
            </tr>
            @if($venta->metodo_pago === 'mixto')
                <tr>
                    <th>Efectivo:</th>
                    <td>${{ number_format($venta->monto_efectivo, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Tarjeta:</th>
                    <td>${{ number_format($venta->monto_tarjeta, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Transferencia:</th>
                    <td>${{ number_format($venta->monto_transferencia, 0, ',', '.') }}</td>
                </tr>
            @endif
        </table>
    </div>
</div>

@if($venta->notas)
    <div class="alert alert-info mb-3">
        <strong>Notas:</strong> {{ $venta->notas }}
    </div>
@endif

@if($venta->estado === 'anulada')
    <div class="alert alert-danger mb-3">
        <strong><i class="bi bi-x-circle"></i> Venta Anulada</strong><br>
        <small>Por: {{ $venta->anulador->name ?? 'Sistema' }} el {{ $venta->anulada_en ? $venta->anulada_en->format('d/m/Y H:i') : '-' }}</small><br>
        <strong>Motivo:</strong> {{ $venta->motivo_anulacion }}
    </div>
@endif

<h6 class="text-muted mt-3">Items de la Venta</h6>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead class="table-light">
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio Unit.</th>
                <th class="text-end">Descuento</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->producto->referencia ?? '-' }}</strong><br>
                        <small>{{ $item->nombre_completo_producto }}</small>
                    </td>
                    <td class="text-center">{{ $item->cantidad }}</td>
                    <td class="text-end">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                    <td class="text-end">${{ number_format($item->descuento, 0, ',', '.') }}</td>
                    <td class="text-end">${{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                <td class="text-end"><strong>${{ number_format($venta->subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @if($venta->descuento > 0)
                <tr>
                    <td colspan="4" class="text-end text-danger">Descuento:</td>
                    <td class="text-end text-danger">-${{ number_format($venta->descuento, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if($venta->iva > 0)
                <tr>
                    <td colspan="4" class="text-end">IVA:</td>
                    <td class="text-end">${{ number_format($venta->iva, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="4" class="text-end"><strong class="h5">TOTAL:</strong></td>
                <td class="text-end"><strong class="h5 text-success">${{ number_format($venta->total, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="text-end mt-3">
    <a href="{{ route('punto-venta.ticket', $venta->id) }}" class="btn btn-secondary" target="_blank">
        <i class="bi bi-printer"></i> Imprimir Ticket
    </a>
</div>
