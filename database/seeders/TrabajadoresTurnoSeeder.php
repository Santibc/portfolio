<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TrabajadorTurno;
use Illuminate\Database\Seeder;

class TrabajadoresTurnoSeeder extends Seeder
{
    public function run(): void
    {
        $trabajadores = [
            ['nombre' => 'Maritza',  'valor_turno_default' => 40000, 'valor_ahorro_default' => 10000],
            ['nombre' => 'Diana',    'valor_turno_default' => 40000, 'valor_ahorro_default' => 10000],
            ['nombre' => 'Leonard',  'valor_turno_default' => 60000, 'valor_ahorro_default' => 15000],
            ['nombre' => 'Jose',     'valor_turno_default' => 60000, 'valor_ahorro_default' => 15000],
            ['nombre' => 'Alex',     'valor_turno_default' => 40000, 'valor_ahorro_default' => 5000],
            ['nombre' => 'Camilo',   'valor_turno_default' => 40000, 'valor_ahorro_default' => 10000],
            ['nombre' => 'Yeny',     'valor_turno_default' => 40000, 'valor_ahorro_default' => 0],
            ['nombre' => 'Cristian', 'valor_turno_default' => 40000, 'valor_ahorro_default' => 10000],
            ['nombre' => 'Michel',   'valor_turno_default' => 60000, 'valor_ahorro_default' => 20000],
            ['nombre' => 'Andrea',   'valor_turno_default' => 40000, 'valor_ahorro_default' => 5000],
        ];

        foreach ($trabajadores as $t) {
            TrabajadorTurno::updateOrCreate(
                ['nombre' => $t['nombre']],
                [
                    'valor_turno_default'  => $t['valor_turno_default'],
                    'valor_ahorro_default' => $t['valor_ahorro_default'],
                    'activo'               => true,
                ],
            );
        }
    }
}
