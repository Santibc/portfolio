<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->nombre }} - {{ $empresa->nombre }}</title>
    <meta name="description" content="{{ $producto->descripcion }}">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3730a3;
            --secondary-color: #6366f1;
            --accent-color: #fbbf24;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --bg-light: #f9fafb;
            --border-color: #e5e7eb;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-primary);
        }

        /* Header (reuso del index) */
        .store-header {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo-container {
            max-width: 150px;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent-color);
            color: var(--text-primary);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: var(--text-secondary);
        }

        /* Product Gallery */
        .product-gallery {
            position: sticky;
            top: 100px;
        }

        .main-image-container {
            position: relative;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: contain;
            background: var(--bg-light);
        }

        .thumbnails-container {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .thumbnail {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border-color: var(--primary-color);
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Product Info */
        .product-details {
            padding: 2rem;
        }

        .product-category {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .product-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .product-reference {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        /* Variants */
        .variant-section {
            margin-bottom: 2rem;
        }

        .variant-label {
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .variant-options {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .variant-option {
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .variant-option:hover {
            border-color: var(--primary-color);
        }

        .variant-option.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .variant-option.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        /* Quantity Selector */
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quantity-input {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .quantity-btn {
            background: none;
            border: none;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .quantity-btn:hover {
            background: var(--bg-light);
        }

        .quantity-value {
            padding: 0.5rem 1rem;
            min-width: 60px;
            text-align: center;
            border-left: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
        }

        /* Stock Info */
        .stock-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .stock-available {
            color: #10b981;
        }

        .stock-low {
            color: #f59e0b;
        }

        .stock-out {
            color: #ef4444;
        }

        /* Add to Cart Button */
        .add-to-cart-section {
            margin-bottom: 2rem;
        }

        .btn-add-to-cart {
            width: 100%;
            padding: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .btn-add-to-cart:hover:not(:disabled) {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-add-to-cart:disabled {
            background: var(--text-secondary);
            cursor: not-allowed;
        }

        /* Product Info Tabs */
        .product-tabs {
            border-top: 1px solid var(--border-color);
            margin-top: 3rem;
            padding-top: 2rem;
        }

        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--text-secondary);
            padding: 0.75rem 1.5rem;
            margin-bottom: -2px;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            color: var(--text-primary);
            border-bottom: 2px solid var(--border-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            font-weight: 600;
        }

        .tab-content {
            padding: 2rem 0;
        }

        /* Related Products */
        .related-products {
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
        }

        .product-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .product-card .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-card .product-info {
            padding: 1rem;
        }

        .product-card .product-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .product-card .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Footer (reuso del index) */
        .store-footer {
            background: var(--text-primary);
            color: white;
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .product-gallery {
                position: static;
            }
            
            .main-image {
                height: 350px;
            }
            
            .product-details {
                padding: 1rem;
            }
            
            .product-title {
                font-size: 1.5rem;
            }
            
            .product-price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="store-header">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-6 col-md-3">
                    <a href="{{ route('tienda.empresa', $empresa->slug) }}" class="d-flex align-items-center text-decoration-none">
                        <div class="logo-container">
                            <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre }}" class="img-fluid">
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-9 text-end">
                    <a href="{{ route('tienda.carrito', $empresa->slug) }}" class="btn btn-outline-primary position-relative">
                        <i class="bi bi-cart3"></i> <span class="d-none d-md-inline">Carrito</span>
                        @if($carrito->total_items > 0)
                            <span class="cart-badge">{{ $carrito->total_items }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('tienda.empresa', $empresa->slug) }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tienda.empresa', [$empresa->slug, 'categoria' => $producto->categoria_id]) }}">{{ $producto->categoria->nombre }}</a></li>
                    <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
                </ol>
            </nav>

            <div class="row">
                <!-- Product Gallery -->
                <div class="col-lg-6">
                    <div class="product-gallery">
                        <div class="main-image-container">
                            <img src="{{ $producto->url_imagen_principal }}" alt="{{ $producto->nombre }}" class="main-image" id="mainImage">
                        </div>
                        
                        @if($producto->imagenes->count() > 1)
                        <div class="thumbnails-container">
                            @foreach($producto->imagenes as $imagen)
                            <div class="thumbnail {{ $loop->first ? 'active' : '' }}" onclick="changeImage('{{ $imagen->url }}', this)">
                                <img src="{{ $imagen->url }}" alt="{{ $producto->nombre }} - Imagen {{ $loop->iteration }}">
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-details">
                        <div class="product-category">{{ $producto->categoria->nombre }}</div>
                        <h1 class="product-title">{{ $producto->nombre }}</h1>
                        
                        @if($producto->precio_actual)
                            <div class="product-price">${{ number_format($producto->precio_actual, 0, ',', '.') }}</div>
                        @else
                            <div class="product-price text-muted">Precio no disponible</div>
                        @endif

                        <div class="product-reference">
                            <strong>Referencia:</strong> {{ $producto->referencia }}
                        </div>

                        <!-- Stock Info -->
                        @if($producto->controlar_stock)
                            @if($producto->tiene_variantes)
                                <div class="stock-info stock-available" id="stockInfo">
                                    <i class="bi bi-info-circle"></i>
                                    <span>Selecciona una opción para ver disponibilidad</span>
                                </div>
                            @else
                                @php
                                    $stockDisponible = $producto->stock_disponible;
                                @endphp
                                <div class="stock-info {{ $stockDisponible > 10 ? 'stock-available' : ($stockDisponible > 0 ? 'stock-low' : 'stock-out') }}">
                                    <i class="bi bi-{{ $stockDisponible > 0 ? 'check-circle' : 'x-circle' }}"></i>
                                    <span>
                                        @if($stockDisponible > 10)
                                            Disponible
                                        @elseif($stockDisponible > 0)
                                            ¡Últimas {{ $stockDisponible }} unidades!
                                        @else
                                            Sin stock
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @endif

                        <!-- Variants -->
                        @if($producto->tiene_variantes && $producto->variantes->count() > 0)
                            @php
                                $tallas = $producto->variantes->pluck('talla')->unique()->filter()->sort();
                                $colores = $producto->variantes->pluck('color')->unique()->filter()->sort();
                            @endphp

                            @if($tallas->count() > 0)
                            <div class="variant-section">
                                <div class="variant-label">Talla:</div>
                                <div class="variant-options">
                                    @foreach($tallas as $talla)
                                        @php
                                            $variantesConTalla = $producto->variantes->where('talla', $talla);
                                            $hayStock = $variantesConTalla->filter(function($v) {
                                                return $v->stock && $v->stock->stock_real > 0;
                                            })->count() > 0;
                                        @endphp
                                        <button class="variant-option {{ !$hayStock ? 'disabled' : '' }}" 
                                                data-type="talla" 
                                                data-value="{{ $talla }}"
                                                {{ !$hayStock ? 'disabled' : '' }}>
                                            {{ $talla }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($colores->count() > 0)
                            <div class="variant-section">
                                <div class="variant-label">Color:</div>
                                <div class="variant-options">
                                    @foreach($colores as $color)
                                        @php
                                            $variantesConColor = $producto->variantes->where('color', $color);
                                            $hayStock = $variantesConColor->filter(function($v) {
                                                return $v->stock && $v->stock->stock_real > 0;
                                            })->count() > 0;
                                        @endphp
                                        <button class="variant-option {{ !$hayStock ? 'disabled' : '' }}" 
                                                data-type="color" 
                                                data-value="{{ $color }}"
                                                {{ !$hayStock ? 'disabled' : '' }}>
                                            {{ $color }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endif

                        <!-- Quantity Selector -->
                        <div class="quantity-selector">
                            <label class="fw-semibold">Cantidad:</label>
                            <div class="quantity-input">
                                <button class="quantity-btn" onclick="updateQuantity(-1)">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" class="quantity-value" id="quantity" value="1" min="1" readonly>
                                <button class="quantity-btn" onclick="updateQuantity(1)">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart -->
                        <div class="add-to-cart-section">
                            <button class="btn-add-to-cart" id="addToCartBtn" 
                                    {{ (!$producto->precio_actual || ($producto->controlar_stock && $producto->stock_disponible <= 0 && !$producto->permitir_venta_sin_stock)) ? 'disabled' : '' }}>
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>
                        </div>

                        <!-- Product Tabs -->
                        <div class="product-tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#descripcion">Descripción</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#especificaciones">Especificaciones</a>
                                </li>
                            </ul>
                            
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="descripcion">
                                    <p>{{ $producto->descripcion ?: 'No hay descripción disponible.' }}</p>
                                </div>
                                <div class="tab-pane fade" id="especificaciones">
                                    <table class="table table-sm">
                                        <tr>
                                            <td class="fw-semibold">Referencia:</td>
                                            <td>{{ $producto->referencia }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Categoría:</td>
                                            <td>{{ $producto->categoria->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Unidad de Venta:</td>
                                            <td>{{ $producto->unidad_venta }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Unidad de Empaque:</td>
                                            <td>{{ $producto->unidad_empaque }}</td>
                                        </tr>
                                        @if($producto->extension)
                                        <tr>
                                            <td class="fw-semibold">Extensión:</td>
                                            <td>{{ $producto->extension }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relacionados->count() > 0)
            <div class="related-products">
                <h3 class="mb-4">Productos Relacionados</h3>
                <div class="row g-4">
                    @foreach($relacionados as $relacionado)
                    <div class="col-6 col-md-3">
                        <a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}" class="text-decoration-none text-dark">
                            <div class="product-card">
                                <img src="{{ $relacionado->url_imagen_principal }}" alt="{{ $relacionado->nombre }}" class="product-image">
                                <div class="product-info">
                                    <div class="product-name">{{ $relacionado->nombre }}</div>
                                    @if($relacionado->precio_actual)
                                        <div class="product-price">${{ number_format($relacionado->precio_actual, 0, ',', '.') }}</div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="store-footer">
        <div class="container">
            <div class="text-center">
                <h5>{{ $empresa->nombre }}</h5>
                <p class="text-white-50 mb-3">{{ $empresa->descripcion }}</p>
                <p class="mb-0">&copy; {{ date('Y') }} {{ $empresa->nombre }}. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert">
            <div class="toast-header">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong class="me-auto">Carrito</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Variantes disponibles del producto
        const variantes = @json($producto->variantes);
        const tieneVariantes = {{ $producto->tiene_variantes ? 'true' : 'false' }};
        let selectedVariant = null;
        let selectedTalla = null;
        let selectedColor = null;

        $(document).ready(function() {
            // CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // Selección de variantes
            $('.variant-option:not(:disabled)').on('click', function() {
                const type = $(this).data('type');
                const value = $(this).data('value');
                
                // Toggle selection
                $(`.variant-option[data-type="${type}"]`).removeClass('selected');
                $(this).addClass('selected');
                
                if (type === 'talla') {
                    selectedTalla = value;
                } else if (type === 'color') {
                    selectedColor = value;
                }
                
                // Actualizar disponibilidad de otras opciones
                updateVariantAvailability();
                
                // Buscar variante seleccionada
                if (tieneVariantes) {
                    findSelectedVariant();
                }
            });

            // Agregar al carrito
            $('#addToCartBtn').on('click', function() {
                const btn = $(this);
                const quantity = parseInt($('#quantity').val());
                
                if (tieneVariantes && !selectedVariant) {
                    showToast('error', 'Por favor selecciona todas las opciones del producto');
                    return;
                }
                
                btn.prop('disabled', true);
                btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Agregando...');
                
                const data = {
                    producto_id: {{ $producto->id }},
                    cantidad: quantity
                };
                
                if (selectedVariant) {
                    data.variante_id = selectedVariant.id;
                }
                
                $.ajax({
                    url: "{{ route('tienda.carrito.agregar', $empresa->slug) }}",
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        showToast('success', 'Producto agregado al carrito');
                        updateCartBadge(response.total_items);
                        btn.html('<i class="bi bi-check"></i> Agregado al Carrito');
                        
                        setTimeout(() => {
                            btn.prop('disabled', false);
                            btn.html('<i class="bi bi-cart-plus"></i> Agregar al Carrito');
                        }, 2000);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.error || 'Error al agregar al carrito';
                        showToast('error', error);
                        btn.prop('disabled', false);
                        btn.html('<i class="bi bi-cart-plus"></i> Agregar al Carrito');
                    }
                });
            });
        });

        // Cambiar imagen principal
        function changeImage(url, thumbnail) {
            document.getElementById('mainImage').src = url;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }

        // Actualizar cantidad
        function updateQuantity(change) {
            const input = document.getElementById('quantity');
            let value = parseInt(input.value) + change;
            
            if (value < 1) value = 1;
            
            // Verificar stock máximo
            if (selectedVariant && selectedVariant.stock) {
                const maxStock = selectedVariant.stock.stock_real || 0;
                if (value > maxStock) {
                    value = maxStock;
                    showToast('error', `Solo hay ${maxStock} unidades disponibles`);
                }
            } else if (!tieneVariantes) {
                const maxStock = {{ $producto->stock_disponible ?? 999 }};
                if (value > maxStock && {{ $producto->controlar_stock ? 'true' : 'false' }}) {
                    value = maxStock;
                    showToast('error', `Solo hay ${maxStock} unidades disponibles`);
                }
            }
            
            input.value = value;
        }

        // Actualizar disponibilidad de variantes
        function updateVariantAvailability() {
            if (!tieneVariantes) return;
            
            // Actualizar colores disponibles según talla seleccionada
            if (selectedTalla) {
                $('.variant-option[data-type="color"]').each(function() {
                    const color = $(this).data('value');
                    const hayStock = variantes.some(v => 
                        v.talla === selectedTalla && 
                        v.color === color && 
                        v.stock && 
                        v.stock.stock_real > 0
                    );
                    
                    $(this).prop('disabled', !hayStock);
                    $(this).toggleClass('disabled', !hayStock);
                });
            }
            
            // Actualizar tallas disponibles según color seleccionado
            if (selectedColor) {
                $('.variant-option[data-type="talla"]').each(function() {
                    const talla = $(this).data('value');
                    const hayStock = variantes.some(v => 
                        v.talla === talla && 
                        v.color === selectedColor && 
                        v.stock && 
                        v.stock.stock_real > 0
                    );
                    
                    $(this).prop('disabled', !hayStock);
                    $(this).toggleClass('disabled', !hayStock);
                });
            }
        }

        // Encontrar variante seleccionada
        function findSelectedVariant() {
            if (!selectedTalla && !selectedColor) {
                selectedVariant = null;
                return;
            }
            
            selectedVariant = variantes.find(v => {
                const tallaMatch = !selectedTalla || v.talla === selectedTalla;
                const colorMatch = !selectedColor || v.color === selectedColor;
                return tallaMatch && colorMatch;
            });
            
            // Actualizar información de stock
            if (selectedVariant) {
                updateStockInfo(selectedVariant);
                $('#addToCartBtn').prop('disabled', false);
            } else {
                $('#stockInfo').html('<i class="bi bi-info-circle"></i> <span>Selecciona todas las opciones</span>');
                $('#addToCartBtn').prop('disabled', true);
            }
        }

        // Actualizar información de stock
        function updateStockInfo(variant) {
            if (!variant.stock) return;
            
            const stock = variant.stock.stock_real || 0;
            let stockClass, stockText, stockIcon;
            
            if (stock > 10) {
                stockClass = 'stock-available';
                stockText = 'Disponible';
                stockIcon = 'check-circle';
            } else if (stock > 0) {
                stockClass = 'stock-low';
                stockText = `¡Últimas ${stock} unidades!`;
                stockIcon = 'exclamation-circle';
            } else {
                stockClass = 'stock-out';
                stockText = 'Sin stock';
                stockIcon = 'x-circle';
                $('#addToCartBtn').prop('disabled', true);
            }
            
            $('#stockInfo').removeClass('stock-available stock-low stock-out')
                          .addClass(stockClass)
                          .html(`<i class="bi bi-${stockIcon}"></i> <span>${stockText}</span>`);
        }

        // Toast notifications
        function showToast(type, message) {
            const toastEl = document.getElementById('cartToast');
            const toast = new bootstrap.Toast(toastEl);
            
            $('.toast-body').text(message);
            if (type === 'error') {
                $('.toast-header i').removeClass('text-success').addClass('text-danger');
                $('.toast-header i').removeClass('bi-check-circle-fill').addClass('bi-exclamation-circle-fill');
            } else {
                $('.toast-header i').removeClass('text-danger').addClass('text-success');
                $('.toast-header i').removeClass('bi-exclamation-circle-fill').addClass('bi-check-circle-fill');
            }
            
            toast.show();
        }

        // Update cart badge
        function updateCartBadge(count) {
            if (count > 0) {
                if ($('.cart-badge').length) {
                    $('.cart-badge').text(count);
                } else {
                    $('.btn-outline-primary').append('<span class="cart-badge">' + count + '</span>');
                }
            } else {
                $('.cart-badge').remove();
            }
        }
    </script>
</body>
</html>