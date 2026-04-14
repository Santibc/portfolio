<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetricasPanelSeeder extends Seeder
{
    public function run()
    {
        $metricas = [
            'admin' => [
                'ordenes_activas' => true,
                'entregas_vencidas' => true,
                'saldo_pendiente_total' => true,
                'recaudado_hoy' => true,
                'garantias_activas' => true,
                'pagos_por_aprobar' => true,
                'ordenes_hoy' => true,
            ],
            'recepcion' => [
                'entregas_hoy' => true,
                'entregas_hoy_manana' => true,
                'entregas_vencidas' => true,
                'ordenes_abiertas' => true,
                'saldo_pendiente' => true,
                'para_complementar' => true,
                'garantias_activas' => true,
            ],
            'operario' => [
                'ordenes_asignadas' => true,
                'piezas_en_proceso' => true,
                'para_complementar' => true,
                'completadas_hoy' => true,
                'garantias_pendientes' => true,
            ],
            'contabilidad' => [
                'ordenes_con_saldo' => true,
                'abonos_por_aprobar' => true,
                'total_pendiente' => true,
                'recaudado_hoy' => true,
                'ultimos_pagos' => true,
                'recaudo_por_metodo' => true,
            ],
        ];

        DB::table('configuracion_sistema')->updateOrInsert(
            ['clave' => 'metricas_panel_visibles'],
            [
                'valor' => json_encode($metricas),
                'tipo' => 'json',
                'descripcion' => 'Metricas visibles en el panel Inicio por rol',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        cache()->forget('config_sistema.metricas_panel_visibles');
    }
}
