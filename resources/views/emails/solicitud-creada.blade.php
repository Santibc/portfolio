<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Cotización Recibida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .alert {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✓</div>
            <h1>¡Solicitud Recibida!</h1>
        </div>

        <p>Estimado/a <strong>{{ $solicitud->cliente->nombre_contacto }}</strong>,</p>

        <p>Hemos recibido su solicitud de cotización exitosamente. Nuestro equipo la revisará y nos pondremos en contacto con usted a la brevedad.</p>

        <div class="info-box">
            <h3 style="margin-top: 0;">Detalles de la Solicitud:</h3>
            <p><strong>Número de Solicitud:</strong> {{ $solicitud->numero_solicitud }}</p>
            <p><strong>Fecha:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Estado:</strong> Pendiente de revisión</p>
        </div>

        <h3>Resumen de su Solicitud:</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    @if($solicitud->items->first() && $solicitud->items->first()->precio_unitario > 0)
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($solicitud->items as $item)
                <tr>
                    <td>
                        {{ $item->nombre_producto }}
                        @if($item->marca_producto)
                            <br><small style="color: #666;">Marca: {{ $item->marca_producto }}</small>
                        @endif
                        @if($item->info_variante)
                            <br><small>{{ $item->info_variante }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->cantidad }}</td>
                    @if($item->precio_unitario > 0)
                    <td style="text-align: right;">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($item->precio_total, 2) }}</td>
                    @endif
                </tr>
                @endforeach
                @if($solicitud->monto_total > 0)
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td style="text-align: right;">${{ number_format($solicitud->monto_total, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($solicitud->notas_cliente)
        <div class="info-box">
            <h4 style="margin-top: 0;">Sus notas:</h4>
            <p>{{ $solicitud->notas_cliente }}</p>
        </div>
        @endif

        <div class="alert">
            <strong>📋 ¿Qué sigue ahora?</strong><br>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Nuestro equipo revisará su solicitud</li>
                <li>Le contactaremos en un plazo máximo de 24-48 horas</li>
                <li>Recibirá una cotización formal por este medio</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>Información de Contacto:</strong><br>
            @if($solicitud->cliente->vendedor)
            Vendedor: {{ $solicitud->cliente->vendedor->name }}<br>
            Email: {{ $solicitud->cliente->vendedor->email }}<br>
            @endif
            </p>
            <p>Si tiene alguna pregunta sobre su solicitud, no dude en contactarnos.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
