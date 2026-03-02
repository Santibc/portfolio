<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Traslado {{ $traslado->numero_traslado }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 8px;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 2px 0 0;
            color: #666;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px 15px;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 140px;
        }
        .info-value {
            display: inline;
        }
        .info-row {
            margin-bottom: 2px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 11px;
            color: white;
        }
        .badge-transito { background-color: #0dcaf0; }
        .badge-completado { background-color: #198754; }
        .badge-pendiente { background-color: #ffc107; color: #333; }
        .badge-cancelado { background-color: #6c757d; }
        .badge-general { background-color: #6c757d; }
        .badge-credito { background-color: #0dcaf0; }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th {
            background-color: #0d6efd;
            color: white;
            padding: 5px 10px;
            text-align: left;
            font-weight: bold;
        }
        .detail-table td {
            padding: 4px 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef !important;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin-top: 20px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TRASLADO DE STOCK</h1>
        <p>No. {{ $traslado->numero_traslado }}</p>
        <p>{{ $traslado->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="info-row">
                        <span class="info-label">Origen:</span>
                        <span class="info-value">{{ $traslado->ubicacionOrigen->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Destino:</span>
                        <span class="info-value">{{ $traslado->ubicacionDestino->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo Operaci&oacute;n:</span>
                        <span class="info-value">
                            @if($traslado->tipo_operacion === 'credito')
                                <span class="badge badge-credito">Cr&eacute;dito</span>
                            @else
                                <span class="badge badge-general">General</span>
                            @endif
                        </span>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <span class="info-value">
                            @php
                                $badgeClass = match($traslado->estado) {
                                    'en_transito' => 'badge-transito',
                                    'completado' => 'badge-completado',
                                    'pendiente' => 'badge-pendiente',
                                    'cancelado' => 'badge-cancelado',
                                    default => 'badge-general',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $traslado->estado_nombre }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Creado por:</span>
                        <span class="info-value">{{ $traslado->usuarioCreador->name ?? 'N/A' }}</span>
                    </div>
                    @if($traslado->usuarioReceptor)
                    <div class="info-row">
                        <span class="info-label">Recibido por:</span>
                        <span class="info-value">{{ $traslado->usuarioReceptor->name }}</span>
                    </div>
                    @endif
                    @if($traslado->recibido_en)
                    <div class="info-row">
                        <span class="info-label">Fecha recepci&oacute;n:</span>
                        <span class="info-value">{{ $traslado->recibido_en->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($traslado->notas)
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Notas:</span>
            <span class="info-value">{{ $traslado->notas }}</span>
        </div>
    </div>
    @endif

    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th>Referencia</th>
                <th>Producto</th>
                <th>Variante</th>
                <th style="text-align: center;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($traslado->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->producto->referencia ?? 'N/A' }}</td>
                <td>{{ $item->producto->nombre ?? 'N/A' }}</td>
                <td>{{ $item->varianteProducto->nombre_variante ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $item->cantidad }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total:</td>
                <td style="text-align: center;">{{ $traslado->items->sum('cantidad') }}</td>
            </tr>
        </tbody>
    </table>

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
