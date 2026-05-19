<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\TipoMenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MenuItemsSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = TipoMenuItem::pluck('id', 'slug');

        // [tipo, nombre, precio, orden, titulo_wikipedia] (busca ES y luego EN automáticamente)
        $items = [
            // === MENÚ ===
            ['menu', 'Ajiaco',                 17500, 1,  'Ajiaco'],
            ['menu', 'Ajiaco Pequeño',         16000, 2,  'Ajiaco'],
            ['menu', 'Mondongo',               17500, 3,  'Mondongo'],
            ['menu', 'Sancocho de Pescado',    18500, 4,  'Sancocho'],
            ['menu', 'Sancocho de Pollo',      18500, 5,  'Sancocho'],
            ['menu', 'Viudo de Capaz',         23000, 6,  null], // sin artículo bueno en Wikipedia, subir manualmente
            ['menu', 'Mazamorra chiquita',     18000, 7,  'Mazamorra'],
            ['menu', 'Mute',                   18000, 8,  'Mute (gastronomía)'],
            ['menu', 'Cuchuco con espinazo',   18500, 9,  'Cuchuco'],
            ['menu', 'Frijoles con pezuña',    18500, 10, 'Bandeja paisa'],
            ['menu', 'Lengua en salsa',        18500, 11, 'Beef tongue'],
            ['menu', 'Pollo con champiñones',  18500, 12, 'Chicken marsala'],
            ['menu', 'Arroz con pollo',        18500, 13, 'Arroz con pollo'],

            // === COMBOS — heredan imagen del primer plato ===
            ['combos', 'Ajiaco mondongo lengua en salsa',       21500, 1, 'Ajiaco'],
            ['combos', 'Ajiaco mondongo pollo con champiñones', 21500, 2, 'Ajiaco'],
            ['combos', 'Ajiaco mondongo arroz con pollo',       21500, 3, 'Arroz con pollo'],

            // === ADICIONES ===
            ['adiciones', 'Arroz',    3000, 1, 'Cooked rice'],
            ['adiciones', 'Aguacate', 2500, 2, 'Avocado'],
            ['adiciones', 'Limonada', 1800, 3, 'Lemonade'],
            ['adiciones', 'Gaseosa',  3500, 4, 'Cola'],
        ];

        $uploadPath = public_path('uploads/menu-items');
        if (! File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($items as [$tipoSlug, $nombre, $precio, $orden, $wikiTitle]) {
            $item = MenuItem::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'tipo_id' => $tipos[$tipoSlug],
                    'precio'  => $precio,
                    'activo'  => true,
                    'orden'   => $orden,
                ],
            );

            if (! $item->imagen && $wikiTitle !== null) {
                $this->descargarDesdeWikipedia($item, $wikiTitle, $uploadPath);
            }
        }

        $this->command->info('Items del menú sembrados.');
    }

    private function descargarDesdeWikipedia(MenuItem $item, string $title, string $uploadPath): void
    {
        $imageUrl = $this->buscarImagenWikipedia($title);

        if ($imageUrl === null) {
            $this->command->warn("  ⚠ '{$item->nombre}': sin imagen en Wikipedia para «{$title}». Se puede subir manualmente desde la UI.");

            return;
        }

        $context = stream_context_create([
            'http' => [
                'timeout'         => 10,
                'follow_location' => 1,
                'user_agent'      => 'SopasYSopitas/1.0 (https://github.com/sopas; contact@sopas.local)',
            ],
        ]);

        try {
            $contents = @file_get_contents($imageUrl, false, $context);
            if ($contents === false || strlen($contents) < 1000) {
                $this->command->warn("  ⚠ '{$item->nombre}': falló descarga desde {$imageUrl}");

                return;
            }

            $ext      = $this->extensionDeUrl($imageUrl);
            $fileName = 'menu_item_' . $item->id . '_' . time() . '.' . $ext;
            file_put_contents($uploadPath . DIRECTORY_SEPARATOR . $fileName, $contents);
            $item->update(['imagen' => $fileName]);

            $this->command->info("  ✓ '{$item->nombre}' ← Wikipedia: {$title}");
        } catch (\Throwable $e) {
            $this->command->warn("  ⚠ Error descargando '{$item->nombre}': " . $e->getMessage());
        }
    }

    /**
     * Busca la imagen principal del artículo de Wikipedia usando varios endpoints.
     * Estrategia: action=query pageimages en ES → mismo en EN → REST summary ES.
     */
    private function buscarImagenWikipedia(string $title): ?string
    {
        foreach (['es', 'en'] as $lang) {
            $url = $this->pageImageQueryUrl($lang, $title);
            $img = $this->fetchPageImage($url);
            if ($img !== null) {
                return $img;
            }
        }

        // Último fallback: REST summary ES (a veces tiene imagen cuando query no la encuentra)
        $url = 'https://es.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode(str_replace(' ', '_', $title));
        $json = $this->httpGet($url);
        if ($json === null) {
            return null;
        }
        $data = json_decode($json, true);

        return $data['originalimage']['source']
            ?? $data['thumbnail']['source']
            ?? null;
    }

    private function pageImageQueryUrl(string $lang, string $title): string
    {
        return "https://{$lang}.wikipedia.org/w/api.php?" . http_build_query([
            'action'      => 'query',
            'format'      => 'json',
            'titles'      => $title,
            'prop'        => 'pageimages',
            'pithumbsize' => 800,
            'pilicense'   => 'any',
            'redirects'   => 1,
        ]);
    }

    private function fetchPageImage(string $url): ?string
    {
        $json = $this->httpGet($url);
        if ($json === null) {
            return null;
        }
        $data  = json_decode($json, true);
        $pages = $data['query']['pages'] ?? [];
        foreach ($pages as $page) {
            $src = $page['thumbnail']['source'] ?? null;
            if ($src) {
                return $src;
            }
        }

        return null;
    }

    private function httpGet(string $url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout'         => 10,
                'follow_location' => 1,
                'user_agent'      => 'SopasYSopitas/1.0 (https://github.com/sopas; contact@sopas.local)',
                'header'          => "Accept: application/json\r\n",
            ],
        ]);

        $r = @file_get_contents($url, false, $context);

        return $r === false ? null : $r;
    }

    private function extensionDeUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? $ext : 'jpg';
    }
}
