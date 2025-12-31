<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Categoria;

class GenerarSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Genera el sitemap.xml del sitio';

    public function handle()
    {
        $this->info('Generando sitemap...');

        $empresa = Empresa::where('activo', true)->first();

        if (!$empresa) {
            $this->error('No hay empresa activa');
            return 1;
        }

        $baseUrl = config('app.url');
        $urls = [];

        // Página principal
        $urls[] = [
            'loc' => $baseUrl,
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        // Catálogo
        $urls[] = [
            'loc' => $baseUrl . '/catalogo',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.9'
        ];

        // Arma tu ramo
        $urls[] = [
            'loc' => $baseUrl . '/arma-tu-ramo',
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.8'
        ];

        // Categorías
        $categorias = Categoria::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->get();

        foreach ($categorias as $categoria) {
            $urls[] = [
                'loc' => $baseUrl . '/catalogo?categoria=' . $categoria->id,
                'lastmod' => $categoria->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        // Productos
        $productos = Producto::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->noEliminados()
            ->get();

        foreach ($productos as $producto) {
            $urls[] = [
                'loc' => $baseUrl . '/producto/' . $producto->id,
                'lastmod' => $producto->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }

        // Generar XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        // Guardar en public
        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap generado con ' . count($urls) . ' URLs');
        $this->info('Ubicación: ' . public_path('sitemap.xml'));

        return 0;
    }
}
