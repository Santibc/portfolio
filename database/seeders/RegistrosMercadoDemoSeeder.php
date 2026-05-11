<?php

namespace Database\Seeders;

use App\Models\ProductoMercado;
use App\Models\RegistroMercado;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RegistrosMercadoDemoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = ProductoMercado::with('tipo')->activos()->get();

        if ($productos->isEmpty()) {
            $this->command->error('No hay productos activos. Ejecuta primero ProductosMercadoDemoSeeder.');
            return;
        }

        $diasAtras = 60;
        $registrosTotal = 150;

        $registros = [];
        for ($i = 0; $i < $registrosTotal; $i++) {
            $producto = $productos->random();
            $fecha = Carbon::now()
                ->subDays(rand(0, $diasAtras))
                ->setTime(rand(6, 18), rand(0, 59));

            $valorBase = match ($producto->tipo->nombre ?? '') {
                'Pollo', 'Pescado', 'Cerdo' => rand(15000, 80000),
                'Plaza'                     => rand(2000, 25000),
                'Makro'                     => rand(8000, 60000),
                'Vísceras'                  => rand(10000, 30000),
                'Gaseosas'                  => rand(3000, 15000),
                'Salsamentaria'             => rand(8000, 35000),
                'Aseo', 'Desechables'       => rand(5000, 25000),
                default                     => rand(5000, 30000),
            };

            $valor = (int) round($valorBase * rand(80, 120) / 100);

            $registros[] = [
                'producto_mercado_id' => $producto->id,
                'cantidad'            => rand(1, 20),
                'valor'               => $valor,
                'created_at'          => $fecha,
                'updated_at'          => $fecha,
            ];
        }

        RegistroMercado::insert($registros);

        $this->command->info("Sembrados {$registrosTotal} registros distribuidos en los últimos {$diasAtras} días.");
    }
}
