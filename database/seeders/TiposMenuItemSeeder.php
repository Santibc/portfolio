<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TipoMenuItem;
use Illuminate\Database\Seeder;

class TiposMenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Menú',      'slug' => 'menu',      'orden' => 1],
            ['nombre' => 'Combos',    'slug' => 'combos',    'orden' => 2],
            ['nombre' => 'Adiciones', 'slug' => 'adiciones', 'orden' => 3],
        ];

        foreach ($tipos as $t) {
            TipoMenuItem::updateOrCreate(['slug' => $t['slug']], $t);
        }
    }
}
