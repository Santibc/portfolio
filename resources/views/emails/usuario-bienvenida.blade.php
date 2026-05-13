<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
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
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .credentials {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido/a!</h1>
        </div>

        <p>Hola <strong>{{ $user->name }}</strong>,</p>

        <p>Se ha creado una cuenta para ti en nuestra plataforma. A continuación encontrarás tus credenciales de acceso:</p>

        <div class="credentials">
            <p style="margin: 0;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 8px 0 0;"><strong>Contraseña:</strong> {{ $plainPassword }}</p>
        </div>

        <div class="info-box">
            <p style="margin: 0;">Por seguridad, te recomendamos cambiar tu contraseña la primera vez que ingreses.</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="btn">Iniciar sesión</a>
        </div>

        <div class="footer">
            <p>Si no esperabas este correo, por favor ignóralo.</p>
            <p>&copy; {{ date('Y') }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
