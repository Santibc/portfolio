<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="color: #FF84D5;">MIRACLE BEAUTY EXPERTS</h2>
        <h3>Reporte de Vales de Caja</h3>
        <small>Generado: {{ now()->format('d/m/Y h:i A') }}</small>
    </div>
    <table>
        <thead><tr><th>Caja</th><th>Descripción</th><th>Monto</th><th>Responsable</th><th>Estado</th><th>Fecha</th></tr></thead>
        <tbody>
            @foreach($vales as $v)
                <tr>
                    <td>{{ $v->caja->nombre ?? '-' }}</td>
                    <td>{{ $v->descripcion }}</td>
                    <td class="right">${{ number_format($v->monto, 2) }}</td>
                    <td>{{ $v->usuario->name ?? '-' }}</td>
                    <td>{{ ucfirst($v->estado) }}</td>
                    <td>{{ $v->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="2" class="right"><strong>Total:</strong></td><td class="right"><strong>${{ number_format($vales->sum('monto'), 2) }}</strong></td><td colspan="3"></td></tr>
        </tfoot>
    </table>
</body>
</html>
