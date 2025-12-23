<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // Tipos de obra
        DB::table('obra_tipos')->insert([
            ['nombre' => 'Desbroce', 'descripcion' => 'Limpieza y desbroce de vegetación', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tala', 'descripcion' => 'Tala de árboles', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Poda', 'descripcion' => 'Poda de árboles y arbustos', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Herbicida', 'descripcion' => 'Aplicación de herbicidas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Emergencia', 'descripcion' => 'Trabajos de emergencia', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Mixto', 'descripcion' => 'Trabajos combinados', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Tipos de maquinaria
        DB::table('maquinaria_tipos')->insert([
            ['nombre' => 'Motosierra', 'descripcion' => 'Motosierra profesional', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Desbrozadora', 'descripcion' => 'Desbrozadora de mochila o ruedas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Sopladora', 'descripcion' => 'Sopladora de hojas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Motocultor', 'descripcion' => 'Motocultor agrícola', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pulverizador', 'descripcion' => 'Equipo de pulverización de herbicidas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Trituradora', 'descripcion' => 'Trituradora de ramas', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Tipos de vehículo
        DB::table('vehiculo_tipos')->insert([
            ['nombre' => 'Furgoneta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Camión', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tractor', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Todoterreno', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Remolque', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Tipos de contrato
        DB::table('contrato_tipos')->insert([
            ['nombre' => 'Fijo', 'descripcion' => 'Contrato de servicios fijo', 'created_at' => now()],
            ['nombre' => 'Esporádico', 'descripcion' => 'Trabajos puntuales', 'created_at' => now()],
            ['nombre' => 'Servicios', 'descripcion' => 'Contrato de servicios profesionales', 'created_at' => now()],
            ['nombre' => 'Mantenimiento', 'descripcion' => 'Contrato de mantenimiento periódico', 'created_at' => now()],
        ]);

        // Categorías de gasto
        DB::table('gasto_categorias')->insert([
            ['nombre' => 'Personal propio', 'codigo' => 'PERS', 'tipo' => 'directo', 'created_at' => now()],
            ['nombre' => 'Subcontratas', 'codigo' => 'SUBC', 'tipo' => 'directo', 'created_at' => now()],
            ['nombre' => 'Maquinaria', 'codigo' => 'MAQ', 'tipo' => 'directo', 'created_at' => now()],
            ['nombre' => 'Combustible', 'codigo' => 'COMB', 'tipo' => 'directo', 'created_at' => now()],
            ['nombre' => 'Mantenimiento', 'codigo' => 'MANT', 'tipo' => 'directo', 'created_at' => now()],
            ['nombre' => 'EPIs', 'codigo' => 'EPI', 'tipo' => 'directo', 'created_at' => now()],
            ['nombre' => 'Gestoría / Seguros', 'codigo' => 'GEST', 'tipo' => 'indirecto', 'created_at' => now()],
            ['nombre' => 'Penalizaciones', 'codigo' => 'PEN', 'tipo' => 'directo', 'created_at' => now()],
            ['nombre' => 'Otros', 'codigo' => 'OTRO', 'tipo' => 'indirecto', 'created_at' => now()],
        ]);

        // Tipos de formación
        DB::table('formacion_tipos')->insert([
            ['nombre' => 'PRL Básico 60h', 'descripcion' => 'Prevención de Riesgos Laborales nivel básico', 'duracion_horas' => 60, 'periodicidad_meses' => null, 'obligatoria' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Trabajos en Altura', 'descripcion' => 'Formación para trabajos en altura', 'duracion_horas' => 8, 'periodicidad_meses' => 24, 'obligatoria' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Motosierra', 'descripcion' => 'Uso seguro de motosierra', 'duracion_horas' => 16, 'periodicidad_meses' => 36, 'obligatoria' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Desbrozadora', 'descripcion' => 'Uso seguro de desbrozadora', 'duracion_horas' => 8, 'periodicidad_meses' => 36, 'obligatoria' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Aplicador Fitosanitarios', 'descripcion' => 'Carnet de aplicador de productos fitosanitarios', 'duracion_horas' => 25, 'periodicidad_meses' => 120, 'obligatoria' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Primeros Auxilios', 'descripcion' => 'Formación en primeros auxilios', 'duracion_horas' => 8, 'periodicidad_meses' => 24, 'obligatoria' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Espacios Confinados', 'descripcion' => 'Trabajos en espacios confinados', 'duracion_horas' => 8, 'periodicidad_meses' => 24, 'obligatoria' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Catálogo de EPIs
        DB::table('epi_catalogo')->insert([
            ['nombre' => 'Casco forestal', 'categoria' => 'Protección cabeza', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad_revision_meses' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Protector auditivo', 'categoria' => 'Protección auditiva', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad_revision_meses' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Gafas de seguridad', 'categoria' => 'Protección ocular', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad_revision_meses' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pantalla facial', 'categoria' => 'Protección facial', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad_revision_meses' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Guantes anticorte', 'categoria' => 'Protección manos', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad_revision_meses' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Botas anticorte', 'categoria' => 'Protección pies', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad_revision_meses' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pantalón anticorte', 'categoria' => 'Protección piernas', 'tiene_caducidad' => false, 'requiere_revision' => true, 'periodicidad_revision_meses' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Arnés anticaídas', 'categoria' => 'Protección altura', 'tiene_caducidad' => true, 'requiere_revision' => true, 'periodicidad_revision_meses' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Chaleco alta visibilidad', 'categoria' => 'Visibilidad', 'tiene_caducidad' => false, 'requiere_revision' => false, 'periodicidad_revision_meses' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Configuración de alertas
        DB::table('alerta_configuraciones')->insert([
            ['tipo' => 'formacion', 'dias_antelacion' => 30, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'epi', 'dias_antelacion' => 30, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'itv', 'dias_antelacion' => 30, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'seguro', 'dias_antelacion' => 45, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'contrato', 'dias_antelacion' => 60, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'documento_cae', 'dias_antelacion' => 30, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tipo' => 'apto_medico', 'dias_antelacion' => 30, 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
