<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateTienda;

/**
 * TemplateTiendaSeeder
 *
 * Seeder para poblar la tabla de templates de tienda con los templates
 * predeterminados del sistema: Default y Brasilia.
 *
 * @package Database\Seeders
 */
class TemplateTiendaSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para crear los templates iniciales
     *
     * Crea dos templates:
     * - Default: Template clásico y elegante, marcado como predeterminado
     * - Brasilia: Template dinámico inspirado en tiendas de moda
     *
     * Utiliza updateOrCreate para evitar duplicados en ejecuciones múltiples.
     *
     * @return void
     */
    public function run(): void
    {
        // Template Default - Clásico y elegante
        TemplateTienda::updateOrCreate(
            ['codigo' => 'default'],
            [
                'nombre' => 'Template Clásico',
                'descripcion' => 'Template elegante y moderno con diseño limpio. Ideal para todo tipo de productos.',
                'vista_index' => 'tienda.index',
                'vista_categoria' => 'tienda.categoria',
                'vista_producto' => 'tienda.producto',
                'layout' => 'tienda.layout',
                'preview_image' => 'images/templates/default-preview.jpg',
                'activo' => true,
                'es_default' => true,
                'orden' => 1,
                'configuracion' => [
                    'color_primario' => '#0d6efd',
                    'fuente_principal' => 'Roboto',
                ],
            ]
        );

        // Template Brasilia - Dinámico y vibrante
        TemplateTienda::updateOrCreate(
            ['codigo' => 'brasilia'],
            [
                'nombre' => 'Template Brasilia',
                'descripcion' => 'Template dinámico inspirado en tiendas de moda. Con carruseles, animaciones y diseño vibrante.',
                'vista_index' => 'tienda.brasilia_index',
                'vista_categoria' => 'tienda.brasilia_categoria',
                'vista_producto' => 'tienda.brasilia_producto',
                'layout' => 'tienda.brasilia_layout',
                'preview_image' => 'images/templates/brasilia-preview.jpg',
                'activo' => true,
                'es_default' => false,
                'orden' => 2,
                'configuracion' => [
                    'color_primario' => '#1a1a1a',
                    'color_secundario' => '#78b13f',
                    'mostrar_adbars' => true,
                    'habilitar_animaciones' => true,
                ],
            ]
        );

        $this->command->info('Templates de tienda creados exitosamente.');
        $this->command->info('- Template Default (código: default) - Marcado como predeterminado');
        $this->command->info('- Template Brasilia (código: brasilia)');
    }
}
