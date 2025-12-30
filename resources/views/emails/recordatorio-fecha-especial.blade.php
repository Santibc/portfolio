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
        }
        .btn:hover {
            background-color: #C71585;
        }
        .info-box {
            background-color: #fff5f8;
            border-left: 4px solid #FF1493;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
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
        }
        .product-card img {
            max-width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-card h4 {
            margin: 10px 0 5px;
            font-size: 14px;
            color: #333;
        }
        .product-card .price {
            color: #FF1493;
            font-weight: bold;
        }
        .countdown {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #333;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .countdown .days {
            font-size: 32px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="emoji">@switch($fechaEspecial->tipo)
                @case('cumpleanos') @break
                @case('aniversario') @break
                @case('dia_madre') @break
                @case('dia_padre') @break
                @default
            @endswitch</span>
            <h1>{{ $fechaEspecial->tipo_nombre }} de {{ $fechaEspecial->nombre }}</h1>
        </div>

        <p>Hola <strong>{{ $fechaEspecial->user->name }}</strong>,</p>

        <p>Te recordamos que se acerca una fecha muy especial que guardaste con nosotros:</p>

        <div class="countdown">
            <div class="days">{{ $fechaEspecial->dias_restantes }}</div>
            <div>{{ $fechaEspecial->dias_restantes == 1 ? 'dia restante' : 'dias restantes' }}</div>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">{{ $fechaEspecial->nombre }}</h3>
            <p><strong>Fecha:</strong> {{ $fechaEspecial->fecha->format('d/m/Y') }}</p>
            <p><strong>Ocasion:</strong> {{ $fechaEspecial->tipo_nombre }}</p>
            @if($fechaEspecial->notas)
            <p><strong>Notas:</strong> {{ $fechaEspecial->notas }}</p>
            @endif
        </div>

        @if(count($productosRecomendados) > 0)
        <h3 style="text-align: center;">Sugerencias perfectas para esta ocasion</h3>
        <div style="text-align: center;">
            @foreach($productosRecomendados as $producto)
            <div class="product-card">
                @if($producto->imagen_principal)
                <img src="{{ asset('storage/' . $producto->imagen_principal) }}" alt="{{ $producto->nombre }}">
                @else
                <img src="{{ asset('images/placeholder-producto.jpg') }}" alt="{{ $producto->nombre }}">
                @endif
                <h4>{{ Str::limit($producto->nombre, 30) }}</h4>
                <p class="price">${{ number_format($producto->precio_base, 0) }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('home') }}" class="btn">Ver Arreglos Florales</a>
        </div>

        <p style="text-align: center;">No dejes pasar esta fecha especial. Un hermoso arreglo floral es el regalo perfecto para demostrar tu carino.</p>

        <div class="footer">
            <p>Este recordatorio fue configurado por ti en tu cuenta de cliente.</p>
            <p>Si deseas modificar tus recordatorios, <a href="{{ route('cliente.puntos') }}">ingresa a tu cuenta</a>.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
