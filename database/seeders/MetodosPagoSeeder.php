<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodosPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['codigo' => 'efectivo',       'nombre' => 'Efectivo',       'es_efectivo' => true,  'orden' => 1, 'activo' => true],
            ['codigo' => 'daviplata',      'nombre' => 'Daviplata',      'es_efectivo' => false, 'orden' => 2, 'activo' => true],
            ['codigo' => 'bancolombia',    'nombre' => 'Bancolombia',    'es_efectivo' => false, 'orden' => 3, 'activo' => true],
            ['codigo' => 'nequi',          'nombre' => 'Nequi',          'es_efectivo' => false, 'orden' => 4, 'activo' => true],
            ['codigo' => 'qr_bancolombia', 'nombre' => 'QR Bancolombia', 'es_efectivo' => false, 'orden' => 5, 'activo' => true],
        ];

        foreach ($metodos as $m) {
            MetodoPago::updateOrCreate(['codigo' => $m['codigo']], $m);
        }
    }
}
