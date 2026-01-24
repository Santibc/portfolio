<?php

namespace Database\Seeders;

use App\Models\FormacionTipo;
use Illuminate\Database\Seeder;

class FormacionTipoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            // Formaciones Obligatorias PRL
            [
                'nombre' => 'PRL Básico (60h)',
                'descripcion' => 'Curso básico de Prevención de Riesgos Laborales de 60 horas según convenio de la construcción',
                'duracion_horas' => 60,
                'periodicidad_meses' => null,
                'obligatoria' => true,
            ],
            [
                'nombre' => 'PRL Específico Jardinería',
                'descripcion' => 'Formación específica en prevención de riesgos para trabajos de jardinería',
                'duracion_horas' => 20,
                'periodicidad_meses' => 48,
                'obligatoria' => true,
            ],
            [
                'nombre' => 'PRL Trabajos Forestales',
                'descripcion' => 'Formación específica en prevención de riesgos para trabajos forestales',
                'duracion_horas' => 20,
                'periodicidad_meses' => 48,
                'obligatoria' => true,
            ],
            [
                'nombre' => 'PRL Trabajos en Altura',
                'descripcion' => 'Formación para trabajos en altura (poda de árboles, plataformas elevadoras)',
                'duracion_horas' => 8,
                'periodicidad_meses' => 24,
                'obligatoria' => true,
            ],

            // Maquinaria y Equipos
            [
                'nombre' => 'Carnet Carretilla Elevadora',
                'descripcion' => 'Formación para manejo de carretillas elevadoras',
                'duracion_horas' => 20,
                'periodicidad_meses' => 60,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Carnet Plataforma Elevadora (PEMP)',
                'descripcion' => 'Formación para manejo de plataformas elevadoras móviles de personal',
                'duracion_horas' => 8,
                'periodicidad_meses' => 60,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Operador Motosierra',
                'descripcion' => 'Formación para uso profesional de motosierra',
                'duracion_horas' => 16,
                'periodicidad_meses' => 36,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Operador Desbrozadora',
                'descripcion' => 'Formación para uso profesional de desbrozadoras',
                'duracion_horas' => 8,
                'periodicidad_meses' => 36,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Operador Retroexcavadora',
                'descripcion' => 'Formación para manejo de retroexcavadoras y miniexcavadoras',
                'duracion_horas' => 20,
                'periodicidad_meses' => 60,
                'obligatoria' => false,
            ],

            // Fitosanitarios
            [
                'nombre' => 'Carnet Fitosanitario Básico',
                'descripcion' => 'Carnet de aplicador de productos fitosanitarios nivel básico',
                'duracion_horas' => 25,
                'periodicidad_meses' => 120,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Carnet Fitosanitario Cualificado',
                'descripcion' => 'Carnet de aplicador de productos fitosanitarios nivel cualificado',
                'duracion_horas' => 60,
                'periodicidad_meses' => 120,
                'obligatoria' => false,
            ],

            // Primeros Auxilios y Emergencias
            [
                'nombre' => 'Primeros Auxilios',
                'descripcion' => 'Formación en primeros auxilios básicos',
                'duracion_horas' => 8,
                'periodicidad_meses' => 24,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Extinción de Incendios',
                'descripcion' => 'Formación en prevención y extinción de incendios',
                'duracion_horas' => 4,
                'periodicidad_meses' => 24,
                'obligatoria' => false,
            ],

            // Específicas de Jardinería
            [
                'nombre' => 'Poda de Árboles Ornamentales',
                'descripcion' => 'Técnicas de poda y mantenimiento de árboles ornamentales',
                'duracion_horas' => 16,
                'periodicidad_meses' => null,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Instalación de Riego',
                'descripcion' => 'Instalación y mantenimiento de sistemas de riego',
                'duracion_horas' => 20,
                'periodicidad_meses' => null,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Tratamientos Fitosanitarios',
                'descripcion' => 'Aplicación de tratamientos fitosanitarios en jardinería',
                'duracion_horas' => 16,
                'periodicidad_meses' => null,
                'obligatoria' => false,
            ],

            // Trepa y Trabajos Verticales
            [
                'nombre' => 'Trepa de Árboles',
                'descripcion' => 'Técnicas de trepa y trabajo en árboles con cuerdas',
                'duracion_horas' => 40,
                'periodicidad_meses' => 36,
                'obligatoria' => false,
            ],
            [
                'nombre' => 'Trabajos Verticales',
                'descripcion' => 'Formación en técnicas de acceso por cuerdas',
                'duracion_horas' => 40,
                'periodicidad_meses' => 36,
                'obligatoria' => false,
            ],
        ];

        foreach ($tipos as $tipo) {
            FormacionTipo::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}
