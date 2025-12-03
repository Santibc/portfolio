<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReglaPenalizacion;
use App\Models\CategoriaProyecto;

class PenaltyRulesSeeder extends Seeder
{
    public function run()
    {
        // Obtener categorías
        $ear = CategoriaProyecto::where('codigo', 'EAR')->first();
        $trading = CategoriaProyecto::where('codigo', 'TRADING')->first();
        $crossFund = CategoriaProyecto::where('codigo', 'CROSS_FUND')->first();

        $reglas = [
            // Reglas para EAR (Early Anticipated Retirement)
            [
                'categoria_id' => $ear->id,
                'nombre' => 'Penalización Retiro Anticipado 0-3 meses',
                'descripcion' => 'Penalización del 50% sobre dividendos para retiros anticipados en los primeros 3 meses',
                'tipo_penalizacion' => 'porcentaje_fijo',
                'valor' => 50.00,
                'aplica_desde_mes' => 0,
                'aplica_hasta_mes' => 3,
                'pierde_capital' => false,
                'pierde_dividendos' => true,
                'permite_venta_posicion' => false,
                'activo' => true,
                'orden' => 1
            ],
            [
                'categoria_id' => $ear->id,
                'nombre' => 'Penalización Retiro Anticipado 4-6 meses',
                'descripcion' => 'Penalización del 30% sobre dividendos para retiros anticipados entre 4 y 6 meses',
                'tipo_penalizacion' => 'porcentaje_fijo',
                'valor' => 30.00,
                'aplica_desde_mes' => 4,
                'aplica_hasta_mes' => 6,
                'pierde_capital' => false,
                'pierde_dividendos' => true,
                'permite_venta_posicion' => false,
                'activo' => true,
                'orden' => 2
            ],
            [
                'categoria_id' => $ear->id,
                'nombre' => 'Penalización Retiro Anticipado 7+ meses',
                'descripcion' => 'Penalización del 10% sobre dividendos para retiros anticipados después de 6 meses',
                'tipo_penalizacion' => 'porcentaje_fijo',
                'valor' => 10.00,
                'aplica_desde_mes' => 7,
                'aplica_hasta_mes' => 18,
                'pierde_capital' => false,
                'pierde_dividendos' => false,
                'permite_venta_posicion' => true,
                'activo' => true,
                'orden' => 3
            ],

            // Reglas para TRADING
            [
                'categoria_id' => $trading->id,
                'nombre' => 'Sin Penalización - Venta en Mercado',
                'descripcion' => 'No aplica penalización para trading, se cobra comisión de plataforma',
                'tipo_penalizacion' => 'porcentaje_fijo',
                'valor' => 0.00,
                'aplica_desde_mes' => 0,
                'aplica_hasta_mes' => 12,
                'pierde_capital' => false,
                'pierde_dividendos' => false,
                'permite_venta_posicion' => true,
                'activo' => true,
                'orden' => 1
            ],

            // Reglas para CROSS FUND
            [
                'categoria_id' => $crossFund->id,
                'nombre' => 'Penalización Retiro Anticipado Cross Fund',
                'descripcion' => 'Penalización del 15% sobre dividendos para retiros anticipados del fondo diversificado',
                'tipo_penalizacion' => 'porcentaje_fijo',
                'valor' => 15.00,
                'aplica_desde_mes' => 0,
                'aplica_hasta_mes' => 18,
                'pierde_capital' => false,
                'pierde_dividendos' => true,
                'permite_venta_posicion' => false,
                'activo' => true,
                'orden' => 1
            ]
        ];

        foreach ($reglas as $regla) {
            ReglaPenalizacion::create($regla);
        }
    }
}
