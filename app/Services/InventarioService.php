<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipoMovimientoInventario;
use App\Models\MovimientoInventario;
use App\Models\ProductoMercado;
use App\Models\RegistroMercado;
use App\Models\Venta;
use App\Models\VentaItem;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Única fuente de la lógica de stock. El stock de un producto es la suma de sus movimientos
 * (kardex): compras de mercado (+), ventas de caja (−) y ajustes manuales (±). Nunca queda negativo.
 */
class InventarioService
{
    /**
     * @param  array<int,int>  $productoIds
     * @return array<int,float> producto_mercado_id => stock (0 si no tiene movimientos)
     */
    public function stockDe(array $productoIds): array
    {
        $ids = array_values(array_unique(array_map('intval', $productoIds)));
        if ($ids === []) {
            return [];
        }

        $sumas = MovimientoInventario::whereIn('producto_mercado_id', $ids)
            ->groupBy('producto_mercado_id')
            ->selectRaw('producto_mercado_id, SUM(cantidad) as stock')
            ->pluck('stock', 'producto_mercado_id');

        $stock = [];
        foreach ($ids as $id) {
            $stock[$id] = round((float) ($sumas[$id] ?? 0), 2);
        }

        return $stock;
    }

    public function stockDeProducto(int $productoId): float
    {
        return $this->stockDe([$productoId])[$productoId];
    }

    /** Entrada por compra de mercado. Solo aplica si el producto controla inventario. */
    public function registrarCompra(RegistroMercado $registro, ?int $userId = null): void
    {
        $producto = ProductoMercado::withTrashed()->find($registro->producto_mercado_id);
        if ($producto === null || ! $producto->controla_inventario) {
            return;
        }

        $movimiento = new MovimientoInventario([
            'producto_mercado_id' => $producto->id,
            'tipo'                => TipoMovimientoInventario::Compra,
            'cantidad'            => (float) $registro->cantidad,
            'registro_mercado_id' => $registro->id,
            'user_id'             => $userId,
        ]);
        // El kardex se ordena por la fecha real de la compra (puede registrarse con fecha pasada).
        $movimiento->created_at = $registro->created_at;
        $movimiento->save();
    }

    /**
     * Ajusta la entrada cuando se edita la cantidad de una compra. Las compras hechas antes de
     * que el producto controlara inventario no tienen movimiento y no se tocan.
     */
    public function sincronizarCompra(RegistroMercado $registro): void
    {
        $movimiento = MovimientoInventario::where('registro_mercado_id', $registro->id)->first();
        if ($movimiento === null) {
            return;
        }

        $nueva = round((float) $registro->cantidad, 2);
        $baja  = round((float) $movimiento->cantidad - $nueva, 2);

        if ($baja > 0) {
            $this->asegurarDisponible(
                [$movimiento->producto_mercado_id => $baja],
                'No se puede reducir la compra de %1$s: ya se usaron unidades y el stock quedaría negativo (stock actual: %2$s %3$s).'
            );
        }

        $movimiento->update(['cantidad' => $nueva]);
    }

    public function revertirCompra(RegistroMercado $registro): void
    {
        $movimiento = MovimientoInventario::where('registro_mercado_id', $registro->id)->first();
        if ($movimiento === null) {
            return;
        }

        $this->asegurarDisponible(
            [$movimiento->producto_mercado_id => (float) $movimiento->cantidad],
            'No se puede eliminar la compra de %1$s: ya se usaron unidades y el stock quedaría negativo (stock actual: %2$s %3$s).'
        );

        $movimiento->delete();
    }

    /**
     * Descuenta del inventario lo que consume cada línea de la venta.
     *
     * @param  Collection<int,VentaItem>  $ventaItems  en el mismo orden que $consumos
     * @param  array<int,array<int,float>>  $consumos  índice de línea => [producto_mercado_id => cantidad]
     * @param  CarbonInterface  $fecha  fecha de la venta (al editarla se conserva la original en el kardex)
     */
    public function descontarVenta(Collection $ventaItems, array $consumos, ?int $userId, CarbonInterface $fecha): void
    {
        $requerido = [];
        foreach ($consumos as $consumoLinea) {
            foreach ($consumoLinea as $productoId => $cantidad) {
                $requerido[$productoId] = round(($requerido[$productoId] ?? 0) + $cantidad, 2);
            }
        }

        if ($requerido === []) {
            return;
        }

        $this->asegurarDisponible($requerido, 'Sin stock suficiente de %1$s: quedan %2$s %3$s.');

        foreach ($ventaItems->values() as $i => $ventaItem) {
            foreach ($consumos[$i] ?? [] as $productoId => $cantidad) {
                $movimiento = new MovimientoInventario([
                    'producto_mercado_id' => $productoId,
                    'tipo'                => TipoMovimientoInventario::Venta,
                    'cantidad'            => -$cantidad,
                    'venta_item_id'       => $ventaItem->id,
                    'user_id'             => $userId,
                ]);
                $movimiento->created_at = $fecha;
                $movimiento->save();
            }
        }
    }

