<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura {{ $factura->numero ?? $factura->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 30px;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 3px solid #2d5016;
            padding-bottom: 20px;
        }

        .header table {
            width: 100%;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 11px;
            color: #666;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2d5016;
            text-align: right;
        }

        .invoice-number {
            font-size: 14px;
            text-align: right;
            color: #333;
        }

        .info-section {
            width: 100%;
            margin-bottom: 25px;
        }

        .info-section table {
            width: 100%;
        }

        .info-box {
            vertical-align: top;
            width: 50%;
            padding: 10px;
        }

        .info-box-title {
            font-size: 11px;
            font-weight: bold;
            color: #2d5016;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #2d5016;
        }

        .info-box-content {
            font-size: 12px;
        }

        .info-box-content p {
            margin: 3px 0;
        }

        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .lines-table th {
            background-color: #2d5016;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }

        .lines-table th.text-right {
            text-align: right;
        }

        .lines-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }

        .lines-table td.text-right {
            text-align: right;
        }

        .lines-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .description {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        .totals-section {
            width: 100%;
            margin-top: 20px;
        }

        .totals-section table {
            width: 100%;
        }

        .totals-left {
            vertical-align: top;
            width: 55%;
        }

        .totals-right {
            vertical-align: top;
            width: 45%;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .totals-table td:first-child {
            text-align: right;
        }

        .totals-table td:last-child {
            text-align: right;
            font-weight: bold;
            width: 120px;
        }

        .totals-table tr.total-row {
            background-color: #2d5016;
            color: white;
        }

        .totals-table tr.total-row td {
            font-size: 16px;
            border: none;
            padding: 15px 10px;
        }

        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f5f5f5;
        }

        .notes-title {
            font-size: 11px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .notes-content {
            font-size: 11px;
            color: #666;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .anulada-watermark {
            position: fixed;
            top: 40%;
            left: 20%;
            font-size: 100px;
            color: rgba(220, 53, 69, 0.15);
            font-weight: bold;
            transform: rotate(-45deg);
        }

        .euro {
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>
<body>
    @if($factura->estado == 'anulada')
        <div class="anulada-watermark">ANULADA</div>
    @endif

    {{-- Cabecera --}}
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="company-name">MANZER AGROFORESTAL, S.R.L.U.</div>
                    <div class="company-info">
                        Servicios forestales y agroforestales<br>
                        CIF: B12345678<br>
                        C/ Ejemplo, 123 - 08001 Barcelona<br>
                        Tel: 93 000 00 00 | info@manzer.es
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top;">
                    <div class="invoice-title">FACTURA</div>
                    <div class="invoice-number">
                        <strong>{{ $factura->numero ?? 'BORRADOR' }}</strong><br>
                        Fecha: {{ $factura->fecha_emision->format('d/m/Y') }}
                        @if($factura->fecha_vencimiento)
                            <br>Vencimiento: {{ $factura->fecha_vencimiento->format('d/m/Y') }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Informacion cliente y obra --}}
    <div class="info-section">
        <table>
            <tr>
                <td class="info-box">
                    <div class="info-box-title">Datos del Cliente</div>
                    <div class="info-box-content">
                        <p><strong>{{ $factura->cliente?->nombre_comercial }}</strong></p>
                        @if($factura->cliente?->razon_social && $factura->cliente->razon_social != $factura->cliente->nombre_comercial)
                            <p>{{ $factura->cliente->razon_social }}</p>
                        @endif
                        @if($factura->cliente?->cif)
                            <p>CIF: {{ $factura->cliente->cif }}</p>
                        @endif
                        @if($factura->cliente?->direccion)
                            <p>{{ $factura->cliente->direccion }}</p>
                        @endif
                        @if($factura->cliente?->codigo_postal || $factura->cliente?->ciudad)
                            <p>
                                {{ $factura->cliente->codigo_postal }}
                                {{ $factura->cliente->ciudad }}
                                @if($factura->cliente?->provincia)
                                    ({{ $factura->cliente->provincia }})
                                @endif
                            </p>
                        @endif
                    </div>
                </td>
                <td class="info-box">
                    @if($factura->obra)
                    <div class="info-box-title">Obra / Proyecto</div>
                    <div class="info-box-content">
                        <p><strong>{{ $factura->obra->codigo }}</strong></p>
                        <p>{{ $factura->obra->nombre }}</p>
                        @if($factura->obra->direccion)
                            <p>{{ $factura->obra->direccion }}</p>
                        @endif
                        @if($factura->obra->localidad)
                            <p>{{ $factura->obra->localidad }} ({{ $factura->obra->provincia }})</p>
                        @endif
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Tabla de lineas --}}
    @php
        $tieneDescuentos = $factura->lineas->contains(fn($linea) => $linea->descuento_porcentaje > 0);
    @endphp
    <table class="lines-table">
        <thead>
            <tr>
                <th style="width: {{ $tieneDescuentos ? '40%' : '45%' }};">Concepto</th>
                <th class="text-right" style="width: {{ $tieneDescuentos ? '12%' : '15%' }};">Cantidad</th>
                <th class="text-right" style="width: {{ $tieneDescuentos ? '15%' : '18%' }};">Precio Unit.</th>
                @if($tieneDescuentos)
                <th class="text-right" style="width: 10%;">Dto.</th>
                @endif
                <th class="text-right" style="width: {{ $tieneDescuentos ? '18%' : '22%' }};">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->lineas as $linea)
            <tr>
                <td>
                    {{ $linea->concepto }}
                    @if($linea->descripcion)
                        <div class="description">{{ $linea->descripcion }}</div>
                    @endif
                </td>
                <td class="text-right">{{ number_format($linea->cantidad, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($linea->precio_unitario, 2, ',', '.') }} EUR</td>
                @if($tieneDescuentos)
                <td class="text-right">{{ number_format($linea->descuento_porcentaje, 0) }}%</td>
                @endif
                <td class="text-right">{{ number_format($linea->importe, 2, ',', '.') }} EUR</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div class="totals-section">
        <table>
            <tr>
                <td class="totals-left">
                    @if($factura->cliente?->condiciones_pago)
                        <div class="notes-section" style="margin-top: 0;">
                            <div class="notes-title">Condiciones de Pago</div>
                            <div class="notes-content">{{ $factura->cliente->condiciones_pago }}</div>
                        </div>
                    @endif
                </td>
                <td class="totals-right">
                    <table class="totals-table">
                        <tr>
                            <td>Base Imponible:</td>
                            <td>{{ number_format($factura->base_imponible, 2, ',', '.') }} EUR</td>
                        </tr>
                        <tr>
                            <td>IVA ({{ number_format($factura->iva_porcentaje, 0) }}%):</td>
                            <td>{{ number_format($factura->iva_importe, 2, ',', '.') }} EUR</td>
                        </tr>
                        @if($factura->retencion_porcentaje > 0)
                        <tr>
                            <td>Retencion ({{ number_format($factura->retencion_porcentaje, 0) }}%):</td>
                            <td>-{{ number_format($factura->retencion_importe, 2, ',', '.') }} EUR</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td>TOTAL:</td>
                            <td>{{ number_format($factura->total, 2, ',', '.') }} EUR</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Notas --}}
    @if($factura->notas)
    <div class="notes-section">
        <div class="notes-title">Observaciones</div>
        <div class="notes-content">{{ $factura->notas }}</div>
    </div>
    @endif

    {{-- Pie de pagina --}}
    <div class="footer">
        {{ $factura->footer_text ?? \App\Models\Factura::DEFAULT_FOOTER_TEXT }}
    </div>
</body>
</html>
