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
     * Crea tres templates:
     * - Default: Template clásico y elegante, marcado como predeterminado
     * - Brasilia: Template dinámico inspirado en tiendas de moda
     * - Lima: Template moderno con header sticky y sliders Swiper
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

        // Template Sport - Dinámico para deportes y lifestyle urbano
        TemplateTienda::updateOrCreate(
            ['codigo' => 'sport'],
            [
                'nombre' => 'Template Sport',
                'descripcion' => 'Template dinámico para tiendas de deportes y lifestyle urbano. Diseño moderno tipo skate shop con tipografía bold.',
                'vista_index' => 'tienda.sport_index',
                'vista_categoria' => 'tienda.sport_categoria',
                'vista_producto' => 'tienda.sport_producto',
                'layout' => 'tienda.sport_layout',
                'preview_image' => 'images/templates/sport-preview.jpg',
                'activo' => true,
                'es_default' => false,
                'orden' => 4,
                'configuracion' => [
                    'color_primario' => '#000000',
                    'color_accent' => '#ff0000',
                    'font_heading' => 'Big Shoulders Display',
                    'font_body' => 'Chivo',
                    'mostrar_newsletter' => true,
                    'habilitar_slider_hero' => true,
                ],
            ]
        );

        // Template Recife - Elegante para muebles y decoración
        TemplateTienda::updateOrCreate(
            ['codigo' => 'recife'],
            [
                'nombre' => 'Template Recife',
                'descripcion' => 'Template elegante inspirado en tiendas de muebles y decoración. Diseño limpio y moderno con énfasis en las imágenes.',
                'vista_index' => 'tienda.recife_index',
                'vista_categoria' => 'tienda.recife_categoria',
                'vista_producto' => 'tienda.recife_producto',
                'layout' => 'tienda.recife_layout',
                'preview_image' => 'images/templates/recife-preview.jpg',
                'activo' => true,
                'es_default' => false,
                'orden' => 5,
                'configuracion' => [
                    'color_primario' => '#7c41e8',
                    'color_secundario' => '#333333',
                ],
            ]
        );

        $this->command->info('Templates de tienda creados exitosamente.');
        $this->command->info('- Template Default (código: default) - Marcado como predeterminado');
        $this->command->info('- Template Brasilia (código: brasilia)');
        $this->command->info('- Template Sport (código: sport)');
        $this->command->info('- Template Recife (código: recife)');
    }
}
