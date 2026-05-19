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
            ['nombre' => 'Maritza',  'valor_turno_default' => 40000],
            ['nombre' => 'Diana',    'valor_turno_default' => 40000],
            ['nombre' => 'Leonard',  'valor_turno_default' => 60000],
            ['nombre' => 'Jose',     'valor_turno_default' => 60000],
            ['nombre' => 'Alex',     'valor_turno_default' => 40000],
            ['nombre' => 'Camilo',   'valor_turno_default' => 40000],
            ['nombre' => 'Yeny',     'valor_turno_default' => 40000],
            ['nombre' => 'Cristian', 'valor_turno_default' => 40000],
            ['nombre' => 'Michel',   'valor_turno_default' => 60000],
            ['nombre' => 'Andrea',   'valor_turno_default' => 40000],
        ];

        foreach ($trabajadores as $t) {
            TrabajadorTurno::updateOrCreate(
                ['nombre' => $t['nombre']],
                ['valor_turno_default' => $t['valor_turno_default'], 'activo' => true],
            );
        }
    }
}
