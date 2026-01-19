<?php

namespace App\Services;

use App\Models\VentaPdv;
use App\Models\ItemVentaPdv;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio para gestionar las ventas del Punto de Venta.
 *
 * Maneja la creación de ventas, descuento de stock y generación de reportes.
 */
class PuntoVentaService
{
    /**
     * Crear una nueva venta PdV
     *
     * @param array $datosVenta
     * @param array $items
     * @param int $usuarioId
     * @return array ['exito' => bool, 'venta' => VentaPdv|null, 'mensaje' => string]
     */
    public function crearVenta(array $datosVenta, array $items, int $usuarioId): array
    {
        if (empty($items)) {
            return [
                'exito' => false,
                'venta' => null,
                'mensaje' => 'No se puede crear una venta sin productos',
            ];
        }

        DB::beginTransaction();

        try {
            // Verificar disponibilidad de stock
            $verificacion = $this->verificarDisponibilidadItems($items, $datosVenta['ubicacion_id']);
            if (!$verificacion['disponible']) {
                DB::rollBack();
                return [
                    'exito' => false,
                    'venta' => null,
                    'mensaje' => 'Stock insuficiente: ' . implode(', ', $verificacion['errores']),
                ];
            }

            // Generar número de venta
            $numeroVenta = VentaPdv::generarNumeroVenta($datosVenta['ubicacion_id']);

            // Calcular totales
            $totales = $this->calcularTotales($items, $datosVenta['descuento'] ?? 0);

            // Crear la venta
            $venta = VentaPdv::create([
                'numero_venta' => $numeroVenta,
                'ubicacion_id' => $datosVenta['ubicacion_id'],
                'cliente_id' => $datosVenta['cliente_id'] ?? null,
                'nombre_cliente' => $datosVenta['nombre_cliente'] ?? null,
                'subtotal' => $totales['subtotal'],
                'descuento' => $totales['descuento'],
                'iva' => $totales['iva'],
                'total' => $totales['total'],
                'metodo_pago' => $datosVenta['metodo_pago'],
                'monto_efectivo' => $datosVenta['monto_efectivo'] ?? null,
                'monto_tarjeta' => $datosVenta['monto_tarjeta'] ?? null,
                'monto_transferencia' => $datosVenta['monto_transferencia'] ?? null,
                'notas' => $datosVenta['notas'] ?? null,
                'usuario_id' => $usuarioId,
                'estado' => 'completada',
            ]);

            // Crear items y descontar stock
            foreach ($items as $item) {
                $this->crearItemYDescontarStock($venta, $item);
            }

            DB::commit();

            return [
                'exito' => true,
                'venta' => $venta->load('items.producto', 'items.variante'),
                'mensaje' => "Venta {$numeroVenta} creada exitosamente",
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al crear venta PdV: " . $e->getMessage());

            return [
                'exito' => false,
                'venta' => null,
                'mensaje' => 'Error al procesar la venta: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Anular una venta y restaurar el stock
     *
     * @param VentaPdv $venta
     * @param int $usuarioId
     * @param string $motivo
     * @return array
     */
    public function anularVenta(VentaPdv $venta, int $usuarioId, string $motivo): array
    {
        if ($venta->estado === 'anulada') {
            return [
                'exito' => false,
                'mensaje' => 'Esta venta ya está anulada',
            ];
        }

        DB::beginTransaction();

        try {
            // Restaurar stock de cada item
            foreach ($venta->items as $item) {
                $this->restaurarStockItem($venta, $item);
            }

            // Marcar venta como anulada
            $venta->anular($usuarioId, $motivo);

            DB::commit();

            return [
                'exito' => true,
                'mensaje' => "Venta {$venta->numero_venta} anulada exitosamente",
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al anular venta {$venta->numero_venta}: " . $e->getMessage());

            return [
                'exito' => false,
                'mensaje' => 'Error al anular la venta: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verificar disponibilidad de stock para los items
     *
     * @param array $items
     * @param int $ubicacionId
     * @return array
     */
    public function verificarDisponibilidadItems(array $items, int $ubicacionId): array
    {
        $resultado = [
            'disponible' => true,
            'errores' => [],
            'detalles' => [],
        ];

        foreach ($items as $index => $item) {
            $producto = Producto::find($item['producto_id']);

            if (!$producto) {
                $resultado['disponible'] = false;
                $resultado['errores'][] = "Producto no encontrado (ID: {$item['producto_id']})";
                continue;
            }

            // Si el producto no controla stock o permite venta sin stock, continuar
            if (!$producto->controlar_stock || $producto->permitir_venta_sin_stock) {
                $resultado['detalles'][$index] = [
                    'producto_id' => $item['producto_id'],
                    'sin_control_stock' => true,
                    'disponible' => true,
                ];
                continue;
            }

            $stock = $this->obtenerStock(
                $item['producto_id'],
                $item['variante_producto_id'] ?? null,
                $ubicacionId
            );

            $stockDisponible = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;
            $cantidadSolicitada = $item['cantidad'];

            if ($stockDisponible < $cantidadSolicitada) {
                $resultado['disponible'] = false;
                $nombreProducto = $producto->nombre;
                if (isset($item['variante_producto_id']) && $item['variante_producto_id']) {
                    $variante = $producto->variantes()->find($item['variante_producto_id']);
                    $nombreProducto .= ' - ' . ($variante->referencia_variante ?? 'Variante');
                }
                $resultado['errores'][] = "{$nombreProducto} (Disponible: {$stockDisponible}, Solicitado: {$cantidadSolicitada})";
            }

            $resultado['detalles'][$index] = [
                'producto_id' => $item['producto_id'],
                'variante_producto_id' => $item['variante_producto_id'] ?? null,
                'stock_disponible' => $stockDisponible,
                'cantidad_solicitada' => $cantidadSolicitada,
                'disponible' => $stockDisponible >= $cantidadSolicitada,
            ];
        }

        return $resultado;
    }

    /**
     * Calcular totales de la venta
     *
     * @param array $items
     * @param float $descuentoGlobal
     * @return array
     */
    public function calcularTotales(array $items, float $descuentoGlobal = 0): array
    {
        $subtotal = 0;
        $ivaTotal = 0;

        foreach ($items as $item) {
            $precioItem = ($item['precio_unitario'] * $item['cantidad']) - ($item['descuento'] ?? 0);
            $subtotal += $precioItem;
            // IVA del item si aplica (por ahora 0)
            $ivaTotal += $item['iva'] ?? 0;
        }

        $subtotalConDescuento = $subtotal - $descuentoGlobal;
        $total = $subtotalConDescuento + $ivaTotal;

        return [
            'subtotal' => round($subtotal, 2),
            'descuento' => round($descuentoGlobal, 2),
            'iva' => round($ivaTotal, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * Crear item de venta y descontar stock
     *
     * @param VentaPdv $venta
     * @param array $item
     * @return ItemVentaPdv
     */
    private function crearItemYDescontarStock(VentaPdv $venta, array $item): ItemVentaPdv
    {
        $subtotalItem = ($item['precio_unitario'] * $item['cantidad']) - ($item['descuento'] ?? 0);
        $ivaItem = $item['iva'] ?? 0;
        $totalItem = $subtotalItem + $ivaItem;

        $itemVenta = ItemVentaPdv::create([
            'venta_pdv_id' => $venta->id,
            'producto_id' => $item['producto_id'],
            'variante_producto_id' => $item['variante_producto_id'] ?? null,
            'cantidad' => $item['cantidad'],
            'precio_unitario' => $item['precio_unitario'],
            'descuento' => $item['descuento'] ?? 0,
            'subtotal' => $subtotalItem,
            'iva' => $ivaItem,
            'total' => $totalItem,
        ]);

        // Descontar stock
        $producto = Producto::find($item['producto_id']);
        if ($producto && $producto->controlar_stock) {
            $stock = $this->obtenerStock(
                $item['producto_id'],
                $item['variante_producto_id'] ?? null,
                $venta->ubicacion_id
            );

            if ($stock) {
                $stockAnterior = $stock->cantidad_disponible;
                $stock->decrement('cantidad_disponible', $item['cantidad']);

                // Registrar movimiento de stock
                MovimientoStock::create([
                    'producto_id' => $item['producto_id'],
                    'variante_producto_id' => $item['variante_producto_id'] ?? null,
                    'ubicacion_id' => $venta->ubicacion_id,
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stock->cantidad_disponible,
                    'origen' => 'venta',
                    'referencia' => $venta->numero_venta,
                    'motivo' => "Venta PdV - {$venta->numero_venta}",
                    'usuario_id' => $venta->usuario_id,
                ]);
            }
        }

        return $itemVenta;
    }

    /**
     * Restaurar stock de un item anulado
     *
     * @param VentaPdv $venta
     * @param ItemVentaPdv $item
     */
    private function restaurarStockItem(VentaPdv $venta, ItemVentaPdv $item): void
    {
        $producto = $item->producto;

        if (!$producto || !$producto->controlar_stock) {
            return;
        }

        $stock = $this->obtenerStock(
            $item->producto_id,
            $item->variante_producto_id,
            $venta->ubicacion_id
        );

        if ($stock) {
            $stockAnterior = $stock->cantidad_disponible;
            $stock->increment('cantidad_disponible', $item->cantidad);

            // Registrar movimiento de stock (entrada por anulación)
            MovimientoStock::create([
                'producto_id' => $item->producto_id,
                'variante_producto_id' => $item->variante_producto_id,
                'ubicacion_id' => $venta->ubicacion_id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $item->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stock->cantidad_disponible,
                'origen' => 'ajuste_inventario',
                'referencia' => $venta->numero_venta,
                'motivo' => "Anulación Venta PdV - {$venta->numero_venta}",
                'usuario_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Obtener stock de un producto en una ubicación
     *
     * @param int $productoId
     * @param int|null $varianteId
     * @param int $ubicacionId
     * @return StockProducto|null
     */
    private function obtenerStock(int $productoId, ?int $varianteId, int $ubicacionId): ?StockProducto
    {
        $query = StockProducto::where('producto_id', $productoId)
            ->where('ubicacion_id', $ubicacionId);

        if ($varianteId) {
            $query->where('variante_producto_id', $varianteId);
        } else {
            $query->whereNull('variante_producto_id');
        }

        return $query->first();
    }

    /**
     * Obtener métricas del día para una ubicación
     *
     * @param int|null $ubicacionId
     * @param string|null $fecha
     * @return array
     */
    public function obtenerMetricasDelDia(?int $ubicacionId = null, ?string $fecha = null): array
    {
        $fecha = $fecha ?? now()->toDateString();

        $query = VentaPdv::completadas()->whereDate('created_at', $fecha);

        if ($ubicacionId) {
            $query->where('ubicacion_id', $ubicacionId);
        }

        $ventas = $query->get();

        $porMetodoPago = $ventas->groupBy('metodo_pago')->map(function ($grupo) {
            return [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('total'),
            ];
        });

        return [
            'fecha' => $fecha,
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->sum('total'),
            'monto_promedio' => $ventas->count() > 0 ? $ventas->avg('total') : 0,
            'total_items' => $ventas->flatMap->items->sum('cantidad'),
            'por_metodo_pago' => $porMetodoPago,
            'efectivo' => $ventas->sum('monto_efectivo') ?? 0,
            'tarjeta' => $ventas->sum('monto_tarjeta') ?? 0,
            'transferencia' => $ventas->sum('monto_transferencia') ?? 0,
        ];
    }

    /**
     * Obtener métricas del mes
     *
     * @param int|null $ubicacionId
     * @param int|null $mes
     * @param int|null $anio
     * @return array
     */
    public function obtenerMetricasDelMes(?int $ubicacionId = null, ?int $mes = null, ?int $anio = null): array
    {
        $mes = $mes ?? now()->month;
        $anio = $anio ?? now()->year;

        $query = VentaPdv::completadas()
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $anio);

        if ($ubicacionId) {
            $query->where('ubicacion_id', $ubicacionId);
        }

        $ventas = $query->get();

        // Ventas por día del mes
        $ventasPorDia = $ventas->groupBy(function ($venta) {
            return $venta->created_at->format('Y-m-d');
        })->map(function ($grupo) {
            return [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('total'),
            ];
        });

        return [
            'mes' => $mes,
            'anio' => $anio,
            'total_ventas' => $ventas->count(),
            'monto_total' => $ventas->sum('total'),
            'monto_promedio' => $ventas->count() > 0 ? $ventas->avg('total') : 0,
            'ventas_por_dia' => $ventasPorDia,
            'dias_con_ventas' => $ventasPorDia->count(),
        ];
    }

    /**
     * Obtener productos más vendidos
     *
     * @param int|null $ubicacionId
     * @param string $periodo ('dia', 'mes', 'anio')
     * @param int $limite
     * @return array
     */
    public function obtenerProductosMasVendidos(
        ?int $ubicacionId = null,
        string $periodo = 'mes',
        int $limite = 10
    ): array {
        $query = ItemVentaPdv::query()
            ->join('ventas_pdv', 'items_venta_pdv.venta_pdv_id', '=', 'ventas_pdv.id')
            ->where('ventas_pdv.estado', 'completada');

        if ($ubicacionId) {
            $query->where('ventas_pdv.ubicacion_id', $ubicacionId);
        }

        switch ($periodo) {
            case 'dia':
                $query->whereDate('ventas_pdv.created_at', now()->toDateString());
                break;
            case 'mes':
                $query->whereMonth('ventas_pdv.created_at', now()->month)
                      ->whereYear('ventas_pdv.created_at', now()->year);
                break;
            case 'anio':
                $query->whereYear('ventas_pdv.created_at', now()->year);
                break;
        }

        return $query->select('items_venta_pdv.producto_id')
            ->selectRaw('SUM(items_venta_pdv.cantidad) as cantidad_total')
            ->selectRaw('SUM(items_venta_pdv.total) as monto_total')
            ->groupBy('items_venta_pdv.producto_id')
            ->orderByDesc('cantidad_total')
            ->limit($limite)
            ->with('producto')
            ->get()
            ->map(function ($item) {
                $producto = Producto::find($item->producto_id);
                return [
                    'producto_id' => $item->producto_id,
                    'nombre' => $producto ? $producto->nombre : 'Producto eliminado',
                    'referencia' => $producto ? $producto->referencia : '-',
                    'cantidad_total' => $item->cantidad_total,
                    'monto_total' => $item->monto_total,
                ];
            })
            ->toArray();
    }

    /**
     * Buscar productos para venta
     *
     * @param string $termino
     * @param int $ubicacionId
     * @param int|null $listaPrecioId
     * @param int $limite
     * @return array
     */
    public function buscarProductos(string $termino, int $ubicacionId, ?int $listaPrecioId = null, int $limite = 20): array
    {
        $productos = Producto::activos()
            ->where(function ($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('referencia', 'like', "%{$termino}%");
            })
            ->with(['variantes', 'precios', 'stock' => function ($query) use ($ubicacionId) {
                $query->where('ubicacion_id', $ubicacionId);
            }])
            ->limit($limite)
            ->get();

        return $productos->map(function ($producto) use ($ubicacionId, $listaPrecioId) {
            $precio = $listaPrecioId
                ? $producto->getPrecioPorLista($listaPrecioId)
                : ($producto->precios->first()->precio ?? 0);

            $stockDisponible = 0;
            if ($producto->controlar_stock) {
                $stock = $producto->stock->first();
                $stockDisponible = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;
            }

            return [
                'id' => $producto->id,
                'referencia' => $producto->referencia,
                'nombre' => $producto->nombre,
                'precio' => $precio,
                'stock_disponible' => $stockDisponible,
                'controla_stock' => $producto->controlar_stock,
                'permite_sin_stock' => $producto->permitir_venta_sin_stock,
                'tiene_variantes' => $producto->tiene_variantes,
                'variantes' => $producto->tiene_variantes ? $producto->variantes->map(function ($v) use ($producto, $ubicacionId, $listaPrecioId) {
                    $stockVariante = StockProducto::where('producto_id', $producto->id)
                        ->where('variante_producto_id', $v->id)
                        ->where('ubicacion_id', $ubicacionId)
                        ->first();

                    $precioVariante = $v->precios()
                        ->where('lista_precio_id', $listaPrecioId)
                        ->first();

                    return [
                        'id' => $v->id,
                        'sku' => $v->sku,
                        'referencia_variante' => $v->referencia_variante,
                        'color' => $v->color,
                        'precio' => $precioVariante ? $precioVariante->precio : null,
                        'stock_disponible' => $stockVariante
                            ? ($stockVariante->cantidad_disponible - $stockVariante->cantidad_reservada)
                            : 0,
                    ];
                }) : [],
                'imagen_url' => $producto->url_imagen_principal,
            ];
        })->toArray();
    }
}
