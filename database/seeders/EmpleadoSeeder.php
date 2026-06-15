<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $metodo = MetodoPago::where('es_efectivo', true)->value('id')
            ?? MetodoPago::orderBy('orden')->value('id');

        $auxilio = (int) config('nomina.auxilio_transporte');
        $pctSalud = (int) config('nomina.porcentaje_salud');
        $pctPension = (int) config('nomina.porcentaje_pension');

        // Empleados reales de la hoja NOMINA (salario corregido al SMMLV 2026
        // donde el Excel traía un typo: 1.750.095 -> 1.750.905).
        $empleados = [
            ['nombre' => 'Luz Yamile Chaparro Cardona', 'documento' => '52000001', 'cargo' => 'Administradora', 'salario' => 5_000_000, 'bono' => 500_000, 'auxilio' => false, 'fondo_p' => 'Protección', 'fondo_c' => 'Protección', 'ingreso' => '2023-07-01'],
            ['nombre' => 'Luz Mery Pachón Jiménez',     'documento' => '52000002', 'cargo' => 'Cocina',        'salario' => 3_150_905, 'bono' => 0,       'auxilio' => true,  'fondo_p' => 'Porvenir',   'fondo_c' => 'Porvenir',   'ingreso' => '2023-07-01'],
            ['nombre' => 'Diana Andrea Vega Camacho',   'documento' => '52000004', 'cargo' => 'Mesera',        'salario' => 1_750_905, 'bono' => 150_000, 'auxilio' => true,  'fondo_p' => 'Protección', 'fondo_c' => 'Protección', 'ingreso' => '2025-01-02'],
            ['nombre' => 'Velenice Díaz Pardo',         'documento' => '52000005', 'cargo' => 'Mesera',        'salario' => 1_750_905, 'bono' => 150_000, 'auxilio' => true,  'fondo_p' => 'Porvenir',   'fondo_c' => 'Porvenir',   'ingreso' => '2025-01-02'],
            ['nombre' => 'Luz Dary Preciado Sánchez',   'documento' => '52000006', 'cargo' => 'Auxiliar',      'salario' => 1_750_905, 'bono' => 0,       'auxilio' => true,  'fondo_p' => 'Protección', 'fondo_c' => 'Protección', 'ingreso' => '2025-03-01'],
        ];

        foreach ($empleados as $e) {
            Empleado::updateOrCreate(
                ['documento' => $e['documento']],
                [
                    'metodo_pago_id' => $metodo,
                    'nombre' => $e['nombre'],
                    'cargo' => $e['cargo'],
                    'salario_base' => $e['salario'],
                    'auxilio_transporte' => $auxilio,
                    'tiene_auxilio' => $e['auxilio'],
                    'bono_default' => $e['bono'],
                    'porcentaje_salud' => $pctSalud,
                    'porcentaje_pension' => $pctPension,
                    'eps' => 'Sura',
                    'fondo_pension' => $e['fondo_p'],
                    'fondo_cesantias' => $e['fondo_c'],
                    'fecha_ingreso' => $e['ingreso'],
                    'activo' => true,
                ]
            );
        }
    }
}
