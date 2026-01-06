<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flores y Algo Mas</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .navbar-brand img {
            height: 40px;
        }

        .hero-section {
            background-color: #f8f9fa;
            padding: 60px 0;
        }

        .hero-image {
            max-width: 100%;
            height: auto;
        }

        .category-btn {
            margin: 5px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .btn-add-cart {
            background-color: #8B4513;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
        }

        .btn-add-cart:hover {
            background-color: #6d3410;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Flores y Algo Mas">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#flores">Flores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ocasiones">Ocasiones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nosotros">Sobre Nosotros</a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register.cliente') }}">Registrarse</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4">Flores frescas entregadas hoy</h1>
                    <p class="lead">Encuentra el arreglo perfecto para cada ocasión</p>
                    <a href="{{ route('tienda.categorias') }}" class="btn btn-primary btn-lg">Comprar ahora</a>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('images/image.png') }}" alt="Arreglo de flores" class="hero-image">
                </div>
            </div>
        </div>
    </section>

    <!-- Categorías -->
    <section class="py-5">
        <div class="container text-center">
            @foreach($categoriasMenu ?? [] as $categoria)
                <a href="{{ route('tienda.categorias', ['categoria' => $categoria->id]) }}" class="btn btn-outline-secondary category-btn">
                    {{ $categoria->nombre }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Productos Destacados -->
    <section class="py-5" id="productos">
        <div class="container">
            <h2 class="text-center mb-4">Nuestros favoritos</h2>
            <div class="row">
                @foreach($productos as $producto)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="product-card">
                            @php
                                $imagen = $producto->imagenes->first();
                                $precio = $producto->precios->first();
                            @endphp
                            <img src="{{ asset($imagen ? $imagen->ruta_imagen : 'images/placeholder.jpg') }}"
                                 alt="{{ $producto->nombre }}"
                                 style="width: 100%; height: 250px; object-fit: cover;">
                            <h5 class="mt-3">{{ $producto->nombre }}</h5>
                            @if($precio)
                                <p class="text-muted">${{ number_format($precio->precio, 0, ',', '.') }}</p>
                            @else
                                <p class="text-muted">Precio variable</p>
                            @endif
                            <a href="{{ route('tienda.producto', $producto->id) }}" class="btn btn-add-cart">Ver Detalles</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('tienda.categorias') }}" class="btn btn-primary btn-lg">Ver Todo el Cat\u00e1logo</a>
            </div>
        </div>
    </section>

    <!-- Sección Especial -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Envía alegría a alguien especial</h2>
                    <p>Selecciona entre una variedad de flores para toda ocasión</p>
                    <button class="btn btn-primary">Explorar ramos</button>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4>Selecciona from una variada de toda ocasione</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 Flores y Algo Mas. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
