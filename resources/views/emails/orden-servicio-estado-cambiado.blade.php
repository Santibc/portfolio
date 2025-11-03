<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Estado - Orden de Servicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #198754;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #198754;
            margin: 0;
            font-size: 24px;
        }
        .orden-numero {
            background-color: #198754;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin: 15px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .estado-cambio {
            background-color: #f8f9fa;
            border-left: 4px solid #198754;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .estado-cambio h2 {
            color: #198754;
            margin-top: 0;
            font-size: 18px;
        }
        .estados-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
        }
        .estado-box {
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            min-width: 150px;
        }
        .estado-anterior {
            background-color: #e9ecef;
            color: #495057;
            text-decoration: line-through;
        }
        .flecha {
            font-size: 24px;
            color: #198754;
            font-weight: bold;
        }
        .estado-nuevo {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 2px solid #198754;
        }
        .estado-recibida {
            background-color: #cfe2ff;
            color: #084298;
        }
        .estado-asignada {
            background-color: #fff3cd;
            color: #664d03;
        }
        .estado-en-proceso {
            background-color: #cff4fc;
            color: #055160;
        }
        .estado-en-espera-repuestos {
            background-color: #f8d7da;
            color: #842029;
        }
        .estado-diagnosticada {
            background-color: #e7d5f7;
            color: #59359a;
        }
        .estado-reparada {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .estado-lista-entrega {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .estado-entregada {
            background-color: #a3cfbb;
            color: #0a3622;
        }
        .estado-cancelada {
            background-color: #f8d7da;
            color: #842029;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h2 {
            color: #198754;
            font-size: 18px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        .info-row {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 140px;
        }
        .info-value {
            color: #333;
        }
        .observaciones-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .observaciones-box h3 {
            color: #664d03;
            margin-top: 0;
            font-size: 16px;
        }
        .observaciones-box p {
            margin: 0;
            color: #664d03;
        }
        .alert-info {
            background-color: #d1e7dd;
            border-left: 4px solid #198754;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-info p {
            margin: 5px 0;
            color: #0f5132;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        @media only screen and (max-width: 600px) {
            .estados-container {
                flex-direction: column;
                gap: 10px;
            }
            .flecha {
                transform: rotate(90deg);
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Actualización de Orden de Servicio</h1>
            <div class="orden-numero">{{ $orden->numero_orden }}</div>
        </div>

        <div class="alert-info">
            <p><strong>Estimado/a {{ $orden->cliente->nombre }},</strong></p>
            <p>Le informamos que el estado de su orden de servicio ha sido actualizado.</p>
        </div>

        <div class="estado-cambio">
            <h2>Cambio de Estado</h2>
            <div class="estados-container">
                <div class="estado-box estado-anterior estado-{{ $estadoAnterior }}">
                    @php
                        $estados = [
                            'recibida' => 'Recibida',
                            'asignada' => 'Asignada',
                            'en_proceso' => 'En Proceso',
                            'diagnosticada' => 'Diagnosticada',
                            'en_espera_repuestos' => 'Esperando Repuestos',
                            'reparada' => 'Reparada',
                            'lista_entrega' => 'Lista para Entrega',
                            'entregada' => 'Entregada',
                            'cancelada' => 'Cancelada'
                        ];
                    @endphp
                    {{ $estados[$estadoAnterior] ?? ucfirst($estadoAnterior) }}
                </div>
                <div class="flecha">→</div>
                <div class="estado-box estado-nuevo estado-{{ $estadoNuevo }}">
                    {{ $estados[$estadoNuevo] ?? ucfirst($estadoNuevo) }}
                </div>
            </div>
            <div style="text-align: center; margin-top: 15px; color: #666; font-size: 14px;">
                Actualizado el {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>

        @if($observaciones)
        <div class="observaciones-box">
            <h3>Observaciones</h3>
            <p>{{ $observaciones }}</p>
        </div>
        @endif

        <div class="info-section">
            <h2>Información de la Orden</h2>
            <div class="info-row">
                <span class="info-label">Número de Orden:</span>
                <span class="info-value">{{ $orden->numero_orden }}</span>
            </div>
            @if($orden->equipo)
            <div class="info-row">
                <span class="info-label">Equipo:</span>
                <span class="info-value">{{ $orden->equipo->tipo_equipo }} - {{ $orden->equipo->marca }} {{ $orden->equipo->modelo }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Tipo de Servicio:</span>
                <span class="info-value">
                    @switch($orden->tipo_servicio)
                        @case('reparacion') Reparación @break
                        @case('mantenimiento') Mantenimiento @break
                        @case('instalacion') Instalación @break
                        @case('revision') Revisión @break
                        @case('garantia') Garantía @break
                        @default {{ $orden->tipo_servicio }}
                    @endswitch
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Ingreso:</span>
                <span class="info-value">{{ $orden->fecha_recepcion ? $orden->fecha_recepcion->format('d/m/Y') : 'N/A' }}</span>
            </div>
            @if($orden->tecnico)
            <div class="info-row">
                <span class="info-label">Técnico Asignado:</span>
                <span class="info-value">{{ $orden->tecnico->nombre_completo }}</span>
            </div>
            @endif
        </div>

        @php
            $estadosInfo = [
                'recibida' => 'Su equipo ha sido recibido y está en proceso de registro en nuestro sistema.',
                'asignada' => 'Su equipo ha sido asignado a un técnico especializado.',
                'en_proceso' => 'Nuestro técnico está trabajando activamente en la reparación de su equipo.',
                'diagnosticada' => 'Se ha completado el diagnóstico de su equipo. Pronto recibirá más información.',
                'en_espera_repuestos' => 'Estamos esperando la llegada de repuestos necesarios para completar la reparación.',
                'reparada' => '¡Buenas noticias! Su equipo ha sido reparado exitosamente.',
                'lista_entrega' => 'Su equipo está listo para ser entregado. Puede pasar a recogerlo en nuestras oficinas.',
                'entregada' => 'Su equipo ha sido entregado. Gracias por confiar en nuestro servicio técnico.',
                'cancelada' => 'Esta orden de servicio ha sido cancelada.'
            ];
        @endphp

        @if(isset($estadosInfo[$estadoNuevo]))
        <div class="alert-info">
            <p><strong>¿Qué significa esto?</strong></p>
            <p>{{ $estadosInfo[$estadoNuevo] }}</p>
            @if($estadoNuevo === 'lista_entrega')
                <p style="margin-top: 10px;"><strong>Por favor traiga esta orden al momento de recoger su equipo.</strong></p>
            @endif
        </div>
        @endif

        <div class="footer">
            <p><strong>Servicio Técnico</strong></p>
            <p>Si tiene alguna pregunta, no dude en contactarnos.</p>
            <p>Este es un correo automático, por favor no responder.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                Generado el {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>
