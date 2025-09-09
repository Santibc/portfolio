<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nuevo mensaje de contacto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
        }
        .content {
            padding: 20px 0;
        }
        .info-row {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .label {
            font-weight: bold;
            color: #495057;
        }
        .message-content {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Nuevo mensaje desde el formulario de contacto</h2>
    </div>
    
    <div class="content">
        <div class="info-row">
            <span class="label">Nombre:</span> {{ $name }}
        </div>
        
        <div class="info-row">
            <span class="label">Email:</span> {{ $email }}
        </div>
        
        <div class="info-row">
            <span class="label">Asunto:</span> {{ $subject }}
        </div>
        
        <div class="info-row">
            <span class="label">Mensaje:</span>
            <div class="message-content">
                {!! nl2br(e($messageContent)) !!}
            </div>
        </div>
        
        <hr style="margin: 20px 0;">
        
        <p style="font-size: 12px; color: #6c757d;">
            Este mensaje fue enviado desde el formulario de contacto del sitio web el {{ now()->format('d/m/Y H:i:s') }}.
        </p>
    </div>
</body>
</html>