<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirigiendo a WebPay...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
        }
        .loading-container {
            text-align: center;
        }
        .spinner {
            width: 50px;
            height: 50px;
            margin: 0 auto 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #00457C;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #333;
            font-weight: normal;
        }
        p {
            color: #666;
        }
        .webpay-logo {
            max-width: 180px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <img src="https://www.transbank.cl/web/assets/logo-webpay-plus.svg" alt="WebPay Plus" class="webpay-logo" onerror="this.style.display='none'">
        <div class="spinner"></div>
        <h2>Redirigiendo a WebPay...</h2>
        <p>Por favor espere un momento, será redirigido a la plataforma de pago seguro</p>
    </div>

    <form id="webpay-form" action="{{ $url }}" method="POST" style="display: none;">
        <input type="hidden" name="token_ws" value="{{ $token }}">
    </form>

    <script>
        // Enviar formulario automáticamente después de cargar la página
        window.onload = function() {
            document.getElementById('webpay-form').submit();
        };
    </script>
</body>
</html>
