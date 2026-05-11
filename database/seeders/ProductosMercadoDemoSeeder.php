<?php

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
            ['nombre' => 'Tomate Chonto',     'tipo' => 'Plaza',         'unidad' => 'kg',      'keyword' => 'tomato'],
            ['nombre' => 'Cebolla Cabezona',  'tipo' => 'Plaza',         'unidad' => 'kg',      'keyword' => 'onion'],
            ['nombre' => 'Papa Criolla',      'tipo' => 'Plaza',         'unidad' => 'libra',   'keyword' => 'potato'],
            ['nombre' => 'Pimentón Rojo',     'tipo' => 'Plaza',         'unidad' => 'kg',      'keyword' => 'pepper'],
            ['nombre' => 'Cilantro',          'tipo' => 'Plaza',         'unidad' => 'atado',   'keyword' => 'cilantro'],
            ['nombre' => 'Aceite Vegetal',    'tipo' => 'Makro',         'unidad' => 'galón',   'keyword' => 'oil'],
            ['nombre' => 'Arroz Blanco',      'tipo' => 'Makro',         'unidad' => 'bulto',   'keyword' => 'rice'],
            ['nombre' => 'Sal Refinada',      'tipo' => 'Makro',         'unidad' => 'kg',      'keyword' => 'salt'],
            ['nombre' => 'Pollo Entero',      'tipo' => 'Pollo',         'unidad' => 'unidad',  'keyword' => 'chicken'],
            ['nombre' => 'Pechuga de Pollo',  'tipo' => 'Pollo',         'unidad' => 'kg',      'keyword' => 'chicken,meat'],
            ['nombre' => 'Costilla de Cerdo', 'tipo' => 'Cerdo',         'unidad' => 'kg',      'keyword' => 'pork'],
            ['nombre' => 'Tilapia',           'tipo' => 'Pescado',       'unidad' => 'kg',      'keyword' => 'fish'],
            ['nombre' => 'Hígado de Res',     'tipo' => 'Vísceras',      'unidad' => 'kg',      'keyword' => 'liver'],
            ['nombre' => 'Coca-Cola 1.5L',    'tipo' => 'Gaseosas',      'unidad' => 'unidad',  'keyword' => 'soda'],
            ['nombre' => 'Chorizo',           'tipo' => 'Salsamentaria', 'unidad' => 'kg',      'keyword' => 'sausage'],
            ['nombre' => 'Detergente',        'tipo' => 'Aseo',          'unidad' => 'litro',   'keyword' => 'detergent'],
            ['nombre' => 'Servilletas',       'tipo' => 'Desechables',   'unidad' => 'paquete', 'keyword' => 'napkin'],
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
                $this->descargarImagen($producto, $data['keyword'], $uploadPath);
            }
        }

        $this->command->info('Productos demo sembrados.');
    }

    private function descargarImagen(ProductoMercado $producto, string $keyword, string $uploadPath): void
    {
        $url = "https://loremflickr.com/640/480/" . urlencode($keyword);

        $context = stream_context_create([
            'http' => [
                'timeout'        => 6,
                'follow_location' => 1,
                'user_agent'     => 'Mozilla/5.0 (compatible; AgromarketSeeder/1.0)',
            ],
        ]);

        try {
            $contents = @file_get_contents($url, false, $context);

            if ($contents === false || strlen($contents) < 1000) {
                $this->command->warn("  ⚠ No se pudo descargar imagen para '{$producto->nombre}' ({$keyword}). Continuando sin imagen.");
                return;
            }

            $fileName = 'producto_' . $producto->id . '_' . time() . '.jpg';
            file_put_contents($uploadPath . DIRECTORY_SEPARATOR . $fileName, $contents);

            $producto->update(['imagen' => $fileName]);
            $this->command->info("  ✓ Imagen descargada para '{$producto->nombre}'.");
        } catch (\Throwable $e) {
            $this->command->warn("  ⚠ Error descargando imagen para '{$producto->nombre}': " . $e->getMessage());
        }
    }
}
