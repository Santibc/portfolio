<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\ProductoMercado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BackfillImagenesFaltantesSeeder extends Seeder
{
    /** @var array<string, string[]> producto.nombre => [intentos de wiki title] */
    private array $productosFallback = [
        'Plátano Verde'    => ['Plátano (planta)', 'Musa × paradisiaca', 'Banana'],
        'Pechuga de Pollo' => ['Pechuga', 'Chicken breast'],
        'Hígado de Res'    => ['Hígado', 'Liver (food)'],
        'Lengua de Res'    => ['Lengua de vaca', 'Beef tongue'],
        'Vasos Desechables'=> ['Vaso desechable', 'Disposable cup', 'Paper cup'],
    ];

    /** @var array<string, string[]> menu_item.nombre => [intentos] */
    private array $menuFallback = [
        'Viudo de Capaz' => ['Viudo de pescado', 'Viudo (gastronomía)', 'Sancocho'],
    ];

    public function run(): void
    {
        $this->procesarProductos();
        $this->procesarMenu();
    }

    private function procesarProductos(): void
    {
        $uploadPath = public_path('uploads/productos-mercado');
        if (! File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($this->productosFallback as $nombre => $intentos) {
            $producto = ProductoMercado::where('nombre', $nombre)->first();
            if (! $producto || $producto->imagen) {
                continue;
            }
            foreach ($intentos as $title) {
                $url = $this->buscarImagenWikipedia($title);
                if ($url === null) {
                    continue;
                }
                if ($this->guardar($url, $uploadPath, 'producto_' . $producto->id)) {
                    $producto->update(['imagen' => $this->lastFileName]);
                    $this->command->info("  ✓ '{$producto->nombre}' ← {$title}");
                    continue 2;
                }
            }
            $this->command->warn("  ⚠ '{$producto->nombre}': sin imagen tras fallback.");
        }
    }

    private function procesarMenu(): void
    {
        $uploadPath = public_path('uploads/menu-items');
        if (! File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($this->menuFallback as $nombre => $intentos) {
            $item = MenuItem::where('nombre', $nombre)->first();
            if (! $item || $item->imagen) {
                continue;
            }
            foreach ($intentos as $title) {
                $url = $this->buscarImagenWikipedia($title);
                if ($url === null) {
                    continue;
                }
                if ($this->guardar($url, $uploadPath, 'menu_item_' . $item->id)) {
                    $item->update(['imagen' => $this->lastFileName]);
                    $this->command->info("  ✓ '{$item->nombre}' ← {$title}");
                    continue 2;
                }
            }
            $this->command->warn("  ⚠ '{$item->nombre}': sin imagen tras fallback.");
        }
    }

    private ?string $lastFileName = null;

    private function guardar(string $url, string $path, string $prefijo): bool
    {
        $context = stream_context_create([
            'http' => [
                'timeout'         => 12,
                'follow_location' => 1,
                'user_agent'      => 'SopasYSopitas/1.0 (https://github.com/sopas; contact@sopas.local)',
            ],
        ]);

        $contents = @file_get_contents($url, false, $context);
        if ($contents === false || strlen($contents) < 1000) {
            return false;
        }
        $ext  = $this->extensionDeUrl($url);
        $name = $prefijo . '_' . time() . '.' . $ext;
        file_put_contents($path . DIRECTORY_SEPARATOR . $name, $contents);
        $this->lastFileName = $name;
        return true;
    }

    private function buscarImagenWikipedia(string $title): ?string
    {
        foreach (['es', 'en'] as $lang) {
            $url = "https://{$lang}.wikipedia.org/w/api.php?" . http_build_query([
                'action'      => 'query',
                'format'      => 'json',
                'titles'      => $title,
                'prop'        => 'pageimages',
                'pithumbsize' => 800,
                'pilicense'   => 'any',
                'redirects'   => 1,
            ]);
            $json = $this->httpGet($url);
            if ($json === null) {
                continue;
            }
            $data  = json_decode($json, true);
            $pages = $data['query']['pages'] ?? [];
            foreach ($pages as $page) {
                $src = $page['thumbnail']['source'] ?? null;
                if ($src) {
                    return $src;
                }
            }
        }
        foreach (['es', 'en'] as $lang) {
            $url  = "https://{$lang}.wikipedia.org/api/rest_v1/page/summary/" . rawurlencode(str_replace(' ', '_', $title));
            $json = $this->httpGet($url);
            if ($json === null) {
                continue;
            }
            $data = json_decode($json, true);
            $src = $data['originalimage']['source'] ?? $data['thumbnail']['source'] ?? null;
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
