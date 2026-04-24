<?php

namespace Database\Seeders;

use App\Models\Moneda;
use Illuminate\Database\Seeder;

class MonedaSeeder extends Seeder
{
    public function run(): void
    {
        $monedas = [
            ['codigo' => 'COP', 'nombre' => 'Peso Colombiano', 'simbolo' => '$', 'es_predeterminada' => true, 'activa' => true],
            ['codigo' => 'USD', 'nombre' => 'Dólar Estadounidense', 'simbolo' => 'US$', 'es_predeterminada' => false, 'activa' => true],
            ['codigo' => 'EUR', 'nombre' => 'Euro', 'simbolo' => '€', 'es_predeterminada' => false, 'activa' => true],
        ];

        foreach ($monedas as $moneda) {
            Moneda::updateOrCreate(['codigo' => $moneda['codigo']], $moneda);
        }
    }
}
