<?php

/*
|--------------------------------------------------------------------------
| Contenido de la landing pública (/)
|--------------------------------------------------------------------------
| Todo el texto y los datos del negocio que muestra la página de inicio.
| Lo marcado con 'pendiente' => true es contenido de ejemplo: la página lo
| señala como tal hasta que el cliente envíe el real. Para actualizarlo basta
| con editar este archivo (y `php artisan config:clear` si la config está en
| caché). Las imágenes viven en public/images/landing/.
*/

return [

    'negocio' => [
        'nombre'         => 'Dorilokos Mix',
        'lema'           => '¡Sabor bien loko!',
        'categoria'      => 'Restaurante de comida rápida',
        'barrio'         => 'Chapinero',
        'ciudad'         => 'Bogotá',
        // Instagram y Rappi. Google Business muestra "Cl. 65 #1175" (le falta el guion): el dueño debe corregirlo.
        'direccion'      => 'Calle 65 #11-73',
        'codigo_postal'  => '110221',
        'consulta_mapa'  => 'Dorilokos Mix, Calle 65 #11-73, Chapinero, Bogotá',
        'coordenadas'    => ['lat' => 4.6514021, 'lng' => -74.0627845],
        'referencias'    => 'Frente al SENA de la Calle 65, cerca de la estación Calle 63 de TransMilenio.',

        // WhatsApp de domicilios (bio de Instagram). Formato internacional sin "+".
        'whatsapp'          => '573142798769',
        'whatsapp_visible'  => '314 279 8769',
        'whatsapp_mensaje'  => '¡Hola, Dorilokos Mix! Quiero hacer un pedido.',

        'instagram'         => 'https://www.instagram.com/dorilokosmix/',
        'instagram_usuario' => '@dorilokosmix',
        'tiktok'            => 'https://www.tiktok.com/@dorilokosmix',
        // Tienda en Rappi (null = no se muestra el enlace).
        'rappi'             => 'https://www.rappi.com.co/restaurantes/900452017-dorilokos-mix-chapinero',
        // Ficha de Google Maps (ahí están las opiniones).
        'google_maps'       => 'https://maps.google.com/?cid=5886717402679264966',

        // Perfil de Google Business (oct. 2026).
        'google_calificacion' => 4.8,
        'google_opiniones'    => 24,
        'rango_precio'        => '$1–40.000 por persona',
        // Temas que se repiten en las opiniones públicas (no son citas).
        'opiniones_destacan'  => ['Atención rápida', 'Buen sabor', 'Buen precio'],
    ],

    // Horario publicado en Google Maps y Rappi (oct. 2026).
    'horario' => [
        'pendiente' => false,
        'dias'      => [
            ['dias' => 'Lunes a viernes', 'horas' => '11:00 a. m. – 9:00 p. m.'],
            ['dias' => 'Sábado',          'horas' => '11:00 a. m. – 5:00 p. m.'],
            ['dias' => 'Domingo',         'horas' => 'Cerrado'],
        ],
        // Mismo horario en formato schema.org (datos estructurados para buscadores).
        'schema'    => ['Mo-Fr 11:00-21:00', 'Sa 11:00-17:00'],
    ],

    // Lo que lleva el producto publicado en Rappi, en orden de capas (de abajo hacia arriba).
    'ingredientes' => [
        ['nombre' => 'Doritos',            'color' => '#f39030'],
        ['nombre' => 'Frijol',             'color' => '#6b3a2a'],
        ['nombre' => 'Carne desmechada',   'color' => '#8a4b2f'],
        ['nombre' => 'Chicharrón carnudo', 'color' => '#c9822f'],
        ['nombre' => 'Plátano maduro',     'color' => '#e9a23b'],
        ['nombre' => 'Maíz dulce',         'color' => '#f5c020'],
        ['nombre' => 'Queso rayado',       'color' => '#f7e3a1'],
        ['nombre' => 'Guacamole',          'color' => '#6f9a3a'],
        ['nombre' => 'Pico de gallo',      'color' => '#d8452f'],
        ['nombre' => 'Salsa de la casa',   'color' => '#e3262a'],
    ],

    // Foto de referencia de "¿Qué es?" (genérica, no del local; se rotula como referencia).
    'referencia' => [
        'imagen' => 'images/landing/dorilokos-referencia.webp',
        'alt'    => 'Nachos cargados con carne de res, frijol, queso fundido, queso rallado y pico de gallo',
    ],

    // "Así se arma": un paso por fila, con su medida.
    'pasos' => [
        [
            'titulo'      => 'Abrimos tu paquete',
            'descripcion' => 'Rasgamos el paquete de Doritos por arriba. Ese es tu plato.',
            'medida'      => '1 paquete',
            'imagen'      => 'images/landing/paso-paquete.webp',
        ],
        [
            'titulo'      => 'La base caliente',
            'descripcion' => 'Frijol y carne desmechada recién hechos, directo sobre los nachos.',
            'medida'      => '1 capa generosa',
            'imagen'      => 'images/landing/paso-frijol.webp',
        ],
        [
            'titulo'      => 'Lo crocante y lo dulce',
            'descripcion' => 'Chicharrón carnudo, plátano maduro y maíz dulce.',
            'medida'      => '1 puñado de cada uno',
            'imagen'      => 'images/landing/paso-chicharron.webp',
        ],
        [
            'titulo'      => 'Lo fresco',
            'descripcion' => 'Queso rayado, guacamole y pico de gallo para equilibrar.',
            'medida'      => 'Hasta el borde',
            'imagen'      => 'images/landing/paso-guacamole.webp',
        ],
        [
            'titulo'      => 'Salsa de la casa',
            'descripcion' => 'El toque final. Se come ahí mismo, en el paquete.',
            'medida'      => 'Al gusto (y un poquito más)',
            'imagen'      => 'images/landing/paso-salsa.webp',
        ],
    ],

    // Menú y precios reales del módulo de menú del sistema (dorilokosmix.com, 18 oct. 2026).
    // Los de Rappi llevan recargo: no usarlos aquí.
    'menu' => [
        'dorilokos' => [
            [
                'nombre'      => 'Pollo',
                'pedido'      => 'Dorilokos de pollo',
                'etiqueta'    => null,
                'descripcion' => 'Pollo desmechado con los toppings de la casa.',
                'personal'    => 15000,
                'familiar'    => 38000,
            ],
            [
                'nombre'      => 'Carne',
                'pedido'      => 'Dorilokos de carne',
                'etiqueta'    => null,
                'descripcion' => 'Carne desmechada con los toppings de la casa.',
                'personal'    => 17000,
                'familiar'    => 42000,
            ],
            [
                'nombre'      => 'Mixto',
                'pedido'      => 'Dorilokos mixto',
                'etiqueta'    => null,
                'descripcion' => 'Carne y pollo desmechados con los toppings de la casa.',
                'personal'    => 20000,
                'familiar'    => 48000,
            ],
            [
                // El dueño lo presenta en Google como su plato más vendido; el detalle es el publicado en Rappi.
                'nombre'      => 'Paisa',
                'pedido'      => 'Dorilokos paisa',
                'etiqueta'    => 'El más vendido',
                'descripcion' => 'Carne desmechada, chicharrón carnudo, plátano maduro, queso rayado, maíz dulce, frijol, pico de gallo, guacamole y salsa de la casa.',
                'personal'    => 23000,
                'familiar'    => 52000,
            ],
        ],
        'combos' => [
            ['nombre' => 'Combo pollo', 'descripcion' => 'Dorilokos de pollo + bebida de 250 ml o personal.', 'precio' => 18000],
            ['nombre' => 'Combo carne', 'descripcion' => 'Dorilokos de carne + bebida de 250 ml o personal.', 'precio' => 20000],
        ],
        'bebidas' => [
            'detalle' => 'Gaseosas (250 ml, personal, litro y litro y medio), jugos Hit y Del Valle, té, agua (natural, con gas o de sabor) y cerveza.',
            'desde'   => 2000,
            'hasta'   => 9500,
        ],
        'adiciones' => [
            ['nombre' => 'Plátano',             'precio' => 1000],
            ['nombre' => 'Empaque para llevar', 'precio' => 1500],
            ['nombre' => 'Paquete extra',       'precio' => 3000],
        ],
    ],

    // Opciones de "Sabor de Dorito" en el sistema (el familiar va en paquete familiar).
    'bases' => ['Doritos Queso', 'Doritos BBQ', "Doritos Flamin' Hot", 'Doritos Incógnita', 'Choclitos', 'Natuchips'],

    // Fotos del local: ventanas vacías hasta que lleguen las reales (ruta relativa a public/).
    'galeria' => [
        ['titulo' => 'La fachada',     'imagen' => null],
        ['titulo' => 'Adentro',        'imagen' => null],
        ['titulo' => 'El equipo',      'imagen' => null],
        ['titulo' => 'Recién armado',  'imagen' => null],
    ],

    // Textos institucionales de ejemplo, a reemplazar por los del cliente.
    'institucional' => [
        'pendiente' => true,
        'historia'  => 'Empezamos con una idea sencilla: el paquete de Doritos también puede ser el plato. Lo abrimos, lo cargamos hasta arriba y lo servimos al momento, aquí en Chapinero.',
        'mision'    => 'Servir dorilokos cargados, frescos y rápidos, con ingredientes que se sienten caseros, para que cada antojo valga la pena.',
        'vision'    => 'Ser el antojo de referencia en Bogotá cuando alguien piense en algo rico, contundente y diferente.',
        'valores'   => ['Sabor sin miedo', 'Frescura al momento', 'Atención de parceros', 'Precio justo'],
    ],

    'seo' => [
        'titulo'      => 'Dorilokos Mix · Dorilokos cargados en Chapinero, Bogotá',
        'descripcion' => 'Abrimos tu paquete de Doritos y lo cargamos con frijol, carne desmechada, chicharrón, maduro, queso, guacamole y salsa de la casa. Pide por WhatsApp al 314 279 8769. Calle 65 #11-73, Chapinero.',
        'imagen'      => 'images/landing/og-dorilokos-mix.jpg',
    ],
];
