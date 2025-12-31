<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu carrito te espera</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
        }
        .header {
            background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .header img {
            max-height: 60px;
            margin-bottom: 15px;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 25px;
            color: #555;
        }
        .cart-items {
            background: #fafafa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 60px;
            height: 60px;
            background: #f0f0f0;
            border-radius: 8px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e91e63;
            font-size: 24px;
        }
        .item-details {
            flex: 1;
        }
        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .item-quantity {
            color: #888;
            font-size: 14px;
        }
        .item-price {
            font-weight: 600;
            color: #e91e63;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 2px solid #e91e63;
            margin-top: 10px;
        }
        .total-label {
            font-weight: 600;
            font-size: 18px;
        }
        .total-value {
            font-weight: 700;
            font-size: 22px;
            color: #e91e63;
        }
        .discount-badge {
            background: #4caf50;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .cta-button {
            display: block;
            background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);
            color: white;
            text-decoration: none;
            padding: 18px 40px;
            border-radius: 30px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            margin: 25px auto;
            max-width: 300px;
        }
        .cta-button:hover {
            opacity: 0.9;
        }
        .urgency {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .urgency-text {
            color: #e65100;
            font-weight: 500;
        }
        .footer {
            background: #fafafa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            color: #e91e63;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($empresa->logo)
                <img src="{{ asset($empresa->logo) }}" alt="{{ $empresa->nombre }}">
            @endif
            <h1>🌸 {{ $empresa->nombre }}</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">¡Hola {{ $nombreCliente }}!</p>

            @if($numeroRecordatorio == 1)
                <p class="message">
                    Notamos que dejaste algunos productos increíbles en tu carrito.
                    ¡No te preocupes! Los hemos guardado para ti.
                </p>
            @elseif($numeroRecordatorio == 2)
                <p class="message">
                    Tus flores favoritas siguen esperándote. 🌷
                    No dejes pasar la oportunidad de sorprender a alguien especial.
                </p>
            @else
                <p class="message">
                    ¡Esta es tu última oportunidad! Como queremos que completes tu pedido,
                    te ofrecemos un <strong>descuento especial</strong>.
                </p>

                <div style="text-align: center;">
                    <span class="discount-badge">🎁 {{ $porcentajeDescuento }}% OFF con código: VUELVE10</span>
                </div>
            @endif

            <!-- Cart Items -->
            <div class="cart-items">
                @foreach($items as $item)
                <div class="cart-item">
                    <div class="item-image">
                        🌸
                    </div>
                    <div class="item-details">
                        <div class="item-name">{{ $item['nombre'] ?? 'Producto' }}</div>
                        <div class="item-quantity">Cantidad: {{ $item['cantidad'] ?? 1 }}</div>
                    </div>
                    <div class="item-price">${{ number_format($item['precio'] ?? 0, 0, ',', '.') }}</div>
                </div>
                @endforeach

                <div class="total-row">
                    <span class="total-label">Total:</span>
                    <span class="total-value">${{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($numeroRecordatorio >= 2)
            <div class="urgency">
                <span class="urgency-text">
                    ⏰ Tu carrito se reservará por tiempo limitado. ¡No pierdas tus productos!
                </span>
            </div>
            @endif

            <!-- CTA Button -->
            <a href="{{ $urlRecuperacion }}" class="cta-button">
                🛒 Completar mi compra
            </a>

            <p style="text-align: center; color: #888; font-size: 14px;">
                ¿Tienes preguntas? Responde a este email o contáctanos por WhatsApp.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="social-links">
                @if($empresa->instagram_url)
                    <a href="{{ $empresa->instagram_url }}">Instagram</a>
                @endif
                @if($empresa->facebook_url)
                    <a href="{{ $empresa->facebook_url }}">Facebook</a>
                @endif
                @if($empresa->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $empresa->whatsapp) }}">WhatsApp</a>
                @endif
            </div>
            <p>{{ $empresa->nombre }} - {{ $empresa->direccion ?? '' }}</p>
            <p style="margin-top: 10px;">
                <a href="#" style="color: #888;">Cancelar suscripción</a>
            </p>
        </div>
    </div>
</body>
</html>
