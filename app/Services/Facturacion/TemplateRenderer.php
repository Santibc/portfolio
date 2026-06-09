<?php

namespace App\Services\Facturacion;

use App\Models\PlantillaFactura;
use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Renderiza una plantilla HTML reemplazando marcadores tipo {{variable.sub}}.
 *
 * Soporta dos mecanismos de loop:
 * 1. `<tr data-loop="items">...</tr>` (preferido) — HTML válido, resistente a sanitizadores de WYSIWYG.
 * 2. `{{#each items}}...{{/each}}` (legacy) — mantenido para retrocompatibilidad.
 *
 * NO ejecuta PHP/eval — sustitución textual con escape HTML.
 */
class TemplateRenderer
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(PlantillaFactura $plantilla, array $data): string
    {
        $html = $this->procesarDataLoop($plantilla->html_content, $data);
        $html = $this->procesarLoops($html, $data);
        $html = $this->procesarVariables($html, $data);

        if (! empty($plantilla->css_content)) {
            $html = '<style>'.strip_tags($plantilla->css_content).'</style>'.$html;
        }

        return $html;
    }

    public function sanitizar(string $html): string
    {
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html) ?? $html;
        $html = preg_replace('/\son[a-z]+="[^"]*"/i', '', $html) ?? $html;
        $html = preg_replace('/\son[a-z]+=\'[^\']*\'/i', '', $html) ?? $html;
        $html = preg_replace('/javascript:/i', '', $html) ?? $html;

        return $html;
    }

    /**
     * Procesa elementos con atributo `data-loop="nombreArray"`.
     * Duplica el nodo por cada item y reemplaza variables dentro con el contexto del item.
     *
     * @param  array<string, mixed>  $data
     */
    private function procesarDataLoop(string $html, array $data): string
    {
        if (stripos($html, 'data-loop') === false) {
            return $html;
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        // loadHTML necesita el meta charset para interpretar UTF-8 correctamente.
        $doc->loadHTML(
            '<?xml encoding="UTF-8"><div id="__wrapper__">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $nodos = $xpath->query('//*[@data-loop]');

        if ($nodos === false || $nodos->length === 0) {
            return $html;
        }

        foreach (iterator_to_array($nodos) as $nodo) {
            if (! $nodo instanceof DOMElement) {
                continue;
            }

            $clave = (string) $nodo->getAttribute('data-loop');
            $items = $this->resolver($clave, $data);

            if (! is_array($items) || $items === []) {
                // No hay items: se elimina el nodo template.
                $nodo->parentNode?->removeChild($nodo);

                continue;
            }

            $parent = $nodo->parentNode;
            if ($parent === null) {
                continue;
            }

            foreach ($items as $index => $item) {
                $contexto = is_array($item) ? $item : [];
                $contexto['@index'] = $index + 1;
                $contexto['@first'] = $index === 0;
                $contexto['@last'] = $index === count($items) - 1;

                $clon = $nodo->cloneNode(true);
                if (! $clon instanceof DOMElement) {
                    continue;
                }
                $clon->removeAttribute('data-loop');
                $this->reemplazarEnNodo($clon, $contexto, $data);

                $parent->insertBefore($clon, $nodo);
            }

            $parent->removeChild($nodo);
        }

        $wrapper = $doc->getElementById('__wrapper__');
        if ($wrapper === null) {
            return $html;
        }

        $salida = '';
        foreach ($wrapper->childNodes as $hijo) {
            $salida .= $doc->saveHTML($hijo);
        }

        // DOMDocument URL-encodea las llaves en atributos (`{{` → `%7B%7B`).
        // Las restauramos para que procesarVariables pueda reemplazarlas.
        return str_ireplace(['%7B', '%7D'], ['{', '}'], $salida);
    }

    /**
     * Reemplaza {{variable}} dentro de un nodo y sus descendientes,
     * consultando primero el contexto del item y cayendo al contexto global.
     *
     * @param  array<string, mixed>  $contexto
     * @param  array<string, mixed>  $data
     */
    private function reemplazarEnNodo(DOMElement $nodo, array $contexto, array $data): void
    {
        $callback = function (array $m) use ($contexto, $data): string {
            $valor = $this->resolver($m[1], $contexto);
            if ($valor === null) {
                $valor = $this->resolver($m[1], $data);
            }

            return $this->escapar($valor);
        };

        $this->walkText($nodo, function (string $texto) use ($callback): string {
            return preg_replace_callback('/\{\{\s*([@a-zA-Z0-9_\.]+)\s*\}\}/', $callback, $texto) ?? $texto;
        });

        foreach ($nodo->attributes ?? [] as $attr) {
            $attr->value = preg_replace_callback('/\{\{\s*([@a-zA-Z0-9_\.]+)\s*\}\}/', $callback, $attr->value) ?? $attr->value;
        }
    }

    /**
     * Recorre recursivamente los text nodes y les aplica un transformador.
     *
     * @param  callable(string): string  $fn
     */
    private function walkText(\DOMNode $nodo, callable $fn): void
    {
        foreach ($nodo->childNodes as $hijo) {
            if ($hijo->nodeType === XML_TEXT_NODE) {
                $hijo->nodeValue = $fn((string) $hijo->nodeValue);
            } elseif ($hijo->hasChildNodes()) {
                $this->walkText($hijo, $fn);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function procesarVariables(string $html, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([@a-zA-Z0-9_\.]+)\s*\}\}/', function (array $match) use ($data): string {
            $valor = $this->resolver($match[1], $data);

            // Campos terminados en _html o _raw se insertan sin escapar
            // (ej: factura.qr_html → permite un <img> completo para el QR de Siigo).
            if ($this->esRaw($match[1])) {
                return $this->raw($valor);
            }

            return $this->escapar($valor);
        }, $html) ?? $html;
    }

    private function esRaw(string $clave): bool
    {
        return str_ends_with($clave, '_html') || str_ends_with($clave, '_raw');
    }

    /**
     * @param  mixed  $valor
     */
    private function raw($valor): string
    {
        if ($valor === null || is_array($valor) || is_object($valor)) {
            return '';
        }

        return (string) $valor;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function procesarLoops(string $html, array $data): string
    {
        $pattern = '/\{\{#each\s+([a-zA-Z0-9_\.]+)\s*\}\}([\s\S]*?)\{\{\/each\}\}/';

        return preg_replace_callback($pattern, function (array $match) use ($data): string {
            $items = $this->resolver($match[1], $data);
            if (! is_array($items)) {
                return '';
            }

            $plantillaItem = $match[2];
            $salida = '';

            foreach ($items as $index => $item) {
                $contexto = is_array($item) ? $item : [];
                $contexto['@index'] = $index + 1;
                $contexto['@first'] = $index === 0;
                $contexto['@last'] = $index === count($items) - 1;

                $salida .= preg_replace_callback('/\{\{\s*([@a-zA-Z0-9_\.]+)\s*\}\}/', function (array $m) use ($contexto, $data): string {
                    $valor = $this->resolver($m[1], $contexto);
                    if ($valor === null || $valor === '') {
                        $valor = $this->resolver($m[1], $data);
                    }

                    return $this->escapar($valor);
                }, $plantillaItem) ?? $plantillaItem;
            }

            return $salida;
        }, $html) ?? $html;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    private function resolver(string $ruta, array $data)
    {
        $partes = explode('.', $ruta);
        $valor = $data;

        foreach ($partes as $parte) {
            if (is_array($valor) && array_key_exists($parte, $valor)) {
                $valor = $valor[$parte];
            } elseif (is_object($valor) && isset($valor->{$parte})) {
                $valor = $valor->{$parte};
            } else {
                return null;
            }
        }

        return $valor;
    }

    /**
     * @param  mixed  $valor
     */
    private function escapar($valor): string
    {
        if ($valor === null) {
            return '';
        }

        if (is_bool($valor)) {
            return $valor ? 'true' : 'false';
        }

        if (is_array($valor) || is_object($valor)) {
            return '';
        }

        return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * @return array<string, mixed>
     */
    public function datosDummy(): array
    {
        $config = app(\App\Services\Settings\ConfigService::class);

        return [
            'empresa' => app(\App\Services\Settings\EmpresaData::class)->paraPlantilla(),
            'cliente' => [
                'nombre' => 'Mytheresa International Service GmbH',
                'identificacion' => 'DE213277271-0',
                'direccion_facturacion' => 'Einsteinring 9 85609 Aschheim/Munich',
                'direccion_envio' => 'Paul-Thiersch-Str. 16 A-E 04435 Schkeuditz Germany',
                'email' => 'accounts@mytheresa.com',
                'telefono' => '+49 89 1276950',
                'incoterm' => 'DDP',
                'puerto' => 'Cali',
                'origen' => 'COLOMBIA',
                'destino' => 'ALEMANIA',
            ],
            'factura' => [
                'numero' => 'CLC780',
                'fecha' => '2026-03-11',
                'vencimiento' => '2026-03-11',
                'moneda' => 'EUR',
                'simbolo' => '€',
                'observaciones' => 'Pago mediante transferencia internacional en EUR. Mercancía entregada bajo Incoterm DDP hasta destino. Garantía de 12 meses por defectos de fabricación.',
                'cufe' => '018da043f31036423b13e8864429b4e8b62f232b6611f47f2a22860ef3e7fe963b00cf927e2b41ce44fe67f278429728',
                'tasa_cambio' => '3.675,81',
                'qr' => '',
                'qr_html' => '',
                'po' => '',
                'awb' => '',
                'shipper' => '',
                'cod' => '',
                'remision' => '',
                'payment_terms' => 'Crédito ACH',
                'version' => 'V 1.4',
            ],
            'items' => [
                ['referencia' => '25C185', 'descripcion' => 'ERES DRESS', 'color' => 'HIBISCUS', 'size' => '1S', 'composition' => '100% ORGANIC COTTON', 'codigo_pa' => '6204420000', 'pais_origen' => 'Colombia', 'country_of_origin' => 'Colombia', 'cantidad' => 45, 'precio_unitario' => '149,64', 'descuento' => '0,00', 'total' => '6.733,80'],
                ['referencia' => '25C186', 'descripcion' => 'ORLY DRESS', 'color' => 'HIBISCUS', 'size' => '2M', 'composition' => '100% ORGANIC COTTON', 'codigo_pa' => '6204420000', 'pais_origen' => 'Colombia', 'country_of_origin' => 'Colombia', 'cantidad' => 40, 'precio_unitario' => '149,64', 'descuento' => '0,00', 'total' => '5.985,60'],
                ['referencia' => '25C246', 'descripcion' => 'RHODA DRESS', 'color' => 'CALICOAMA', 'size' => '3L', 'composition' => '100% ORGANIC COTTON', 'codigo_pa' => '6204420000', 'pais_origen' => 'Colombia', 'country_of_origin' => 'Colombia', 'cantidad' => 50, 'precio_unitario' => '149,64', 'descuento' => '0,00', 'total' => '7.482,00'],
            ],
            'totales' => [
                'subtotal' => '87.814,60',
                'iva' => '0,00',
                'descuento' => '0,00',
                'flete' => '0,00',
                'seguro' => '0,00',
                'total' => '87.814,60',
                'total_cop' => '322.772.436,00',
            ],
            'banco' => [
                'nombre' => (string) $config->get('banco.nombre', ''),
                'pais' => (string) $config->get('banco.pais', ''),
                'direccion' => (string) $config->get('banco.direccion', ''),
                'titular' => (string) $config->get('banco.titular', ''),
                'moneda' => (string) $config->get('banco.moneda', ''),
                'swift' => (string) $config->get('banco.swift', ''),
                'numero_cuenta' => (string) $config->get('banco.numero_cuenta', ''),
            ],
            'contacto' => [
                'nombre' => (string) $config->get('contacto_financiero.nombre', ''),
                'email' => (string) $config->get('contacto_financiero.email', ''),
                'telefono' => (string) $config->get('contacto_financiero.telefono', ''),
            ],
        ];
    }
}
