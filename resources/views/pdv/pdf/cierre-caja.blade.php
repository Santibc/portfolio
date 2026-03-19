<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #FF84D5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px 8px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .right { text-align: right; }
        .total-row { background: #FFF1DD; font-weight: bold; }
        .firma { margin-top: 60px; text-align: center; }
        .firma-line { border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>MIRACLE BEAUTY EXPERTS</h2>
        <h3>Reporte de Cierre de Caja</h3>
    </div>

    <table>
        <tr><td><strong>Caja:</strong></td><td>{{ $sesion->caja->nombre }}</td><td><strong>Ubicación:</strong></td><td>{{ $sesion->caja->ubicacion->nombre ?? '-' }}</td></tr>
        <tr><td><strong>Cajero:</strong></td><td>{{ $sesion->usuario->name }}</td><td><strong>Fecha:</strong></td><td>{{ $sesion->abierta_en->format('d/m/Y') }}</td></tr>
        <tr><td><strong>Apertura:</strong></td><td>{{ $sesion->abierta_en->format('h:i A') }}</td><td><strong>Cierre:</strong></td><td>{{ $sesion->cerrada_en ? $sesion->cerrada_en->format('h:i A') : '-' }}</td></tr>
    </table>

    <h4>Resumen del Turno</h4>
    <table>
        <tr><td>Base (apertura):</td><td class="right">${{ number_format($resumen['monto_apertura'], 2) }}</td></tr>
        <tr><td>(+) Ventas en efectivo:</td><td class="right">${{ number_format($resumen['ventas']['efectivo'], 2) }}</td></tr>
        <tr><td>(-) Cambio entregado:</td><td class="right">-${{ number_format($resumen['ventas']['cambio_entregado'], 2) }}</td></tr>
        <tr><td>(-) Vales:</td><td class="right">-${{ number_format($resumen['vales']['total'], 2) }}</td></tr>
        <tr class="total-row"><td>(=) Efectivo esperado:</td><td class="right">${{ number_format($resumen['monto_esperado_efectivo'], 2) }}</td></tr>
        <tr><td>Efectivo contado:</td><td class="right">${{ number_format($sesion->monto_contado, 2) }}</td></tr>
        <tr class="total-row"><td>Diferencia ({{ $sesion->diferencia_label }}):</td><td class="right">${{ number_format($sesion->diferencia, 2) }}</td></tr>
    </table>

    <h4>Ventas del Turno</h4>
    <table>
        <tr><td>Total ventas completadas:</td><td class="right">{{ $resumen['ventas']['cantidad'] }}</td><td class="right">${{ number_format($resumen['ventas']['total'], 2) }}</td></tr>
        <tr><td>Total transferencias:</td><td class="right">-</td><td class="right">${{ number_format($resumen['ventas']['transferencia'], 2) }}</td></tr>
        <tr><td>Total anulaciones:</td><td class="right">{{ $resumen['anulaciones']['cantidad'] }}</td><td class="right">${{ number_format($resumen['anulaciones']['total'], 2) }}</td></tr>
        <tr><td>Total vales:</td><td class="right">{{ $resumen['vales']['cantidad'] }}</td><td class="right">${{ number_format($resumen['vales']['total'], 2) }}</td></tr>
    </table>

    @if($sesion->observaciones_cierre)
        <p><strong>Observaciones:</strong> {{ $sesion->observaciones_cierre }}</p>
    @endif

    <div class="firma">
        <div class="firma-line">{{ $sesion->usuario->name }}<br><small>Responsable de Caja</small></div>
    </div>
</body>
</html>
