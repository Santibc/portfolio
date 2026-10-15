<?php

/*
|--------------------------------------------------------------------------
| Datos públicos de la panadería (landing en "/")
|--------------------------------------------------------------------------
|
| Fuente única de lo que muestra la landing. Lo marcado con "PENDIENTE" es
| texto genérico de ejemplo: se reemplaza cuando el cliente envíe la
| información real (horario, historia, misión, visión, cifras, redes).
|
*/

return [

    'nombre' => 'El Pan Dorado DC',
    'nombre_largo' => 'Panadería y Pastelería El Pan Dorado DC',
    'lema' => 'Calidad & Servicio',
    'propietaria' => 'Dorahaliz Corzo Quijano',
    'propietaria_cargo' => 'Propietaria', // PENDIENTE: confirmar (¿fundadora?)

    // Teléfono local (10 dígitos) y número de WhatsApp con indicativo de Colombia.
    'telefono' => '3188655792',
    'whatsapp' => '573188655792',
    'whatsapp_mensaje' => 'Hola, El Pan Dorado DC. Quisiera hacer un pedido.',

    'direccion' => 'Carrera 41A # 4-42',
    'barrio' => 'Primavera',
    'ciudad' => 'Bogotá D.C.',
    'pais' => 'Colombia',

    // Sin key el mapa usa el embed público de Google; con key usa la Maps Embed API oficial.
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),

    // PENDIENTE: horario real del local.
    'horario' => [
        ['dias' => 'Lunes a sábado', 'horas' => '6:00 a.m. – 8:00 p.m.'],
        ['dias' => 'Domingos y festivos', 'horas' => '7:00 a.m. – 2:00 p.m.'],
    ],

    // URL completa de cada red; null = no se muestra.
    'redes' => [
        'instagram' => null,
        'facebook' => null,
        'tiktok' => null,
    ],

    // PENDIENTE: historia real del negocio.
    'historia' => [
        'Nacimos con una idea sencilla: que cada mañana el barrio despierte con el olor del pan recién horneado. Desde nuestro horno en Primavera preparamos panes, hojaldres y tortas con recetas de casa, ingredientes seleccionados y mucha paciencia.',
        'Creemos que el buen pan se hace sin afán. Por eso amasamos a mano, respetamos los tiempos de cada masa y horneamos varias veces al día, para que siempre encuentres algo calientico en nuestra vitrina.',
    ],

    // PENDIENTE: cifras reales.
    'cifras' => [
        ['valor' => 40, 'prefijo' => '+', 'sufijo' => '', 'texto' => 'productos horneados a diario'],
        ['valor' => 100, 'prefijo' => '', 'sufijo' => '%', 'texto' => 'hecho en nuestro horno'],
        ['valor' => 6, 'prefijo' => '', 'sufijo' => ' a.m.', 'texto' => 'sale la primera horneada'],
    ],

    // PENDIENTE: misión y visión reales.
    'mision' => 'Llevar a cada hogar del barrio pan y pastelería frescos, hechos con ingredientes de calidad y recetas de tradición, acompañados de una atención cercana que haga de cada visita un buen momento.',
    'vision' => 'Ser la panadería y pastelería de referencia en Bogotá por el sabor de nuestros productos, la calidad de nuestro servicio y el cariño con el que horneamos cada día.',

    'valores' => [
        ['icono' => 'award', 'titulo' => 'Calidad', 'texto' => 'Ingredientes seleccionados y recetas cuidadas en cada horneada.'],
        ['icono' => 'heart-handshake', 'titulo' => 'Servicio', 'texto' => 'Atención cálida y cercana, como en casa.'],
        ['icono' => 'sunrise', 'titulo' => 'Frescura', 'texto' => 'Horneamos varias veces al día para que todo llegue fresco.'],
        ['icono' => 'wheat', 'titulo' => 'Tradición', 'texto' => 'El sabor del pan de barrio de siempre, hecho con paciencia.'],
    ],

    // Categorías de la sección "Productos". `imagen` = nombre base en public/images/landing (-600/-1000.webp).
    'productos' => [
        ['titulo' => 'Pan artesanal', 'descripcion' => 'Masas de fermentación lenta, corteza crujiente y miga suave.', 'imagen' => 'pan-artesanal'],
        ['titulo' => 'Pan tradicional', 'descripcion' => 'Pan de sal, mogollas, roscones y los clásicos de la mañana.', 'imagen' => 'pan-tradicional'],
        ['titulo' => 'Pastelería', 'descripcion' => 'Hojaldres, croissants y pasteles recién salidos del horno.', 'imagen' => 'pasteleria'],
        ['titulo' => 'Tortas por encargo', 'descripcion' => 'Cumpleaños, bautizos y celebraciones, a tu gusto.', 'imagen' => 'tortas'],
        ['titulo' => 'Desayunos y café', 'descripcion' => 'Un buen café con algo recién horneado para empezar el día.', 'imagen' => 'desayunos'],
        ['titulo' => 'Galletas y postres', 'descripcion' => 'Antojos dulces para compartir o llevar de regalo.', 'imagen' => 'galletas'],
    ],

];
