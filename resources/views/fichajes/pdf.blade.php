<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fichajes - {{ $fechaDesde }} a {{ $fechaHasta }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }
        .summary-label {
            color: #666;
            font-size: 9px;
        }
        .summary-value {
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Fichajes</h1>
        <p>Período: {{ $fechaDesde }} - {{ $fechaHasta }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Total Fichajes:</span>
            <span class="summary-value">{{ $fichajes->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Horas Trabajadas:</span>
            <span class="summary-value">{{ number_format($fichajes->sum('horas_trabajadas'), 1) }}h</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Horas Extra:</span>
            <span class="summary-value">{{ number_format($fichajes->sum('horas_extra'), 1) }}h</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Pendientes:</span>
            <span class="summary-value">{{ $fichajes->where('validado', false)->count() }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Trabajador</th>
                <th>Obra</th>
                <th class="text-center">Entrada</th>
                <th class="text-center">Salida</th>
                <th class="text-center">Horas</th>
                <th class="text-center">Extra</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fichajes as $fichaje)
                <tr>
                    <td>
                        {{ $fichaje->fecha->format('d/m/Y') }}
                        <br><small style="color: #666;">{{ $fichaje->fecha->translatedFormat('l') }}</small>
                    </td>
                    <td>{{ $fichaje->trabajador ? $fichaje->trabajador->nombre . ' ' . $fichaje->trabajador->apellidos : '-' }}</td>
                    <td>{{ $fichaje->obra ? $fichaje->obra->nombre : '-' }}</td>
                    <td class="text-center">{{ $fichaje->hora_entrada ? \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') : '-' }}</td>
                    <td class="text-center">{{ $fichaje->hora_salida ? \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') : '-' }}</td>
                    <td class="text-center">{{ $fichaje->horas_trabajadas ? number_format($fichaje->horas_trabajadas, 1) . 'h' : '-' }}</td>
                    <td class="text-center">{{ $fichaje->horas_extra > 0 ? '+' . number_format($fichaje->horas_extra, 1) . 'h' : '-' }}</td>
                    <td class="text-center">
                        @if($fichaje->validado)
                            <span class="badge badge-success">Validado</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay fichajes para mostrar</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} - Manzer
    </div>
</body>
</html>
