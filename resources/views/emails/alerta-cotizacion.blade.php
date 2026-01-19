<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Cotización</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .alert-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .info-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #BCA9F5;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .monto-destacado {
            background: linear-gradient(135deg, #FF84D5 0%, #BCA9F5 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .monto-destacado .label {
            font-size: 14px;
            opacity: 0.9;
        }
        .monto-destacado .valor {
            font-size: 28px;
            font-weight: bold;
            margin-top: 5px;
        }
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .productos-table th {
            background-color: #BCA9F5;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .productos-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .action-btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #FF84D5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
        }
        .action-btn.secondary {
            background-color: #BCA9F5;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="alert-icon">&#9989;</div>
            <h1>Cotización Aprobada</h1>
        </div>

        @if($tipoDestino === 'vendedor')
        <p>Hola <strong>{{ $solicitud->cliente->vendedor->name ?? 'Vendedor' }}</strong>,</p>
        <p>Te informamos que la cotización que gestionaste ha sido <strong>aprobada exitosamente</strong>.</p>
        @else
        <p>Se ha aprobado una nueva cotización que requiere seguimiento:</p>
        @endif

        <div class="info-card">
            <h4 style="margin-top: 0; color: #382E65;">Detalles de la Cotización</h4>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 5px 0;"><strong>Número:</strong></td>
                    <td>{{ $solicitud->numero_solicitud }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Cliente:</strong></td>
                    <td>{{ $solicitud->cliente->nombre_contacto }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Email Cliente:</strong></td>
                    <td>{{ $solicitud->cliente->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Teléfono:</strong></td>
                    <td>{{ $solicitud->cliente->telefono ?? 'No registrado' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Vendedor:</strong></td>
                    <td>{{ $solicitud->cliente->vendedor->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Fecha Aprobación:</strong></td>
                    <td>{{ $solicitud->aplicada_en ? $solicitud->aplicada_en->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Estado Pago:</strong></td>
                    <td>
                        <span class="badge badge-{{ $solicitud->estado_pago === 'pagado' ? 'success' : 'warning' }}">
                            {{ ucfirst($solicitud->estado_pago ?? 'pendiente') }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="monto-destacado">
            <div class="label">Monto Total</div>
            <div class="valor">$ {{ number_format($solicitud->monto_total, 0, ',', '.') }}</div>
        </div>

        <h4 style="color: #382E65;">Productos Incluidos ({{ $solicitud->items->count() }})</h4>
        <table class="productos-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="text-align: center;">Cant.</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitud->items->take(5) as $item)
                <tr>
                    <td>{{ $item->nombre_producto }}</td>
                    <td style="text-align: center;">{{ $item->cantidad }}</td>
                    <td style="text-align: right;">$ {{ number_format($item->precio_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @if($solicitud->items->count() > 5)
                <tr>
                    <td colspan="3" style="text-align: center; color: #666; font-style: italic;">
                        ... y {{ $solicitud->items->count() - 5 }} producto(s) más
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($solicitud->observaciones_vendedor)
        <div class="info-card" style="border-left-color: #28a745;">
            <h4 style="margin-top: 0; color: #155724;">Observaciones</h4>
            <p style="margin-bottom: 0;">{{ $solicitud->observaciones_vendedor }}</p>
        </div>
        @endif

        <div style="text-align: center; margin-top: 25px;">
            <a href="{{ route('solicitudes') }}" class="action-btn">Ver Cotización</a>
            @if($solicitud->estado_pago !== 'pagado')
            <a href="{{ route('pagos.create', $solicitud) }}" class="action-btn secondary">Registrar Pago</a>
            @endif
        </div>

        <div class="footer">
            <p><strong>Miracle Beauty Experts</strong></p>
            <p>Este es un correo automático de notificación interna.</p>
            <p style="color: #999; font-size: 11px;">
                Generado el {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</body>
</html>
