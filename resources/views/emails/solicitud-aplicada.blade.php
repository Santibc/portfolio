<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Cotización Confirmada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #382E65;
            background-color: #faf8fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(188,169,245,0.2);
        }
        .header {
            background: linear-gradient(135deg, #B9DFDE 0%, #BCA9F5 100%);
            color: #382E65;
            padding: 10px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .info-box {
            background-color: #faf8fc;
            border-left: 4px solid #B9DFDE;
            padding: 15px;
            margin: 20px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th {
            background-color: #FF84D5;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #E8E1FA;
        }
        .total-row {
            background-color: #FFF1DD;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #382E65;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #BCA9F5;
        }
        .alert {
            background-color: #B9DFDE;
            border: 1px solid #9ed1cf;
            color: #382E65;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Miracle Beauty Experts" style="height: 60px; width: auto;">
        </div>

        <div class="header">
            <div class="success-icon">✓</div>
            <h1>¡Solicitud de Cotización Confirmada!</h1>
        </div>

        <p>Estimado/a <strong>{{ $solicitud->cliente->nombre_contacto }}</strong>,</p>
        
        <p>Nos complace informarle que su solicitud de cotización ha sido <strong>confirmada y procesada</strong> exitosamente.</p>
        
        <div class="info-box">
            <h3 style="margin-top: 0;">Detalles de la Solicitud:</h3>
            <p><strong>Número de Solicitud:</strong> {{ $solicitud->numero_solicitud }}</p>
            <p><strong>Fecha de Solicitud:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Fecha de Confirmación:</strong> {{ $solicitud->aplicada_en->format('d/m/Y H:i') }}</p>
            <p><strong>Procesada por:</strong> {{ $solicitud->aplicadaPor->name }}</p>
        </div>
        
        <h3>Resumen del Pedido:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotalItems = $solicitud->items->sum('precio_total');
                    $flete = $solicitud->valor_flete ?? 0;
                    $descuento = $solicitud->descuento_total ?? 0;
                    $porcentajeIva = $solicitud->porcentaje_iva ?? 0;
                    $valorIva = $solicitud->valor_iva ?? 0;
                    $totalFinal = $solicitud->monto_total + $valorIva;
                @endphp
                @foreach($solicitud->items as $item)
                <tr>
                    <td>
                        {{ $item->nombre_producto }}
                        @if($item->info_variante)
                            <br><small>{{ $item->info_variante }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->cantidad }}</td>
                    <td style="text-align: right;">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($item->precio_total, 2) }}</td>
                </tr>
                @endforeach
                {{-- Subtotal --}}
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                    <td style="text-align: right;"><strong>${{ number_format($subtotalItems, 2) }}</strong></td>
                </tr>
                {{-- Flete --}}
                @if($flete > 0)
                <tr>
                    <td colspan="3" style="text-align: right;">Flete:</td>
                    <td style="text-align: right;">${{ number_format($flete, 2) }}</td>
                </tr>
                @endif
                {{-- Descuento --}}
                @if($descuento > 0)
                <tr>
                    <td colspan="3" style="text-align: right;">Descuento:</td>
                    <td style="text-align: right; color: #dc3545;">-${{ number_format($descuento, 2) }}</td>
                </tr>
                @endif
                {{-- IVA --}}
                @if($porcentajeIva > 0 && $valorIva > 0)
                <tr>
                    <td colspan="3" style="text-align: right;">IVA ({{ number_format($porcentajeIva, 0) }}%):</td>
                    <td style="text-align: right;">${{ number_format($valorIva, 2) }}</td>
                </tr>
                @endif
                {{-- Total Final --}}
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td style="text-align: right;"><strong>${{ number_format($totalFinal, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        @if($solicitud->notas_cliente)
        <div class="info-box">
            <h4 style="margin-top: 0;">Sus notas:</h4>
            <p>{{ $solicitud->notas_cliente }}</p>
        </div>
        @endif
        
        @if($solicitud->observaciones_admin)
        <div class="info-box" style="border-left-color: #007bff;">
            <h4 style="margin-top: 0;">Observaciones del vendedor:</h4>
            <p>{{ $solicitud->observaciones_admin }}</p>
        </div>
        @endif
        
        <div class="alert">
            <strong>📎 Documento Adjunto:</strong><br>
            Encontrará adjunto a este correo el PDF con el detalle completo de su solicitud de cotización.
        </div>
        
        <h3>Próximos Pasos:</h3>
        <ol>
            <li>Revise el PDF adjunto con el detalle completo de su solicitud</li>
            <li>Su vendedor se pondrá en contacto con usted próximamente</li>
            <li>Si tiene alguna pregunta, no dude en contactarnos</li>
        </ol>
        
        <div class="footer">
            <p><strong>Información de Contacto:</strong><br>
            Vendedor: {{ $solicitud->cliente->vendedor->name }}<br>
            Email: {{ $solicitud->cliente->vendedor->email }}<br>
            @if($solicitud->cliente->vendedor->telefono)
            Teléfono: {{ $solicitud->cliente->vendedor->telefono }}
            @endif
            </p>
            <p>&copy; {{ date('Y') }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>