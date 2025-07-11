<?php
// database/seeders/PipelineStatusSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PipelineStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Llamada agendada',
            'Reprogramada',
            'Cancelada',
            'No Show',
            'No Califica',
            'Cerrada/Venta hecha',
            'Seguimiento 1',
            'Seguimiento 2',
            'Seguimiento 3',
        ];

        foreach ($statuses as $status) {
            DB::table('pipeline_statuses')->insert([
                'name' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}