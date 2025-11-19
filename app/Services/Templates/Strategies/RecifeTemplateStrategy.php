<?php

namespace App\Services\Templates\Strategies;

use App\Services\Templates\AbstractTemplateStrategy;

/**
 * Class RecifeTemplateStrategy
 *
 * Estrategia para el template Recife.
 * Template elegante inspirado en tiendas de muebles y decoración
 * con diseño limpio y moderno.
 *
 * @package App\Services\Templates\Strategies
 */
class RecifeTemplateStrategy extends AbstractTemplateStrategy
{
    /**
     * @var string Vista index
     */
    protected string $viewIndex = 'tienda.recife_index';

    /**
     * @var string Vista categoría
     */
    protected string $viewCategoria = 'tienda.recife_categoria';

    /**
     * @var string Vista producto
     */
    protected string $viewProducto = 'tienda.recife_producto';

    /**
     * @var string Layout base
     */
    protected string $layout = 'tienda.recife_layout';

    /**
     * {@inheritdoc}
     */
    public function getAssets(): array
    {
        return [
            'css' => [
                asset('css/recife-fixes.css'),
            ],
            'js' => [],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Recife puede necesitar preparar datos de forma específica.
     */
    public function prepareData(array $data): array
    {
        // Obtener configuraciones del template
        $colorPrimario = $this->getConfigValue('color_primario', '#7c41e8');
        $colorSecundario = $this->getConfigValue('color_secundario', '#333333');

        // Agregar configuraciones específicas de Recife al array de datos
        $data['recifeConfig'] = [
            'colorPrimario' => $colorPrimario,
            'colorSecundario' => $colorSecundario,
        ];

        return $data;
    }
}
