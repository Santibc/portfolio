<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $factura->numero }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #2d5a27;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .factura-details {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .factura-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .factura-details td {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .factura-details td:first-child {
            font-weight: 600;
            color: #666;
            width: 40%;
        }
        .factura-details tr:last-child td {
            border-bottom: none;
        }
        .total-row td {
            font-size: 18px;
            font-weight: bold;
            color: #2d5a27;
            padding-top: 15px !important;
        }
        .message {
            margin: 20px 0;
            color: #555;
        }
        .attachment-notice {
            background-color: #e8f5e9;
            border-left: 4px solid #2d5a27;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .attachment-notice strong {
            color: #2d5a27;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .footer a {
            color: #2d5a27;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MANZER AGROFORESTAL</h1>
            <p>Factura Electrónica</p>
        </div>

        <div class="content">
            <p class="greeting">
                Estimado/a {{ $cliente->persona_contacto ?? $cliente->nombre_comercial }},
            </p>

            <p class="message">
                Adjunto encontrará la factura correspondiente a los servicios prestados.
                A continuación le detallamos la información principal:
            </p>

            <div class="factura-details">
                <table>
                    <tr>
                        <td>Número de Factura:</td>
                        <td><strong>{{ $factura->numero }}</strong></td>
                    </tr>
                    <tr>
                        <td>Fecha de Emisión:</td>
                        <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                    </tr>
                    @if($factura->fecha_vencimiento)
                    <tr>
                        <td>Fecha de Vencimiento:</td>
                        <td>{{ $factura->fecha_vencimiento->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($factura->obra)
                    <tr>
                        <td>Obra/Proyecto:</td>
                        <td>{{ $factura->obra->codigo }} - {{ $factura->obra->nombre }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Base Imponible:</td>
                        <td>{{ number_format($factura->base_imponible, 2, ',', '.') }} €</td>
                    </tr>
                    <tr>
                        <td>IVA ({{ $factura->iva_porcentaje }}%):</td>
                        <td>{{ number_format($factura->iva_importe, 2, ',', '.') }} €</td>
                    </tr>
                    @if($factura->retencion_importe > 0)
                    <tr>
                        <td>Retención ({{ $factura->retencion_porcentaje }}%):</td>
                        <td>-{{ number_format($factura->retencion_importe, 2, ',', '.') }} €</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>TOTAL:</td>
                        <td>{{ number_format($factura->total, 2, ',', '.') }} €</td>
                    </tr>
                </table>
            </div>

            <div class="attachment-notice">
                <strong>📎 Documento adjunto:</strong> Factura_{{ $factura->numero }}.pdf
            </div>

            <p class="message">
                Si tiene alguna pregunta o necesita información adicional,
                no dude en ponerse en contacto con nosotros.
            </p>

            <p class="message">
                Atentamente,<br>
                <strong>Manzer Agroforestal, S.R.L.U.</strong>
            </p>
        </div>

        <div class="footer">
            <p>
                <strong>MANZER AGROFORESTAL, S.R.L.U.</strong><br>
                Este es un mensaje automático generado por el sistema de facturación.
            </p>
            <p style="margin-top: 10px;">
                © {{ date('Y') }} Manzer Agroforestal. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
