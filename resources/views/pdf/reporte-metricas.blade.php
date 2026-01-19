<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Métricas - Miracle</title>
    <style>
        /* Variables de colores Miracle */
        :root {
            --miracle-pink: #FF84D5;
            --miracle-lilac: #BCA9F5;
            --miracle-dark: #382E65;
            --miracle-aqua: #B9DFDE;
            --miracle-gold: #D4AF37;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #FF84D5, #BCA9F5);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header .periodo {
            font-size: 14px;
            opacity: 0.9;
        }

        .header .fecha-generacion {
            font-size: 10px;
            opacity: 0.8;
            margin-top: 10px;
        }

        /* KPIs Grid */
        .kpis-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .kpi-row {
            display: table-row;
        }

        .kpi-card {
            display: table-cell;
            width: 25%;
            padding: 5px;
            vertical-align: top;
        }

        .kpi-card-inner {
            background: #fff;
            border: 2px solid #FF84D5;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .kpi-card-inner.success {
            border-color: #28a745;
        }

        .kpi-card-inner.warning {
            border-color: #ffc107;
        }

        .kpi-card-inner.lilac {
            border-color: #BCA9F5;
        }

        .kpi-title {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .kpi-value {
            font-size: 20px;
            font-weight: bold;
            color: #382E65;
        }

        .kpi-subtitle {
            font-size: 9px;
            color: #888;
            margin-top: 3px;
        }

        /* Secciones */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #BCA9F5;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
        }

        .section-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 15px;
            border-radius: 0 0 5px 5px;
        }

        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            background: #FF84D5;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr.highlight {
            background: #FFE4F3;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        /* Distribución por estado */
        .estado-grid {
            display: table;
            width: 100%;
        }

        .estado-item {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
        }

        .estado-box {
            padding: 15px;
            border-radius: 8px;
        }

        .estado-box.pendiente {
            background: #fff3cd;
            border: 2px solid #ffc107;
        }

        .estado-box.aplicada {
            background: #d4edda;
            border: 2px solid #28a745;
        }

        .estado-box.rechazada {
            background: #f8d7da;
            border: 2px solid #dc3545;
        }

        .estado-cantidad {
            font-size: 24px;
            font-weight: bold;
            color: #382E65;
        }

        .estado-monto {
            font-size: 12px;
            color: #666;
        }

        .estado-porcentaje {
            font-size: 10px;
            color: #888;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #BCA9F5;
            text-align: center;
            font-size: 9px;
            color: #888;
        }

        .footer img {
            height: 30px;
            margin-bottom: 5px;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Reporte de Métricas</h1>
            <div class="periodo">
                Período: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}
            </div>
            <div class="fecha-generacion">
                Generado el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- KPIs Principales -->
        <div class="kpis-grid">
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-card-inner success">
                        <div class="kpi-title">Total Ventas</div>
                        <div class="kpi-value">${{ number_format($metricas['resumen']['total_ventas'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-subtitle">{{ $metricas['resumen']['total_transacciones'] ?? 0 }} transacciones</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-card-inner warning">
                        <div class="kpi-title">Pendientes</div>
                        <div class="kpi-value">{{ $metricas['cotizaciones']['pendientes']['cantidad'] ?? 0 }}</div>
                        <div class="kpi-subtitle">${{ number_format($metricas['cotizaciones']['pendientes']['monto'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-card-inner">
                        <div class="kpi-title">Aplicadas</div>
                        <div class="kpi-value">{{ $metricas['cotizaciones']['aplicadas']['cantidad'] ?? 0 }}</div>
                        <div class="kpi-subtitle">${{ number_format($metricas['cotizaciones']['aplicadas']['monto'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-card-inner lilac">
                        <div class="kpi-title">Tasa Conversión</div>
                        <div class="kpi-value">{{ $metricas['cotizaciones']['tasa_conversion'] ?? 0 }}%</div>
                        <div class="kpi-subtitle">Aplicadas / Total</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución por Estado -->
        <div class="section">
            <div class="section-title">Distribución de Cotizaciones por Estado</div>
            <div class="section-content">
                <div class="estado-grid">
                    <div class="estado-item">
                        <div class="estado-box pendiente">
                            <div class="estado-cantidad">{{ $metricas['cotizaciones']['pendientes']['cantidad'] ?? 0 }}</div>
                            <div class="estado-monto">${{ number_format($metricas['cotizaciones']['pendientes']['monto'] ?? 0, 0, ',', '.') }}</div>
                            <div class="estado-porcentaje">{{ $metricas['cotizaciones']['pendientes']['porcentaje'] ?? 0 }}% del total</div>
                            <strong>Pendientes</strong>
                        </div>
                    </div>
                    <div class="estado-item">
                        <div class="estado-box aplicada">
                            <div class="estado-cantidad">{{ $metricas['cotizaciones']['aplicadas']['cantidad'] ?? 0 }}</div>
                            <div class="estado-monto">${{ number_format($metricas['cotizaciones']['aplicadas']['monto'] ?? 0, 0, ',', '.') }}</div>
                            <div class="estado-porcentaje">{{ $metricas['cotizaciones']['aplicadas']['porcentaje'] ?? 0 }}% del total</div>
                            <strong>Aplicadas</strong>
                        </div>
                    </div>
                    <div class="estado-item">
                        <div class="estado-box rechazada">
                            <div class="estado-cantidad">{{ $metricas['cotizaciones']['rechazadas']['cantidad'] ?? 0 }}</div>
                            <div class="estado-monto">${{ number_format($metricas['cotizaciones']['rechazadas']['monto'] ?? 0, 0, ',', '.') }}</div>
                            <div class="estado-porcentaje">{{ $metricas['cotizaciones']['rechazadas']['porcentaje'] ?? 0 }}% del total</div>
                            <strong>Rechazadas</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Vendedores -->
        <div class="section">
            <div class="section-title">Ranking de Vendedores</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Vendedor</th>
                            <th class="text-center">Cotizaciones</th>
                            <th class="text-right">Monto Cotizado</th>
                            <th class="text-center">Aplicadas</th>
                            <th class="text-right">Monto Aplicadas</th>
                            <th class="text-center">Conversión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metricas['top_vendedores'] ?? [] as $index => $vendedor)
                            <tr class="{{ $index === 0 ? 'highlight' : '' }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $vendedor['vendedor'] }}</td>
                                <td class="text-center">{{ $vendedor['total_cotizaciones'] }}</td>
                                <td class="text-right">${{ number_format($vendedor['monto_total'], 0, ',', '.') }}</td>
                                <td class="text-center">{{ $vendedor['aplicadas'] }}</td>
                                <td class="text-right">${{ number_format($vendedor['monto_aplicadas'], 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $vendedor['tasa_conversion'] >= 50 ? 'badge-success' : ($vendedor['tasa_conversion'] >= 30 ? 'badge-warning' : 'badge-danger') }}">
                                        {{ $vendedor['tasa_conversion'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Sin datos para este período</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Productos -->
        <div class="section">
            <div class="section-title">Productos Más Vendidos</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Referencia</th>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Monto Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metricas['top_productos'] ?? [] as $index => $producto)
                            <tr class="{{ $index < 3 ? 'highlight' : '' }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><strong>{{ $producto['referencia'] }}</strong></td>
                                <td>{{ Str::limit($producto['nombre'], 40) }}</td>
                                <td class="text-center">{{ $producto['cantidad_vendida'] }}</td>
                                <td class="text-right">${{ number_format($producto['monto_total'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Sin datos para este período</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Miracle Platform</strong></p>
            <p>Reporte generado automáticamente - {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
