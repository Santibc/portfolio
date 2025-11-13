<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Cotización Rechazada</title>
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
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .warning-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .motivo-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th {
            background-color: #6c757d;
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
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="warning-icon">✕</div>
            <h1>Solicitud de Cotización Rechazada</h1>
        </div>

        <p>Estimado/a <strong>{{ $solicitud->cliente->nombre_contacto }}</strong>,</p>

        <p>Lamentamos informarle que su solicitud de cotización <strong>no ha sido aprobada</strong> en esta ocasión.</p>

        <div class="info-box">
            <h3 style="margin-top: 0;">Detalles de la Solicitud:</h3>
            <p><strong>Número de Solicitud:</strong> {{ $solicitud->numero_solicitud }}</p>
            <p><strong>Fecha de Solicitud:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Fecha de Rechazo:</strong> {{ $solicitud->rechazada_en->format('d/m/Y H:i') }}</p>
            <p><strong>Procesada por:</strong> {{ $solicitud->rechazadaPor->name }}</p>
        </div>

        <div class="motivo-box">
            <h4 style="margin-top: 0;">📋 Motivo del Rechazo:</h4>
            <p>{{ $solicitud->motivo_rechazo }}</p>
        </div>

        <h3>Resumen de la Solicitud Rechazada:</h3>
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
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td style="text-align: right;">${{ number_format($solicitud->monto_total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($solicitud->notas_cliente)
        <div class="info-box">
            <h4 style="margin-top: 0;">Sus notas originales:</h4>
            <p>{{ $solicitud->notas_cliente }}</p>
        </div>
        @endif

        <div class="alert">
            <strong>💬 ¿Tiene preguntas?</strong><br>
            No dude en contactar a su vendedor para más información o para discutir alternativas.
        </div>

        <h3>Próximos Pasos:</h3>
        <ol>
            <li>Puede contactar a su vendedor para más detalles sobre el rechazo</li>
            <li>Si desea, puede realizar una nueva solicitud con los ajustes necesarios</li>
            <li>Su vendedor estará disponible para ayudarle con cualquier duda</li>
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
