<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $venta->numero_venta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 80mm;
            padding: 5mm;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
        }
        .info {
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .items {
            margin-bottom: 10px;
        }
        .item {
            margin-bottom: 8px;
        }
        .item-name {
            font-weight: bold;
        }
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .total-final {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        .anulada {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #666;
            border: 3px solid #666;
            padding: 10px;
            margin: 10px 0;
            transform: rotate(-15deg);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MIRACLE</h1>
        <p>{{ $venta->ubicacion->nombre ?? 'Punto de Venta' }}</p>
        <p>{{ $venta->ubicacion->direccion ?? '' }}</p>
    </div>

    @if($venta->estado === 'anulada')
        <div class="anulada">*** ANULADA ***</div>
    @endif

    <div class="info">
        <div class="info-row">
            <span>N° Venta:</span>
            <span>{{ $venta->numero_venta }}</span>
        </div>
        <div class="info-row">
            <span>Fecha:</span>
            <span>{{ $venta->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span>Hora:</span>
            <span>{{ $venta->created_at->format('H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span>Vendedor:</span>
            <span>{{ $venta->usuario->name ?? '-' }}</span>
        </div>
        @if($venta->cliente || $venta->nombre_cliente)
            <div class="info-row">
                <span>Cliente:</span>
                <span>{{ $venta->nombre_cliente_display }}</span>
            </div>
        @endif
    </div>

    <div class="divider"></div>

    <div class="items">
        @foreach($venta->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->producto->nombre ?? 'Producto' }}</div>
                @if($item->variante)
                    <div style="font-size: 10px;">{{ $item->variante->referencia_variante }}</div>
                @endif
                <div class="item-detail">
                    <span>{{ $item->cantidad }} x ${{ number_format($item->precio_unitario, 0, ',', '.') }}</span>
                    <span>${{ number_format($item->total, 0, ',', '.') }}</span>
                </div>
                @if($item->descuento > 0)
                    <div class="item-detail" style="color: #666;">
                        <span>Desc:</span>
                        <span>-${{ number_format($item->descuento, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>${{ number_format($venta->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($venta->descuento > 0)
            <div class="total-row">
                <span>Descuento:</span>
                <span>-${{ number_format($venta->descuento, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($venta->iva > 0)
            <div class="total-row">
                <span>IVA:</span>
                <span>${{ number_format($venta->iva, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="total-row total-final">
            <span>TOTAL:</span>
            <span>${{ number_format($venta->total, 0, ',', '.') }}</span>
        </div>

        <div style="margin-top: 10px;">
            <div class="total-row">
                <span>Método:</span>
                <span>{{ ucfirst($venta->metodo_pago) }}</span>
            </div>
            @if($venta->metodo_pago === 'efectivo' && $venta->monto_efectivo)
                <div class="total-row">
                    <span>Recibido:</span>
                    <span>${{ number_format($venta->monto_efectivo, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Cambio:</span>
                    <span>${{ number_format($venta->monto_efectivo - $venta->total, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($venta->metodo_pago === 'mixto')
                @if($venta->monto_efectivo > 0)
                    <div class="total-row" style="font-size: 10px;">
                        <span>Efectivo:</span>
                        <span>${{ number_format($venta->monto_efectivo, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($venta->monto_tarjeta > 0)
                    <div class="total-row" style="font-size: 10px;">
                        <span>Tarjeta:</span>
                        <span>${{ number_format($venta->monto_tarjeta, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($venta->monto_transferencia > 0)
                    <div class="total-row" style="font-size: 10px;">
                        <span>Transferencia:</span>
                        <span>${{ number_format($venta->monto_transferencia, 0, ',', '.') }}</span>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @if($venta->notas)
        <div class="divider"></div>
        <div style="font-size: 10px;">
            <strong>Notas:</strong> {{ $venta->notas }}
        </div>
    @endif

    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
