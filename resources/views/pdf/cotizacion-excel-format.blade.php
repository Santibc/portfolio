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
            margin-bottom: 1px;
        }

        .dato-label {
            background-color: #BCA9F5;
            color: #FFFFFF;
            font-size: 8pt;
            font-weight: bold;
            padding: 2px 6px;
            display: inline-block;
            min-width: 80px;
            border-radius: 3px;
        }

        .dato-valor {
            font-size: 8pt;
            padding: 2px 6px;
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
            padding: 5px 4px;
            border: 1px solid #BCA9F5;
            text-align: center;
        }

        .productos-table td {
            border: 1px solid #BCA9F5;
            padding: 3px 4px;
            vertical-align: middle;
            font-size: 8pt;
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
            font-size: 9pt;
            font-weight: bold;
            padding: 3px 10px;
            display: inline-block;
        }

        .total-valor {
            font-size: 9pt;
            font-weight: bold;
            padding: 3px 10px;
            display: inline-block;
            min-width: 100px;
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
                    <span class="dato-label">Correo</span>
                    <span class="dato-valor">{{ $solicitud->cliente->email ?? '' }}</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="dato-row">
                    <span class="dato-label">Ciudad</span>
                    <span class="dato-valor">{{ $solicitud->cliente->ciudad->nombre ?? '' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Direccion</span>
                    <span class="dato-valor">{{ $solicitud->cliente->direccion ?? '' }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Asesor</span>
                    <span class="dato-valor">{{ $solicitud->createdBy->name ?? ($solicitud->cliente->vendedor->name ?? 'N/A') }}</span>
                </div>
                <div class="dato-row">
                    <span class="dato-label">Fecha</span>
                    <span class="dato-valor">{{ $solicitud->created_at->format('d/m/Y') }}</span>
                </div>
            </td>
        </tr>
        @if($solicitud->sucursal)
        <tr>
            <td colspan="2" style="vertical-align: top; padding-top: 6px;">
                <div class="dato-row">
                    <span class="dato-label">Sucursal Entrega</span>
                    <span class="dato-valor">
                        {{ $solicitud->sucursal->nombre }}
                        @php
                            $extrasSucursal = [];
                            if (!empty($solicitud->sucursal->direccion)) {
                                $extrasSucursal[] = $solicitud->sucursal->direccion;
                            }
                            if ($solicitud->sucursal->ciudad) {
                                $extrasSucursal[] = $solicitud->sucursal->ciudad->nombre;
                            }
                            if (!empty($solicitud->sucursal->telefono)) {
                                $extrasSucursal[] = 'Tel: ' . $solicitud->sucursal->telefono;
                            }
                            if (!empty($solicitud->sucursal->contacto)) {
                                $extrasSucursal[] = 'Contacto: ' . $solicitud->sucursal->contacto;
                            }
                        @endphp
                        @if(!empty($extrasSucursal))
                            — {{ implode(' / ', $extrasSucursal) }}
                        @endif
                    </span>
                </div>
            </td>
        </tr>
        @endif
    </table>

    {{-- TABLA DE PRODUCTOS --}}
    <table class="productos-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
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

            @foreach($solicitud->items as $index => $item)
                @php
                    $totalGeneral += $item->precio_total;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td class="col-referencia">{{ $item->referencia_producto }}</td>
                    <td class="col-descripcion">
                        {{ $item->nombre_producto }}
                        @if($item->info_variante || $item->varianteProducto)
                            <br><small>{{ $item->info_variante ?: $item->varianteProducto->nombre_variante }}</small>
                        @endif
                        @if($item->observacion)
                            <br><small style="color: #666; font-style: italic;">Obs: {{ $item->observacion }}</small>
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
        @elseif($solicitud->cliente && $solicitud->cliente->aplica_flete)
        <div class="total-row">
            <span class="total-label">Flete:</span>
            <span class="total-valor">Aplica flete</span>
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
            <span class="total-label" style="font-size: 10pt;">TOTAL:</span>
            @php
                $totalFinal = $totalGeneral + ($solicitud->valor_flete ?? 0) - ($solicitud->descuento_total ?? 0) + ($solicitud->valor_iva ?? 0);
            @endphp
            <span class="total-valor" style="font-size: 10pt;">$ {{ number_format($totalFinal, 0) }}</span>
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
            <strong style="color: #382E65; font-size: 9pt;">Observaciones:</strong>
            <span style="font-size: 9pt;">{{ $solicitud->observaciones_vendedor }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- OBSERVACIONES DE PAGO --}}
    @if($solicitud->pagos->whereNotNull('notas')->where('notas', '!=', '')->count() > 0)
    <div style="margin-top: 10px; margin-bottom: 10px;">
        @foreach($solicitud->pagos->filter(fn($p) => !empty($p->notas)) as $pago)
        <div style="background-color: #E8F5E9; padding: 8px 12px; border-radius: 4px; margin-bottom: 5px;">
            <strong style="color: #382E65; font-size: 9pt;">Obs. Pago ({{ $pago->registradoPor->name ?? 'N/A' }} - {{ $pago->created_at->format('d/m/Y') }}):</strong>
            <span style="font-size: 9pt;">{{ $pago->notas }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- GARANTÍAS VINCULADAS --}}
    @if(isset($garantiasVinculadas) && $garantiasVinculadas->isNotEmpty())
    <div style="margin-top: 14px; margin-bottom: 10px; page-break-inside: avoid;">
        <div style="background-color: #382E65; color: #FFFFFF; padding: 6px 12px; font-size: 10pt; font-weight: bold; border-radius: 4px 4px 0 0;">
            GARANTÍAS VINCULADAS A ESTA COTIZACIÓN
        </div>
        <div style="border: 1px solid #BCA9F5; border-top: 0; padding: 8px 12px; border-radius: 0 0 4px 4px;">
            @foreach($garantiasVinculadas as $g)
                @php
                    $esLiberada = $g->estado === \App\Models\Garantia::ESTADO_LIBERADO;
                    $colorBorde = $esLiberada ? '#28A745' : '#FFC107';
                    $estadoTexto = $esLiberada ? 'LIBERADA' : 'PENDIENTE';
                    $estadoBg = $esLiberada ? '#28A745' : '#FFC107';
                    $estadoColor = $esLiberada ? '#FFFFFF' : '#333333';
                @endphp
                <div style="border-left: 4px solid {{ $colorBorde }}; padding: 6px 10px; margin-bottom: 8px; background-color: #FAFAFA;">
                    <table style="width: 100%; font-size: 9pt; margin-bottom: 4px;">
                        <tr>
                            <td style="vertical-align: top;">
                                <strong style="color: #382E65;">Garantía #{{ $g->id }}</strong>
                                — {{ $g->producto?->nombre ?? '—' }}@if($g->variante && $g->variante->nombre_variante) — {{ $g->variante->nombre_variante }}@endif
                            </td>
                            <td style="width: 90px; text-align: right; vertical-align: top;">
                                <span style="background-color: {{ $estadoBg }}; color: {{ $estadoColor }}; padding: 2px 8px; border-radius: 3px; font-size: 8pt; font-weight: bold;">{{ $estadoTexto }}</span>
                            </td>
                        </tr>
                    </table>
                    <div style="font-size: 9pt; margin-bottom: 2px;"><strong>Tipo:</strong> {{ $g->tipoLegible() }}</div>
                    <div style="font-size: 8.5pt; color: #555; margin-bottom: 4px;">
                        Registrada el {{ $g->created_at?->format('d/m/Y H:i') }}
                        @if($g->usuarioCreador) por {{ $g->usuarioCreador->name }}@endif
                    </div>

                    @if($g->observacion_creacion)
                    <div style="background-color: #E7F1FF; padding: 5px 8px; margin-bottom: 4px; border-radius: 3px; font-size: 8.5pt;">
                        <strong>Observación de creación:</strong> {{ $g->observacion_creacion }}
                    </div>
                    @endif

                    @if($esLiberada)
                        @if($g->observacion_liberacion)
                        <div style="background-color: #E8F5E9; padding: 5px 8px; margin-bottom: 4px; border-radius: 3px; font-size: 8.5pt;">
                            <strong>Observación de liberación:</strong> {{ $g->observacion_liberacion }}
                        </div>
                        @endif
                        <div style="font-size: 8.5pt; color: #555; margin-bottom: 4px;">
                            Liberada el {{ $g->liberado_en?->format('d/m/Y H:i') }}
                            @if($g->usuarioLiberador) por {{ $g->usuarioLiberador->name }}@endif
                        </div>

                        @if($g->productosLiberacion && $g->productosLiberacion->isNotEmpty())
                        <div style="margin-top: 4px;">
                            <strong style="font-size: 9pt; color: #382E65;">Productos descontados de stock:</strong>
                            <table style="width: 100%; border-collapse: collapse; margin-top: 3px; font-size: 8.5pt;">
                                <thead>
                                    <tr style="background-color: #F0E7FF;">
                                        <th style="border: 1px solid #BCA9F5; padding: 3px 6px; text-align: left;">Producto</th>
                                        <th style="border: 1px solid #BCA9F5; padding: 3px 6px; text-align: left;">Variante</th>
                                        <th style="border: 1px solid #BCA9F5; padding: 3px 6px; text-align: left;">Ubicación</th>
                                        <th style="border: 1px solid #BCA9F5; padding: 3px 6px; text-align: center; width: 50px;">Cant.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($g->productosLiberacion as $p)
                                    <tr>
                                        <td style="border: 1px solid #BCA9F5; padding: 3px 6px;">{{ $p->producto?->nombre ?? '—' }}</td>
                                        <td style="border: 1px solid #BCA9F5; padding: 3px 6px;">{{ $p->variante?->nombre_variante ?? '—' }}</td>
                                        <td style="border: 1px solid #BCA9F5; padding: 3px 6px;">{{ $p->ubicacionRelacion?->nombre ?? '—' }}</td>
                                        <td style="border: 1px solid #BCA9F5; padding: 3px 6px; text-align: center;">{{ $p->cantidad }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- TEXTOS LEGALES --}}
    <div class="texto-legal" style="color: #382E65; border-left: 3px solid #BCA9F5; padding-left: 10px;">
        Autorizo el tratamiento de mis datos personales con fines exclusivamente comerciales, garantizándose que esta información no será revelada a terceros salvo orden de autoridad competente - Ley 1581 de 2012, Decreto 1377 de 2013.
    </div>

</body>
</html>
