<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            margin: 0;
            padding: 2mm;
            box-sizing: border-box;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; font-size: 10px; }
        .right { text-align: right; }
        .small { font-size: 8px; }
        .watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%,-50%) rotate(-45deg);
            font-size: 30px; color: rgba(255,0,0,0.15); font-weight: bold;
        }
    </style>
</head>
<body>
    @if($venta->estado === 'anulada')
        <div class="watermark">ANULADA</div>
    @endif

    <div class="center bold" style="font-size:12px;">MIRACLE BEAUTY EXPERTS</div>
    @if($venta->caja && $venta->caja->ubicacion)
        <div class="center small">{{ $venta->caja->ubicacion->nombre }}</div>
        <div class="center small">{{ $venta->caja->ubicacion->direccion ?? '' }}</div>
    @endif
    <div class="line"></div>

    <div class="center bold">TICKET DE VENTA</div>
    <div>Nro: {{ $venta->numero_venta }}</div>
    <div>Fecha: {{ $venta->created_at->format('d/m/Y h:i A') }}</div>
    <div>Cajero: {{ $venta->usuario->name ?? '-' }}</div>
    @php
        $nombreCliente = $venta->nombre_cliente ?? null;
        if (!$nombreCliente && $venta->cliente_id) {
            $cl = $venta->cliente;
            $nombreCliente = $cl ? ($cl->razon_social ?: $cl->nombre_contacto) : null;
        }
        $nombreCliente = $nombreCliente ?: 'Consumidor Final';
    @endphp
    <div>Cliente: {{ Str::limit($nombreCliente, 30) }}</div>
    <div>Pago: {{ ucfirst($venta->metodo_pago) }}</div>
    <div class="line"></div>

    @foreach($venta->items as $item)
        <div>{{ $item->producto->referencia ?? '-' }} {{ Str::limit($item->producto->nombre ?? '', 20) }}</div>
        <div>
            <span>{{ $item->cantidad }} x ${{ number_format($item->precio_unitario, 0) }}</span>
            <span style="float:right; font-weight:bold;">${{ number_format($item->total, 0) }}</span>
        </div>
        @if(($item->descuento_porcentaje ?? 0) > 0)
            <div class="small"> Desc: {{ number_format($item->descuento_porcentaje, 1) }}%
                (-${{ number_format($item->descuento_valor ?? 0, 0) }})
            </div>
        @endif
    @endforeach

    <div class="line"></div>
    <table>
        <tr><td>Subtotal:</td><td class="right">${{ number_format($venta->subtotal, 0) }}</td></tr>
        @if(($venta->descuento_global ?? 0) > 0)
            <tr><td>Descuento:</td><td class="right">-${{ number_format($venta->descuento_global, 0) }}</td></tr>
        @endif
        @if(($venta->iva ?? 0) > 0)
            <tr><td>IVA:</td><td class="right">${{ number_format($venta->iva, 0) }}</td></tr>
        @endif
        <tr class="bold" style="font-size:13px;">
            <td>TOTAL:</td>
            <td class="right">${{ number_format($venta->total, 0) }}</td>
        </tr>
    </table>

    @if($venta->metodo_pago === 'efectivo' && $venta->monto_recibido)
        <div class="line"></div>
        <div>Recibido: ${{ number_format($venta->monto_recibido, 0) }}</div>
        <div class="bold">Cambio: ${{ number_format($venta->cambio, 0) }}</div>
    @endif

    <div class="line"></div>
    <div class="center small">Gracias por su compra</div>
    <div class="center small">Miracle Beauty Experts</div>
</body>
</html>
