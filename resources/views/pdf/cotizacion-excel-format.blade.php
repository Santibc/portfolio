<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $solicitud->numero_solicitud }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
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
            padding: 10mm 15mm;
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

        .cotizacion-header {
            width: 50%;
            text-align: right;
            vertical-align: middle;
            padding: 5px;
        }

        .cotizacion-label {
            background-color: #FF84D5;
            color: #FFFFFF;
            padding: 5px 15px;
            font-size: 12pt;
            font-weight: bold;
            display: inline-block;
            border-radius: 4px;
        }

        .cotizacion-numero {
            font-size: 12pt;
            font-weight: bold;
            padding: 5px 15px;
            display: inline-block;
            border: 2px solid #BCA9F5;
            border-radius: 4px;
            color: #382E65;
        }

        .empresa-info {
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .empresa-info-left {
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .empresa-info-right {
            width: 50%;
            vertical-align: top;
            padding-left: 10px;
            text-align: center;
        }

        .empresa-nombre {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .empresa-detalle {
            font-size: 9pt;
            margin-bottom: 3px;
        }

        .marcas-titulo {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .marcas-img {
            width: 100%;
            max-width: 400px;
            height: auto;
        }

        .cliente-asesor-section {
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .dato-row {
            margin-bottom: 3px;
        }

        .dato-label {
            background-color: #BCA9F5;
            color: #FFFFFF;
            font-size: 11pt;
            font-weight: bold;
            padding: 4px 8px;
            display: inline-block;
            min-width: 120px;
            border-radius: 3px;
        }

        .dato-valor {
            font-size: 11pt;
            padding: 4px 8px;
            display: inline-block;
        }

        .estado-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10pt;
            margin-left: 10px;
        }

        .estado-pendiente {
            background-color: #FFF1DD;
            color: #382E65;
            border: 1px solid #D4AF37;
        }

        .estado-confirmada {
            background-color: #B9DFDE;
            color: #382E65;
        }

        .estado-rechazada {
            background-color: #FFE4F3;
            color: #382E65;
            border: 1px solid #FF84D5;
        }

        .estado-aplicada {
            background-color: #BCA9F5;
            color: #fff;
        }

        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .productos-table th {
            background-color: #FF84D5;
            color: #FFFFFF;
            font-size: 11pt;
            font-weight: bold;
            padding: 8px 5px;
            border: 1px solid #BCA9F5;
            text-align: center;
        }

        .productos-table td {
            border: 1px solid #BCA9F5;
            padding: 6px 5px;
            vertical-align: middle;
            font-size: 10pt;
            color: #382E65;
        }

        .productos-table .col-referencia {
            width: 15%;
            font-weight: bold;
        }

        .productos-table .col-descripcion {
            width: 40%;
        }

        .productos-table .col-cantidad {
            width: 10%;
            text-align: center;
        }

        .productos-table .col-precio {
            width: 12.5%;
            text-align: right;
        }

        .productos-table .col-subtotal {
            width: 12.5%;
            text-align: right;
        }

        .producto-img {
            max-width: 60px;
            max-height: 60px;
            height: auto;
        }

        .totales-section {
            margin-top: 10px;
        }

        .texto-legal {
            width: 100%;
            vertical-align: top;
            padding: 10px 0;
            font-size: 9pt;
            text-align: justify;
            line-height: 1.3;
        }

        .totales-container {
            width: 100%;
            margin-top: 15px;
            text-align: right;
        }

        .total-row {
            text-align: right;
            margin-bottom: 5px;
        }

        .total-label {
            font-size: 12pt;
            font-weight: bold;
            padding: 5px 15px;
            display: inline-block;
        }

        .total-valor {
            font-size: 16pt;
            font-weight: bold;
            padding: 5px 15px;
            display: inline-block;
            min-width: 150px;
        }

        .condiciones-finales {
            margin-top: 15px;
            padding: 10px;
            font-size: 9pt;
            text-align: center;
            line-height: 1.3;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- HEADER: Logo centrado y Cotización --}}
    <table class="header-section">
        <tr>
            <td class="logo-cell" style="text-align: center;">
                <img src="{{ public_path('images/logo.png') }}" alt="Miracle Beauty Experts" class="logo-img" style="height: 80px;">
            </td>
            <td class="cotizacion-header">
                <span class="cotizacion-label">COTIZACION</span>
                <span class="cotizacion-numero">{{ $solicitud->numero_solicitud }}</span>
            </td>
        </tr>
    </table>

    {{-- Línea decorativa --}}
    <div style="border-bottom: 2px solid #FF84D5; margin: 10px 0 15px 0;"></div>

    {{-- DATOS DEL CLIENTE Y ASESOR --}}
    <table class="cliente-asesor-section">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="dato-row">
                    <span class="dato-label">Cliente</span>
                    <span class="dato-valor">{{ $solicitud->cliente->razon_social ?: ($solicitud->cliente->nombre_contacto ?? 'N/A') }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">NIT</span>
                    <span class="dato-valor">{{ $solicitud->cliente->numero_identificacion ?? '' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Telefono</span>
                    <span class="dato-valor">{{ $solicitud->cliente->telefono ?? '' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Correo electronico</span>
                    <span class="dato-valor">{{ $solicitud->cliente->email ?? '' }}</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="dato-row">
                    <span class="dato-label">Asesor</span>
                    <span class="dato-valor">{{ $solicitud->cliente->vendedor->name ?? 'N/A' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Fecha</span>
                    <span class="dato-valor">{{ $solicitud->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="dato-row">
                    @php
                        $estadoClasses = [
                            'Pendiente' => 'estado-pendiente',
                            'Confirmada' => 'estado-confirmada',
                            'Rechazada' => 'estado-rechazada',
                            'Aplicada' => 'estado-aplicada',
                        ];
                        $estadoClass = $estadoClasses[$solicitud->estado] ?? 'estado-pendiente';
                    @endphp
                    <span class="dato-label">Estado</span>
                    <span class="estado-badge {{ $estadoClass }}">{{ strtoupper($solicitud->estado) }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- TABLA DE PRODUCTOS --}}
    <table class="productos-table">
        <thead>
            <tr>
                <th class="col-referencia">REFERENCIA CODIGO</th>
                <th class="col-descripcion">DESCRIPCION</th>

                <th class="col-cantidad">CANTIDAD</th>
                <th class="col-precio">VALOR UNITARIO</th>
                <th class="col-subtotal">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGeneral = 0;
            @endphp

            @foreach($solicitud->items as $item)
                @php
                    $totalGeneral += $item->precio_total;
                @endphp
                <tr>
                    <td class="col-referencia">{{ $item->referencia_producto }}</td>
                    <td class="col-descripcion">
                        {{ $item->nombre_producto }}
                        @if($item->variante)
                            <br><small>{{ $item->variante }}</small>
                        @endif
                    </td>

                    <td class="col-cantidad">{{ number_format($item->cantidad, 0) }}</td>
                    <td class="col-precio">$ {{ number_format($item->precio_unitario, 0) }}</td>
                    <td class="col-subtotal">$ {{ number_format($item->precio_total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALES --}}
    <div class="totales-container">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-valor">$ {{ number_format($totalGeneral, 0) }}</span>
        </div>
        @if($solicitud->valor_flete && $solicitud->valor_flete > 0)
        <div class="total-row">
            <span class="total-label">Flete:</span>
            <span class="total-valor">$ {{ number_format($solicitud->valor_flete, 0) }}</span>
        </div>
        @endif
        @if($solicitud->descuento_total && $solicitud->descuento_total > 0)
        <div class="total-row">
            <span class="total-label">Descuento:</span>
            <span class="total-valor">-$ {{ number_format($solicitud->descuento_total, 0) }}</span>
        </div>
        @endif
        @if($solicitud->porcentaje_iva && $solicitud->valor_iva)
        <div class="total-row">
            <span class="total-label">IVA {{ number_format($solicitud->porcentaje_iva, 0) }}%:</span>
            <span class="total-valor">$ {{ number_format($solicitud->valor_iva, 0) }}</span>
        </div>
        @endif
        <div class="total-row" style="margin-top: 5px; padding-top: 5px; border-top: 2px solid #FF84D5;">
            <span class="total-label" style="font-size: 14pt;">TOTAL:</span>
            @php
                $totalFinal = $totalGeneral + ($solicitud->valor_flete ?? 0) - ($solicitud->descuento_total ?? 0) + ($solicitud->valor_iva ?? 0);
            @endphp
            <span class="total-valor" style="font-size: 16pt;">$ {{ number_format($totalFinal, 0) }}</span>
        </div>
    </div>

    {{-- OBSERVACIONES --}}
    @if($solicitud->notas_cliente || $solicitud->observaciones_vendedor)
    <div style="margin-top: 10px; margin-bottom: 10px;">
        @if($solicitud->notas_cliente)
        <div style="background-color: #f0f0f0; padding: 8px 12px; border-radius: 4px; margin-bottom: 5px;">
            <strong style="color: #382E65; font-size: 9pt;">Notas del Cliente:</strong>
            <span style="font-size: 9pt;">{{ $solicitud->notas_cliente }}</span>
        </div>
        @endif
        @if($solicitud->observaciones_vendedor)
        <div style="background-color: #FFF9E6; padding: 8px 12px; border-radius: 4px;">
            <strong style="color: #382E65; font-size: 9pt;">Observaciones del Vendedor:</strong>
            <span style="font-size: 9pt;">{{ $solicitud->observaciones_vendedor }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- TEXTOS LEGALES --}}
    <div class="texto-legal" style="color: #382E65; border-left: 3px solid #BCA9F5; padding-left: 10px;">
        Autorizo el tratamiento de mis datos personales con fines exclusivamente comerciales, garantizándose que esta información no será revelada a terceros salvo orden de autoridad competente - Ley 1581 de 2012, Decreto 1377 de 2013.
    </div>

    {{-- CONDICIONES FINALES --}}
    <div class="condiciones-finales" style="background-color: #FFF1DD; padding: 15px; border-radius: 5px; color: #382E65;">
        LA FECHA DE ENTREGA SE DETERMINARÁ EN EL MOMENTO DE EFECTUAR EL PEDIDO. LOS PRECIOS DE ESTA OFERTA SE PODRÁN MODIFICAR SIN PREVIO AVISO.
    </div>
</body>
</html>
