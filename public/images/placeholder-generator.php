<?php
// Script para generar imágenes placeholder para productos
$categorias = [
    'ramos' => '#FF69B4',
    'arreglos' => '#9370DB',
    'plantas' => '#32CD32',
    'especiales' => '#FFD700',
];

// Crear placeholders para categorías
foreach ($categorias as $nombre => $color) {
    $img = imagecreatetruecolor(800, 600);
    $bgColor = imagecolorallocate($img, hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));
    $textColor = imagecolorallocate($img, 255, 255, 255);

    imagefilledrectangle($img, 0, 0, 800, 600, $bgColor);

    $text = strtoupper($nombre);
    imagestring($img, 5, 350, 290, $text, $textColor);

    imagejpeg($img, __DIR__ . "/categorias/{$nombre}.jpg", 85);
    imagedestroy($img);
}

// Crear placeholders para productos (15 productos)
$productos = [
    'ramo-rosas-rojas' => '#DC143C',
    'ramo-mixto-primaveral' => '#FFB6C1',
    'ramo-tulipanes' => '#FF1493',
    'arreglo-caja-premium' => '#8B008B',
    'centro-mesa-romantic' => '#FFF0F5',
    'arreglo-girasoles' => '#FFD700',
    'orquidea-phalaenopsis' => '#DA70D6',
    'suculentas-set' => '#90EE90',
    'lavanda' => '#E6E6FA',
    'bouquet-novia' => '#FFFFFF',
    'arreglo-condolencias' => '#F5F5F5',
    'ramo-dia-madre' => '#FFC0CB',
    'arreglo-aniversario' => '#FF0000',
    'ramo-claveles' => '#FF69B4',
    'arreglo-tropical' => '#00CED1',
];

foreach ($productos as $nombre => $color) {
    // Imagen principal
    $img = imagecreatetruecolor(800, 800);

    list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
    $bgColor = imagecolorallocate($img, $r, $g, $b);
    $textColor = imagecolorallocate($img, 255, 255, 255);

    imagefilledrectangle($img, 0, 0, 800, 800, $bgColor);

    // Dibujar un círculo decorativo
    $centerX = 400;
    $centerY = 400;
    $radius = 200;
    $circleColor = imagecolorallocate($img, min(255, $r + 40), min(255, $g + 40), min(255, $b + 40));
    imagefilledellipse($img, $centerX, $centerY, $radius * 2, $radius * 2, $circleColor);

    // Texto
    $displayName = str_replace('-', ' ', strtoupper($nombre));
    imagestring($img, 5, 300, 390, substr($displayName, 0, 30), $textColor);

    imagejpeg($img, __DIR__ . "/productos/{$nombre}.jpg", 85);
    imagedestroy($img);

    // Crear 2 imágenes adicionales para algunos productos
    if (in_array($nombre, ['ramo-rosas-rojas', 'ramo-mixto-primaveral', 'arreglo-caja-premium', 'bouquet-novia', 'ramo-dia-madre', 'arreglo-girasoles', 'suculentas-set', 'arreglo-tropical'])) {
        for ($i = 2; $i <= 3; $i++) {
            $img2 = imagecreatetruecolor(800, 800);
            $variation = ($i - 1) * 30;
            $varColor = imagecolorallocate($img2, max(0, $r - $variation), max(0, $g - $variation), max(0, $b - $variation));
            imagefilledrectangle($img2, 0, 0, 800, 800, $varColor);

            imagestring($img2, 5, 320, 390, "VIEW {$i}", $textColor);

            imagejpeg($img2, __DIR__ . "/productos/{$nombre}-{$i}.jpg", 85);
            imagedestroy($img2);
        }
    }
}

echo "✓ Imágenes placeholder generadas correctamente\n";
echo "  - 4 categorías\n";
echo "  - 15 productos principales\n";
echo "  - 16 vistas adicionales\n";
