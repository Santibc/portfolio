<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Flores y Algo Mas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --flores-primary: #1a1a1a;
            --flores-primary-hover: #333333;
            --flores-beige: #f5f5f5;
            --flores-text: #333333;
            --flores-text-light: #666666;
            --flores-bg: #FFFFFF;
            --flores-border: #E5E5E5;
            --font-serif: 'Playfair Display', Georgia, serif;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background-color: #fafafa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header minimalista */
        .auth-header {
            padding: 24px 0;
            background: white;
            border-bottom: 1px solid var(--flores-border);
        }

        .auth-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .auth-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--flores-text);
        }

        .auth-logo i {
            font-size: 20px;
            color: var(--flores-primary);
        }

        .auth-logo span {
            font-family: var(--font-serif);
            font-size: 18px;
            font-weight: 600;
        }

        .auth-header-link {
            font-size: 14px;
            color: var(--flores-text-light);
            text-decoration: none;
        }

        .auth-header-link:hover {
            color: var(--flores-primary);
        }

        /* Contenido principal */
        .auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .auth-title {
            font-family: var(--font-serif);
            font-size: 28px;
            font-weight: 500;
            color: var(--flores-text);
            text-align: center;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: var(--flores-text-light);
            text-align: center;
            margin-bottom: 32px;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--flores-text);
            margin-bottom: 8px;
        }

        .form-label .optional {
            font-weight: 400;
            color: var(--flores-text-light);
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            font-size: 15px;
            color: var(--flores-text);
            background: #fafafa;
            border: 1px solid var(--flores-border);
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            background: white;
            border-color: var(--flores-primary);
            box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
        }

        .form-input::placeholder {
            color: #999;
        }

        .form-error {
            margin-top: 6px;
            font-size: 13px;
            color: #dc3545;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Botón principal */
        .btn-primary {
            width: 100%;
            height: 50px;
            background: var(--flores-primary);
            color: white;
            font-size: 15px;
            font-weight: 500;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 8px;
        }

        .btn-primary:hover {
            background: var(--flores-primary-hover);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Divider y links */
        .auth-divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: var(--flores-text-light);
            font-size: 13px;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--flores-border);
        }

        .auth-divider span {
            padding: 0 16px;
        }

        .login-link {
            display: block;
            text-align: center;
            font-size: 14px;
            color: var(--flores-text-light);
        }

        .login-link a {
            color: var(--flores-primary);
            font-weight: 500;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Beneficios */
        .benefits {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--flores-border);
        }

        .benefits-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--flores-text);
            text-align: center;
            margin-bottom: 16px;
        }

        .benefits-list {
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--flores-text-light);
        }

        .benefit-item i {
            color: var(--flores-primary);
            font-size: 16px;
        }

        /* Alert de estado */
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Footer */
        .auth-footer {
            padding: 24px;
            text-align: center;
            color: var(--flores-text-light);
            font-size: 13px;
        }

        .auth-footer a {
            color: var(--flores-text-light);
            text-decoration: none;
        }

        .auth-footer a:hover {
            color: var(--flores-primary);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .auth-card {
                padding: 32px 24px;
            }

            .auth-title {
                font-size: 24px;
            }

            .benefits-list {
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="auth-header">
        <div class="container">
            <a href="{{ route('home') }}" class="auth-logo">
                <i class="bi bi-flower1"></i>
                <span>Flores y Algo Mas</span>
            </a>
            <a href="{{ route('home') }}" class="auth-header-link">
                <i class="bi bi-arrow-left me-1"></i> Volver a la tienda
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="auth-main">
        <div class="auth-container">
            <div class="auth-card">
                <h1 class="auth-title">Crea tu cuenta</h1>
                <p class="auth-subtitle">Únete y disfruta de beneficios exclusivos</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.cliente.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">Nombre completo</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-input"
                            placeholder="Tu nombre"
                            required
                            autofocus
                            autocomplete="name"
                        >
                        @if ($errors->has('name'))
                            <div class="form-error">
                                @foreach ($errors->get('name') as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-input"
                            placeholder="tu@correo.com"
                            required
                            autocomplete="username"
                        >
                        @if ($errors->has('email'))
                            <div class="form-error">
                                @foreach ($errors->get('email') as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="telefono" class="form-label">
                            Teléfono <span class="optional">(opcional)</span>
                        </label>
                        <input
                            id="telefono"
                            type="tel"
                            name="telefono"
                            value="{{ old('telefono') }}"
                            class="form-input"
                            placeholder="+56 9 1234 5678"
                            autocomplete="tel"
                        >
                        @if ($errors->has('telefono'))
                            <div class="form-error">
                                @foreach ($errors->get('telefono') as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Password Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="form-label">Contraseña</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="form-input"
                                placeholder="Mín. 8 caracteres"
                                required
                                autocomplete="new-password"
                            >
                            @if ($errors->has('password'))
                                <div class="form-error">
                                    @foreach ($errors->get('password') as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirmar</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                class="form-input"
                                placeholder="Repite contraseña"
                                required
                                autocomplete="new-password"
                            >
                            @if ($errors->has('password_confirmation'))
                                <div class="form-error">
                                    @foreach ($errors->get('password_confirmation') as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        Crear mi cuenta
                    </button>
                </form>

                <div class="auth-divider">
                    <span>o</span>
                </div>

                <p class="login-link">
                    ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
                </p>

                <!-- Benefits -->
                <div class="benefits">
                    <p class="benefits-title">Al registrarte obtienes:</p>
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Seguimiento de pedidos</span>
                        </div>
                        <div class="benefit-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Ofertas exclusivas</span>
                        </div>
                        <div class="benefit-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Historial de compras</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="auth-footer">
        <p>&copy; {{ date('Y') }} Flores y Algo Mas. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
