<?php

namespace Database\Seeders;

use App\Models\DiaSemana;
use Illuminate\Database\Seeder;

class DiasSemanaSeeder extends Seeder
{
    public function run(): void
    {
        // ISO-8601: 1=Lunes … 7=Domingo (coincide con Carbon::dayOfWeekIso).
        $dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        foreach ($dias as $id => $nombre) {
            DiaSemana::updateOrCreate(['id' => $id], ['nombre' => $nombre]);
        }
    }
}
