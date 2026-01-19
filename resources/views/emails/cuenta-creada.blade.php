<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Miracle</title>
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
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .btn {
            display: inline-block;
            padding: 14px 35px;
            background-color: #FF84D5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #BCA9F5;
        }
        .credentials-box {
            background-color: #FFF1DD;
            border: 2px solid #FF84D5;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .credentials-box h3 {
            margin-top: 0;
            color: #382E65;
        }
        .credential-item {
            background-color: #fff;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #BCA9F5;
        }
        .credential-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .credential-value {
            font-size: 16px;
            font-weight: bold;
            color: #382E65;
            font-family: monospace;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #BCA9F5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenido a Miracle Beauty Experts</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Tu cuenta ha sido creada exitosamente</p>
        </div>

        <p>Estimado/a <strong>{{ $cliente->nombre_contacto }}</strong>,</p>

        <p>Nos complace informarte que se ha creado una cuenta en nuestro portal para que puedas hacer seguimiento de tus cotizaciones y pedidos.</p>

        <div class="credentials-box">
            <h3>Tus Credenciales de Acceso</h3>
            <div class="credential-item">
                <div class="credential-label">Correo electrónico</div>
                <div class="credential-value">{{ $usuario->email }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Contraseña temporal</div>
                <div class="credential-value">{{ $passwordTemporal }}</div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $urlLogin }}" class="btn">Iniciar Sesión</a>
        </div>

        <div class="warning-box">
            <strong>Importante:</strong> Te recomendamos cambiar tu contraseña temporal después de iniciar sesión por primera vez para mayor seguridad.
        </div>

        <div class="info-box">
            <h4 style="margin-top: 0;">Con tu cuenta podrás:</h4>
            <ul style="margin-bottom: 0;">
                <li>Ver el estado de tus cotizaciones</li>
                <li>Hacer seguimiento de tus pedidos</li>
                <li>Descargar facturas y documentos</li>
                <li>Consultar tu historial de compras</li>
            </ul>
        </div>

        @if($cliente->vendedor)
        <div class="info-box" style="border-left-color: #FF84D5;">
            <h4 style="margin-top: 0;">Tu asesor comercial:</h4>
            <p style="margin-bottom: 0;">
                <strong>{{ $cliente->vendedor->name }}</strong><br>
                @if($cliente->vendedor->email)
                    Email: {{ $cliente->vendedor->email }}<br>
                @endif
                @if($cliente->vendedor->telefono)
                    Tel: {{ $cliente->vendedor->telefono }}
                @endif
            </p>
        </div>
        @endif

        <div class="footer">
            <p><strong>Miracle Beauty Experts</strong></p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p style="font-size: 12px; color: #999;">
                Si no solicitaste esta cuenta o crees que recibiste este correo por error,
                por favor contacta a tu asesor comercial.
            </p>
        </div>
    </div>
</body>
</html>
