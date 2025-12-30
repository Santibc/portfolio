<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #{{ $compra->numero_compra }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo-section h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }

        .logo-section p {
            color: #666;
            font-size: 11px;
        }

        .order-info {
            text-align: right;
        }

        .order-number {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .order-date {
            color: #666;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .status-pagada { background: #d1fae5; color: #065f46; }
        .status-enviada { background: #dbeafe; color: #1e40af; }
        .status-entregada { background: #d1fae5; color: #065f46; }
        .status-pendiente { background: #fef3c7; color: #92400e; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
        }

        .info-box h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .info-box p {
            margin-bottom: 4px;
        }

        .info-box strong {
            color: #333;
        }

        .destinatario-box {
            background: linear-gradient(135deg, #fdf2f8, #fce7f3);
            border-color: #f9a8d4;
        }

        .destinatario-box h3 {
            color: #be185d;
        }

        .sorpresa-badge {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            margin-left: 5px;
        }

        .mensaje-tarjeta {
            background: white;
            border: 1px dashed #f9a8d4;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-style: italic;
            text-align: center;
        }

        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .productos-table th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #ddd;
        }

        .productos-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .productos-table .text-right {
            text-align: right;
        }

        .productos-table .text-center {
            text-align: center;
        }

        .totales {
            width: 300px;
            margin-left: auto;
            margin-bottom: 20px;
        }

        .totales-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .totales-row.total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: none;
            padding-top: 10px;
        }

        .repartidor-section {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .repartidor-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
        }

        .repartidor-box h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #1e40af;
            margin-bottom: 8px;
        }

        .firma-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 40px;
            padding-top: 20px;
        }

        .firma-box {
            text-align: center;
        }

        .firma-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }

        .notas-section {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .notas-section h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 30px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        Imprimir
    </button>

    <div class="header">
        <div class="logo-section">
            <h1>{{ $compra->empresa->nombre }}</h1>
            <p>{{ $compra->empresa->direccion ?? '' }}</p>
            <p>Tel: {{ $compra->empresa->telefono ?? '' }}</p>
        </div>
        <div class="order-info">
            <div class="order-number">#{{ $compra->numero_compra }}</div>
            <div class="order-date">{{ $compra->created_at->format('d/m/Y H:i') }}</div>
            <span class="status-badge status-{{ $compra->estado }}">{{ ucfirst($compra->estado) }}</span>
        </div>
    </div>

    <div class="info-grid">
        {{-- Quien compra --}}
        <div class="info-box">
            <h3>Quien Compra</h3>
            <p><strong>{{ $compra->nombre_cliente }}</strong></p>
            <p>{{ $compra->email_cliente }}</p>
            <p>{{ $compra->telefono_cliente }}</p>
        </div>

        {{-- Destinatario --}}
        <div class="info-box destinatario-box">
            <h3>
                Destinatario
                @if($compra->es_sorpresa)
                    <span class="sorpresa-badge">SORPRESA</span>
                @endif
            </h3>
            <p><strong>{{ $compra->nombre_destinatario ?: $compra->nombre_cliente }}</strong></p>
            <p>{{ $compra->telefono_destinatario ?: $compra->telefono_cliente }}</p>

            @if($compra->mensaje_tarjeta)
                <div class="mensaje-tarjeta">
                    "{{ $compra->mensaje_tarjeta }}"
                </div>
            @endif
        </div>

        {{-- Entrega --}}
        <div class="info-box">
            <h3>Direccion de Entrega</h3>
            <p><strong>{{ $compra->direccion_envio }}</strong></p>
            <p>{{ $compra->ciudad->nombre }}, {{ $compra->ciudad->departamento->nombre }}</p>
            @if($compra->fecha_entrega_deseada)
                <p style="margin-top: 10px;">
                    <strong>Fecha:</strong> {{ $compra->fecha_entrega_deseada->format('d/m/Y') }}
                    @if($compra->horario_entrega)
                        <br>{{ $compra->horario_entrega }}
                    @endif
                </p>
            @endif
            @if($compra->instrucciones_entrega)
                <p style="margin-top: 5px; font-size: 10px; color: #666;">
                    {{ $compra->instrucciones_entrega }}
                </p>
            @endif
        </div>
    </div>

    {{-- Productos --}}
    <table class="productos-table">
        <thead>
            <tr>
                <th style="width: 50%;">Producto</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Precio</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compra->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->nombre_producto }}</strong>
                        @if($item->info_variante)
                            <br><small style="color: #666;">{{ $item->info_variante }}</small>
                        @endif
                        <br><small style="color: #999;">Ref: {{ $item->referencia_producto }}</small>
                    </td>
                    <td class="text-center">{{ $item->cantidad }}</td>
                    <td class="text-right">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>${{ number_format($item->precio_total, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div class="totales">
        <div class="totales-row">
            <span>Subtotal</span>
            <span>${{ number_format($compra->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($compra->descuento_total > 0)
            <div class="totales-row" style="color: #10b981;">
                <span>Descuento</span>
                <span>-${{ number_format($compra->descuento_total, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($compra->costo_envio > 0)
            <div class="totales-row">
                <span>Envio</span>
                <span>${{ number_format($compra->costo_envio, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($compra->impuestos > 0)
            <div class="totales-row">
                <span>Impuestos</span>
                <span>${{ number_format($compra->impuestos, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="totales-row total">
            <span>TOTAL</span>
            <span>${{ number_format($compra->total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Repartidor --}}
    @if($compra->repartidor)
        <div class="repartidor-section">
            <div class="repartidor-box">
                <h3>Repartidor Asignado</h3>
                <p><strong>{{ $compra->repartidor->nombre }}</strong></p>
                <p>Tel: {{ $compra->repartidor->telefono }}</p>
                @if($compra->repartidor->vehiculo)
                    <p>{{ ucfirst($compra->repartidor->vehiculo) }} {{ $compra->repartidor->placa ? '- '.$compra->repartidor->placa : '' }}</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Notas --}}
    @if($compra->notas)
        <div class="notas-section">
            <h3>Notas</h3>
            <p>{{ $compra->notas }}</p>
        </div>
    @endif

    {{-- Firmas --}}
    <div class="firma-section">
        <div class="firma-box">
            <div class="firma-line">Firma del Repartidor</div>
        </div>
        <div class="firma-box">
            <div class="firma-line">Firma de Recibido</div>
        </div>
    </div>

    <div class="footer">
        <p>Impreso el {{ now()->format('d/m/Y H:i') }} | {{ $compra->empresa->nombre }}</p>
    </div>
</body>
</html>
