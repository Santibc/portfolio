<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Flores y Algo Mas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --flores-primary: #6B7456;
            --flores-primary-hover: #5a6248;
            --flores-beige: #F5EDD8;
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
            padding: 60px 24px;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 48px 40px;
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
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--flores-text);
            margin-bottom: 8px;
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
            box-shadow: 0 0 0 3px rgba(107, 116, 86, 0.1);
        }

        .form-input::placeholder {
            color: #999;
        }

        .form-error {
            margin-top: 6px;
            font-size: 13px;
            color: #dc3545;
        }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--flores-primary);
            cursor: pointer;
        }

        .form-check label {
            font-size: 14px;
            color: var(--flores-text-light);
            cursor: pointer;
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
        }

        .btn-primary:hover {
            background: var(--flores-primary-hover);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Links */
        .auth-links {
            margin-top: 24px;
            text-align: center;
        }

        .auth-link {
            font-size: 14px;
            color: var(--flores-text-light);
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-link:hover {
            color: var(--flores-primary);
        }

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

        .register-link {
            display: block;
            text-align: center;
            font-size: 14px;
            color: var(--flores-text-light);
        }

        .register-link a {
            color: var(--flores-primary);
            font-weight: 500;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
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
                <h1 class="auth-title">Bienvenido de vuelta</h1>
                <p class="auth-subtitle">Ingresa tus credenciales para continuar</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

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
                            autofocus
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

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        @if ($errors->has('password'))
                            <div class="form-error">
                                @foreach ($errors->get('password') as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Recordarme</label>
                    </div>

                    <button type="submit" class="btn-primary">
                        Iniciar sesión
                    </button>

                    <div class="auth-links">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="auth-link">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                </form>

                <div class="auth-divider">
                    <span>o</span>
                </div>

                <p class="register-link">
                    ¿No tienes cuenta? <a href="{{ route('register.cliente') }}">Regístrate aquí</a>
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="auth-footer">
        <p>&copy; {{ date('Y') }} Flores y Algo Mas. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
