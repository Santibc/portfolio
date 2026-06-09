<?php

namespace App\Services\Facturacion;

use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\PlantillaFactura;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacturaService
{
    public function __construct(
        private readonly NumeradorService $numerador,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $items
     */
    public function crearBorrador(array $data, array $items, ?int $userId = null): Factura
    {
        return DB::transaction(function () use ($data, $items, $userId): Factura {
            $factura = new Factura;
            $factura->numero_interno = $this->numerador->siguienteConsecutivoInterno();
            $factura->token_publico = Str::random(40);
            $factura->created_by = $userId;
            $factura->estado = 'borrador';
            $factura->fill($this->normalizar($data));

            $plantilla = $this->elegirPlantilla($factura);
            $factura->plantilla_factura_id = $plantilla?->id;

            $factura->save();

            $this->guardarItems($factura, $items);
            $this->recalcular($factura);

            return $factura->fresh(['items', 'cliente', 'moneda']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $items
     */
    public function actualizarBorrador(Factura $factura, array $data, array $items): Factura
    {
        if (! $factura->esEditable()) {
            abort(422, 'Esta factura ya fue emitida y no se puede editar.');
        }

        return DB::transaction(function () use ($factura, $data, $items): Factura {
            $factura->fill($this->normalizar($data));
            $factura->save();

            $factura->items()->delete();
            $this->guardarItems($factura, $items);
            $this->recalcular($factura);

            return $factura->fresh(['items', 'cliente', 'moneda']);
        });
    }

    public function emitirNoElectronica(Factura $factura): Factura
    {
        if (! $factura->esEditable()) {
            abort(422, 'La factura ya está emitida.');
        }

        $factura->estado = 'emitida';
        $factura->es_electronica = false;
        $factura->emitida_at = now();
        $factura->save();

        return $factura->fresh();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function guardarItems(Factura $factura, array $items): void
    {
        // Los campos descriptivos (referencia, descripción, color, etc.) se toman
        // SIEMPRE del producto en BD, no del cliente: garantiza integridad y que no
        // se cuelen productos inexistentes. Solo cantidad, precio, descuento e IVA
        // son editables y vienen del formulario.
        $ids = collect($items)->pluck('producto_id')->filter()->map(fn ($id) => (int) $id)->unique();
        $productos = Producto::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($items as $orden => $item) {
            $producto = $productos->get((int) ($item['producto_id'] ?? 0));
            if ($producto === null) {
                continue;
            }

            $cantidad = (float) ($item['cantidad'] ?? 0);
            $precio = (float) ($item['precio_unitario'] ?? 0);
            $descuento = (float) ($item['descuento'] ?? 0);
            $descuentoTipo = ($item['descuento_tipo'] ?? 'valor') === 'porcentaje' ? 'porcentaje' : 'valor';
            $iva = (float) ($item['impuesto_porcentaje'] ?? 0);

            $base = $cantidad * $precio;
            $descuentoValor = FacturaItem::calcularDescuento($base, $descuentoTipo, $descuento);
            $subtotal = $base - $descuentoValor;
            $totalLinea = $subtotal + ($subtotal * $iva / 100);

            FacturaItem::create([
                'factura_id' => $factura->id,
                'producto_id' => $producto->id,
                'referencia' => (string) $producto->referencia,
                'descripcion' => (string) $producto->descripcion,
                'color' => $producto->color,
                'composicion' => $producto->composicion,
                'codigo_pa' => $producto->codigo_pa,
                'pais_origen' => $producto->pais_origen,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'descuento' => $descuento,
                'descuento_tipo' => $descuentoTipo,
                'impuesto_porcentaje' => $iva,
                'total_linea' => $totalLinea,
                'tallas_json' => $this->parsearTallas($item['tallas'] ?? null),
                'orden' => $orden,
            ]);
        }
    }

    /**
     * Normaliza el campo de tallas (texto libre "S, M, L" o array) a un array de
     * strings limpio, o null si no hay tallas. Es lo que consume formatearTallas().
     *
     * @param  mixed  $tallas
     * @return array<int, string>|null
     */
    private function parsearTallas($tallas): ?array
    {
        if (is_array($tallas)) {
            $partes = array_map('strval', $tallas);
        } elseif (is_string($tallas) && trim($tallas) !== '') {
            $partes = explode(',', $tallas);
        } else {
            return null;
        }

        $partes = array_values(array_filter(array_map('trim', $partes), fn ($t) => $t !== ''));

        return $partes === [] ? null : $partes;
    }

    public function recalcular(Factura $factura): void
    {
        $items = $factura->items;
        $subtotal = 0.0;
        $ivaTotal = 0.0;
        $descuentoTotal = 0.0;

        foreach ($items as $item) {
            $cantidad = (float) $item->cantidad;
            $precio = (float) $item->precio_unitario;
            $descuento = $item->descuentoValor();
            $ivaPct = (float) $item->impuesto_porcentaje;

            $subLinea = $cantidad * $precio;
            $subtotal += $subLinea;
            $descuentoTotal += $descuento;
            $ivaTotal += ($subLinea - $descuento) * $ivaPct / 100;
        }

        $total = $subtotal - $descuentoTotal + $ivaTotal + (float) $factura->flete + (float) $factura->seguro;

        $factura->subtotal = $subtotal;
        $factura->descuento_total = $descuentoTotal;
        $factura->iva_total = $ivaTotal;
        $factura->total = $total;

        if ($factura->tasa_cambio && $factura->moneda?->codigo !== 'COP') {
            $factura->total_cop = $total * (float) $factura->tasa_cambio;
        } else {
            $factura->total_cop = $factura->moneda?->codigo === 'COP' ? $total : null;
        }

        $factura->save();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizar(array $data): array
    {
        $camposPermitidos = [
            'fecha', 'vencimiento', 'cliente_id', 'moneda_id', 'tasa_cambio',
            'flete', 'seguro', 'observaciones', 'es_electronica',
            'plantilla_factura_id', 'po_numero', 'awb', 'shipper',
            'remision', 'payment_terms',
        ];

        return array_intersect_key($data, array_flip($camposPermitidos));
    }

    /**
     * Resuelve la plantilla a usar para la factura, por prioridad:
     *   1. La que el usuario seleccionó explícitamente en el formulario.
     *   2. La que tenga asignada el cliente.
     *   3. La plantilla marcada como default en el sistema.
     */
    private function elegirPlantilla(Factura $factura): ?PlantillaFactura
    {
        // 1. Selección explícita del usuario (viene del form → fill()).
        if ($factura->plantilla_factura_id) {
            $plantilla = PlantillaFactura::find($factura->plantilla_factura_id);
            if ($plantilla) {
                return $plantilla;
            }
        }

        // 2. Fallback: plantilla asignada al cliente.
        $factura->loadMissing('cliente');
        if ($factura->cliente?->plantilla_factura_id) {
            return PlantillaFactura::find($factura->cliente->plantilla_factura_id);
        }

        // 3. Fallback final: plantilla default.
        return PlantillaFactura::query()->default()->first();
    }
}
