<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Fecha Especial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .emoji {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 14px 35px;
            background-color: #FF1493;
            color: white !important;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #C71585;
        }
        .btn-primary {
            background-color: #FF1493;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .info-box {
            background-color: #fff5f8;
            border-left: 4px solid #FF1493;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .discount-banner {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #333;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 25px 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .discount-banner h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            color: #000;
        }
        .discount-code {
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            display: inline-block;
            margin: 15px 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #FF1493;
            border: 2px dashed #FF1493;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .product-card {
            display: inline-block;
            width: 45%;
            margin: 2%;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
            text-align: center;
            vertical-align: top;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .product-card img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-card h4 {
            margin: 10px 0 5px;
            font-size: 14px;
            color: #333;
            min-height: 40px;
        }
        .product-card .price {
            color: #FF1493;
            font-weight: bold;
            font-size: 18px;
        }
        .countdown {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .countdown .days {
            font-size: 48px;
            font-weight: bold;
            line-height: 1;
        }
        .countdown .label {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .highlight {
            background-color: #fff9c4;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="emoji">@switch($fechaEspecial->tipo)
                @case('cumpleanos') 🎂 @break
                @case('aniversario') 💝 @break
                @case('dia_madre') 🌸 @break
                @case('dia_padre') 👔 @break
                @case('navidad') 🎄 @break
                @default 🎉
            @endswitch</span>
            <h1>{{ $fechaEspecial->tipo_nombre }}@if($fechaEspecial->receptor) de {{ $fechaEspecial->receptor }}@endif</h1>
        </div>

        <p>Hola <strong>{{ $fechaEspecial->user->name }}</strong>,</p>

        <p>¡No dejes pasar esta fecha especial! Te recordamos que se acerca:</p>

        <div class="countdown">
            <div class="days">{{ $fechaEspecial->dias_restantes }}</div>
            <div class="label">{{ $fechaEspecial->dias_restantes == 1 ? 'día restante' : 'días restantes' }}</div>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">📅 {{ $fechaEspecial->nombre }}</h3>
            <p><strong>Fecha:</strong> {{ $fechaEspecial->proxima_fecha->isoFormat('dddd, D [de] MMMM') }}</p>
            <p><strong>Ocasión:</strong> {{ $fechaEspecial->tipo_nombre }}</p>
            @if($fechaEspecial->receptor)
            <p><strong>Para:</strong> {{ $fechaEspecial->receptor }}</p>
            @endif
            @if($fechaEspecial->notas)
            <p><strong>Notas:</strong> {{ $fechaEspecial->notas }}</p>
            @endif
        </div>

        @if($fechaEspecial->tieneDescuento())
        <div class="discount-banner">
            <h2>🎁 ¡Descuento Especial Para Ti!</h2>
            <p style="margin: 10px 0; font-size: 18px;">Usa este cupón y obtén:</p>
            <div style="font-size: 32px; font-weight: bold; color: #000; margin: 10px 0;">
                {{ $fechaEspecial->descuento_especial }}% OFF
            </div>
            <p style="margin: 15px 0 10px; font-size: 14px;">Tu código de descuento:</p>
            <div class="discount-code">{{ $fechaEspecial->cupon_descuento }}</div>
            <p style="font-size: 13px; margin-top: 15px;">
                ⏰ <span class="highlight">Válido hasta {{ $fechaEspecial->proxima_fecha->addDays(3)->format('d/m/Y') }}</span>
            </p>
        </div>
        @endif

        @if(count($productosRecomendados) > 0)
        <h3 style="text-align: center; color: #FF1493; margin-top: 30px;">
            ✨ Sugerencias perfectas para esta ocasión
        </h3>
        <p style="text-align: center; color: #666;">Arreglos florales especialmente seleccionados para ti</p>
        <div style="text-align: center;">
            @foreach($productosRecomendados as $producto)
            <div class="product-card">
                @php
                    $imagenUrl = $producto->url_imagen_principal ?? asset('images/placeholder-producto.jpg');
                @endphp
                <img src="{{ $imagenUrl }}" alt="{{ $producto->nombre }}">
                <h4>{{ Str::limit($producto->nombre, 40) }}</h4>
                @php
                    $precio = $producto->precios()->where('activo', true)->first();
                @endphp
                @if($precio)
                    <p class="price">${{ number_format($precio->precio, 0, ',', '.') }}</p>
                @else
                    <p class="price">Consultar</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            @if($fechaEspecial->tieneDescuento())
                <a href="{{ route('home') }}?cupon={{ $fechaEspecial->cupon_descuento }}" class="btn btn-success">
                    🎁 Usar Mi Descuento Ahora
                </a>
            @else
                <a href="{{ route('home') }}" class="btn btn-primary">
                    🌺 Ver Arreglos Florales
                </a>
            @endif
        </div>

        <p style="text-align: center; font-size: 16px; color: #666; line-height: 1.8;">
            💐 No dejes pasar esta fecha especial. <br>
            Un hermoso arreglo floral es el regalo perfecto para demostrar tu cariño.
        </p>

        @if($fechaEspecial->tieneDescuento())
        <div style="background-color: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196f3;">
            <p style="margin: 0; font-size: 14px; color: #1976d2;">
                <strong>💡 Consejo:</strong> Copia tu código <strong>{{ $fechaEspecial->cupon_descuento }}</strong> y úsalo al finalizar tu compra para aplicar el descuento.
            </p>
        </div>
        @endif

        <div class="footer">
            <p>📧 Este recordatorio fue configurado por ti en tu cuenta de cliente.</p>
            <p>✏️ Si deseas modificar tus recordatorios, <a href="{{ route('cliente.puntos') }}" style="color: #FF1493;">ingresa a tu cuenta</a>.</p>
            <p style="margin-top: 20px; color: #999; font-size: 12px;">
                &copy; {{ date('Y') }} {{ config('app.name') }} - Todos los derechos reservados
            </p>
        </div>
    </div>
</body>
</html>
