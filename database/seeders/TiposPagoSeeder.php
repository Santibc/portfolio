<?php

namespace Database\Seeders;

use App\Models\TipoPago;
use Illuminate\Database\Seeder;

class TiposPagoSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['codigo' => 'efectivo',      'nombre' => 'Efectivo',      'color' => 'success',   'icono' => 'bi-cash',         'orden' => 1],
            ['codigo' => 'nequi',         'nombre' => 'Nequi',         'color' => 'purple',    'icono' => 'bi-phone',        'orden' => 2],
            ['codigo' => 'transferencia', 'nombre' => 'Transferencia', 'color' => 'info',      'icono' => 'bi-bank',         'orden' => 3],
            ['codigo' => 'tarjeta',       'nombre' => 'Tarjeta',       'color' => 'warning',   'icono' => 'bi-credit-card',  'orden' => 4],
            ['codigo' => 'otro',          'nombre' => 'Otro',          'color' => 'secondary', 'icono' => 'bi-three-dots',   'orden' => 5],
        ];

        foreach ($tipos as $tipo) {
            TipoPago::updateOrCreate(
                ['codigo' => $tipo['codigo']],
                array_merge($tipo, ['activo' => true])
            );
        }
    }
}
