<?php

namespace App\Services\Templates\Strategies;

use App\Services\Templates\AbstractTemplateStrategy;

/**
 * Class SportTemplateStrategy
 *
 * Estrategia para el template Sport.
 * Template dinámico para tiendas de deportes y lifestyle urbano,
 * con diseño moderno tipo skate shop.
 *
 * @package App\Services\Templates\Strategies
 */
class SportTemplateStrategy extends AbstractTemplateStrategy
{
    /**
     * @var string Vista index
     */
    protected string $viewIndex = 'tienda.sport_index';

    /**
     * @var string Vista categoría
     */
    protected string $viewCategoria = 'tienda.sport_categoria';

    /**
     * @var string Vista producto
     */
    protected string $viewProducto = 'tienda.sport_producto';

    /**
     * @var string Layout base
     */
    protected string $layout = 'tienda.sport_layout';

    /**
     * {@inheritdoc}
     */
    public function getAssets(): array
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Sport puede necesitar preparar datos de forma específica.
     * Por ejemplo, agregar configuraciones específicas del template.
     */
    public function prepareData(array $data): array
    {
        // Obtener configuraciones del template
        $showNewsletter = $this->getConfigValue('mostrar_newsletter', true);
        $enableHeroSlider = $this->getConfigValue('habilitar_slider_hero', true);

        // Agregar configuraciones específicas de Sport al array de datos
        $data['sportConfig'] = [
            'showNewsletter' => $showNewsletter,
            'enableHeroSlider' => $enableHeroSlider,
            'colorPrimario' => $this->getConfigValue('color_primario', '#000000'),
            'colorAccent' => $this->getConfigValue('color_accent', '#ff0000'),
            'fontHeading' => $this->getConfigValue('font_heading', 'Big Shoulders Display'),
            'fontBody' => $this->getConfigValue('font_body', 'Chivo'),
        ];

        return $data;
    }
}
