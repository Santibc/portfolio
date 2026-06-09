<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PaisesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/paises.json');

        if (! File::exists($path)) {
            $this->command->warn("No se encontró el archivo de países: {$path}");

            return;
        }

        /** @var array<int, array{name: string, alpha2?: string, alpha3?: string}> $paises */
        $paises = json_decode(File::get($path), true) ?? [];

        foreach ($paises as $pais) {
            Pais::updateOrCreate(
                ['nombre' => $pais['name']],
                [
                    'iso2' => isset($pais['alpha2']) ? strtoupper($pais['alpha2']) : null,
                    'iso3' => isset($pais['alpha3']) ? strtoupper($pais['alpha3']) : null,
                    'activo' => true,
                ]
            );
        }

        $this->command->info(count($paises).' países cargados.');
    }
}
