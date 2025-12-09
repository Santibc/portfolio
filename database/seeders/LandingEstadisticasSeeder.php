<?php

namespace Database\Seeders;

use App\Models\LandingEstadistica;
use Illuminate\Database\Seeder;

class LandingEstadisticasSeeder extends Seeder
{
    public function run()
    {
        LandingEstadistica::create([
            'numero' => '500+',
            'etiqueta' => 'Emprendedores',
            'orden' => 1,
            'activo' => true,
        ]);

        LandingEstadistica::create([
            'numero' => '50+',
            'etiqueta' => 'Fundaciones',
            'orden' => 2,
            'activo' => true,
        ]);

        LandingEstadistica::create([
            'numero' => '20+',
            'etiqueta' => 'Eventos',
            'orden' => 3,
            'activo' => true,
        ]);

        LandingEstadistica::create([
            'numero' => '10,000+',
            'etiqueta' => 'Visitantes',
            'orden' => 4,
            'activo' => true,
        ]);
    }
}
