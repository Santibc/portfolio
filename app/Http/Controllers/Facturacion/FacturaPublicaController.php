<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\PlantillaFactura;
use App\Services\Facturacion\TemplateRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

/**
 * Portal pÃºblico: descarga de factura por token Ãºnico (sin auth).
 * El token viaja en URL firmada/no guesseable â€” cualquier ruta aquÃ­
 * NO requiere login.
 */
class FacturaPublicaController extends Controller
{
    public function __construct(private readonly TemplateRenderer $renderer) {}

    public function descargar(string $token): Response
    {
        $factura = Factura::query()
            ->with(['items', 'cliente.incoterm', 'cliente.puerto', 'moneda', 'plantilla'])
            ->where('token_publico', $token)
            ->firstOrFail();

        abort_if($factura->estado === 'anulada', 404);
        abort_if($factura->estado === 'borrador', 404);

        $plantilla = $factura->plantilla ?? PlantillaFactura::query()->default()->first();
        abort_if($plantilla === null, 422, 'No hay plantilla configurada.');

        $html = $this->renderer->render($plantilla, $this->datosFactura($factura));

        return Pdf::loadHTML($html)
            ->setPaper('letter')
            ->download($factura->numero_interno.'.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFactura(Factura $factura): array
    {
        $simbolo = match ($factura->moneda?->codigo) {
            'EUR' => 'â‚¬',
            'USD' => 'US$',
            'COP' => '$',
            default => (string) ($factura->moneda?->simbolo ?? ''),
        };

        return [
            'empresa' => app(\App\Services\Settings\EmpresaData::class)->paraPlantilla(),
            'cliente' => [
                'nombre' => (string) $factura->cliente?->nombre,
                'identificacion' => (string) $factura->cliente?->identificacion,
                'direccion_facturacion' => (string) $factura->cliente?->direccion_facturacion,
                'direccion_envio' => (string) $factura->cliente?->direccion_envio,
                'email' => (string) $factura->cliente?->email,
                'telefono' => (string) $factura->cliente?->telefono,
                'incoterm' => (string) $factura->cliente?->incoterm?->codigo,
                'puerto' => (string) $factura->cliente?->puerto?->nombre,
            ],
            'factura' => [
                'numero' => $factura->numero_siigo ?? $factura->numero_interno,
                'fecha' => $factura->fecha->format('Y-m-d'),
                'vencimiento' => $factura->vencimiento?->format('Y-m-d'),
                'moneda' => (string) $factura->moneda?->codigo,
                'simbolo' => $simbolo,
                'cufe' => (string) $factura->cufe,
                'tasa_cambio' => $factura->tasa_cambio,
                'observaciones' => (string) $factura->observaciones,
                'remision' => (string) $factura->remision,
                'payment_terms' => (string) $factura->payment_terms,
            ],
            'items' => $factura->items->map(fn ($item) => [
                'referencia' => (string) $item->referencia,
                'descripcion' => (string) $item->descripcion,
                'color' => (string) $item->color,
                'composicion' => (string) $item->composicion,
                'composition' => (string) $item->composicion,
                'size' => is_array($item->tallas_json) ? implode(', ', $item->tallas_json) : '',
                'codigo_pa' => (string) $item->codigo_pa,
                'pais_origen' => (string) $item->pais_origen,
                'country_of_origin' => (string) $item->pais_origen,
                'cantidad' => number_format((float) $item->cantidad, 0, ',', '.'),
                'precio_unitario' => number_format((float) $item->precio_unitario, 2, ',', '.'),
                'descuento' => number_format($item->descuentoValor(), 2, ',', '.'),
                'total' => number_format((float) $item->total_linea, 2, ',', '.'),
            ])->all(),
            'totales' => [
                'subtotal' => number_format((float) $factura->subtotal, 2, ',', '.'),
                'iva' => number_format((float) $factura->iva_total, 2, ',', '.'),
                'descuento' => number_format((float) $factura->descuento_total, 2, ',', '.'),
                'flete' => number_format((float) $factura->flete, 2, ',', '.'),
                'seguro' => number_format((float) $factura->seguro, 2, ',', '.'),
                'total' => number_format((float) $factura->total, 2, ',', '.'),
                'total_cop' => $factura->total_cop ? number_format((float) $factura->total_cop, 2, ',', '.') : '',
            ],
            'banco' => $this->datosBanco(),
            'contacto' => $this->datosContacto(),
        ];
    }

    /**
     * Datos bancarios para la plantilla. Remapea las claves planas de
     * configuraciÃ³n (`banco.nombre`) a la estructura anidada que espera el
     * renderer (`{{banco.nombre}}` â†’ $data['banco']['nombre']).
     *
     * @return array<string, string>
     */
    private function datosBanco(): array
    {
        $config = app(\App\Services\Settings\ConfigService::class);

        return [
            'nombre' => (string) $config->get('banco.nombre', ''),
            'pais' => (string) $config->get('banco.pais', ''),
            'direccion' => (string) $config->get('banco.direccion', ''),
            'titular' => (string) $config->get('banco.titular', ''),
            'moneda' => (string) $config->get('banco.moneda', ''),
            'swift' => (string) $config->get('banco.swift', ''),
            'numero_cuenta' => (string) $config->get('banco.numero_cuenta', ''),
        ];
    }

    /**
     * Datos del contacto financiero. Las claves se guardan con el prefijo
     * `contacto_financiero.*` pero la plantilla las consume como `{{contacto.*}}`.
     *
     * @return array<string, string>
     */
    private function datosContacto(): array
    {
        $config = app(\App\Services\Settings\ConfigService::class);

        return [
            'nombre' => (string) $config->get('contacto_financiero.nombre', ''),
            'email' => (string) $config->get('contacto_financiero.email', ''),
            'telefono' => (string) $config->get('contacto_financiero.telefono', ''),
        ];
    }
}
