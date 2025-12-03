<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaProyecto;

class ProjectCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            [
                'codigo' => 'STAKING',
                'nombre' => 'Staking Agrícola',
                'descripcion' => 'Inversión a plazo fijo con retornos garantizados. Capital bloqueado durante el período acordado.',
                'duracion_minima_meses' => 3,
                'duracion_maxima_meses' => 24,
                'roi_minimo' => 8.00,
                'roi_maximo' => 15.00,
                'inversion_minima' => 100000.00,
                'inversion_maxima' => 50000000.00,
                'permite_retiro_anticipado' => false,
                'permite_trading' => false,
                'activo' => true,
                'orden' => 1
            ],
            [
                'codigo' => 'TRADING',
                'nombre' => 'Trading de Inversiones',
                'descripcion' => 'Posibilidad de comprar y vender inversiones en el mercado secundario.',
                'duracion_minima_meses' => 6,
                'duracion_maxima_meses' => 12,
                'roi_minimo' => 10.00,
                'roi_maximo' => 25.00,
                'inversion_minima' => 500000.00,
                'inversion_maxima' => 100000000.00,
                'permite_retiro_anticipado' => true,
                'permite_trading' => true,
                'activo' => true,
                'orden' => 2
            ],
            [
                'codigo' => 'EAR',
                'nombre' => 'Retiro Anticipado con Penalización',
                'descripcion' => 'Permite retiro anticipado aplicando penalizaciones según el tiempo transcurrido.',
                'duracion_minima_meses' => 6,
                'duracion_maxima_meses' => 18,
                'roi_minimo' => 12.00,
                'roi_maximo' => 20.00,
                'inversion_minima' => 200000.00,
                'inversion_maxima' => 75000000.00,
                'permite_retiro_anticipado' => true,
                'permite_trading' => false,
                'activo' => true,
                'orden' => 3
            ],
            [
                'codigo' => 'FUTUROS',
                'nombre' => 'Contratos a Futuro',
                'descripcion' => 'Inversión en proyectos con cosecha programada. Retornos al finalizar el ciclo agrícola.',
                'duracion_minima_meses' => 4,
                'duracion_maxima_meses' => 12,
                'roi_minimo' => 15.00,
                'roi_maximo' => 35.00,
                'inversion_minima' => 1000000.00,
                'inversion_maxima' => 150000000.00,
                'permite_retiro_anticipado' => false,
                'permite_trading' => true,
                'activo' => true,
                'orden' => 4
            ],
            [
                'codigo' => 'CROSS_FUND',
                'nombre' => 'Fondo Diversificado',
                'descripcion' => 'Inversión distribuida en múltiples proyectos para minimizar riesgos.',
                'duracion_minima_meses' => 6,
                'duracion_maxima_meses' => 18,
                'roi_minimo' => 10.00,
                'roi_maximo' => 18.00,
                'inversion_minima' => 300000.00,
                'inversion_maxima' => 100000000.00,
                'permite_retiro_anticipado' => true,
                'permite_trading' => false,
                'activo' => true,
                'orden' => 5
            ]
        ];

        foreach ($categorias as $categoria) {
            CategoriaProyecto::create($categoria);
        }
    }
}
