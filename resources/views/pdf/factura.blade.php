<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $solicitud->numero_factura }}</title>
    <style>
        @page {
            margin: 15mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-section {
            margin-bottom: 10px;
        }

        .logo-cell {
            width: 50%;
            vertical-align: middle;
            padding: 5px;
        }

        .logo-img {
            height: 70px;
            width: auto;
        }

        .factura-header {
            width: 50%;
            text-align: right;
            vertical-align: middle;
            padding: 5px;
        }

        .factura-label {
            background-color: #BCA9F5;
            color: #FFFFFF;
            padding: 8px 20px;
            font-size: 14pt;
            font-weight: bold;
            display: inline-block;
            border-radius: 4px;
        }

        .factura-numero {
            font-size: 14pt;
            font-weight: bold;
            padding: 8px 20px;
            display: inline-block;
            border: 2px solid #FF84D5;
            border-radius: 4px;
            color: #382E65;
            margin-top: 5px;
        }

        .dato-row {
            margin-bottom: 4px;
        }

        .dato-label {
            background-color: #BCA9F5;
            color: #FFFFFF;
            font-size: 10pt;
            font-weight: bold;
            padding: 4px 8px;
            display: inline-block;
            min-width: 100px;
            border-radius: 3px;
        }

        .dato-valor {
            font-size: 10pt;
            padding: 4px 8px;
            display: inline-block;
            color: #382E65;
        }

        .estado-pagada {
            background-color: #28a745;
            color: #fff;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10pt;
        }

        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .productos-table th {
            background-color: #FF84D5;
            color: #FFFFFF;
            font-size: 9pt;
            font-weight: bold;
            padding: 8px 4px;
            border: 1px solid #BCA9F5;
            text-align: center;
        }

        .productos-table td {
            border: 1px solid #BCA9F5;
            padding: 5px 4px;
            vertical-align: middle;
            font-size: 9pt;
            color: #382E65;
        }

        .productos-table .col-item { width: 5%; text-align: center; }
        .productos-table .col-codigo { width: 12%; font-weight: bold; }
        .productos-table .col-cantidad { width: 8%; text-align: center; }
        .productos-table .col-descripcion { width: 35%; }
        .productos-table .col-unitario { width: 12%; text-align: right; }
        .productos-table .col-iva { width: 8%; text-align: center; }
        .productos-table .col-bruto { width: 10%; text-align: right; }
        .productos-table .col-total { width: 10%; text-align: right; }

        .producto-img {
            max-width: 40px;
            max-height: 40px;
            height: auto;
        }

        .totales-section {
            margin-top: 15px;
        }

        .totales-table {
            width: 100%;
        }

        .info-pago {
            width: 55%;
            vertical-align: top;
            padding-right: 20px;
        }

        .totales-box {
            width: 45%;
            vertical-align: top;
        }

        .totales-inner {
            border: 1px solid #BCA9F5;
            border-radius: 5px;
            overflow: hidden;
        }

        .total-row-item {
            padding: 8px 15px;
            border-bottom: 1px solid #BCA9F5;
        }

        .total-row-item:last-child {
            border-bottom: none;
            background-color: #FF84D5;
            color: #fff;
        }

        .total-row-item .label {
            font-weight: bold;
        }

        .total-row-item .valor {
            float: right;
            font-weight: bold;
        }

        .valor-letras {
            background-color: #FFF1DD;
            padding: 10px;
            border-radius: 5px;
            font-size: 9pt;
            color: #382E65;
            margin-top: 10px;
            border-left: 3px solid #BCA9F5;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 8px;
            border-left: 3px solid #BCA9F5;
        }

        .info-box-label {
            font-weight: bold;
            font-size: 9pt;
            color: #382E65;
        }

        .info-box-valor {
            font-size: 9pt;
            color: #666;
        }

        .observaciones {
            background-color: #fff;
            border: 1px solid #BCA9F5;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            font-size: 9pt;
        }

        .firmas-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .firma-box {
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .firma-linea {
            border-top: 1px solid #382E65;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 9pt;
            color: #382E65;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #BCA9F5;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    {{-- HEADER: Logo y Factura --}}
    <table class="header-section">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo.png') }}" alt="Miracle Beauty Experts" class="logo-img">
                <div style="font-size: 9pt; color: #382E65; margin-top: 5px;">
                    <strong>MIRACLE BEAUTY EXPERTS S.A.S</strong><br>
                    NIT: 901.471.543-6<br>
                    Carrera 21 No. 9-10 Lc 202<br>
                    Tel: (601) 2373870<br>
                    Bogotá - Colombia
                </div>
            </td>
            <td class="factura-header">
                <div class="factura-label">FACTURA</div>
                <div class="factura-numero">{{ $solicitud->numero_factura }}</div>
                <div style="margin-top: 10px; font-size: 9pt; color: #382E65;">
                    <strong>Fecha:</strong> {{ $solicitud->facturada_en->format('d/m/Y') }}<br>
                    @if($solicitud->fecha_vencimiento)
                    <strong>Vence:</strong> {{ $solicitud->fecha_vencimiento->format('d/m/Y') }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Línea decorativa --}}
    <div style="border-bottom: 2px solid #FF84D5; margin: 10px 0 15px 0;"></div>

    {{-- DATOS DEL CLIENTE --}}
    <table style="margin-bottom: 15px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="dato-row">
                    <span class="dato-label">Señores</span>
                    <span class="dato-valor">{{ $solicitud->cliente->nombre_contacto ?? 'N/A' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">NIT / C.C.</span>
                    <span class="dato-valor">{{ $solicitud->cliente->numero_identificacion ?? '' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Dirección</span>
                    <span class="dato-valor">{{ $solicitud->cliente->direccion ?? '' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Ciudad</span>
                    <span class="dato-valor">{{ $solicitud->cliente->ciudad->nombre ?? '' }} - {{ $solicitud->cliente->ciudad->departamento->nombre ?? '' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Teléfono</span>
                    <span class="dato-valor">{{ $solicitud->cliente->telefono ?? '' }}</span>
                </div>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <span class="estado-pagada">PAGADA</span>
                <div style="margin-top: 10px; font-size: 9pt; color: #382E65;">
                    <strong>Cotización:</strong> {{ $solicitud->numero_solicitud }}<br>
                    <strong>Asesor:</strong> {{ $solicitud->cliente->vendedor->name ?? 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- TABLA DE PRODUCTOS --}}
    <table class="productos-table">
        <thead>
            <tr>
                <th class="col-item">Item</th>
                <th class="col-codigo">Código</th>
                <th class="col-cantidad">Cant.</th>
                <th class="col-descripcion">Descripción</th>
                <th class="col-unitario">Vr. Unitario</th>
                @if($solicitud->porcentaje_iva)
                <th class="col-iva">IVA</th>
                <th class="col-bruto">Vr. Bruto</th>
                @endif
                <th class="col-total">Vr. Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotal = 0;
                $itemNum = 1;
            @endphp

            @foreach($solicitud->items as $item)
                @php
                    $subtotal += $item->precio_total;
                    $ivaItem = $solicitud->porcentaje_iva ? ($item->precio_total * $solicitud->porcentaje_iva / 100) : 0;
                @endphp
                <tr>
                    <td class="col-item">{{ $itemNum++ }}</td>
                    <td class="col-codigo">{{ $item->referencia_producto }}</td>
                    <td class="col-cantidad">{{ number_format($item->cantidad, 0) }}</td>
                    <td class="col-descripcion">
                        {{ $item->nombre_producto }}
                        @if($item->info_variante)
                            <br><small style="color: #666;">{{ $item->info_variante }}</small>
                        @endif
                    </td>
                    <td class="col-unitario">$ {{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                    @if($solicitud->porcentaje_iva)
                    <td class="col-iva">{{ number_format($solicitud->porcentaje_iva, 0) }}%</td>
                    <td class="col-bruto">$ {{ number_format($item->precio_total + $ivaItem, 0, ',', '.') }}</td>
                    @endif
                    <td class="col-total">$ {{ number_format($item->precio_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALES Y PAGO --}}
    <table class="totales-table">
        <tr>
            <td class="info-pago">
                <div class="info-box">
                    <span class="info-box-label">Total Items:</span>
                    <span class="info-box-valor">{{ $solicitud->items->count() }}</span>
                </div>

                <div class="valor-letras">
                    <strong>Valor en Letras:</strong><br>
                    {{ $valorEnLetras }}
                </div>

                <div class="info-box" style="margin-top: 10px;">
                    <span class="info-box-label">Forma de pago:</span>
                    <span class="info-box-valor">{{ $solicitud->forma_pago_factura ?? 'Contado' }}</span>
                </div>

                <div class="info-box">
                    <span class="info-box-label">Medio de pago:</span>
                    <span class="info-box-valor">{{ \App\Models\SolicitudCotizacion::METODOS_PAGO[$solicitud->metodo_pago] ?? 'Transferencia' }}</span>
                </div>

                @if($solicitud->observaciones_vendedor)
                <div class="observaciones">
                    <strong>Observaciones:</strong><br>
                    {{ $solicitud->observaciones_vendedor }}
                </div>
                @endif
            </td>
            <td class="totales-box">
                <div class="totales-inner">
                    <div class="total-row-item">
                        <span class="label">Subtotal</span>
                        <span class="valor">$ {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($solicitud->valor_flete && $solicitud->valor_flete > 0)
                    <div class="total-row-item">
                        <span class="label">Flete</span>
                        <span class="valor">$ {{ number_format($solicitud->valor_flete, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($solicitud->descuento_total && $solicitud->descuento_total > 0)
                    <div class="total-row-item">
                        <span class="label">Descuento</span>
                        <span class="valor">-$ {{ number_format($solicitud->descuento_total, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($solicitud->porcentaje_iva && $solicitud->valor_iva)
                    <div class="total-row-item">
                        <span class="label">IVA {{ number_format($solicitud->porcentaje_iva, 0) }}%</span>
                        <span class="valor">$ {{ number_format($solicitud->valor_iva, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="total-row-item">
                        <span class="label">TOTAL A PAGAR</span>
                        @php
                            $totalFinal = $solicitud->monto_total + ($solicitud->valor_iva ?? 0);
                        @endphp
                        <span class="valor">$ {{ number_format($totalFinal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- FIRMAS --}}
    <div class="firmas-section">
        <table style="width: 100%;">
            <tr>
                <td style="width: 45%; text-align: center;">
                    <div class="firma-linea">ALISTADO POR</div>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 45%; text-align: center;">
                    <div class="firma-linea">REVISADO POR</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <strong>Miracle Beauty Experts S.A.S</strong> - miracle.beautyexperts2020@gmail.com - (310) 2390526
    </div>
</body>
</html>
