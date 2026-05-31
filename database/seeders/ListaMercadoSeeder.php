<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ListaMercado;
use App\Models\ListaMercadoItem;
use App\Models\ProductoMercado;
use Illuminate\Database\Seeder;

class ListaMercadoSeeder extends Seeder
{
    public function run(): void
    {
        $lista = ListaMercado::firstOrCreate(
            ['slug' => 'lista-semanal'],
            ['nombre' => 'Lista semanal', 'activa' => true]
        );

        $items = [
            // Plaza
            ['nombre' => 'Tomate Chonto',     'cantidad' => 5],
            ['nombre' => 'Cebolla Cabezona',  'cantidad' => 3],
            ['nombre' => 'Cebolla Larga',     'cantidad' => 2],
            ['nombre' => 'Cilantro',          'cantidad' => 2],
            ['nombre' => 'Papa Criolla',      'cantidad' => 4],
            ['nombre' => 'Papa Pastusa',      'cantidad' => 10],
            ['nombre' => 'Zanahoria',         'cantidad' => 3],
            ['nombre' => 'Ajo',               'cantidad' => 1],
            ['nombre' => 'Yuca',              'cantidad' => 5],
            ['nombre' => 'Mazorca',           'cantidad' => 12],
            ['nombre' => 'Aguacate Hass',     'cantidad' => 10],
            ['nombre' => 'Limón Tahití',      'cantidad' => 3],
            ['nombre' => 'Plátano Verde',     'cantidad' => 15],
            ['nombre' => 'Guascas',           'cantidad' => 3],
            ['nombre' => 'Pimentón Rojo',     'cantidad' => 2],
            // Pollo
            ['nombre' => 'Pechuga de Pollo',  'cantidad' => 4],
            ['nombre' => 'Pollo Entero',      'cantidad' => 2],
            // Cerdo
            ['nombre' => 'Costilla de Cerdo', 'cantidad' => 2],
            // Pescado
            ['nombre' => 'Tilapia',           'cantidad' => 3],
            // Vísceras
            ['nombre' => 'Mondongo',          'cantidad' => 3],
            // Makro
            ['nombre' => 'Aceite Vegetal',    'cantidad' => 1],
            ['nombre' => 'Arroz Blanco',      'cantidad' => 1],
            ['nombre' => 'Azúcar',            'cantidad' => 5],
            ['nombre' => 'Sal Refinada',      'cantidad' => 2],
            // Gaseosas
            ['nombre' => 'Coca-Cola 1.5L',    'cantidad' => 6],
            // Salsamentaria
            ['nombre' => 'Chorizo',           'cantidad' => 2],
            // Aseo / Desechables
            ['nombre' => 'Detergente',        'cantidad' => 1],
            ['nombre' => 'Servilletas',       'cantidad' => 2],
            ['nombre' => 'Vasos Desechables', 'cantidad' => 2],
        ];

        $orden = 1;
        $agregados = 0;
        foreach ($items as $data) {
            $producto = ProductoMercado::where('nombre', $data['nombre'])->first();
            if (! $producto) {
                $this->command->warn("Producto '{$data['nombre']}' no existe, saltando.");
                continue;
            }

            ListaMercadoItem::firstOrCreate(
                [
                    'lista_id'            => $lista->id,
                    'producto_mercado_id' => $producto->id,
                ],
                [
                    'cantidad_sugerida' => $data['cantidad'],
                    'orden'             => $orden,
                ]
            );
            $orden++;
            $agregados++;
        }

        $this->command->info("Lista '{$lista->nombre}' sembrada con {$agregados} items.");
    }
}
