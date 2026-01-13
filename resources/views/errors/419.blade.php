<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesion Expirada - Flores y Algo Mas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 90%;
        }
        .error-icon {
            font-size: 5rem;
            color: #f59e0b;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 4rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.5rem;
            color: #374151;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1rem;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-outline-secondary {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="bi bi-clock-history error-icon"></i>
        <div class="error-code">419</div>
        <h1 class="error-title">Sesion Expirada</h1>
        <p class="error-message">
            Tu sesion ha expirado por inactividad o el formulario ya no es valido.
            Esto es una medida de seguridad para proteger tu informacion.
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="bi bi-house me-2"></i>Ir al Inicio
            </a>
        </div>
        <p class="text-muted mt-4 mb-0 small">
            <i class="bi bi-lightbulb me-1"></i>
            Consejo: Evita dejar formularios abiertos por mucho tiempo
        </p>
    </div>
</body>
</html>
