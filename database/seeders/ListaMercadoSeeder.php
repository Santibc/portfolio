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
            ['nombre' => 'Tomate Chonto',     'cantidad' => 5],
            ['nombre' => 'Cebolla Cabezona',  'cantidad' => 3],
            ['nombre' => 'Cilantro',          'cantidad' => 2],
            ['nombre' => 'Papa Criolla',      'cantidad' => 4],
            ['nombre' => 'Pechuga de Pollo',  'cantidad' => 4],
            ['nombre' => 'Pollo Entero',      'cantidad' => 2],
            ['nombre' => 'Costilla de Cerdo', 'cantidad' => 2],
            ['nombre' => 'Aceite Vegetal',    'cantidad' => 1],
            ['nombre' => 'Arroz Blanco',      'cantidad' => 1],
            ['nombre' => 'Coca-Cola 1.5L',    'cantidad' => 6],
            ['nombre' => 'Detergente',        'cantidad' => 1],
            ['nombre' => 'Servilletas',       'cantidad' => 2],
        ];

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
                ]
            );
            $agregados++;
        }

        $this->command->info("Lista '{$lista->nombre}' sembrada con {$agregados} items.");
    }
}
