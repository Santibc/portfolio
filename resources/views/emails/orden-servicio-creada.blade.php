<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Orden de Servicio</title>
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
            border-bottom: 3px solid #0d6efd;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 24px;
        }
        .orden-numero {
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin: 15px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h2 {
            color: #0d6efd;
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
        .estado {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .estado-recibida {
            background-color: #cfe2ff;
            color: #084298;
        }
        .estado-asignada {
            background-color: #fff3cd;
            color: #664d03;
        }
        .prioridad {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
        }
        .prioridad-alta {
            background-color: #f8d7da;
            color: #842029;
        }
        .prioridad-media {
            background-color: #fff3cd;
            color: #664d03;
        }
        .prioridad-baja {
            background-color: #d1e7dd;
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
        .alert-info {
            background-color: #cfe2ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-info p {
            margin: 0;
            color: #084298;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Nueva Orden de Servicio Técnico</h1>
            <div class="orden-numero">{{ $orden->numero_orden }}</div>
        </div>

        <div class="alert-info">
            <p><strong>Estimado/a {{ $orden->cliente->nombre }},</strong></p>
            <p>Se ha creado una nueva orden de servicio técnico para su equipo. A continuación encontrará los detalles:</p>
        </div>

        <div class="info-section">
            <h2>Información del Cliente</h2>
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span class="info-value">{{ $orden->cliente->nombre }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Teléfono:</span>
                <span class="info-value">{{ $orden->cliente->telefono }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $orden->cliente->email }}</span>
            </div>
            @if($orden->cliente->direccion)
            <div class="info-row">
                <span class="info-label">Dirección:</span>
                <span class="info-value">{{ $orden->cliente->direccion }}</span>
            </div>
            @endif
        </div>

        <div class="info-section">
            <h2>Información del Equipo</h2>
            <div class="info-row">
                <span class="info-label">Tipo de Equipo:</span>
                <span class="info-value">{{ $orden->equipo->tipo_equipo }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Marca:</span>
                <span class="info-value">{{ $orden->equipo->marca }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Modelo:</span>
                <span class="info-value">{{ $orden->equipo->modelo }}</span>
            </div>
            @if($orden->equipo->numero_serie)
            <div class="info-row">
                <span class="info-label">Número de Serie:</span>
                <span class="info-value">{{ $orden->equipo->numero_serie }}</span>
            </div>
            @endif
        </div>

        <div class="info-section">
            <h2>Detalles de la Orden</h2>
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
                <span class="info-label">Prioridad:</span>
                <span class="info-value">
                    @switch($orden->prioridad)
                        @case('alta')
                            <span class="prioridad prioridad-alta">ALTA</span>
                            @break
                        @case('media')
                            <span class="prioridad prioridad-media">MEDIA</span>
                            @break
                        @case('baja')
                            <span class="prioridad prioridad-baja">BAJA</span>
                            @break
                    @endswitch
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value">
                    @switch($orden->estado)
                        @case('recibida')
                            <span class="estado estado-recibida">RECIBIDA</span>
                            @break
                        @case('asignada')
                            <span class="estado estado-asignada">ASIGNADA</span>
                            @break
                        @default
                            <span class="estado">{{ strtoupper($orden->estado) }}</span>
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

        @if($orden->descripcion_problema)
        <div class="info-section">
            <h2>Descripción del Problema</h2>
            <div class="info-row">
                <p style="margin: 10px 0; color: #333;">{{ $orden->descripcion_problema }}</p>
            </div>
        </div>
        @endif

        @if($orden->observaciones_ingreso)
        <div class="info-section">
            <h2>Observaciones</h2>
            <div class="info-row">
                <p style="margin: 10px 0; color: #333;">{{ $orden->observaciones_ingreso }}</p>
            </div>
        </div>
        @endif

        <div class="alert-info" style="margin-top: 30px;">
            <p><strong>¿Qué sigue ahora?</strong></p>
            <p>Nuestro equipo técnico revisará su equipo y le mantendremos informado sobre el progreso de la reparación. Recibirá notificaciones por correo cada vez que cambie el estado de su orden.</p>
        </div>

        <div class="footer">
            <p><strong>Servicio Técnico</strong></p>
            <p>Este es un correo automático, por favor no responder.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                Generado el {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>
