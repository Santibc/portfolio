<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FF84D5 0%, #BCA9F5 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .status-icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
        .info-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #BCA9F5;
        }
        .monto-destacado {
            background: linear-gradient(135deg, #FF84D5 0%, #BCA9F5 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 15px 0;
        }
        .monto-destacado .label {
            font-size: 12px;
            opacity: 0.9;
        }
        .monto-destacado .valor {
            font-size: 24px;
            font-weight: bold;
            margin-top: 3px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-badge.envio-pendiente { background-color: #6c757d; color: white; }
        .status-badge.envio-preparando { background-color: #17a2b8; color: white; }
        .status-badge.envio-despachado { background-color: #0d6efd; color: white; }
        .status-badge.envio-en_transito { background-color: #ffc107; color: #333; }
        .status-badge.envio-entregado { background-color: #28a745; color: white; }
        .status-badge.pago { background-color: #28a745; color: white; }
        .timeline {
            margin: 20px 0;
            padding: 0;
            list-style: none;
        }
        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .timeline-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 14px;
        }
        .timeline-icon.active {
            background-color: #28a745;
            color: white;
        }
        .timeline-icon.current {
            background-color: #FF84D5;
            color: white;
        }
        .timeline-icon.pending {
            background-color: #e9ecef;
            color: #6c757d;
        }
        .timeline-content h4 {
            margin: 0 0 3px 0;
            font-size: 14px;
        }
        .timeline-content p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
        .guia-info {
            background-color: #e7f5ff;
            border: 1px solid #74c0fc;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .guia-info h4 {
            color: #1c7ed6;
            margin: 0 0 10px 0;
        }
        .guia-numero {
            font-family: monospace;
            font-size: 18px;
            background-color: white;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
        }
        .action-btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #FF84D5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($tipoEstado === 'envio')
                @if($estadoNuevo === 'despachado')
                    <div class="status-icon">&#128666;</div>
                    <h1>¡Tu pedido va en camino!</h1>
                @elseif($estadoNuevo === 'entregado')
                    <div class="status-icon">&#9989;</div>
                    <h1>¡Pedido Entregado!</h1>
                @elseif($estadoNuevo === 'preparando')
                    <div class="status-icon">&#128230;</div>
                    <h1>Preparando tu pedido</h1>
                @elseif($estadoNuevo === 'en_transito')
                    <div class="status-icon">&#128666;</div>
                    <h1>En camino hacia ti</h1>
                @else
                    <div class="status-icon">&#128230;</div>
                    <h1>Actualización de Envío</h1>
                @endif
            @elseif($tipoEstado === 'pago')
                <div class="status-icon">&#9989;</div>
                <h1>Pago Confirmado</h1>
            @else
                <div class="status-icon">&#128221;</div>
                <h1>Actualización de Pedido</h1>
            @endif
        </div>

        <p>Hola <strong>{{ $solicitud->cliente->nombre_contacto ?? 'Cliente' }}</strong>,</p>

        @php
            $mailable = new \App\Mail\EstadoPedidoCambiado($solicitud, $tipoEstado, $estadoAnterior, $estadoNuevo);
        @endphp
        <p>{{ $mailable->getMensajeCambio() }}</p>

        <div style="text-align: center;">
            <span class="status-badge envio-{{ $estadoNuevo }}">
                @if($tipoEstado === 'envio')
                    {{ \App\Models\SolicitudCotizacion::estadosEnvio()[$estadoNuevo] ?? ucfirst($estadoNuevo) }}
                @elseif($tipoEstado === 'pago')
                    Pago Confirmado
                @else
                    {{ ucfirst($estadoNuevo) }}
                @endif
            </span>
        </div>

        <div class="info-card">
            <h4 style="margin-top: 0; color: #382E65;">Detalles del Pedido</h4>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 5px 0;"><strong>Número:</strong></td>
                    <td>{{ $solicitud->numero_solicitud }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Fecha:</strong></td>
                    <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Productos:</strong></td>
                    <td>{{ $solicitud->items->count() }} artículos</td>
                </tr>
            </table>
        </div>

        <div class="monto-destacado">
            <div class="label">Total del Pedido</div>
            <div class="valor">$ {{ number_format($solicitud->monto_total_con_iva, 0, ',', '.') }}</div>
        </div>

        @if($tipoEstado === 'envio' && in_array($estadoNuevo, ['despachado', 'en_transito', 'entregado']))
            @if($solicitud->numero_guia || $solicitud->transportadora)
            <div class="guia-info">
                <h4>&#128666; Información de Envío</h4>
                @if($solicitud->transportadora)
                <p><strong>Transportadora:</strong> {{ $solicitud->transportadora }}</p>
                @endif
                @if($solicitud->numero_guia)
                <p><strong>Número de Guía:</strong></p>
                <span class="guia-numero">{{ $solicitud->numero_guia }}</span>
                @endif
                @if($solicitud->despachado_en)
                <p style="margin-top: 10px;"><strong>Fecha de despacho:</strong> {{ $solicitud->despachado_en->format('d/m/Y H:i') }}</p>
                @endif
            </div>
            @endif
        @endif

        @if($tipoEstado === 'envio')
        <h4 style="color: #382E65;">Estado del Envío</h4>
        <ul class="timeline">
            @php
                $estadosEnvio = [
                    'pendiente' => ['icon' => '&#128337;', 'label' => 'Pendiente'],
                    'preparando' => ['icon' => '&#128230;', 'label' => 'Preparando'],
                    'despachado' => ['icon' => '&#128666;', 'label' => 'Despachado'],
                    'en_transito' => ['icon' => '&#128506;', 'label' => 'En Tránsito'],
                    'entregado' => ['icon' => '&#9989;', 'label' => 'Entregado'],
                ];
                $estadosKeys = array_keys($estadosEnvio);
                $indiceActual = array_search($estadoNuevo, $estadosKeys);
            @endphp
            @foreach($estadosEnvio as $key => $estado)
                @php
                    $indice = array_search($key, $estadosKeys);
                    if ($key === $estadoNuevo) {
                        $iconClass = 'current';
                    } elseif ($indice < $indiceActual) {
                        $iconClass = 'active';
                    } else {
                        $iconClass = 'pending';
                    }
                @endphp
                <li class="timeline-item">
                    <div class="timeline-icon {{ $iconClass }}">{!! $estado['icon'] !!}</div>
                    <div class="timeline-content">
                        <h4>{{ $estado['label'] }}</h4>
                        @if($key === $estadoNuevo)
                            <p style="color: #FF84D5;">Estado actual</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        @endif

{{--         <div style="text-align: center; margin-top: 25px;">
            <p style="color: #666; font-size: 14px;">
                Puedes hacer seguimiento de tu pedido en nuestro portal:
            </p>

            <a href="{{ config('app.url') }}/portal" class="action-btn">Ver Mi Pedido</a>
        </div> --}}

        <div class="footer">
            <p><strong>Miracle Beauty Experts</strong></p>
            <p>¿Tienes preguntas? Contáctanos:</p>
            <p>
                &#128231; ventas@miraclebeauty.com<br>
                &#128222; +57 300 123 4567
            </p>
            <p style="color: #999; font-size: 11px;">
                Este es un correo automático. Por favor no responder directamente a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
