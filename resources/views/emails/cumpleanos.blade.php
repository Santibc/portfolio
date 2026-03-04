<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Feliz Cumpleaños!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #2d5a27;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .header .emoji {
            font-size: 40px;
            display: block;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
        }
        .content p {
            margin: 0 0 15px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="emoji">🎂</span>
            <h1>MANZER AGROFORESTAL</h1>
            <p>¡Feliz Cumpleaños!</p>
        </div>

        <div class="content">
            {!! $cuerpoHtml !!}
        </div>

        <div class="footer">
            <p>
                <strong>MANZER AGROFORESTAL, S.R.L.U.</strong><br>
                Este es un mensaje automático.
            </p>
            <p style="margin-top: 10px;">
                &copy; {{ date('Y') }} Manzer Agroforestal. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
