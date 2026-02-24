<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TablaPreciosSeeder extends Seeder
{
    public function run()
    {
        $servicios = [
            ['clave' => 'corte_doblez_hr_cr_galv', 'etiqueta' => 'CORTE DOBLEZ HR CR GALVANIZADO', 'precio_minimo' => 6839],
            ['clave' => 'doblez_inox', 'etiqueta' => 'DOBLEZ INOX', 'precio_minimo' => 7816],
            ['clave' => 'corte_inox', 'etiqueta' => 'CORTE INOX', 'precio_minimo' => 6839],
            ['clave' => 'corte_doblez_aluminio_alfajor', 'etiqueta' => 'CORTE DOBLEZ ALUMINIO LISO Y ALFAJOR', 'precio_minimo' => 7816],
            ['clave' => 'corte_doblez_alfajor_hr', 'etiqueta' => 'CORTE DOBLEZ ALFAJOR HR', 'precio_minimo' => 7295],
            ['clave' => 'corte_doblez_acero_430', 'etiqueta' => 'CORTE DOBLEZ ACERO 430', 'precio_minimo' => 6437],
        ];

        $calibres = [
            ['clave' => '#22', 'mm' => 0.76],
            ['clave' => '#20', 'mm' => 0.91],
            ['clave' => '#18', 'mm' => 1.21],
            ['clave' => '#16', 'mm' => 1.52],
            ['clave' => '#14', 'mm' => 1.90],
            ['clave' => '#12', 'mm' => 2.66],
            ['clave' => '1/8"', 'mm' => 3.18],
            ['clave' => '4mm', 'mm' => 4.00],
            ['clave' => '3/16"', 'mm' => 4.76],
            ['clave' => '1/4"', 'mm' => 6.35],
            ['clave' => '5/16"', 'mm' => 7.94],
            ['clave' => '3/8"', 'mm' => 9.53],
            ['clave' => '1/2"', 'mm' => 12.70],
        ];

        $largos = [
            ['min' => 0, 'max' => 50],
            ['min' => 51, 'max' => 100],
            ['min' => 101, 'max' => 200],
            ['min' => 201, 'max' => null],
        ];

        $cantidades = [
            ['min' => 1, 'max' => 10],
            ['min' => 11, 'max' => 50],
            ['min' => 51, 'max' => 100],
            ['min' => 101, 'max' => 500],
            ['min' => 501, 'max' => 1000],
            ['min' => 1001, 'max' => null],
        ];

        $now = now();
        $records = [];

        foreach ($servicios as $servicio) {
            foreach ($calibres as $calibre) {
                foreach ($largos as $largo) {
                    foreach ($cantidades as $cantidad) {
                        $records[] = [
                            'tipo_servicio' => $servicio['clave'],
                            'etiqueta_servicio' => $servicio['etiqueta'],
                            'clave_calibre' => $calibre['clave'],
                            'calibre_mm' => $calibre['mm'],
                            'largo_rango_min' => $largo['min'],
                            'largo_rango_max' => $largo['max'],
                            'cantidad_rango_min' => $cantidad['min'],
                            'cantidad_rango_max' => $cantidad['max'],
                            'precio' => $servicio['precio_minimo'],
                            'precio_minimo' => $servicio['precio_minimo'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        // Insert en chunks de 500 para rendimiento
        foreach (array_chunk($records, 500) as $chunk) {
            DB::table('tabla_precios_servicios')->insert($chunk);
        }
    }
}
