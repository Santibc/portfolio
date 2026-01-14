<?php

namespace Database\Seeders;

use App\Models\EstiloRamo;
use App\Models\EnvolturaRamo;
use Illuminate\Database\Seeder;

class EstilosEnvolturasRamoSeeder extends Seeder
{
    public function run()
    {
        // Estilos de Ramo
        $estilos = [
            [
                'nombre' => 'Romantico',
                'slug' => 'romantico',
                'descripcion' => 'Ramos delicados con tonos suaves y flores clasicas como rosas, peonias y ranunculos. Perfecto para expresar amor y ternura.',
                'precio_base' => 15000,
                'flores_minimo' => 6,
                'flores_maximo' => 25,
                'icono' => 'bi-heart',
                'color_principal' => '#e91e63',
                'activo' => true,
                'orden' => 1,
            ],
            [
                'nombre' => 'Elegante',
                'slug' => 'elegante',
                'descripcion' => 'Composiciones sofisticadas con lineas limpias y flores premium. Ideal para ocasiones formales y regalos ejecutivos.',
                'precio_base' => 20000,
                'flores_minimo' => 8,
                'flores_maximo' => 30,
                'icono' => 'bi-star',
                'color_principal' => '#9c27b0',
                'activo' => true,
                'orden' => 2,
            ],
            [
                'nombre' => 'Silvestre',
                'slug' => 'silvestre',
                'descripcion' => 'Ramos naturales y espontaneos con flores de campo, follaje verde y texturas variadas. Para quienes aman lo natural.',
                'precio_base' => 12000,
                'flores_minimo' => 5,
                'flores_maximo' => 20,
                'icono' => 'bi-flower2',
                'color_principal' => '#4caf50',
                'activo' => true,
                'orden' => 3,
            ],
        ];

        foreach ($estilos as $estilo) {
            EstiloRamo::updateOrCreate(
                ['slug' => $estilo['slug']],
                $estilo
            );
        }

        // Envolturas de Ramo
        $envolturas = [
            [
                'nombre' => 'Papel Kraft',
                'slug' => 'papel-kraft',
                'descripcion' => 'Envoltura ecologica en papel kraft natural. Sencilla, elegante y amigable con el medio ambiente.',
                'precio_adicional' => 0,
                'icono' => 'bi-recycle',
                'activo' => true,
                'orden' => 1,
            ],
            [
                'nombre' => 'Celofan Transparente',
                'slug' => 'celofan-transparente',
                'descripcion' => 'Envoltura clasica en celofan transparente que permite apreciar todas las flores. Incluye lazo decorativo.',
                'precio_adicional' => 5000,
                'icono' => 'bi-brilliance',
                'activo' => true,
                'orden' => 2,
            ],
            [
                'nombre' => 'Tela Premium',
                'slug' => 'tela-premium',
                'descripcion' => 'Envoltura exclusiva en tela de alta calidad. Presentacion de lujo para ocasiones especiales.',
                'precio_adicional' => 15000,
                'icono' => 'bi-gem',
                'activo' => true,
                'orden' => 3,
            ],
        ];

        foreach ($envolturas as $envoltura) {
            EnvolturaRamo::updateOrCreate(
                ['slug' => $envoltura['slug']],
                $envoltura
            );
        }

        $this->command->info('Estilos y Envolturas de Ramo creados correctamente.');
    }
}
