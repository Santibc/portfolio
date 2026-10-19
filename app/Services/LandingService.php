<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Prepara los datos de la landing pública a partir de config/landing.php:
 * enlaces de WhatsApp con el pedido ya escrito, mapa, cómo llegar, precios
 * formateados y los datos estructurados para buscadores. No toca la BD.
 */
class LandingService
{
    private const URL_WHATSAPP = 'https://wa.me/';
    private const URL_MAPS     = 'https://www.google.com/maps';

    /** Todo lo que necesita la vista landing.index. */
    public function vista(): array
    {
        return [
            'negocio'            => $this->negocio(),
            'enlaces'            => $this->enlaces(),
            'horario'            => config('landing.horario'),
            'ingredientes'       => config('landing.ingredientes'),
            'referencia'         => config('landing.referencia'),
            'pasos'              => config('landing.pasos'),
            'menu'               => $this->menu(),
            'bases'              => config('landing.bases'),
            'galeria'            => config('landing.galeria'),
            'institucional'      => config('landing.institucional'),
            'seo'                => config('landing.seo'),
            'datosEstructurados' => $this->datosEstructurados(),
        ];
    }

    public function urlWhatsapp(?string $mensaje = null): string
    {
        $mensaje ??= (string) config('landing.negocio.whatsapp_mensaje');

        return self::URL_WHATSAPP . config('landing.negocio.whatsapp') . '?text=' . rawurlencode($mensaje);
    }

    /** WhatsApp con el producto ya escrito en el mensaje. */
    public function urlPedido(string $producto): string
    {
        return $this->urlWhatsapp(sprintf('¡Hola, %s! Quiero pedir: %s.', config('landing.negocio.nombre'), $producto));
    }

    /** Mapa embebible sin API key (iframe). */
    public function urlMapaEmbebido(): string
    {
        return self::URL_MAPS . '?q=' . rawurlencode($this->consultaMapa()) . '&z=17&output=embed';
    }

    public function urlComoLlegar(): string
    {
        return self::URL_MAPS . '/dir/?api=1&destination=' . rawurlencode($this->consultaMapa());
    }

    /** Ficha del lugar en Google Maps (ahí están las opiniones). */
    public function urlEnGoogle(): string
    {
        return config('landing.negocio.google_maps')
            ?: self::URL_MAPS . '/search/?api=1&query=' . rawurlencode($this->consultaMapa());
    }

    public function precioFormateado(?int $precio): ?string
    {
        return $precio === null ? null : '$ ' . number_format($precio, 0, ',', '.');
    }

    private function negocio(): array
    {
        $negocio = config('landing.negocio');

        return $negocio + [
            'calificacion_formateada' => number_format((float) $negocio['google_calificacion'], 1, ',', '.'),
            'telefono_internacional'  => '+57 ' . $negocio['whatsapp_visible'],
        ];
    }

    private function enlaces(): array
    {
        return [
            'whatsapp'    => $this->urlWhatsapp(),
            'mapa'        => $this->urlMapaEmbebido(),
            'como_llegar' => $this->urlComoLlegar(),
            'google'      => $this->urlEnGoogle(),
            'instagram'   => config('landing.negocio.instagram'),
            'tiktok'      => config('landing.negocio.tiktok'),
            'rappi'       => config('landing.negocio.rappi'),
        ];
    }

    private function menu(): array
    {
        $menu = config('landing.menu');

        $menu['dorilokos'] = array_map(fn (array $item): array => $item + [
            'personal_formateado' => $this->precioFormateado($item['personal']),
            'familiar_formateado' => $this->precioFormateado($item['familiar']),
            'url_pedido'          => $this->urlPedido($item['pedido']),
        ], $menu['dorilokos']);

        $menu['combos'] = array_map(fn (array $combo): array => $combo + [
            'precio_formateado' => $this->precioFormateado($combo['precio']),
            'url_pedido'        => $this->urlPedido($combo['nombre']),
        ], $menu['combos']);

        $menu['adiciones'] = array_map(fn (array $adicion): array => $adicion + [
            'precio_formateado' => $this->precioFormateado($adicion['precio']),
        ], $menu['adiciones']);

        $menu['bebidas'] += [
            'desde_formateado' => $this->precioFormateado($menu['bebidas']['desde']),
            'hasta_formateado' => $this->precioFormateado($menu['bebidas']['hasta']),
        ];

        // El producto con etiqueta (el más vendido) encabeza la tabla del inicio.
        $menu['destacado'] = collect($menu['dorilokos'])->first(fn (array $item): bool => $item['etiqueta'] !== null)
            ?? $menu['dorilokos'][0];

        return $menu;
    }

    /** Schema.org Restaurant (sin aggregateRating: Google no admite reseñas propias del negocio). */
    private function datosEstructurados(): array
    {
        $negocio = config('landing.negocio');

        return [
            '@context'      => 'https://schema.org',
            '@type'         => 'Restaurant',
            'name'          => $negocio['nombre'],
            'description'   => config('landing.seo.descripcion'),
            'url'           => url('/'),
            'image'         => asset(config('landing.seo.imagen')),
            'logo'          => asset('images/logo.png'),
            'telephone'     => '+57 ' . $negocio['whatsapp_visible'],
            'servesCuisine' => ['Comida rápida', 'Dorilokos'],
            'priceRange'    => '$',
            'address'       => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $negocio['direccion'],
                'addressLocality' => $negocio['ciudad'],
                'addressRegion'   => 'Bogotá D.C.',
                'postalCode'      => $negocio['codigo_postal'],
                'addressCountry'  => 'CO',
            ],
            'geo'           => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => $negocio['coordenadas']['lat'],
                'longitude' => $negocio['coordenadas']['lng'],
            ],
            'openingHours'  => config('landing.horario.schema'),
            'hasMap'        => $this->urlEnGoogle(),
            'sameAs'        => array_values(array_filter([
                $negocio['instagram'], $negocio['tiktok'], $negocio['rappi'], $negocio['google_maps'],
            ])),
        ];
    }

    private function consultaMapa(): string
    {
        return (string) config('landing.negocio.consulta_mapa');
    }
}
