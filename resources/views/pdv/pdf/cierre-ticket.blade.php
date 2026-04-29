<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cierre de Caja {{ $sesion->id }}</title>
    <style>
        @page { size: 72mm auto; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 3mm 4mm 3mm 2mm;
            box-sizing: border-box;
            color: #000;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        .bold { font-weight: bold; }
        .small { font-size: 8.5px; }
        .xsmall { font-size: 7.5px; }
        .line-solid { border-top: 1px solid #000; margin: 4px 0; }
        .line-dashed { border-top: 1px dashed #000; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; }

        .logo { width: 42mm; height: auto; margin: 0 auto 2px; display: block; }

        .empresa { font-size: 11px; font-weight: bold; letter-spacing: 0.3px; }
        .info-empresa { font-size: 8px; line-height: 1.3; }

        .redes-titulo { font-size: 8px; margin-top: 2px; }
        .redes-tabla { width: auto; margin: 2px auto 0; }
        .redes-tabla td { font-size: 8px; padding: 1px 3px; }
        .ico-red { width: 10px; height: 10px; vertical-align: middle; }
        .red-icono { width: 14px; }
        .red-texto { vertical-align: middle; }

        .titulo-doc {
            text-align: center;
            font-size: 10.5px;
            font-weight: bold;
            margin: 4px 0 2px;
            letter-spacing: 0.4px;
        }

        .meta td { font-size: 8px; padding: 1px 0; }
        .meta .label { font-weight: bold; }

        .seccion-titulo {
            text-align: center;
            font-size: 8.5px;
            font-weight: bold;
            margin: 5px 0 2px;
            letter-spacing: 0.3px;
        }

        .totales td { font-size: 8.5px; padding: 1px 2px; }
        .total-final td {
            font-size: 10.5px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .observaciones {
            font-size: 8px;
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px dashed #000;
        }

        .gracias {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>

    {{-- Encabezado: logo + datos empresa --}}
    <div class="center">
        <img src="{{ asset('images/logo-black.png') }}" alt="Miracle Beauty Experts" class="logo">
    </div>
    <div class="center empresa">MIRACLE BEAUTY EXPERTS</div>
    <div class="center info-empresa">Direccion: Cr 21 # 9 - 10 Local 202</div>
    <div class="center info-empresa">Telefono: 321 964 8580 - 311 232 0134</div>

    {{-- Redes sociales --}}
    <div class="center redes-titulo bold">Visita Nuestras Redes</div>
    <table class="redes-tabla">
        <tr>
            <td class="right red-icono">
                <img src="{{ asset('images/icons/instagram.png') }}" class="ico-red" alt="IG">
            </td>
            <td class="left red-texto">@miracleinternacional</td>
        </tr>
        <tr>
            <td class="right red-icono">
                <img src="{{ asset('images/icons/facebook.png') }}" class="ico-red" alt="FB">
            </td>
            <td class="left red-texto">Miracle Beauty Experts</td>
        </tr>
    </table>

    <div class="line-solid"></div>

    <div class="titulo-doc">RESUMEN DE CIERRE DE CAJA</div>

    {{-- Meta --}}
    @php
        $cajeroNombre = strtoupper($sesion->usuario->name ?? '-');
    @endphp
    <table class="meta">
        <tr>
            <td><span class="label">Caja:</span> {{ $sesion->caja->nombre ?? '-' }}</td>
            <td class="right"><span class="label">Sesion N°</span></td>
        </tr>
        <tr>
            <td><span class="label">Ubicacion:</span> {{ $sesion->caja->ubicacion->nombre ?? '-' }}</td>
            <td class="right bold">{{ $sesion->id }}</td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Cajero:</span> {{ $cajeroNombre }}</td>
        </tr>
        <tr>
            <td><span class="label">Apertura:</span> {{ $sesion->abierta_en->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td><span class="label">Cierre:</span> {{ $sesion->cerrada_en ? $sesion->cerrada_en->format('d/m/Y h:i A') : '-' }}</td>
        </tr>
    </table>

    <div class="line-dashed"></div>

    {{-- Cuadre de Caja --}}
    <div class="seccion-titulo">--------- CUADRE DE CAJA ---------</div>
    @php
        $fmt = fn($v) => '$' . number_format((float) $v, 0, ',', '.');
    @endphp
    <table class="totales">
        <tr>
            <td>Base apertura</td>
            <td class="right" style="padding-right:2px;">{{ $fmt($resumen['monto_apertura']) }}</td>
        </tr>
        <tr>
            <td>(+) Ventas en efectivo</td>
            <td class="right" style="padding-right:2px;">{{ $fmt($resumen['ventas']['efectivo']) }}</td>
        </tr>
        <tr>
            <td>(-) Vales emitidos</td>
            <td class="right" style="padding-right:2px;">-{{ $fmt($resumen['vales']['total']) }}</td>
        </tr>
        <tr class="total-final">
            <td>(=) Efectivo esperado</td>
            <td class="right" style="padding-right:2px;">{{ $fmt($resumen['monto_esperado_efectivo']) }}</td>
        </tr>
        @if($sesion->estado === 'cerrada')
            <tr>
                <td>Efectivo contado</td>
                <td class="right" style="padding-right:2px;">{{ $fmt($sesion->monto_contado) }}</td>
            </tr>
            <tr>
                <td class="bold" style="border-top:1px dashed #000; padding-top:3px;">
                    Diferencia ({{ $sesion->diferencia_label }})
                </td>
                <td class="right bold" style="border-top:1px dashed #000; padding-top:3px; padding-right:2px;">
                    {{ $fmt($sesion->diferencia) }}
                </td>
            </tr>
        @endif
    </table>

    {{-- Desglose por Metodo de Pago --}}
    @if($resumen['por_metodo_pago']->count() > 0)
        <div class="seccion-titulo">------ DESGLOSE POR METODO DE PAGO ------</div>
        <table class="totales">
            @foreach($resumen['por_metodo_pago'] as $metodo => $datos)
                <tr>
                    <td>
                        {{ ucfirst($metodo) }}
                        <span class="xsmall">({{ $datos['cantidad'] }})</span>
                    </td>
                    <td class="right" style="padding-right:2px;">{{ $fmt($datos['total']) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Por tipo de transferencia --}}
    @if($resumen['por_tipo_transferencia']->count() > 0)
        <div class="seccion-titulo">------ TIPO DE TRANSFERENCIA ------</div>
        <table class="totales">
            @foreach($resumen['por_tipo_transferencia'] as $tipo => $datos)
                @php
                    $etiquetaTipo = match (strtolower($tipo)) {
                        'nequi'                  => 'Nequi',
                        'daviplata'              => 'DaviPlata',
                        'transferencia_bancaria' => 'Transferencia Bancaria',
                        default                  => ucfirst($tipo),
                    };
                @endphp
                <tr>
                    <td>
                        {{ $etiquetaTipo }}
                        <span class="xsmall">({{ $datos['cantidad'] }})</span>
                    </td>
                    <td class="right" style="padding-right:2px;">{{ $fmt($datos['total']) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Totales del Turno --}}
    <div class="seccion-titulo">--------- TOTALES DEL TURNO ---------</div>
    <table class="totales">
        <tr>
            <td>Ventas completadas <span class="xsmall">({{ $resumen['ventas']['cantidad'] }})</span></td>
            <td class="right" style="padding-right:2px;">{{ $fmt($resumen['ventas']['total']) }}</td>
        </tr>
        <tr>
            <td>Anulaciones <span class="xsmall">({{ $resumen['anulaciones']['cantidad'] }})</span></td>
            <td class="right" style="padding-right:2px;">{{ $fmt($resumen['anulaciones']['total']) }}</td>
        </tr>
        <tr>
            <td>Vales <span class="xsmall">({{ $resumen['vales']['cantidad'] }})</span></td>
            <td class="right" style="padding-right:2px;">{{ $fmt($resumen['vales']['total']) }}</td>
        </tr>
    </table>

    @if($sesion->observaciones_cierre)
        <div class="observaciones">
            <span class="bold">Observaciones:</span> {{ $sesion->observaciones_cierre }}
        </div>
    @endif

    <div class="line-solid"></div>
    <div class="gracias">**FIN DE TURNO**</div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 350);
        });
        window.addEventListener('afterprint', function () {
            window.close();
        });
    </script>
</body>
</html>
