<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MenuItem;
use App\Models\MetodoPago;
use App\Models\TurnoCaja;
use App\Models\Venta;
use DomainException;
use Illuminate\Support\Facades\DB;

class VentaService
{
    public function __construct(
        private TurnoCajaService $turnos,
        private InventarioService $inventario,
    ) {
    }

    public function crear(array $items, array $pagos, int $userId, ?string $notas = null): Venta
    {
        $turno = $this->turnos->turnoActivo();
        if ($turno === null) {
            throw new DomainException('No hay un turno de caja abierto. Abre la caja para registrar ventas.');
        }

        return DB::transaction(function () use ($items, $pagos, $userId, $notas, $turno) {
            return $this->persistir($turno, $items, $pagos, $userId, $notas);
        });
    }

    public function actualizar(Venta $venta, array $items, array $pagos, ?string $notas = null): Venta
    {
        return DB::transaction(function () use ($venta, $items, $pagos, $notas) {
            // Devolver primero lo que la venta había descontado, para validar el stock nuevo
            // contra el inventario sin esta venta.
            $this->inventario->revertirVenta($venta);
            $venta->items()->delete();
            $venta->pagos()->delete();

            [$total, $itemsData, $consumos] = $this->calcularItems($items);
            [$sumNoEfectivo, $sumEfectivoPago, $pagosData] = $this->calcularPagos($pagos);

            $this->validarBalance($total, $sumNoEfectivo, $sumEfectivoPago);

            $efectivoRequerido = max(0, $total - $sumNoEfectivo);
            $cambio            = max(0, $sumEfectivoPago - $efectivoRequerido);

            $venta->fill([
                'total'             => $total,
                'efectivo_recibido' => $sumEfectivoPago,
                'cambio'            => $cambio,
                'notas'             => $notas,
            ])->save();

            $ventaItems = $venta->items()->createMany($itemsData);
            $venta->pagos()->createMany($pagosData);

            $this->inventario->descontarVenta($ventaItems, $consumos, $venta->user_id, $venta->created_at);

            return $venta->fresh(['items', 'pagos']);
        });
    }

    public function eliminar(Venta $venta): void
    {
        DB::transaction(function () use ($venta) {
            $this->inventario->revertirVenta($venta);
            $venta->delete();
        });
    }

    private function persistir(TurnoCaja $turno, array $items, array $pagos, int $userId, ?string $notas): Venta
    {
        [$total, $itemsData, $consumos] = $this->calcularItems($items);
        [$sumNoEfectivo, $sumEfectivoPago, $pagosData] = $this->calcularPagos($pagos);

        $this->validarBalance($total, $sumNoEfectivo, $sumEfectivoPago);

        $efectivoRequerido = max(0, $total - $sumNoEfectivo);
        $cambio            = max(0, $sumEfectivoPago - $efectivoRequerido);

        $venta = Venta::create([
            'turno_caja_id'     => $turno->id,
            'user_id'           => $userId,
            'total'             => $total,
            'efectivo_recibido' => $sumEfectivoPago,
            'cambio'            => $cambio,
            'notas'             => $notas,
        ]);

        $ventaItems = $venta->items()->createMany($itemsData);
        $venta->pagos()->createMany($pagosData);

        $this->inventario->descontarVenta($ventaItems, $consumos, $userId, $venta->created_at);

        return $venta->fresh(['items', 'pagos']);
    }

