<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva solicitud de cotización</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; color: #333; background:#f4f4f4; margin:0; padding:0; }
        .container { max-width: 600px; margin: 20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 8px rgba(0,0,0,.08); }
        .header { background:#007bff; color:#fff; padding:18px 20px; text-align:center; border-radius:8px 8px 0 0; margin:-20px -20px 16px -20px; }
        .info { background:#f8f9fa; border-left:4px solid #007bff; padding:12px 16px; margin:14px 0; }
        .btn { display:inline-block; padding:10px 22px; background:#007bff; color:#fff; text-decoration:none; border-radius:5px; margin-top:10px; }
        .footer { text-align:center; color:#888; font-size:12px; margin-top:24px; padding-top:14px; border-top:1px solid #eee; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th { background:#e9f3ff; color:#003e7e; padding:8px; text-align:left; font-size:12px; }
        td { padding:6px 8px; border-bottom:1px solid #eee; font-size:12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">Nueva solicitud de cotización</h2>
        </div>

        <p>Se ha registrado una nueva solicitud que requiere tu revisión.</p>

        <div class="info">
            <p style="margin:0 0 6px;"><strong>Código:</strong> {{ $solicitud->codigo_corto }}</p>
            <p style="margin:0 0 6px;"><strong>Cliente:</strong> {{ $solicitud->cliente->nombre_contacto }}
                @if($solicitud->cliente->nombre_empresa) ({{ $solicitud->cliente->nombre_empresa }}) @endif
            </p>
            <p style="margin:0 0 6px;"><strong>Vendedor:</strong> {{ $solicitud->cliente->vendedor->name ?? '—' }}</p>
            <p style="margin:0 0 6px;"><strong>Fecha:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin:0;"><strong>Total estimado:</strong> ${{ number_format($solicitud->monto_total, 2) }}</p>
        </div>

        <table>
            <thead>
                <tr><th>Producto</th><th style="text-align:center;">Cant.</th><th style="text-align:right;">Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach($solicitud->items as $item)
                <tr>
                    <td>{{ $item->nombre_producto }}
                        @if($item->info_variante) <small style="color:#888;">{{ $item->info_variante }}</small> @endif
                    </td>
                    <td style="text-align:center;">{{ $item->cantidad }}</td>
                    <td style="text-align:right;">${{ number_format($item->precio_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="text-align:center;">
            <a href="{{ $detalleUrl }}" class="btn">Ver en el sistema</a>
        </p>

        <div class="footer">
            Este correo se envió automáticamente al crear la solicitud.
        </div>
    </div>
</body>
</html>
