<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProductoMercado;
use App\Models\TipoProductoMercado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductosMercadoDemoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Plaza (verduras/frutas/condimentos)
            ['nombre' => 'Tomate Chonto',     'tipo' => 'Plaza',         'unidad' => 'kg',      'wiki' => 'Tomate'],
            ['nombre' => 'Cebolla Cabezona',  'tipo' => 'Plaza',         'unidad' => 'kg',      'wiki' => 'Cebolla'],
            ['nombre' => 'Cebolla Larga',     'tipo' => 'Plaza',         'unidad' => 'atado',   'wiki' => 'Allium fistulosum'],
            ['nombre' => 'Papa Criolla',      'tipo' => 'Plaza',         'unidad' => 'libra',   'wiki' => 'Solanum phureja'],
            ['nombre' => 'Papa Pastusa',      'tipo' => 'Plaza',         'unidad' => 'kg',      'wiki' => 'Solanum tuberosum'],
            ['nombre' => 'Pimentón Rojo',     'tipo' => 'Plaza',         'unidad' => 'kg',      'wiki' => 'Capsicum annuum'],
            ['nombre' => 'Cilantro',          'tipo' => 'Plaza',         'unidad' => 'atado',   'wiki' => 'Coriandrum sativum'],
            ['nombre' => 'Zanahoria',         'tipo' => 'Plaza',         'unidad' => 'kg',      'wiki' => 'Zanahoria'],
            ['nombre' => 'Ajo',               'tipo' => 'Plaza',         'unidad' => 'libra',   'wiki' => 'Allium sativum'],
            ['nombre' => 'Yuca',              'tipo' => 'Plaza',         'unidad' => 'kg',      'wiki' => 'Manihot esculenta'],
            ['nombre' => 'Mazorca',           'tipo' => 'Plaza',         'unidad' => 'unidad',  'wiki' => 'Mazorca de maíz'],
            ['nombre' => 'Aguacate Hass',     'tipo' => 'Plaza',         'unidad' => 'unidad',  'wiki' => 'Aguacate'],
            ['nombre' => 'Limón Tahití',      'tipo' => 'Plaza',         'unidad' => 'kg',      'wiki' => 'Citrus latifolia'],
            ['nombre' => 'Plátano Verde',     'tipo' => 'Plaza',         'unidad' => 'unidad',  'wiki' => 'Plátano (alimento)'],
            ['nombre' => 'Guascas',           'tipo' => 'Plaza',         'unidad' => 'atado',   'wiki' => 'Galinsoga parviflora'],

            // Makro (abarrotes/granos)
            ['nombre' => 'Aceite Vegetal',    'tipo' => 'Makro',         'unidad' => 'galón',   'wiki' => 'Aceite vegetal'],
            ['nombre' => 'Arroz Blanco',      'tipo' => 'Makro',         'unidad' => 'bulto',   'wiki' => 'Arroz'],
            ['nombre' => 'Sal Refinada',      'tipo' => 'Makro',         'unidad' => 'kg',      'wiki' => 'Sal de mesa'],
            ['nombre' => 'Azúcar',            'tipo' => 'Makro',         'unidad' => 'kg',      'wiki' => 'Azúcar'],
            ['nombre' => 'Frijol Cargamanto', 'tipo' => 'Makro',         'unidad' => 'kg',      'wiki' => 'Phaseolus vulgaris'],
            ['nombre' => 'Lenteja',           'tipo' => 'Makro',         'unidad' => 'kg',      'wiki' => 'Lens culinaris'],
            ['nombre' => 'Maíz Pelado',       'tipo' => 'Makro',         'unidad' => 'kg',      'wiki' => 'Zea mays'],

            // Pollo
            ['nombre' => 'Pollo Entero',      'tipo' => 'Pollo',         'unidad' => 'unidad',  'wiki' => 'Pollo (alimento)'],
            ['nombre' => 'Pechuga de Pollo',  'tipo' => 'Pollo',         'unidad' => 'kg',      'wiki' => 'Pechuga de pollo'],
            ['nombre' => 'Alas de Pollo',     'tipo' => 'Pollo',         'unidad' => 'kg',      'wiki' => 'Alitas de pollo'],

            // Cerdo
            ['nombre' => 'Costilla de Cerdo', 'tipo' => 'Cerdo',         'unidad' => 'kg',      'wiki' => 'Costilla de cerdo'],
            ['nombre' => 'Pezuña de Cerdo',   'tipo' => 'Cerdo',         'unidad' => 'kg',      'wiki' => 'Pernil'],

            // Pescado
            ['nombre' => 'Tilapia',           'tipo' => 'Pescado',       'unidad' => 'kg',      'wiki' => 'Tilapia'],
            ['nombre' => 'Capaz',             'tipo' => 'Pescado',       'unidad' => 'kg',      'wiki' => 'Pimelodus grosskopfii'],

            // Vísceras
            ['nombre' => 'Hígado de Res',     'tipo' => 'Vísceras',      'unidad' => 'kg',      'wiki' => 'Hígado (alimento)'],
            ['nombre' => 'Mondongo',          'tipo' => 'Vísceras',      'unidad' => 'kg',      'wiki' => 'Mondongo'],
            ['nombre' => 'Lengua de Res',     'tipo' => 'Vísceras',      'unidad' => 'kg',      'wiki' => 'Lengua (alimento)'],

            // Gaseosas
            ['nombre' => 'Coca-Cola 1.5L',    'tipo' => 'Gaseosas',      'unidad' => 'unidad',  'wiki' => 'Coca-Cola'],
            ['nombre' => 'Pepsi 1.5L',        'tipo' => 'Gaseosas',      'unidad' => 'unidad',  'wiki' => 'Pepsi'],
            ['nombre' => 'Postobón Manzana',  'tipo' => 'Gaseosas',      'unidad' => 'unidad',  'wiki' => 'Postobón'],

            // Salsamentaria
            ['nombre' => 'Chorizo',           'tipo' => 'Salsamentaria', 'unidad' => 'kg',      'wiki' => 'Chorizo'],
            ['nombre' => 'Salchichón',        'tipo' => 'Salsamentaria', 'unidad' => 'kg',      'wiki' => 'Salchichón'],

            // Aseo
            ['nombre' => 'Detergente',        'tipo' => 'Aseo',          'unidad' => 'litro',   'wiki' => 'Detergente'],
            ['nombre' => 'Jabón de Loza',     'tipo' => 'Aseo',          'unidad' => 'unidad',  'wiki' => 'Jabón'],

            // Desechables
            ['nombre' => 'Servilletas',       'tipo' => 'Desechables',   'unidad' => 'paquete', 'wiki' => 'Servilleta'],
            ['nombre' => 'Vasos Desechables', 'tipo' => 'Desechables',   'unidad' => 'paquete', 'wiki' => 'Vaso (recipiente)'],
        ];

        $uploadPath = public_path('uploads/productos-mercado');
        if (! File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($productos as $data) {
            $tipo = TipoProductoMercado::where('nombre', $data['tipo'])->first();
            if (! $tipo) {
                $this->command->warn("Tipo '{$data['tipo']}' no existe, saltando '{$data['nombre']}'.");
                continue;
            }

            $producto = ProductoMercado::firstOrCreate(
                ['nombre' => $data['nombre']],
                [
                    'unidad_empaque' => $data['unidad'],
                    'tipo_id'        => $tipo->id,
                    'activo'         => true,
                ]
            );

            if (! $producto->imagen) {
                $this->descargarDesdeWikipedia($producto, $data['wiki'], $uploadPath);
            }
        }

        $this->command->info('Productos demo sembrados.');
    }

    private function descargarDesdeWikipedia(ProductoMercado $producto, string $title, string $uploadPath): void
    {
        $imageUrl = $this->buscarImagenWikipedia($title);

        if ($imageUrl === null) {
            $this->command->warn("  ⚠ '{$producto->nombre}': sin imagen en Wikipedia para «{$title}».");
            return;
        }

        $context = stream_context_create([
            'http' => [
                'timeout'         => 12,
                'follow_location' => 1,
                'user_agent'      => 'SopasYSopitas/1.0 (https://github.com/sopas; contact@sopas.local)',
            ],
        ]);

        try {
            $contents = @file_get_contents($imageUrl, false, $context);
            if ($contents === false || strlen($contents) < 1000) {
                $this->command->warn("  ⚠ '{$producto->nombre}': falló descarga desde {$imageUrl}");
                return;
            }

            $ext      = $this->extensionDeUrl($imageUrl);
            $fileName = 'producto_' . $producto->id . '_' . time() . '.' . $ext;
            file_put_contents($uploadPath . DIRECTORY_SEPARATOR . $fileName, $contents);
            $producto->update(['imagen' => $fileName]);

            $this->command->info("  ✓ '{$producto->nombre}' ← {$title}");
        } catch (\Throwable $e) {
            $this->command->warn("  ⚠ Error '{$producto->nombre}': " . $e->getMessage());
        }
    }

    private function buscarImagenWikipedia(string $title): ?string
    {
        foreach (['es', 'en'] as $lang) {
            $url = $this->pageImageQueryUrl($lang, $title);
            $img = $this->fetchPageImage($url);
            if ($img !== null) {
                return $img;
            }
        }

        $url  = 'https://es.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode(str_replace(' ', '_', $title));
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
