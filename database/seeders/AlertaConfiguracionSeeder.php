<?php

namespace Database\Seeders;

use App\Models\AlertaConfiguracion;
use Illuminate\Database\Seeder;

class AlertaConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            [
                'tipo' => 'formacion',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'documento_trabajador',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'apto_medico',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'epi_caducidad',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'epi_revision',
                'dias_antelacion' => 15,
                'activa' => true,
            ],
            [
                'tipo' => 'itv',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'seguro_vehiculo',
                'dias_antelacion' => 45,
                'activa' => true,
            ],
            [
                'tipo' => 'documento_vehiculo',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'contrato_vencimiento',
                'dias_antelacion' => 60,
                'activa' => true,
            ],
            [
                'tipo' => 'contrato_garantia',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'documento_cae',
                'dias_antelacion' => 30,
                'activa' => true,
            ],
            [
                'tipo' => 'caducidad_general',
                'dias_antelacion' => 45,
                'activa' => true,
            ],
        ];

        foreach ($configuraciones as $config) {
            AlertaConfiguracion::updateOrCreate(
                ['tipo' => $config['tipo']],
                $config
            );
        }

        $this->command->info('Configuraciones de alertas creadas correctamente.');
    }
}
