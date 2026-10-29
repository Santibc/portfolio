{{--
    Recibo para impresora térmica de rollo de 58 mm (Xprinter XP-58). Documento suelto, sin
    el layout ni el CSS del panel: va siempre negro sobre blanco (es papel, no sigue el tema
    claro/oscuro). La caja lo carga en un iframe oculto e imprime.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $titulo }} · {{ $negocio->nombre }}</title>
    <style>
        * { box-sizing: border-box; }
        /* Sin size a propósito: en una impresora real Chrome imprime sobre la hoja del driver y,
           si el tamaño de página no coincide, centra el recibo en la hoja e imprime la fecha y el
           enlace en los márgenes que sobran. Así arranca arriba y el driver no imprime el blanco
           que queda al final. */
        @page { margin: 0; }
        html, body { margin: 0; padding: 0; background: white; color: black; }
        body { font: 600 12px/1.3 Arial, Helvetica, sans-serif; }
        /* Pegado a la izquierda y angosto: en la XP-58 del POS solo salen ~42 mm desde el borde
           izquierdo de la hoja (centrado o con 48 mm se cortaba la derecha). La regla del recibo
           de prueba muestra hasta dónde imprime. */
        .recibo { width: 42mm; max-width: 100%; margin: 0; padding: 2mm 1.5mm 0; overflow-wrap: anywhere; }
        .centro { text-align: center; }
        .negocio { font-size: 17px; font-weight: 700; line-height: 1.15; }
        .chico { font-size: 11px; font-weight: 400; }
        .titulo { margin-top: 2mm; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .sep { margin: 2mm 0; border: 0; border-top: 1px dashed black; }
        .fila { display: flex; justify-content: space-between; align-items: baseline; gap: 2mm; }
        .fila > :last-child { flex-shrink: 0; text-align: right; white-space: nowrap; }
        .linea + .linea { margin-top: 1.2mm; }
        .detalle { padding-left: 4mm; font-size: 11px; font-weight: 400; }
        .total { margin-bottom: .8mm; font-size: 16px; font-weight: 700; }
        .prueba { margin-top: 2mm; padding: 1.5mm; border: 1px dashed black; text-align: center; }
        .gracias { margin-top: 3mm; font-size: 12px; font-weight: 700; }
        /* A 6px el renglón mide ~38 mm: cabe en los 39 mm del recibo. */
        .creditos { margin-top: 3mm; font-size: 6px; font-weight: 400; white-space: nowrap; }
        /* Solo en la prueba, de borde a borde de la hoja (overflow: hidden para que no la ensanche). */
        .regla { position: relative; height: 5mm; overflow: hidden; font: 400 7px/1 Arial, Helvetica, sans-serif; }
        .regla i { position: absolute; bottom: 0; height: 1.5mm; border-left: 1px solid black; }
        .regla i.diez { height: 3mm; }
        .regla b { position: absolute; top: 0; padding-left: .4mm; font-weight: 400; }
        /* El último renglón tiene que pasar la barra de corte; el punto evita que el driver
           recorte ese espacio en blanco del final. */
        .corte { padding-top: 12mm; font-size: 8px; text-align: center; }

        @media screen {
            html { background: gainsboro; }
            body { width: 58mm; margin: 6mm auto; background: white; box-shadow: 0 1px 6px rgb(0 0 0 / .25); }
            .recibo { margin: 0 auto; }
        }
    </style>
</head>
<body>
    @if ($prueba)
        {{-- Regla en mm desde el borde izquierdo de la hoja: el último número que sale entero
             dice hasta dónde imprime el cabezal. --}}
        <div class="regla" aria-hidden="true">
            @foreach (range(0, 58, 2) as $mm)
                <i @class(['diez' => $mm % 10 === 0]) style="left: {{ $mm }}mm"></i>
                @if ($mm % 10 === 0 && $mm < 58)
                    <b style="left: {{ $mm }}mm">{{ $mm }}</b>
                @endif
            @endforeach
        </div>
    @endif
    <main class="recibo" data-recibo>
        <header class="centro">
            <div class="negocio">{{ $negocio->nombre }}</div>
            <div class="chico">{{ $negocio->direccionCompleta() }}</div>
            <div class="chico">Tel. {{ $negocio->telefonoVisible() }}</div>
            <div class="titulo">{{ $titulo }}</div>
            <div class="chico">
                {{ $fecha->format('d/m/Y g:i a') }}@if ($cajero) · {{ $cajero }}@endif
            </div>
        </header>

        @if ($prueba)
            <div class="prueba">
                Prueba de impresión. Si este recuadro se ve completo, sin cortes a los lados, la impresora quedó bien.
            </div>
        @endif

        <hr class="sep">

        @foreach ($lineas as $linea)
            <div class="linea">
                <div class="fila">
                    <span>{{ $linea['cantidad'] }} x {{ $linea['nombre'] }}</span>
                    <span>{{ $linea['subtotal'] }}</span>
                </div>
                @if ($linea['detalle'])
                    <div class="detalle">{{ $linea['detalle'] }}</div>
                @endif
            </div>
        @endforeach

        <hr class="sep">

        <div class="fila total"><span>TOTAL</span><span>{{ $total }}</span></div>
        @foreach ($pagos as $pago)
            <div class="fila"><span>{{ $pago['metodo'] }}</span><span>{{ $pago['monto'] }}</span></div>
        @endforeach
        @if ($cambio)
            <div class="fila"><span>Cambio</span><span>{{ $cambio }}</span></div>
        @endif

        @if ($notas)
            <hr class="sep">
            <div class="chico">Nota: {{ $notas }}</div>
        @endif

        <footer class="centro">
            <div class="gracias">¡Gracias por su compra!</div>
            <div class="chico">{{ $negocio->lema }}</div>
            <div class="chico">{{ $web }}</div>
            <div class="creditos">Desarrollador: Santiago Bellaizan · Tel: 320 223 0467</div>
        </footer>
        <div class="corte">.</div>
    </main>
</body>
</html>
