<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - {{ $empresa->nombre }}</title>
    
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
            --success-color: #10b981;
            --error-color: #ef4444;
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

        /* Checkout Steps */
        .checkout-steps {
            background: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .step {
            display: flex;
            align-items: center;
            position: relative;
            padding: 0 1rem;
        }

        .step-number {
            width: 36px;
            height: 36px;
            background: var(--border-color);
            color: var(--text-secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 0.75rem;
            transition: all 0.3s;
        }

        .step.active .step-number {
            background: var(--primary-color);
            color: white;
        }

        .step.completed .step-number {
            background: var(--success-color);
            color: white;
        }

        .step-title {
            font-size: 0.875rem;
            color: var(--text-secondary);
            transition: color 0.3s;
        }

        .step.active .step-title {
            color: var(--text-primary);
            font-weight: 600;
        }

        .step-line {
            position: absolute;
            top: 18px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--border-color);
            z-index: -1;
        }

        /* Checkout Form */
        .checkout-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(55, 48, 163, 0.1);
        }

        .invalid-feedback {
            font-size: 0.875rem;
        }

        /* Order Summary */
        .order-summary {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
            position: sticky;
            top: 100px;
        }

        .summary-item {
            display: flex;
            align-items: start;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-item:last-of-type {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-right: 1rem;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .item-variant {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .item-quantity {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .item-price {
            font-weight: 600;
            text-align: right;
        }

        .summary-totals {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.25rem;
            font-weight: 700;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        /* Payment Button */
        .btn-payment {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.2s;
            margin-top: 2rem;
        }

        .btn-payment:hover:not(:disabled) {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-payment:disabled {
            background: var(--text-secondary);
            cursor: not-allowed;
        }

        /* Secure Payment Info */
        .secure-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .secure-info i {
            color: var(--success-color);
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            flex-direction: column;
        }

        .loading-overlay.show {
            display: flex;
        }

        .loading-text {
            margin-top: 1rem;
            font-size: 1.125rem;
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .checkout-steps {
                overflow-x: auto;
            }
            
            .step-title {
                display: none;
            }
            
            .order-summary {
                position: static;
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Procesando...</span>
        </div>
        <p class="loading-text">Procesando tu pago...</p>
    </div>

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
                    <a href="{{ route('tienda.carrito', $empresa->slug) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Carrito
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Checkout Steps -->
    <div class="container mt-4">
        <div class="checkout-steps">
            <div class="row">
                <div class="col-4">
                    <div class="step completed">
                        <div class="step-number">
                            <i class="bi bi-check"></i>
                        </div>
                        <span class="step-title">Carrito</span>
                        <div class="step-line"></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="step active">
                        <div class="step-number">2</div>
                        <span class="step-title">Información</span>
                        <div class="step-line"></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="step">
                        <div class="step-number">3</div>
                        <span class="step-title">Confirmación</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <form action="{{ route('tienda.procesar-compra', $empresa->slug) }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row">
                    <!-- Checkout Form -->
                    <div class="col-lg-7">
                        <!-- Customer Information -->
                        <div class="checkout-container">
                            <h2 class="section-title">
                                <i class="bi bi-person"></i> Información del Cliente
                            </h2>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="nombre" class="form-label">Nombre completo *</label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre') }}" 
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Correo electrónico *</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono *</label>
                                    <input type="tel" 
                                           class="form-control @error('telefono') is-invalid @enderror" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="{{ old('telefono') }}" 
                                           required>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="checkout-container">
                            <h2 class="section-title">
                                <i class="bi bi-geo-alt"></i> Información de Envío
                            </h2>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="direccion" class="form-label">Dirección de envío *</label>
                                    <input type="text" 
                                           class="form-control @error('direccion') is-invalid @enderror" 
                                           id="direccion" 
                                           name="direccion" 
                                           value="{{ old('direccion') }}" 
                                           placeholder="Calle, número, apartamento, etc."
                                           required>
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="departamento" class="form-label">Departamento *</label>
                                    <select class="form-select" id="departamento" required>
                                        <option value="">Seleccione departamento</option>
                                        @foreach($departamentos as $departamento)
                                            <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="ciudad_id" class="form-label">Ciudad *</label>
                                    <select class="form-select @error('ciudad_id') is-invalid @enderror" 
                                            id="ciudad_id" 
                                            name="ciudad_id" 
                                            required>
                                        <option value="">Primero seleccione departamento</option>
                                    </select>
                                    @error('ciudad_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="notas" class="form-label">Notas adicionales (opcional)</label>
                                    <textarea class="form-control" 
                                              id="notas" 
                                              name="notas" 
                                              rows="3" 
                                              placeholder="Instrucciones especiales para la entrega...">{{ old('notas') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-5">
                        <div class="order-summary">
                            <h2 class="section-title">
                                <i class="bi bi-receipt"></i> Resumen del Pedido
                            </h2>
                            
                            <!-- Items -->
                            @foreach($carrito->items as $item)
                                @php
                                    $producto = \App\Models\Producto::find($item['producto_id']);
                                @endphp
                                <div class="summary-item">
                                    <img src="{{ $producto->url_imagen_principal }}" 
                                         alt="{{ $item['nombre'] }}" 
                                         class="item-image">
                                    <div class="item-details">
                                        <div class="item-name">{{ $item['nombre'] }}</div>
                                        @if(isset($item['info_variante']))
                                            <div class="item-variant">
                                                {{ $item['info_variante']['talla'] ?? '' }}
                                                {{ $item['info_variante']['color'] ? '- ' . $item['info_variante']['color'] : '' }}
                                            </div>
                                        @endif
                                        <div class="item-quantity">Cantidad: {{ $item['cantidad'] }}</div>
                                    </div>
                                    <div class="item-price">
                                        ${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Totals -->
                            <div class="summary-totals">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($carrito->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="summary-row text-muted">
                                    <span>Envío</span>
                                    <span>Por calcular</span>
                                </div>
                                <div class="summary-row text-muted">
                                    <span>Impuestos</span>
                                    <span>Incluidos</span>
                                </div>
                                <div class="summary-total">
                                    <span>Total a pagar</span>
                                    <span>${{ number_format($carrito->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Payment Button -->
                            <button type="submit" class="btn-payment" id="paymentBtn">
                                <i class="bi bi-lock-fill"></i> Proceder al Pago
                            </button>
                            
                            <!-- Secure Info -->
                            <div class="secure-info">
                                <i class="bi bi-shield-lock-fill"></i>
                                <span>Pago seguro con Wompi</span>
                            </div>
                            
                            @if($configuracionPasarela && $configuracionPasarela->modo_prueba)
                                <div class="alert alert-warning mt-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <small>Modo de prueba activado</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Load cities when department changes
            $('#departamento').on('change', function() {
                const departamentoId = $(this).val();
                const ciudadSelect = $('#ciudad_id');
                
                ciudadSelect.html('<option value="">Cargando ciudades...</option>');
                
                if (departamentoId) {
                    $.ajax({
                        url: "{{ route('ajax.ciudades') }}",
                        data: { departamento_id: departamentoId },
                        success: function(ciudades) {
                            let options = '<option value="">Seleccione ciudad</option>';
                            ciudades.forEach(ciudad => {
                                options += `<option value="${ciudad.id}">${ciudad.nombre}</option>`;
                            });
                            ciudadSelect.html(options);
                        },
                        error: function() {
                            ciudadSelect.html('<option value="">Error al cargar ciudades</option>');
                        }
                    });
                } else {
                    ciudadSelect.html('<option value="">Primero seleccione departamento</option>');
                }
            });

            // Form validation
            $('#checkoutForm').on('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const requiredFields = ['nombre', 'email', 'telefono', 'direccion', 'ciudad_id'];
                let isValid = true;
                
                requiredFields.forEach(field => {
                    const input = $(`#${field}`);
                    if (!input.val()) {
                        input.addClass('is-invalid');
                        isValid = false;
                    } else {
                        input.removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    alert('Por favor complete todos los campos obligatorios');
                    return;
                }
                
                // Show loading
                $('#loadingOverlay').addClass('show');
                $('#paymentBtn').prop('disabled', true);
                
                // Submit form
                this.submit();
            });

            // Remove validation errors on input
            $('.form-control, .form-select').on('input change', function() {
                $(this).removeClass('is-invalid');
            });
        });
    </script>
</body>
</html>