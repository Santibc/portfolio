<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vale {{ $vale->id }}</title>
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
            font-size: 12px;
            font-weight: bold;
            margin: 4px 0 4px;
            letter-spacing: 0.4px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
        }

        .vale-meta td { font-size: 9px; padding: 2px 0; vertical-align: top; }
        .vale-meta .label { font-weight: bold; }
        .vale-meta .num {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .vale-meta .vale-titulo {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .monto-row td {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }

        .anulado-watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%,-50%) rotate(-45deg);
            font-size: 30px; color: rgba(255,0,0,0.18); font-weight: bold;
        }

        .firma {
            text-align: center;
            font-size: 8px;
            margin-top: 14px;
        }
        .firma-line {
            border-top: 1px solid #000;
            width: 70%;
            margin: 0 auto 2px;
        }

        .gracias {
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>
    @if($vale->estado === 'anulado')
        <div class="anulado-watermark">ANULADO</div>
    @endif

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

    <div class="titulo-doc">Vale de Egreso</div>

    {{-- Meta del vale --}}
    @php
        $emisor = strtoupper($vale->usuario->name ?? 'VENDEDOR GENERAL');
        $fmt = fn($v) => number_format((float) $v, 2, ',', '.');
        $estadoLabel = match($vale->estado) {
            'pendiente' => 'PENDIENTE',
            'redimido'  => 'REDIMIDO',
            'anulado'   => 'ANULADO',
            default     => strtoupper($vale->estado),
        };
    @endphp
    <table class="vale-meta">
        <tr>
            <td><span class="label">Fecha:</span> {{ $vale->created_at->format('d/m/Y') }}</td>
            <td class="right vale-titulo">Vale</td>
        </tr>
        <tr>
            <td><span class="label">Hora:</span> {{ $vale->created_at->format('h:i A') }}</td>
            <td class="right num">{{ str_pad($vale->id, 3, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Emitido a:</span> {{ Str::limit($vale->descripcion ?? '-', 38) }}</td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Motivo:</span> {{ Str::limit($vale->descripcion ?? '-', 38) }}</td>
        </tr>
        <tr class="monto-row">
            <td><span class="label">Monto</span></td>
            <td class="right">${{ $fmt($vale->monto) }}</td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Emitido por:</span> {{ $emisor }}</td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Caja:</span> {{ $vale->caja->nombre ?? '-' }}</td>
        </tr>
        @if($vale->caja && $vale->caja->ubicacion)
            <tr>
                <td colspan="2"><span class="label">Ubicacion:</span> {{ $vale->caja->ubicacion->nombre }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="2"><span class="label">Estado:</span> {{ $estadoLabel }}</td>
        </tr>
        @if($vale->estado === 'redimido' && $vale->redimido_en)
            <tr>
                <td colspan="2" class="xsmall">
                    Redimido el {{ \Carbon\Carbon::parse($vale->redimido_en)->format('d/m/Y h:i A') }}
                </td>
            </tr>
        @endif
        @if($vale->estado === 'anulado' && $vale->motivo_anulacion)
            <tr>
                <td colspan="2" class="xsmall">
                    Motivo anulacion: {{ Str::limit($vale->motivo_anulacion, 60) }}
                </td>
            </tr>
        @endif
    </table>

    <div class="firma">
        <div style="height: 18px;"></div>
        <div class="firma-line"></div>
        Firma autorizado
    </div>

    <div class="line-solid"></div>
    <div class="gracias">**MIRACLE BEAUTY EXPERTS**</div>

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
