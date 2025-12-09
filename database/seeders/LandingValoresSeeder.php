<?php

namespace Database\Seeders;

use App\Models\LandingValor;
use Illuminate\Database\Seeder;

class LandingValoresSeeder extends Seeder
{
    public function run()
    {
        LandingValor::create([
            'icono' => 'bi bi-people-fill',
            'titulo' => 'Comunidad',
            'descripcion' => 'Creemos en el poder de la colaboración. Juntos logramos más que solos.',
            'orden' => 1,
            'activo' => true,
        ]);

        LandingValor::create([
            'icono' => 'bi bi-lightbulb-fill',
            'titulo' => 'Innovación',
            'descripcion' => 'Buscamos constantemente nuevas formas de ayudar a nuestros miembros a crecer.',
            'orden' => 2,
            'activo' => true,
        ]);

        LandingValor::create([
            'icono' => 'bi bi-heart-fill',
            'titulo' => 'Compromiso',
            'descripcion' => 'Estamos comprometidos con el éxito de cada emprendedor que confía en nosotros.',
            'orden' => 3,
            'activo' => true,
        ]);

        LandingValor::create([
            'icono' => 'bi bi-shield-check',
            'titulo' => 'Transparencia',
            'descripcion' => 'Actuamos con honestidad y claridad en todas nuestras relaciones.',
            'orden' => 4,
            'activo' => true,
        ]);
    }
}
