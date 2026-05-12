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
            background-color: #000000;
            color: #FFFFFF;
            padding: 5px 15px;
            font-size: 12pt;
            font-weight: bold;
            display: inline-block;
        }

        .cotizacion-numero {
            font-size: 12pt;
            font-weight: bold;
            padding: 5px 15px;
            display: inline-block;
            border: 1px solid #000000;
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
            background-color: #000000;
            color: #FFFFFF;
            font-size: 11pt;
            font-weight: bold;
            padding: 4px 8px;
            display: inline-block;
            min-width: 120px;
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
            background-color: #ffc107;
            color: #000;
        }

        .estado-confirmada {
            background-color: #28a745;
            color: #fff;
        }

        .estado-rechazada {
            background-color: #dc3545;
            color: #fff;
        }

        .estado-aplicada {
            background-color: #007bff;
            color: #fff;
        }

        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .productos-table th {
            background-color: #000000;
            color: #FFFFFF;
            font-size: 11pt;
            font-weight: bold;
            padding: 8px 5px;
            border: 1px solid #000000;
            text-align: center;
        }

        .productos-table td {
            border: 1px solid #000000;
            padding: 6px 5px;
            vertical-align: middle;
            font-size: 10pt;
        }

        .productos-table .col-referencia {
            width: 15%;
            font-weight: bold;
        }

        .productos-table .col-descripcion {
            width: 40%;
        }

        .productos-table .col-foto {
            width: 10%;
            text-align: center;
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
    {{-- HEADER: Logo y Cotización --}}
    <table class="header-section">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo.png') }}" alt="INNOVATECH Global" class="logo-img">
            </td>
            <td class="cotizacion-header">
                <span class="cotizacion-label">COTIZACION</span>
                <span class="cotizacion-numero">{{ $solicitud->numero_solicitud }}</span>
            </td>
        </tr>
    </table>

    {{-- DATOS DE LA EMPRESA Y MARCAS --}}
    <table class="empresa-info">
        <tr>
            <td class="empresa-info-left">
                <div class="empresa-nombre">INNOVATECH GLOBAL SAS</div>
                <div class="empresa-detalle">NIT: 901543944-6</div>
                <div class="empresa-detalle"><strong>TELEFONOS CALI:</strong> 3174422343 - 3043097203 - 3145954725 - 3022040377 - 3022040377 - 3</div>
                <div class="empresa-detalle"><strong>TELEFONOS PEREIRA:</strong> 3002942179 - 3002942155 - 3004036493</div>
                <div class="empresa-detalle"><strong>CORREO:</strong> ventas@innovatechglobal.com.co</div>
                <div class="empresa-detalle"><strong>DIRECCION CALI NORTE:</strong> CALLE 24AN #5N 58 LOCAL 12 CC ASTROCENTRO</div>
                <div class="empresa-detalle"><strong>DIRECCION CALI SEDE CC CHINA:</strong> Carrera 6 # 16-17, Local 113</div>
                <div class="empresa-detalle"><strong>DIRECCION CALI SEDE SAN NICOLAS:</strong> Carrera 6 # 18-13</div>
                <div class="empresa-detalle"><strong>DIRECCION PEREIRA:</strong> Calle 16 # 4 - 44 centro</div>
                <div class="empresa-detalle">WEB: WWW.INNOVATECHGLOBAL.COM.CO</div>
            </td>
            <td class="empresa-info-right">
                <div class="marcas-titulo">DISTRIBUIMOS LAS MEJORES MARCAS</div>
                <img src="{{ public_path('images/marcas-distribuidas.png') }}" alt="Marcas" class="marcas-img">
            </td>
        </tr>
    </table>

    {{-- DATOS DEL CLIENTE Y ASESOR --}}
    <table class="cliente-asesor-section">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="dato-row">
                    <span class="dato-label">Cliente</span>
                    <span class="dato-valor">{{ $solicitud->cliente->nombre_contacto ?? 'N/A' }}</span>
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
                <th class="col-foto">FOTO</th>
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
                    <td class="col-referencia">{{ $item->nombre_producto }}</td>
                    <td class="col-descripcion">
                        {{ $item->producto?->descripcion ?: '-' }}
                        @if($item->info_variante)
                            <br><small>{{ $item->info_variante }}</small>
                        @endif
                    </td>
                    <td class="col-foto">
                        @if($item->producto && $item->producto->imagenPrincipal)
                            <img src="{{ public_path($item->producto->imagenPrincipal->ruta_imagen) }}"
                                 alt="{{ $item->nombre_producto }}"
                                 class="producto-img">
                        @endif
                    </td>
                    <td class="col-cantidad">{{ number_format($item->cantidad, 0) }}</td>
                    <td class="col-precio">$ {{ number_format($item->precio_unitario, 0) }}</td>
                    <td class="col-subtotal">$ {{ number_format($item->precio_total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTAL --}}
    <div class="totales-container">
        <div class="total-row">
            <span class="total-label">TOTAL:</span>
            <span class="total-valor">$ {{ number_format($totalGeneral, 0) }}</span>
        </div>
    </div>

    {{-- TEXTOS LEGALES --}}
    <div class="texto-legal">
        Autorizo a INOVATECH GLOBAL S.A.S. para recaudar, almacenar, utilizar y actualizar mis datos personales con fines exclusivamente comerciales garantizándome que esta información no será revelada a terceros salvo orden de autoridad competente - Ley 1581 de 2012, Decreto 1377 de 2013.
    </div>

    {{-- CONDICIONES FINALES --}}
    <div class="condiciones-finales">
        LA FECHA DE ENTREGA SE DETERMINARÁ EN EL MOMENTO DE EFECTUAR EL PEDIDO, LOS PRECIOS DE ESTA OFERTA SE PODRÁN MODIFICAR SIN PREVIO AVISO
    </div>
</body>
</html>
