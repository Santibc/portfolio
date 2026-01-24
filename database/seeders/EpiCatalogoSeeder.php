<?php

namespace Database\Seeders;

use App\Models\EpiCatalogo;
use Illuminate\Database\Seeder;

class EpiCatalogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            'Proteccion de la cabeza' => [
                ['nombre' => 'Casco de seguridad', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Casco forestal con pantalla y orejeras', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
            ],
            'Proteccion ocular y facial' => [
                ['nombre' => 'Gafas de proteccion', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Pantalla facial', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Gafas de sol polarizadas', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
            ],
            'Proteccion auditiva' => [
                ['nombre' => 'Tapones auditivos reutilizables', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Tapones auditivos desechables', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Orejeras', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
            ],
            'Proteccion respiratoria' => [
                ['nombre' => 'Mascarilla FFP2', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Mascarilla FFP3', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Mascara con filtros intercambiables', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 6],
            ],
            'Proteccion de manos' => [
                ['nombre' => 'Guantes anticorte (motosierra)', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad' => 6],
                ['nombre' => 'Guantes de trabajo mecanico', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Guantes de nitrilo', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Guantes aislantes electricos', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Guantes anticorte nivel 5', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad' => 6],
            ],
            'Proteccion de pies' => [
                ['nombre' => 'Botas de seguridad S3', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Botas anticorte (motosierra)', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad' => 6],
                ['nombre' => 'Botas de agua', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Polainas anticorte', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad' => 6],
            ],
            'Proteccion contra caidas' => [
                ['nombre' => 'Arnes anticaidas', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Cuerda de seguridad', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Absorbedor de energia', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Conector/Mosqueton', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Bloqueador anticaidas', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Linea de vida portatil', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad' => 12],
            ],
            'Ropa de proteccion' => [
                ['nombre' => 'Pantalon anticorte clase 1', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Pantalon anticorte clase 2', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad' => 12],
                ['nombre' => 'Chaleco reflectante', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Chaqueta de alta visibilidad', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Traje de agua', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Mono de trabajo', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
            ],
            'Proteccion quimica' => [
                ['nombre' => 'Traje fitosanitario', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Guantes de aplicador', 'tiene_caducidad' => true, 'requiere_revision' => false, 'periodicidad' => null],
                ['nombre' => 'Botas de aplicador', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad' => null],
            ],
        ];

        foreach ($categorias as $categoria => $epis) {
            foreach ($epis as $epi) {
                EpiCatalogo::firstOrCreate(
                    ['nombre' => $epi['nombre']],
                    [
                        'categoria' => $categoria,
                        'tiene_caducidad' => $epi['tiene_caducidad'],
                        'requiere_revision' => $epi['requiere_revision'],
                        'periodicidad_revision_meses' => $epi['periodicidad'],
                    ]
                );
            }
        }

        $this->command->info('Catalogo de EPIs creado correctamente: ' . EpiCatalogo::count() . ' tipos de EPIs.');
    }
}
