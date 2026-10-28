<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Negocio;
use App\Models\LandingAjuste;
use App\Models\MetodoPago;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\VentaPago;
use Illuminate\Database\Eloquent\Collection;

/**
 * Datos del recibo que la caja imprime en la impresora térmica al cobrar. Es un comprobante
 * de la venta para el cliente, no una factura electrónica.
 */
class ReciboVentaService
{
    /** Separador que VentaService pone entre el item y las opciones elegidas en nombre_snapshot. */
    private const SEPARADOR_OPCIONES = ' · ';

    /** @return array<string, mixed> */
    public function deVenta(Venta $venta): array
    {
        $venta->loadMissing(['user', 'items', 'pagos.metodo']);

        return [
            'negocio' => Negocio::desde(LandingAjuste::actual(), [], null),
            'titulo'  => 'Recibo N.º '.$venta->id,
            'prueba'  => false,
            'fecha'   => $venta->created_at,
            'cajero'  => $venta->user?->name,
            'lineas'  => $venta->items->map(fn (VentaItem $item) => $this->linea($item))->all(),
            'total'   => $venta->total_formateado,
            'pagos'   => $venta->pagos->map(fn (VentaPago $pago) => [
                'metodo' => $pago->metodo?->nombre ?? 'Pago',
                'monto'  => $pago->monto_formateado,
            ])->all(),
            'cambio'  => $venta->cambio > 0 ? $venta->cambio_formateado : null,
            'notas'   => $venta->notas,
        ];
    }

    /**
     * Recibo de ejemplo para probar la impresora. Arma una venta sin guardar y la pasa por el
     * mismo formato que una real, así la prueba muestra exactamente cómo saldrán los recibos.
     *
     * @return array<string, mixed>
     */
    public function dePrueba(?User $cajero): array
    {
        $venta = new Venta(['total' => 117500, 'efectivo_recibido' => 120000, 'cambio' => 2500]);
        $venta->created_at = now();
        $venta->setRelation('user', $cajero);
        $venta->setRelation('items', new Collection([
            new VentaItem(['cantidad' => 2, 'nombre_snapshot' => 'Producto de ejemplo', 'subtotal' => 5000]),
            new VentaItem(['cantidad' => 1, 'nombre_snapshot' => 'Producto con un nombre largo para ver cómo se acomoda'.self::SEPARADOR_OPCIONES.'Opción elegida', 'subtotal' => 12500]),
            new VentaItem(['cantidad' => 10, 'nombre_snapshot' => 'Otro producto', 'subtotal' => 100000]),
        ]));
        $pago = new VentaPago(['monto' => 120000]);
        $pago->setRelation('metodo', new MetodoPago(['nombre' => 'Efectivo']));
        $venta->setRelation('pagos', new Collection([$pago]));

        return array_merge($this->deVenta($venta), ['titulo' => 'Recibo de prueba', 'prueba' => true]);
    }

    /** @return array{cantidad: int, nombre: string, detalle: ?string, subtotal: string} */
    private function linea(VentaItem $item): array
    {
        // "Onces con gaseosa · Gaseosa manzana": las opciones van en un renglón aparte.
        [$nombre, $detalle] = array_pad(explode(self::SEPARADOR_OPCIONES, (string) $item->nombre_snapshot, 2), 2, null);

        return [
            'cantidad' => (int) $item->cantidad,
            'nombre'   => $nombre,
            'detalle'  => $detalle,
            'subtotal' => $item->subtotal_formateado,
        ];
    }
}
