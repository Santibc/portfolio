<?php

namespace Database\Seeders;

use App\Models\TipoProductoMercado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TiposProductoMercadoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Plaza',
            'Makro',
            'Desechables',
            'Aseo',
            'Salsamentaria',
            'Pescado',
            'Gaseosas',
            'Vísceras',
            'Pollo',
            'Cerdo',
        ];

        foreach ($tipos as $nombre) {
            TipoProductoMercado::firstOrCreate(
                ['nombre' => $nombre],
                ['slug' => Str::slug($nombre)]
            );
        }
    }
}