    /**
     * @return array{0:int, 1:array<int,array<string,mixed>>, 2:array<int,array<int,float>>}
     *                                                      consumos: línea => [producto_mercado_id => cantidad]
     */
    private function calcularItems(array $items): array
    {
        if (count($items) === 0) {
            throw new DomainException('La venta debe tener al menos un item.');
        }

        $ids       = collect($items)->pluck('menu_item_id')->unique()->values()->all();
        $menuItems = MenuItem::whereIn('id', $ids)->withTrashed()->with('componentes.opciones')->get()->keyBy('id');

        $total     = 0;
        $itemsData = [];
        $consumos  = [];

        foreach ($items as $row) {
            $mid      = (int) $row['menu_item_id'];
            $cantidad = (int) $row['cantidad'];
            $mi       = $menuItems->get($mid);

            if ($mi === null) {
                throw new DomainException("Item de menú #{$mid} no existe.");
            }
            if ($cantidad < 1) {
                throw new DomainException("Cantidad inválida para '{$mi->nombre}'.");
            }

            // El precio puede editarse en el carrito (a veces se cobra más o menos que
            // el precio de catálogo). Si llega un precio explícito se usa ese; si no,
            // se toma el precio actual del item de menú como respaldo.
            $precio = array_key_exists('precio_unitario', $row) && $row['precio_unitario'] !== ''
                ? max(0, (int) $row['precio_unitario'])
                : (int) $mi->precio;

            $subtotal = $precio * $cantidad;
            $total   += $subtotal;

            [$consumo, $elegidas] = $this->resolverComponentes($mi, $cantidad, (array) ($row['opciones'] ?? []));

            $nombre = $elegidas === [] ? $mi->nombre : $mi->nombre.' · '.implode(', ', $elegidas);

            $itemsData[] = [
                'menu_item_id'    => $mid,
                'nombre_snapshot' => mb_substr($nombre, 0, 200),
                'precio_unitario' => $precio,
                'cantidad'        => $cantidad,
                'subtotal'        => $subtotal,
            ];
            $consumos[] = $consumo;
        }

        return [$total, $itemsData, $consumos];
    }

    /**
     * Qué productos de inventario descuenta una línea. Un componente con una sola opción descuenta
     * esa; con varias, la que eligió el cajero en $opciones[componente_id] = producto_mercado_id.
     *
     * @return array{0:array<int,float>, 1:array<int,string>} consumo por producto, nombres elegidos
     */
    private function resolverComponentes(MenuItem $mi, int $cantidad, array $opciones): array
    {
        $consumo  = [];
        $elegidas = [];

        foreach ($mi->componentes as $componente) {
            $disponibles = $componente->opciones;
            if ($disponibles->isEmpty()) {
                continue;
            }

            if ($disponibles->count() === 1) {
                $producto = $disponibles->first();
            } else {
                $producto = $disponibles->firstWhere('id', (int) ($opciones[$componente->id] ?? 0));
                if ($producto === null) {
                    throw new DomainException("Elige {$componente->nombre} para '{$mi->nombre}'.");
                }
                $elegidas[] = $producto->nombre;
            }

            if (! $producto->controla_inventario) {
                continue;
            }

            $consumo[$producto->id] = round(($consumo[$producto->id] ?? 0) + (float) $componente->cantidad * $cantidad, 2);
        }

        return [$consumo, $elegidas];
    }

    /**
     * @return array{0:int, 1:int, 2:array<int,array<string,mixed>>}
     */
    private function calcularPagos(array $pagos): array
    {
        if (count($pagos) === 0) {
            throw new DomainException('La venta debe tener al menos un método de pago.');
        }

        $ids     = collect($pagos)->pluck('metodo_pago_id')->unique()->values()->all();
        $metodos = MetodoPago::whereIn('id', $ids)->get()->keyBy('id');

        $sumNoEfectivo   = 0;
        $sumEfectivoPago = 0;
        $pagosData       = [];

        foreach ($pagos as $row) {
            $mpId       = (int) $row['metodo_pago_id'];
            $monto      = (int) $row['monto'];
            $referencia = $row['referencia'] ?? null;
            $metodo     = $metodos->get($mpId);

            if ($metodo === null) {
                throw new DomainException("Método de pago #{$mpId} no existe.");
            }
            if ($monto < 1) {
                throw new DomainException("Monto inválido para '{$metodo->nombre}'.");
            }

            if ($metodo->es_efectivo) {
                $sumEfectivoPago += $monto;
            } else {
                $sumNoEfectivo += $monto;
            }

            $pagosData[] = [
                'metodo_pago_id' => $mpId,
                'monto'          => $monto,
                'referencia'     => $referencia,
            ];
        }

        return [$sumNoEfectivo, $sumEfectivoPago, $pagosData];
    }

    private function validarBalance(int $total, int $sumNoEfectivo, int $sumEfectivoDisponible): void
    {
        if ($sumNoEfectivo > $total) {
            throw new DomainException('Los pagos no-efectivo superan el total de la venta.');
        }

        $efectivoRequerido = max(0, $total - $sumNoEfectivo);
        if ($sumEfectivoDisponible < $efectivoRequerido) {
            throw new DomainException("Efectivo insuficiente. Se requieren $ " . number_format($efectivoRequerido, 0, ',', '.') . ' en efectivo.');
        }
    }
}
