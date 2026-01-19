<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Entrada {{ $numero }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        .info-value {
            flex: 1;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th {
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .detail-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin-top: 30px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        .badge-entrada {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>NOTA DE ENTRADA</h1>
        <p>No. {{ $numero }}</p>
        <p>{{ $fecha }}</p>
    </div>

    <div class="info-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div class="info-row">
                        <span class="info-label">Ubicación:</span>
                        <span class="info-value">{{ $movimiento->ubicacion->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo:</span>
                        <span class="info-value"><span class="badge-entrada">ENTRADA</span></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Origen:</span>
                        <span class="info-value">{{ $movimiento->descripcion_origen }}</span>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="info-row">
                        <span class="info-label">Registrado por:</span>
                        <span class="info-value">{{ $movimiento->usuario->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo Operación:</span>
                        <span class="info-value">{{ $movimiento->descripcion_tipo_operacion }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Referencia:</span>
                        <span class="info-value">{{ $movimiento->referencia_documento ?? 'N/A' }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="detail-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Variante</th>
                <th style="text-align: center;">Cantidad</th>
                <th style="text-align: center;">Stock Anterior</th>
                <th style="text-align: center;">Stock Nuevo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $movimiento->producto->nombre ?? 'N/A' }}</strong><br>
                    <small>Ref: {{ $movimiento->producto->referencia ?? 'N/A' }}</small>
                </td>
                <td>{{ $movimiento->variante->nombre_variante ?? 'Sin variante' }}</td>
                <td style="text-align: center; font-weight: bold; color: #28a745; font-size: 16px;">
                    +{{ $movimiento->cantidad }}
                </td>
                <td style="text-align: center;">{{ $movimiento->stock_anterior }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $movimiento->stock_nuevo }}</td>
            </tr>
        </tbody>
    </table>

    @if($movimiento->motivo)
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Motivo:</span>
            <span class="info-value">{{ $movimiento->motivo }}</span>
        </div>
    </div>
    @endif

    <div class="footer">
        <div class="signature-box">
            <div class="signature-line">
                Entrega
            </div>
        </div>
        <div class="signature-box" style="float: right;">
            <div class="signature-line">
                Recibe
            </div>
        </div>
    </div>
</body>
</html>
