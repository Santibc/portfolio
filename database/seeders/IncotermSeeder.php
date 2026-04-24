<?php

namespace Database\Seeders;

use App\Models\Incoterm;
use Illuminate\Database\Seeder;

class IncotermSeeder extends Seeder
{
    public function run(): void
    {
        $incoterms = [
            ['codigo' => 'DDP', 'descripcion' => 'Delivered Duty Paid — entregado con derechos pagados', 'activo' => true],
            ['codigo' => 'FOB', 'descripcion' => 'Free On Board — libre a bordo', 'activo' => true],
            ['codigo' => 'CIF', 'descripcion' => 'Cost, Insurance and Freight — costo, seguro y flete', 'activo' => true],
            ['codigo' => 'EXW', 'descripcion' => 'Ex Works — en fábrica', 'activo' => true],
            ['codigo' => 'DAP', 'descripcion' => 'Delivered At Place — entregado en lugar', 'activo' => true],
            ['codigo' => 'CPT', 'descripcion' => 'Carriage Paid To — transporte pagado hasta', 'activo' => true],
            ['codigo' => 'DPU', 'descripcion' => 'Delivered At Place Unloaded — entregado descargado', 'activo' => true],
        ];

        foreach ($incoterms as $incoterm) {
            Incoterm::updateOrCreate(['codigo' => $incoterm['codigo']], $incoterm);
        }
    }
}
