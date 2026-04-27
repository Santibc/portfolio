<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 3mm 8mm 3mm 3mm;
            box-sizing: border-box;
            color: #000;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        .bold { font-weight: bold; }
        .small { font-size: 8.5px; }
        .xsmall { font-size: 7.5px; }
        .line-solid { border-top: 1px solid #000; margin: 4px 0; }
        .line-dashed { border-top: 1px dashed #000; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; }

        .logo { width: 50mm; height: auto; margin: 0 auto 2px; display: block; }

        .empresa { font-size: 12px; font-weight: bold; letter-spacing: 0.5px; }
        .info-empresa { font-size: 8.5px; line-height: 1.35; }

        .redes-titulo { font-size: 8.5px; margin-top: 3px; }
        .redes-tabla { width: auto; margin: 2px auto 0; }
        .redes-tabla td { font-size: 9px; padding: 1px 4px; }
        .ico-red { width: 11px; height: 11px; vertical-align: middle; }
        .red-icono { width: 16px; }
        .red-texto { vertical-align: middle; }

        .meta td { font-size: 9px; padding: 1px 0; }
        .meta .label { font-weight: bold; }

        .items thead td {
            font-size: 8.5px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
            padding: 3px 0;
        }
        .items tbody td { font-size: 9px; padding: 2px 0; }

        .totales td { font-size: 9.5px; padding: 1px 2px; }
        .total-final td {
            font-size: 12px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .pago-titulo {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            margin: 4px 0 2px;
            letter-spacing: 0.5px;
        }
        .pago td { font-size: 9px; padding: 1px 0; }

        .gracias {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }

        .watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%,-50%) rotate(-45deg);
            font-size: 30px; color: rgba(255,0,0,0.18); font-weight: bold;
        }
    </style>
</head>
<body>
    @if($venta->estado === 'anulada')
        <div class="watermark">ANULADA</div>
    @endif

    {{-- Encabezado: logo + datos empresa --}}
    <div class="center">
        <img src="{{ public_path('images/logo-black.png') }}" alt="Miracle Beauty Experts" class="logo">
    </div>
    <div class="center empresa">MIRACLE BEAUTY EXPERTS</div>
    <div class="center info-empresa">Dirección: Cr 21 # 9 - 10 Local 202</div>
    <div class="center info-empresa">Telefono: 321 964 8580 - 311 232 0134</div>

    {{-- Redes sociales --}}
    <div class="center redes-titulo bold">Visita Nuestras Redes</div>
    <table class="redes-tabla">
        <tr>
            <td class="right red-icono">
                <img src="{{ public_path('images/icons/instagram.png') }}" class="ico-red" alt="IG">
            </td>
            <td class="left red-texto">@miracleinternacional</td>
        </tr>
        <tr>
            <td class="right red-icono">
                <img src="{{ public_path('images/icons/facebook.png') }}" class="ico-red" alt="FB">
            </td>
            <td class="left red-texto">Miracle Beauty Experts</td>
        </tr>
    </table>

    <div class="line-solid"></div>

    {{-- Meta: fecha / recibo / hora / vendedor --}}
    @php
        $vendedor = $venta->vendedora_prefactura
            ?: ($venta->usuario->name ?? 'VENDEDOR GENERAL');
        $vendedor = strtoupper($vendedor);
    @endphp
    <table class="meta">
        <tr>
            <td><span class="label">Fecha:</span> {{ $venta->created_at->format('d/m/Y') }}</td>
            <td class="right"><span class="label">Recibo de Caja</span></td>
        </tr>
        <tr>
            <td><span class="label">Hora:</span> {{ $venta->created_at->format('h:i A') }}</td>
            <td class="right bold">{{ $venta->numero_venta }}</td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Vendedor:</span> {{ $vendedor }}</td>
        </tr>
        @php
            $nombreCliente = $venta->nombre_cliente ?? null;
            if (!$nombreCliente && $venta->cliente_id) {
                $cl = $venta->cliente;
                $nombreCliente = $cl ? ($cl->razon_social ?? $cl->nombre_contacto ?? null) : null;
            }
        @endphp
        @if($nombreCliente)
            <tr>
                <td colspan="2"><span class="label">Cliente:</span> {{ Str::limit($nombreCliente, 38) }}</td>
            </tr>
        @endif
    </table>

    {{-- Items --}}
    <table class="items">
        <thead>
            <tr>
                <td style="width: 40%;">DESCRIPCIÓN</td>
                <td class="right" style="width: 20%; padding-right:2px;">PRECIO U</td>
                <td class="right" style="width: 12%; padding-right:2px;">CANT</td>
                <td class="right" style="width: 28%; padding-right:2px;">TOTAL</td>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->items as $item)
                @php
                    $nombreItem = $item->producto->nombre ?? '-';
                    if ($item->variante) {
                        $extra = trim(($item->variante->talla ? $item->variante->talla : '')
                            . ' ' . ($item->variante->color ? $item->variante->color : ''));
                        if ($extra !== '') {
                            $nombreItem .= ' (' . $extra . ')';
                        }
                    }
                @endphp
                <tr>
                    <td style="word-wrap:break-word;">{{ Str::limit($nombreItem, 20) }}</td>
                    <td class="right" style="padding-right:2px;">{{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                    <td class="right" style="padding-right:2px;">{{ $item->cantidad }}</td>
                    <td class="right" style="padding-right:2px;">{{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @if(($item->descuento_porcentaje ?? 0) > 0 || ($item->descuento_valor ?? 0) > 0)
                    <tr>
                        <td colspan="4" class="xsmall">
                            &nbsp;&nbsp;Desc: {{ number_format($item->descuento_porcentaje ?? 0, 1) }}%
                            (-${{ number_format($item->descuento_valor ?? 0, 0, ',', '.') }})
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <table class="totales">
        @if(($venta->iva ?? 0) > 0)
            <tr>
                <td>Subtotal:</td>
                <td class="right" style="padding-right:2px;">${{ number_format($venta->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>IVA:</td>
                <td class="right" style="padding-right:2px;">${{ number_format($venta->iva, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="total-final">
            <td>Total</td>
            <td class="right" style="padding-right:2px;">${{ number_format($venta->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Detalle de pago: solo efectivo / transferencia / mixto --}}
    @php
        $metodo   = strtolower($venta->metodo_pago ?? '');
        $tipoTrf  = strtolower($venta->tipo_transferencia ?? '');

        $efectivo      = (float) ($venta->monto_efectivo ?? 0);
        $transferencia = (float) ($venta->monto_transferencia ?? 0);
        $cambio        = (float) ($venta->cambio ?? 0);

        // Pago 100% efectivo: si no se guardó el monto_efectivo, lo derivamos del total.
        if ($metodo === 'efectivo' && $efectivo <= 0) {
            $efectivo = (float) ($venta->monto_recibido ?? $venta->total);
        }
        // Pago 100% transferencia: si no se guardó el monto_transferencia, usamos el total.
        if ($metodo === 'transferencia' && $transferencia <= 0) {
            $transferencia = (float) $venta->total;
        }

        $descuento = (float) (($venta->descuento ?? 0) + ($venta->descuento_global ?? 0));

        $etiquetaTransfer = match ($tipoTrf) {
            'nequi'                  => 'Nequi',
            'daviplata'              => 'DaviPlata',
            'transferencia_bancaria' => 'Transferencia Bancaria',
            default                  => 'Transferencia',
        };

        $fmt = fn($v) => '$' . number_format($v, 0, ',', '.');
    @endphp

    <div class="pago-titulo">--------- DETALLE DE PAGO ---------</div>
    <table class="pago">
        @if($descuento > 0)
            <tr><td>Descuento</td><td class="right" style="padding-right:2px;">{{ $fmt($descuento) }}</td></tr>
        @endif
        @if($metodo === 'efectivo' || $metodo === 'mixto')
            <tr><td>Efectivo</td><td class="right" style="padding-right:2px;">{{ $fmt($efectivo) }}</td></tr>
            @if($cambio > 0)
                <tr><td>Su cambio</td><td class="right" style="padding-right:2px;">{{ $fmt($cambio) }}</td></tr>
            @endif
        @endif
        @if($metodo === 'transferencia' || $metodo === 'mixto')
            <tr><td>{{ $etiquetaTransfer }}</td><td class="right" style="padding-right:2px;">{{ $fmt($transferencia) }}</td></tr>
        @endif
        <tr>
            <td class="bold" style="border-top:1px dashed #000; padding-top:3px;">Total Pagado</td>
            <td class="right bold" style="border-top:1px dashed #000; padding-top:3px; padding-right:2px;">
                {{ $fmt($efectivo + $transferencia) }}
            </td>
        </tr>
    </table>

    <div class="line-solid"></div>
    <div class="gracias">**GRACIAS POR SU COMPRA**</div>
</body>
</html>
