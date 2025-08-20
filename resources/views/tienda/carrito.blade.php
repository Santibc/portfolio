<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - {{ $empresa->nombre }}</title>
    
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
            background: var(--bg-light);
        }

        /* Header */
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

        /* Cart Container */
        .cart-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .cart-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        /* Cart Item */
        .cart-item {
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
        }

        .item-details h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .item-variant {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .item-reference {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .item-price {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Quantity Controls */
        .quantity-controls {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            overflow: hidden;
            width: fit-content;
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

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-value {
            padding: 0.5rem 1rem;
            min-width: 60px;
            text-align: center;
            border-left: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            background: white;
            border: none;
        }

        /* Remove Button */
        .btn-remove {
            color: #ef4444;
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-remove:hover {
            opacity: 0.7;
        }

        /* Cart Summary */
        .cart-summary {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border-color);
            font-size: 1.25rem;
            font-weight: 700;
        }

        .btn-checkout {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1.125rem;
            margin-top: 1.5rem;
            transition: all 0.2s;
        }

        .btn-checkout:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-continue-shopping {
            width: 100%;
            background: none;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            margin-top: 0.75rem;
            transition: all 0.2s;
        }

        .btn-continue-shopping:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-cart-icon {
            font-size: 4rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .empty-cart-text {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        /* Loading State */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 0.75rem;
        }

        @media (max-width: 768px) {
            .cart-item {
                padding: 1rem 0;
            }
            
            .item-image {
                width: 80px;
                height: 80px;
            }
            
            .cart-summary {
                position: static;
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="store-header">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <a href="{{ route('tienda.empresa', $empresa->slug) }}" class="d-flex align-items-center text-decoration-none">
                        <div class="logo-container">
                            <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre }}" class="img-fluid">
                        </div>
                    </a>
                </div>
                <div class="col-6 text-end">
                    <a href="{{ route('tienda.empresa', $empresa->slug) }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Seguir Comprando
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="cart-container position-relative">
                        <h1 class="cart-title">
                            <i class="bi bi-cart3"></i> Carrito de Compras
                            @if($carrito->total_items > 0)
                                <span class="text-muted fs-6">({{ $carrito->total_items }} {{ $carrito->total_items == 1 ? 'producto' : 'productos' }})</span>
                            @endif
                        </h1>

                        @if($carrito->items && count($carrito->items) > 0)
                            <div id="cartItems">
                                @foreach($carrito->items as $key => $item)
                                    @php
                                        $producto = \App\Models\Producto::find($item['producto_id']);
                                    @endphp
                                    <div class="cart-item" data-key="{{ $key }}">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img src="{{ $producto->url_imagen_principal }}" 
                                                     alt="{{ $item['nombre'] }}" 
                                                     class="item-image">
                                            </div>
                                            <div class="col">
                                                <div class="item-details">
                                                    <h5>{{ $item['nombre'] }}</h5>
                                                    @if(isset($item['info_variante']))
                                                        <div class="item-variant">
                                                            @if($item['info_variante']['talla'])
                                                                Talla: {{ $item['info_variante']['talla'] }}
                                                            @endif
                                                            @if($item['info_variante']['color'])
                                                                | Color: {{ $item['info_variante']['color'] }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                    <div class="item-reference">Ref: {{ $item['referencia'] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn" onclick="updateCartQuantity('{{ $key }}', -1)">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" class="quantity-value" 
                                                           id="qty-{{ $key }}" 
                                                           value="{{ $item['cantidad'] }}" 
                                                           min="1" 
                                                           data-key="{{ $key }}"
                                                           readonly>
                                                    <button class="quantity-btn" onclick="updateCartQuantity('{{ $key }}', 1)">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-auto text-end">
                                                <div class="item-price mb-2">
                                                    ${{ number_format($item['precio'], 0, ',', '.') }}
                                                </div>
                                                <div class="fw-bold">
                                                    ${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button class="btn-remove" onclick="removeFromCart('{{ $key }}')" title="Eliminar">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Loading overlay -->
                            <div class="loading-overlay" id="cartLoading" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Actualizando...</span>
                                </div>
                            </div>
                        @else
                            <div class="empty-cart">
                                <i class="bi bi-cart-x empty-cart-icon"></i>
                                <p class="empty-cart-text">Tu carrito está vacío</p>
                                <a href="{{ route('tienda.empresa', $empresa->slug) }}" class="btn btn-primary">
                                    <i class="bi bi-shop"></i> Ir a Comprar
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Cart Summary -->
                @if($carrito->items && count($carrito->items) > 0)
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h2 class="summary-title">Resumen del Pedido</h2>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="summarySubtotal">${{ number_format($carrito->subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="summary-row text-muted">
                            <span>Envío</span>
                            <span>Por calcular</span>
                        </div>
                        
                        <div class="summary-total">
                            <span>Total</span>
                            <span id="summaryTotal">${{ number_format($carrito->subtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        <a href="{{ route('tienda.checkout', $empresa->slug) }}" class="btn btn-checkout">
                            Proceder al Pago
                        </a>
                        
                        <a href="{{ route('tienda.empresa', $empresa->slug) }}" class="btn btn-continue-shopping">
                            Seguir Comprando
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert">
            <div class="toast-header">
                <i class="bi bi-info-circle-fill text-primary me-2"></i>
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
        $(document).ready(function() {
            // CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        });

        // Update cart quantity
        function updateCartQuantity(key, change) {
            const input = document.getElementById('qty-' + key);
            let newQuantity = parseInt(input.value) + change;
            
            if (newQuantity < 1) newQuantity = 1;
            
            // Show loading
            $('#cartLoading').show();
            
            $.ajax({
                url: "{{ route('tienda.carrito.actualizar', $empresa->slug) }}",
                method: 'POST',
                data: {
                    key: key,
                    cantidad: newQuantity
                },
                success: function(response) {
                    input.value = newQuantity;
                    updateSummary(response);
                    showToast('success', 'Carrito actualizado');
                    $('#cartLoading').hide();
                },
                error: function(xhr) {
                    $('#cartLoading').hide();
                    const error = xhr.responseJSON?.error || 'Error al actualizar el carrito';
                    showToast('error', error);
                    
                    // Revert value on error
                    input.value = parseInt(input.value) - change;
                }
            });
        }

        // Remove item from cart
        function removeFromCart(key) {
            if (!confirm('¿Estás seguro de eliminar este producto del carrito?')) return;
            
            $('#cartLoading').show();
            
            $.ajax({
                url: "{{ route('tienda.carrito.quitar', $empresa->slug) }}",
                method: 'POST',
                data: {
                    key: key
                },
                success: function(response) {
                    // Remove item from DOM
                    $(`.cart-item[data-key="${key}"]`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if cart is empty
                        if (response.total_items === 0) {
                            location.reload();
                        }
                    });
                    
                    updateSummary(response);
                    showToast('success', 'Producto eliminado del carrito');
                    $('#cartLoading').hide();
                },
                error: function(xhr) {
                    $('#cartLoading').hide();
                    showToast('error', 'Error al eliminar el producto');
                }
            });
        }

        // Update summary totals
        function updateSummary(data) {
            const formattedSubtotal = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(data.subtotal);
            
            $('#summarySubtotal').text(formattedSubtotal);
            $('#summaryTotal').text(formattedSubtotal);
            
            // Update header cart count
            if (data.total_items > 0) {
                const itemText = data.total_items === 1 ? 'producto' : 'productos';
                $('.cart-title span').text(`(${data.total_items} ${itemText})`);
            }
        }

        // Show toast notification
        function showToast(type, message) {
            const toastEl = document.getElementById('cartToast');
            const toast = new bootstrap.Toast(toastEl);
            
            $('.toast-body').text(message);
            
            if (type === 'error') {
                $('.toast-header i').removeClass('text-primary').addClass('text-danger');
                $('.toast-header i').removeClass('bi-info-circle-fill').addClass('bi-exclamation-circle-fill');
            } else {
                $('.toast-header i').removeClass('text-danger').addClass('text-primary');
                $('.toast-header i').removeClass('bi-exclamation-circle-fill').addClass('bi-info-circle-fill');
            }
            
            toast.show();
        }
    </script>
</body>
</html>