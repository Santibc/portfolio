<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - {{ $empresa->nombre }}</title>

    {{-- Analytics Scripts (GA4, Facebook Pixel, GTM) --}}
    @include('components.analytics.head-scripts')

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
    {{-- GTM noscript fallback --}}
    @include('components.analytics.body-scripts')

    {{-- Tracking: begin_checkout --}}
    @php
        $checkoutItems = [];
        foreach($carrito->items ?? [] as $item) {
            $checkoutItems[] = [
                'referencia' => $item['referencia'] ?? '',
                'producto_id' => $item['producto_id'] ?? '',
                'nombre' => $item['nombre'] ?? '',
                'precio' => $item['precio'] ?? 0,
                'cantidad' => $item['cantidad'] ?? 1,
                'categoria' => 'Flores',
            ];
        }
    @endphp
    @include('components.analytics.ecommerce-events', [
        'event' => 'begin_checkout',
        'items' => $checkoutItems,
        'value' => $carrito->total,
        'coupon' => $carrito->codigo_descuento
    ])

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
                    <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
                        <div class="logo-container">
                            <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre }}" class="img-fluid">
                        </div>
                    </a>
                </div>
                <div class="col-6 text-end">
                    <a href="{{ route('tienda.carrito') }}" class="btn btn-outline-secondary">
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
            <form action="{{ route('tienda.procesar-compra') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row">
                    <!-- Checkout Form -->
                    <div class="col-lg-7">
                        <!-- Customer Information -->
                        <div class="checkout-container">
                            <h2 class="section-title">
                                <i class="bi bi-person"></i> Información del Cliente
                            </h2>

                            @guest
                            <div class="alert alert-info d-flex align-items-start mb-4" role="alert" style="background: linear-gradient(135deg, rgba(255,0,193,0.1), rgba(11,0,249,0.1)); border: 1px solid rgba(255,0,193,0.3); border-radius: 0.75rem;">
                                <i class="bi bi-gift-fill me-3" style="font-size: 1.5rem; color: #FF00C1;"></i>
                                <div>
                                    <strong style="color: #FF00C1;">¿Quieres ver tu historial de compras?</strong>
                                    <p class="mb-2 small text-secondary">
                                        Regístrate para acceder a tus compras anteriores y poder calificar los productos que compres.
                                    </p>
                                    <a href="{{ route('register.cliente') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #FF00C1, #0B00F9); color: white; border: none;">
                                        <i class="bi bi-person-plus me-1"></i> Crear Cuenta
                                    </a>
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Ya tengo cuenta
                                    </a>
                                </div>
                            </div>
                            @endguest

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
                                    <label for="departamento" class="form-label">Región *</label>
                                    <select class="form-select" id="departamento" required>
                                        <option value="">Seleccione región</option>
                                        @foreach($departamentos as $departamento)
                                            <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="ciudad_id" class="form-label">Comuna *</label>
                                    <select class="form-select @error('ciudad_id') is-invalid @enderror"
                                            id="ciudad_id"
                                            name="ciudad_id"
                                            required>
                                        <option value="">Primero seleccione región</option>
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

                        <!-- Información del Destinatario y Entrega (Floristería) -->
                        <div class="checkout-container">
                            <h2 class="section-title">
                                <i class="bi bi-flower1"></i> Detalles del Envío Floral
                            </h2>

                            <div class="alert alert-light border mb-4">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                <small>¿El arreglo es para ti o para alguien más? Completa los datos del destinatario.</small>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="mismo_destinatario" checked onchange="toggleDestinatario()">
                                        <label class="form-check-label" for="mismo_destinatario">
                                            El pedido es para mí (usar mis datos como destinatario)
                                        </label>
                                    </div>
                                </div>

                                <div id="destinatario_fields" style="display: none;">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nombre_destinatario" class="form-label">Nombre del destinatario</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="nombre_destinatario"
                                                   name="nombre_destinatario"
                                                   value="{{ old('nombre_destinatario') }}"
                                                   placeholder="Nombre de quien recibe las flores">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="telefono_destinatario" class="form-label">Teléfono del destinatario</label>
                                            <input type="tel"
                                                   class="form-control"
                                                   id="telefono_destinatario"
                                                   name="telefono_destinatario"
                                                   value="{{ old('telefono_destinatario') }}"
                                                   placeholder="Para coordinar la entrega">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="fecha_entrega_deseada" class="form-label">
                                        <i class="bi bi-calendar-event text-primary"></i> Fecha de entrega deseada
                                    </label>
                                    <input type="date"
                                           class="form-control"
                                           id="fecha_entrega_deseada"
                                           name="fecha_entrega_deseada"
                                           value="{{ old('fecha_entrega_deseada', date('Y-m-d', strtotime('+1 day'))) }}"
                                           min="{{ date('Y-m-d') }}">
                                    <small class="text-muted">Pedidos antes de las 2pm pueden entregarse el mismo día</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="horario_entrega" class="form-label">
                                        <i class="bi bi-clock text-primary"></i> Horario preferido
                                    </label>
                                    <select class="form-select" id="horario_entrega" name="horario_entrega">
                                        <option value="">Cualquier hora del día</option>
                                        <option value="manana" {{ old('horario_entrega') == 'manana' ? 'selected' : '' }}>Mañana (8am - 12pm)</option>
                                        <option value="tarde" {{ old('horario_entrega') == 'tarde' ? 'selected' : '' }}>Tarde (12pm - 6pm)</option>
                                        <option value="noche" {{ old('horario_entrega') == 'noche' ? 'selected' : '' }}>Noche (6pm - 9pm)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="es_sorpresa" name="es_sorpresa" value="1" {{ old('es_sorpresa') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="es_sorpresa">
                                            <i class="bi bi-gift text-danger"></i> Es una sorpresa (no llamar al destinatario antes de entregar)
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="mensaje_tarjeta" class="form-label">
                                        <i class="bi bi-envelope-heart text-danger"></i> Mensaje para la tarjeta (opcional)
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <textarea class="form-control"
                                                      id="mensaje_tarjeta"
                                                      name="mensaje_tarjeta"
                                                      rows="4"
                                                      maxlength="250"
                                                      placeholder="Escribe un mensaje especial que incluiremos en una tarjeta con tu arreglo...">{{ old('mensaje_tarjeta', $carrito->mensaje_tarjeta ?? '') }}</textarea>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted"><span id="mensaje_contador">0</span>/250 caracteres</small>
                                                <button type="button" class="btn btn-link btn-sm p-0" onclick="togglePreviewTarjeta()">
                                                    <i class="bi bi-eye"></i> Ver vista previa
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            {{-- Vista previa de la tarjeta --}}
                                            <div id="preview_tarjeta" class="card border-2" style="background: linear-gradient(135deg, #fff5f5 0%, #fff 100%); min-height: 150px; display: none;">
                                                <div class="card-body text-center p-3">
                                                    <div class="mb-2">
                                                        <i class="bi bi-flower1 text-danger" style="font-size: 1.5rem;"></i>
                                                    </div>
                                                    <p id="preview_mensaje" class="mb-2 fst-italic" style="font-family: 'Georgia', serif; font-size: 0.9rem; white-space: pre-wrap;">
                                                        Tu mensaje aparecerá aquí...
                                                    </p>
                                                    <hr style="border-color: rgba(255,0,100,0.2);">
                                                    <small class="text-muted" style="font-family: 'Georgia', serif;">
                                                        Con cariño, de parte de <span id="preview_remitente">tu nombre</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="instrucciones_entrega" class="form-label">Instrucciones especiales de entrega</label>
                                    <textarea class="form-control"
                                              id="instrucciones_entrega"
                                              name="instrucciones_entrega"
                                              rows="2"
                                              placeholder="Ej: Dejar en recepción, tocar el timbre 2 veces, preguntar por...">{{ old('instrucciones_entrega') }}</textarea>
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
                                    // Para ramos personalizados u items sin producto, usar imagen por defecto
                                    $imagenUrl = $producto ? $producto->url_imagen_principal : asset('images/flores/default.png');
                                @endphp
                                <div class="summary-item">
                                    <img src="{{ $imagenUrl }}"
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

                                        {{-- Descuentos aplicados al item --}}
                                        @if(isset($item['descuentos']) && !empty($item['descuentos']))
                                            <div class="item-discounts-checkout mt-1">
                                                @foreach($item['descuentos'] as $desc)
                                                    <div class="small text-success">
                                                        <i class="bi bi-tag-fill"></i>
                                                        {{ $desc['nombre'] }}:
                                                        @if($desc['porcentaje'])
                                                            -{{ $desc['porcentaje'] }}%
                                                        @endif
                                                        -${{ number_format($desc['monto'], 0, ',', '.') }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="item-price">
                                        ${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                            
                            {{-- Productos Adicionales --}}
                            @if($carrito->adicionales && count($carrito->adicionales) > 0)
                                <div class="adicionales-checkout border-top pt-3 mt-2">
                                    <small class="text-muted d-block mb-2"><i class="bi bi-gift"></i> Adicionales incluidos:</small>
                                    @foreach($carrito->adicionales as $adicional)
                                    <div class="summary-item py-2">
                                        <div class="item-details">
                                            <div class="item-name small">{{ $adicional['nombre'] }}</div>
                                            <div class="item-quantity small text-muted">Cantidad: {{ $adicional['cantidad'] }}</div>
                                        </div>
                                        <div class="item-price small">
                                            ${{ number_format($adicional['precio'] * $adicional['cantidad'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Totals -->
                            <div class="summary-totals">
                                <div class="summary-row">
                                    <span>Subtotal productos</span>
                                    <span>${{ number_format($carrito->subtotal, 0, ',', '.') }}</span>
                                </div>

                                @if($carrito->adicionales && count($carrito->adicionales) > 0)
                                <div class="summary-row">
                                    <span>Adicionales</span>
                                    <span>${{ number_format($carrito->total_adicionales ?? 0, 0, ',', '.') }}</span>
                                </div>
                                @endif

                                @if($carrito->descuentos_aplicados && count($carrito->descuentos_aplicados) > 0)
                                    @foreach($carrito->descuentos_aplicados as $desc)
                                        <div class="summary-row text-success">
                                            <span>
                                                <i class="bi bi-tag-fill"></i>
                                                <small>{{ $desc['descripcion'] ?? 'Descuento' }}</small>
                                            </span>
                                            <span>-${{ number_format($desc['monto'] ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                    <div class="summary-row">
                                        <strong class="text-success">Total Descuento</strong>
                                        <strong class="text-success">-${{ number_format($carrito->descuento_total, 0, ',', '.') }}</strong>
                                    </div>
                                @endif

                                <div class="summary-row" id="envio-row">
                                    <span>Envío</span>
                                    <span id="envio-valor" class="text-muted">Selecciona comuna</span>
                                </div>
                                <div id="envio-info" class="small text-success mb-2" style="display: none;"></div>
                                <input type="hidden" name="costo_envio" id="costo_envio_input" value="0">
                                <div class="summary-row text-muted">
                                    <span>Impuestos</span>
                                    <span>Incluidos</span>
                                </div>
                                <div class="summary-total">
                                    <span>Total a pagar</span>
                                    <span id="total-valor">${{ number_format($carrito->total ?? $carrito->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Payment Button -->
                            <button type="submit" class="btn-payment" id="paymentBtn">
                                <i class="bi bi-lock-fill"></i> Proceder al Pago
                            </button>
                            
                            <!-- Secure Info -->
                            <div class="secure-info">
                                <i class="bi bi-shield-lock-fill"></i>
                                <span>Pago seguro con WebPay</span>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Subtotal base del carrito (sin envío)
            const subtotalBase = {{ $carrito->total ?? $carrito->subtotal }};
            let costoEnvioActual = 0;

            // Load cities when department changes
            $('#departamento').on('change', function() {
                const departamentoId = $(this).val();
                const ciudadSelect = $('#ciudad_id');

                ciudadSelect.html('<option value="">Cargando comunas...</option>');

                // Reset envío al cambiar departamento
                resetEnvio();

                if (departamentoId) {
                    $.ajax({
                        url: "{{ route('ajax.ciudades') }}",
                        data: { departamento_id: departamentoId },
                        success: function(ciudades) {
                            let options = '<option value="">Seleccione comuna</option>';
                            ciudades.forEach(ciudad => {
                                options += `<option value="${ciudad.id}">${ciudad.nombre}</option>`;
                            });
                            ciudadSelect.html(options);
                        },
                        error: function() {
                            ciudadSelect.html('<option value="">Error al cargar comunas</option>');
                        }
                    });
                } else {
                    ciudadSelect.html('<option value="">Primero seleccione región</option>');
                }
            });

            // Calcular envío cuando se selecciona ciudad
            $('#ciudad_id').on('change', function() {
                const ciudadId = $(this).val();

                if (!ciudadId) {
                    resetEnvio();
                    return;
                }

                // Mostrar loading en envío
                $('#envio-valor').html('<span class="spinner-border spinner-border-sm"></span> Calculando...');
                $('#envio-info').hide();

                $.ajax({
                    url: "{{ route('tienda.calcular-envio') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ciudad_id: ciudadId,
                        subtotal: subtotalBase
                    },
                    success: function(response) {
                        if (response.success) {
                            costoEnvioActual = response.costo_envio;
                            $('#costo_envio_input').val(costoEnvioActual);

                            if (response.envio_gratis) {
                                $('#envio-valor').html('<span class="text-success fw-bold">¡GRATIS!</span>');
                                $('#envio-row').removeClass('text-muted');
                            } else {
                                $('#envio-valor').html('<span class="fw-bold">' + response.costo_envio_formateado + '</span>');
                                $('#envio-row').removeClass('text-muted');
                            }

                            // Mostrar mensajes informativos
                            if (response.mensajes && response.mensajes.length > 0) {
                                let mensajesHtml = response.mensajes.map(m => '<div><i class="bi bi-info-circle"></i> ' + m + '</div>').join('');
                                $('#envio-info').html(mensajesHtml).slideDown();
                            } else {
                                $('#envio-info').hide();
                            }

                            // Actualizar total
                            actualizarTotal();
                        } else {
                            // Sin cobertura o sin tarifa
                            $('#envio-valor').html('<span class="text-danger">' + response.error + '</span>');
                            $('#envio-info').hide();
                            costoEnvioActual = 0;
                            $('#costo_envio_input').val(0);

                            // Mostrar alerta
                            Swal.fire({
                                icon: 'warning',
                                title: 'Zona sin cobertura',
                                text: response.error,
                                confirmButtonColor: '#e91e63'
                            });
                        }
                    },
                    error: function() {
                        $('#envio-valor').html('<span class="text-danger">Error al calcular</span>');
                        $('#envio-info').hide();
                        costoEnvioActual = 0;
                    }
                });
            });

            // Función para resetear envío
            function resetEnvio() {
                costoEnvioActual = 0;
                $('#costo_envio_input').val(0);
                $('#envio-valor').html('<span class="text-muted">Selecciona comuna</span>');
                $('#envio-row').addClass('text-muted');
                $('#envio-info').hide();
                actualizarTotal();
            }

            // Función para actualizar el total
            function actualizarTotal() {
                const nuevoTotal = subtotalBase + costoEnvioActual;
                $('#total-valor').text('$' + formatNumber(nuevoTotal));
            }

            // Formatear número con separadores de miles
            function formatNumber(num) {
                return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

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
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos incompletos',
                        text: 'Por favor complete todos los campos obligatorios',
                        confirmButtonColor: '#e91e63'
                    });
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

            // Contador de caracteres para mensaje de tarjeta y actualización de vista previa
            $('#mensaje_tarjeta').on('input', function() {
                const mensaje = $(this).val();
                const count = mensaje.length;
                $('#mensaje_contador').text(count);

                // Actualizar vista previa
                if (mensaje.trim()) {
                    $('#preview_mensaje').text(mensaje);
                } else {
                    $('#preview_mensaje').text('Tu mensaje aparecerá aquí...');
                }
            });

            // Actualizar nombre del remitente en vista previa
            $('#nombre').on('input', function() {
                const nombre = $(this).val();
                if (nombre.trim()) {
                    $('#preview_remitente').text(nombre);
                } else {
                    $('#preview_remitente').text('tu nombre');
                }
            });

            // Inicializar contador y vista previa al cargar
            const mensajeInicial = $('#mensaje_tarjeta').val();
            if (mensajeInicial) {
                $('#mensaje_contador').text(mensajeInicial.length);
                $('#preview_mensaje').text(mensajeInicial);
            }

            const nombreInicial = $('#nombre').val();
            if (nombreInicial) {
                $('#preview_remitente').text(nombreInicial);
            }
        });

        // Toggle campos de destinatario
        function toggleDestinatario() {
            const checked = $('#mismo_destinatario').is(':checked');
            if (checked) {
                $('#destinatario_fields').slideUp();
                $('#nombre_destinatario').val('');
                $('#telefono_destinatario').val('');
            } else {
                $('#destinatario_fields').slideDown();
            }
        }

        // Toggle vista previa de tarjeta
        function togglePreviewTarjeta() {
            const preview = $('#preview_tarjeta');
            if (preview.is(':visible')) {
                preview.slideUp();
            } else {
                preview.slideDown();
                // Actualizar vista previa con valores actuales
                const mensaje = $('#mensaje_tarjeta').val();
                const nombre = $('#nombre').val();
                if (mensaje.trim()) {
                    $('#preview_mensaje').text(mensaje);
                }
                if (nombre.trim()) {
                    $('#preview_remitente').text(nombre);
                }
            }
        }

        // Mostrar mensajes de sesión con SweetAlert
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '{!! session('error') !!}',
            confirmButtonColor: '#e91e63'
        });
        @endif

        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e91e63'
        });
        @endif

        @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            html: '<ul class="text-start mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#e91e63'
        });
        @endif
    </script>
</body>
</html>