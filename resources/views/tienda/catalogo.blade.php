<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat\u00e1logo - {{ $empresa->nombre }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-img {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .category-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .price-tag {
            font-size: 1.5rem;
            font-weight: bold;
            color: #FF00C1;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .category-btn {
            margin: 5px;
        }
        .hero-catalogo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="{{ $empresa->nombre }}" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('tienda.catalogo') }}">Cat\u00e1logo</a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar sesi\u00f3n</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar sesi\u00f3n</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="hero-catalogo">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Nuestro Cat\u00e1logo</h1>
            <p class="lead">Encuentra las flores perfectas para cada ocasi\u00f3n</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Filtros -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <form method="GET" action="{{ route('tienda.catalogo') }}" class="d-flex">
                        <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar productos..." value="{{ request('busqueda') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="text-md-end">
                        <span class="me-2">Filtrar por:</span>
                        <a href="{{ route('tienda.catalogo') }}" class="btn btn-sm btn-outline-secondary category-btn {{ !request('categoria') ? 'active' : '' }}">
                            Todos
                        </a>
                        @foreach($categorias as $categoria)
                            <a href="{{ route('tienda.catalogo', ['categoria' => $categoria->id]) }}"
                               class="btn btn-sm btn-outline-primary category-btn {{ request('categoria') == $categoria->id ? 'active' : '' }}">
                                {{ $categoria->nombre }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Productos -->
        @if($productos->count() > 0)
            <div class="row g-4">
                @foreach($productos as $producto)
                    <div class="col-md-4 col-lg-3">
                        <div class="card product-card">
                            <img src="{{ asset($producto->imagenPrincipal ? $producto->imagenPrincipal->ruta_imagen : 'images/placeholder.jpg') }}"
                                 class="card-img-top product-img"
                                 alt="{{ $producto->nombre }}">
                            <div class="card-body">
                                <span class="badge bg-secondary category-badge mb-2">{{ $producto->categoria->nombre }}</span>
                                <h5 class="card-title">{{ $producto->nombre }}</h5>
                                <p class="card-text text-muted small">{{ Str::limit($producto->descripcion, 80) }}</p>

                                @if($producto->preciosProducto->isNotEmpty())
                                    <p class="price-tag mb-3">${{ number_format($producto->preciosProducto->first()->precio, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-muted mb-3">Precio variable</p>
                                @endif

                                <a href="{{ route('tienda.producto', $producto->id) }}" class="btn btn-primary w-100">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginaci\u00f3n -->
            <div class="mt-4">
                {{ $productos->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                No se encontraron productos con los filtros seleccionados.
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} {{ $empresa->nombre }}. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
