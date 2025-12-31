<?php

namespace App\Services;

use App\Models\Empresa;

class AnalyticsService
{
    /**
     * Obtener configuración de analytics de la empresa
     */
    public static function getConfig(): array
    {
        $empresa = Empresa::where('activo', true)->first();

        return [
            'ga4_id' => $empresa->ga4_measurement_id ?? config('analytics.ga4_measurement_id'),
            'fb_pixel_id' => $empresa->fb_pixel_id ?? config('analytics.fb_pixel_id'),
            'gtm_id' => $empresa->gtm_container_id ?? config('analytics.gtm_container_id'),
            'custom_scripts' => $empresa->custom_scripts_head ?? null,
            'enabled' => config('analytics.enabled', true),
        ];
    }

    /**
     * Verificar si GA4 está configurado
     */
    public static function hasGA4(): bool
    {
        $config = self::getConfig();
        return !empty($config['ga4_id']);
    }

    /**
     * Verificar si Facebook Pixel está configurado
     */
    public static function hasFBPixel(): bool
    {
        $config = self::getConfig();
        return !empty($config['fb_pixel_id']);
    }

    /**
     * Verificar si GTM está configurado
     */
    public static function hasGTM(): bool
    {
        $config = self::getConfig();
        return !empty($config['gtm_id']);
    }

    /**
     * Generar datos de producto para eventos de e-commerce
     */
    public static function formatProductForGA4($producto, $cantidad = 1, $index = 0): array
    {
        return [
            'item_id' => $producto->referencia ?? $producto->id,
            'item_name' => $producto->nombre,
            'item_category' => $producto->categoria->nombre ?? 'Sin categoría',
            'item_brand' => 'Flores y Algo Más',
            'price' => (float) $producto->precio_actual,
            'quantity' => $cantidad,
            'index' => $index,
        ];
    }

    /**
     * Generar datos de producto para eventos de Facebook Pixel
     */
    public static function formatProductForFB($producto, $cantidad = 1): array
    {
        return [
            'id' => $producto->referencia ?? $producto->id,
            'name' => $producto->nombre,
            'category' => $producto->categoria->nombre ?? 'Sin categoría',
            'price' => (float) $producto->precio_actual,
            'quantity' => $cantidad,
        ];
    }

    /**
     * Generar datos del carrito para eventos
     */
    public static function formatCartForGA4($items): array
    {
        $formattedItems = [];
        $index = 0;

        foreach ($items as $item) {
            $formattedItems[] = [
                'item_id' => $item['referencia'] ?? $item['producto_id'],
                'item_name' => $item['nombre'],
                'item_category' => $item['categoria'] ?? 'General',
                'item_brand' => 'Flores y Algo Más',
                'price' => (float) $item['precio'],
                'quantity' => $item['cantidad'],
                'index' => $index++,
            ];
        }

        return $formattedItems;
    }

    /**
     * Generar content_ids para Facebook Pixel
     */
    public static function getContentIds($items): array
    {
        return array_map(function ($item) {
            return $item['referencia'] ?? $item['producto_id'];
        }, $items);
    }
}
