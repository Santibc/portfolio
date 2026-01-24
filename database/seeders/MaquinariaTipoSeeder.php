<?php

namespace Database\Seeders;

use App\Models\MaquinariaTipo;
use Illuminate\Database\Seeder;

class MaquinariaTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Motosierra', 'descripcion' => 'Sierra de cadena motorizada para corte de madera'],
            ['nombre' => 'Desbrozadora', 'descripcion' => 'Herramienta para limpieza de vegetacion'],
            ['nombre' => 'Sopladora', 'descripcion' => 'Equipo para limpieza con aire'],
            ['nombre' => 'Cortasetos', 'descripcion' => 'Herramienta para poda de setos y arbustos'],
            ['nombre' => 'Motoguadaña', 'descripcion' => 'Desbrozadora profesional de alta potencia'],
            ['nombre' => 'Biotrituradora', 'descripcion' => 'Maquina para triturar restos vegetales'],
            ['nombre' => 'Ahoyadora', 'descripcion' => 'Herramienta para hacer hoyos en el suelo'],
            ['nombre' => 'Pulverizador', 'descripcion' => 'Equipo para aplicacion de tratamientos fitosanitarios'],
            ['nombre' => 'Plataforma Elevadora', 'descripcion' => 'Equipo de elevacion para trabajos en altura'],
            ['nombre' => 'Mini Excavadora', 'descripcion' => 'Excavadora compacta para trabajos forestales'],
            ['nombre' => 'Dumper', 'descripcion' => 'Vehiculo de carga para transporte de materiales'],
            ['nombre' => 'Grupo Electrogeno', 'descripcion' => 'Generador electrico portatil'],
            ['nombre' => 'Compresor', 'descripcion' => 'Equipo para aire comprimido'],
            ['nombre' => 'Martillo Hidraulico', 'descripcion' => 'Herramienta de percusion para demolicion'],
        ];

        foreach ($tipos as $tipo) {
            MaquinariaTipo::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                ['descripcion' => $tipo['descripcion']]
            );
        }
    }
}