    /** Devuelve al inventario lo que la venta había descontado. */
    public function revertirVenta(Venta $venta): void
    {
        MovimientoInventario::whereIn('venta_item_id', $venta->items()->select('id'))->delete();
    }

    /**
     * Kardex de un producto en un rango de fechas: saldo al inicio del rango y movimientos con el
     * saldo corriente (`saldo`) después de cada uno.
     *
     * @return array{saldoInicial: float, movimientos: EloquentCollection<int,MovimientoInventario>}
     */
    public function kardex(ProductoMercado $producto, CarbonInterface $desde, CarbonInterface $hasta): array
    {
        $saldoInicial = round((float) MovimientoInventario::where('producto_mercado_id', $producto->id)
            ->where('created_at', '<', $desde)
            ->sum('cantidad'), 2);

        $saldo       = $saldoInicial;
        $movimientos = MovimientoInventario::with(['user', 'ventaItem.venta', 'registroMercado'])
            ->where('producto_mercado_id', $producto->id)
            ->whereBetween('created_at', [$desde, $hasta])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->each(function (MovimientoInventario $m) use (&$saldo) {
                $saldo    = round($saldo + (float) $m->cantidad, 2);
                $m->saldo = $saldo;
            });

        return ['saldoInicial' => $saldoInicial, 'movimientos' => $movimientos];
    }

    /** @return array<int,Carbon> producto_mercado_id => fecha del último movimiento */
    public function ultimoMovimientoDe(array $productoIds): array
    {
        return MovimientoInventario::whereIn('producto_mercado_id', $productoIds)
            ->groupBy('producto_mercado_id')
            ->selectRaw('producto_mercado_id, MAX(created_at) as ultimo')
            ->pluck('ultimo', 'producto_mercado_id')
            ->map(fn ($fecha) => Carbon::parse($fecha))
            ->all();
    }

    /** Ajuste manual (stock inicial, conteo físico, mermas). Positivo = entrada, negativo = salida. */
    public function ajustar(ProductoMercado $producto, float $cantidad, string $motivo, int $userId): MovimientoInventario
    {
        if (! $producto->controla_inventario) {
            throw new DomainException("{$producto->nombre} no controla inventario.");
        }

        $cantidad = round($cantidad, 2);
        if ($cantidad == 0) {
            throw new DomainException('La cantidad del ajuste no puede ser cero.');
        }

        return DB::transaction(function () use ($producto, $cantidad, $motivo, $userId) {
            if ($cantidad < 0) {
                $this->asegurarDisponible(
                    [$producto->id => -$cantidad],
                    'No se puede sacar esa cantidad de %1$s: solo hay %2$s %3$s.'
                );
            }

            return MovimientoInventario::create([
                'producto_mercado_id' => $producto->id,
                'tipo'                => TipoMovimientoInventario::Ajuste,
                'cantidad'            => $cantidad,
                'user_id'             => $userId,
                'motivo'              => $motivo,
            ]);
        });
    }

    /**
     * Bloquea las filas de los productos (serializa ventas/ajustes concurrentes) y verifica que
     * cada uno tenga al menos la cantidad requerida. Debe llamarse dentro de una transacción.
     *
     * @param  array<int,float>  $requerido  producto_mercado_id => cantidad que se va a sacar
     * @param  string  $mensaje  sprintf con %1$s nombre, %2$s stock disponible, %3$s unidad
     */
    private function asegurarDisponible(array $requerido, string $mensaje): void
    {
        $productos = ProductoMercado::withTrashed()
            ->whereIn('id', array_keys($requerido))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $stock = $this->stockDe(array_keys($requerido));

        foreach ($requerido as $productoId => $cantidad) {
            $disponible = $stock[$productoId] ?? 0.0;
            if (round($disponible - $cantidad, 2) < 0) {
                $producto = $productos->get($productoId);
                throw new DomainException(sprintf(
                    $mensaje,
                    $producto?->nombre ?? "#{$productoId}",
                    ProductoMercado::formatearCantidad(max(0, $disponible)),
                    $producto?->unidad_empaque ?? ''
                ));
            }
        }
    }
}
