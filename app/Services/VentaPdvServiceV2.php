<?php

namespace App\Services;

use App\Models\VentaPdv;
use App\Models\ItemVentaPdv;
use App\Models\DevolucionParcialPdv;
use App\Models\ItemDevolucionParcialPdv;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use App\Models\SesionCaja;
use App\Models\ConfiguracionPdv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class VentaPdvServiceV2
{
    public function crearVenta(array $datosVenta, array $items, int $usuarioId, ?int $sesionCajaId = null): array
    {
        if (empty($items)) {
            return ['exito' => false, 'venta' => null, 'mensaje' => 'No se puede crear una venta sin productos'];
        }

        DB::beginTransaction();
        try {
            $ubicacionId = $datosVenta['ubicacion_id'];

            // Verificar stock
            $verificacion = $this->verificarDisponibilidadItems($items, $ubicacionId);
            if (!$verificacion['disponible']) {
                DB::rollBack();
                return ['exito' => false, 'venta' => null, 'mensaje' => 'Stock insuficiente: ' . implode(', ', $verificacion['errores'])];
            }

            // Obtener sesion y caja
            $sesion = $sesionCajaId ? SesionCaja::find($sesionCajaId) : null;
            $cajaId = $sesion ? $sesion->caja_id : null;

            $numeroVenta = VentaPdv::generarNumeroVenta($ubicacionId);

            // Los precios del catálogo YA incluyen IVA. Descomponer iva por línea
            // (factor = 0.19/1.19 con iva=19%) y recalcular totales en server-side
            // sin confiar en el iva enviado por el cliente.
            $ivaPorcentaje = ConfiguracionPdv::obtenerNumero('iva_porcentaje', 0);
            $ivaFactor = $ivaPorcentaje > 0 ? ($ivaPorcentaje / (100 + $ivaPorcentaje)) : 0;

            $subtotalConIva = 0;
            foreach ($items as $idx => $item) {
                $lineaConIva = $item['precio_unitario'] * $item['cantidad'];
                $descuentoItem = $item['descuento_valor'] ?? ($item['descuento'] ?? 0);
                $lineaConIvaNeto = $lineaConIva - $descuentoItem;
                $subtotalConIva += $lineaConIvaNeto;

                // Sobrescribir iva por ítem para que crearItemYDescontarStock lo use
                $items[$idx]['iva'] = $ivaPorcentaje > 0
                    ? round($lineaConIvaNeto * $ivaFactor, 2)
                    : 0;
            }

            $descuentoGlobal = $datosVenta['descuento_global'] ?? 0;
            $totalConIva = $subtotalConIva - $descuentoGlobal;
            $ivaTotal = $ivaPorcentaje > 0 ? round($totalConIva * $ivaFactor, 2) : 0;
            $subtotal = round($totalConIva - $ivaTotal, 2); // base gravable sin IVA
            $total = round($totalConIva, 2);

            $cambioFinal = $datosVenta['cambio'] ?? null;
            if ($cambioFinal === null || (float) $cambioFinal <= 0) {
                $efectivoEntregado = (float) ($datosVenta['monto_efectivo'] ?? 0);
                $transferenciaEntregada = (float) ($datosVenta['monto_transferencia'] ?? 0);
                $sobrante = ($efectivoEntregado + $transferenciaEntregada) - $total;
                $cambioFinal = $sobrante > 0 ? round($sobrante, 2) : ($datosVenta['cambio'] ?? null);
            }

            $venta = VentaPdv::create([
                'numero_venta' => $numeroVenta,
                'sesion_caja_id' => $sesionCajaId,
                'caja_id' => $cajaId,
                'prefactura_id' => $datosVenta['prefactura_id'] ?? null,
                'ubicacion_id' => $ubicacionId,
                'cliente_id' => $datosVenta['cliente_id'] ?? null,
                'nombre_cliente' => $datosVenta['nombre_cliente'] ?? 'Consumidor Final',
                'lista_precio_id' => $datosVenta['lista_precio_id'] ?? null,
                'subtotal' => round($subtotal, 2),
                'descuento' => 0,
                'descuento_global' => round($descuentoGlobal, 2),
                'iva' => round($ivaTotal, 2),
                'total' => round($total, 2),
                'metodo_pago' => $datosVenta['metodo_pago'],
                'monto_efectivo' => $datosVenta['monto_efectivo'] ?? null,
                'monto_tarjeta' => $datosVenta['monto_tarjeta'] ?? null,
                'monto_transferencia' => $datosVenta['monto_transferencia'] ?? null,
                'monto_recibido' => $datosVenta['monto_recibido'] ?? null,
                'cambio' => $cambioFinal,
                'tipo_transferencia' => $datosVenta['tipo_transferencia'] ?? null,
                'comprobante_pago' => $datosVenta['comprobante_pago'] ?? null,
                'notas' => $datosVenta['notas'] ?? null,
                'usuario_id' => $usuarioId,
                'vendedora_prefactura' => $datosVenta['vendedora_prefactura'] ?? null,
                'descuento_autorizado_por' => $datosVenta['descuento_autorizado_por'] ?? null,
                'precio_autorizado_por' => $datosVenta['precio_autorizado_por'] ?? null,
                'estado' => 'completada',
            ]);

            // Crear items y descontar stock
            foreach ($items as $item) {
                $this->crearItemYDescontarStock($venta, $item);
            }

            // Actualizar totales de la sesión
            if ($sesion) {
                $sesion->increment('total_ventas', $venta->total);
                $sesion->increment('cantidad_ventas');
                if ($venta->monto_efectivo) {
                    $sesion->increment('total_ventas_efectivo', $venta->monto_efectivo);
                }
                if ($venta->monto_transferencia) {
                    $sesion->increment('total_ventas_transferencia', $venta->monto_transferencia);
                }
            }

            DB::commit();

            return [
                'exito' => true,
                'venta' => $venta->load('items.producto', 'items.variante'),
                'mensaje' => "Venta {$numeroVenta} creada exitosamente",
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al crear venta PdV V2: " . $e->getMessage());
            return ['exito' => false, 'venta' => null, 'mensaje' => 'Error al procesar la venta: ' . $e->getMessage()];
        }
    }

    public function anularVenta(VentaPdv $venta, int $usuarioId, string $motivo): array
    {
        if ($venta->estado === 'anulada') {
            return ['exito' => false, 'mensaje' => 'Esta venta ya está anulada'];
        }

        DB::beginTransaction();
        try {
            foreach ($venta->items as $item) {
                // Si hay devoluciones parciales previas, solo restaurar lo no devuelto
                $cantidadRestante = $item->cantidad - $item->cantidad_devuelta;
                if ($cantidadRestante > 0) {
                    $this->restaurarStockItem($venta, $item, $cantidadRestante, 'Anulación');
                }
            }

            $venta->anular($usuarioId, $motivo);

            // Update session totals only when session is still open.
            // If the session is already closed (cuadre histórico), leave its totals untouched
            // so the historical balance remains intact; the anulación queda registrada en la venta.
            if ($venta->sesion_caja_id) {
                $sesion = $venta->sesionCaja;
                if ($sesion && $sesion->estaAbierta()) {
                    $sesion->increment('total_anulaciones', $venta->total);
                    $sesion->decrement('total_ventas', $venta->total);
                    $sesion->decrement('cantidad_ventas');
                    if ($venta->monto_efectivo) {
                        $sesion->decrement('total_ventas_efectivo', $venta->monto_efectivo);
                    }
                    if ($venta->monto_transferencia) {
                        $sesion->decrement('total_ventas_transferencia', $venta->monto_transferencia);
                    }
                }
            }

            DB::commit();

            return ['exito' => true, 'mensaje' => "Venta {$venta->numero_venta} anulada exitosamente"];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al anular venta {$venta->numero_venta}: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al anular la venta: ' . $e->getMessage()];
        }
    }

    public function verificarDisponibilidadItems(array $items, int $ubicacionId): array
    {
        $resultado = ['disponible' => true, 'errores' => [], 'detalles' => []];

        foreach ($items as $index => $item) {
            $producto = Producto::find($item['producto_id']);
            if (!$producto) {
                $resultado['disponible'] = false;
                $resultado['errores'][] = "Producto no encontrado (ID: {$item['producto_id']})";
                continue;
            }

            if (!$producto->controlar_stock || $producto->permitir_venta_sin_stock) {
                $resultado['detalles'][$index] = ['disponible' => true, 'sin_control_stock' => true];
                continue;
            }

            $stock = $this->obtenerStock($item['producto_id'], $item['variante_producto_id'] ?? null, $ubicacionId);
            $stockDisponible = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;
            $cantidadSolicitada = $item['cantidad'];

            if ($stockDisponible < $cantidadSolicitada) {
                $resultado['disponible'] = false;
                $nombreProducto = $producto->nombre;
                if (!empty($item['variante_producto_id'])) {
                    $variante = $producto->variantes()->find($item['variante_producto_id']);
                    $nombreProducto .= ' - ' . ($variante->referencia_variante ?? 'Variante');
                }
                $resultado['errores'][] = "{$nombreProducto} (Disponible: {$stockDisponible}, Solicitado: {$cantidadSolicitada})";
            }

            $resultado['detalles'][$index] = [
                'disponible' => $stockDisponible >= $cantidadSolicitada,
                'stock_disponible' => $stockDisponible,
            ];
        }

        return $resultado;
    }

    public function buscarProductos(string $termino, int $ubicacionId, ?int $listaPrecioId = null, int $limiteFilas = 25): array
    {
        $terminoTrim = trim($termino);
        $pareceCodigoBarras = preg_match('/^\d{6,}$/', $terminoTrim) === 1;

        $productos = Producto::activos()
            ->where(function ($query) use ($terminoTrim) {
                $query->where('nombre', 'like', "%{$terminoTrim}%")
                    ->orWhere('referencia', 'like', "%{$terminoTrim}%")
                    ->orWhere('codigo_barras', 'like', "%{$terminoTrim}%")
                    ->orWhereHas('variantes', function ($q) use ($terminoTrim) {
                        $q->where('codigo_barras', 'like', "%{$terminoTrim}%")
                          ->orWhere('sku', 'like', "%{$terminoTrim}%")
                          ->orWhere('referencia_variante', 'like', "%{$terminoTrim}%")
                          ->orWhere('color', 'like', "%{$terminoTrim}%");
                    });
            })
            ->with(['variantes' => function ($q) use ($terminoTrim) {
                $q->where(function ($qq) use ($terminoTrim) {
                    $qq->where('codigo_barras', 'like', "%{$terminoTrim}%")
                       ->orWhere('sku', 'like', "%{$terminoTrim}%")
                       ->orWhere('referencia_variante', 'like', "%{$terminoTrim}%")
                       ->orWhere('color', 'like', "%{$terminoTrim}%");
                });
            }, 'precios', 'stock' => function ($query) use ($ubicacionId) {
                $query->where('ubicacion_id', $ubicacionId)->whereNull('variante_producto_id');
            }])
            ->limit(40)
            ->get();

        $filas = [];
        foreach ($productos as $producto) {
            if ($producto->tiene_variantes) {
                $productoCoincidePorCamposPropios =
                    stripos((string) $producto->nombre, $terminoTrim) !== false
                    || stripos((string) $producto->referencia, $terminoTrim) !== false
                    || stripos((string) $producto->codigo_barras, $terminoTrim) !== false;

                if ($productoCoincidePorCamposPropios && $producto->variantes->isEmpty()) {
                    $producto->load('variantes');
                }

                if ($producto->variantes->isEmpty()) {
                    continue;
                }
                foreach ($producto->variantes as $variante) {
                    $filas[] = $this->filaDesdeVariante($producto, $variante, $ubicacionId, $listaPrecioId);
                }
            } else {
                $filas[] = $this->filaDesdeProducto($producto, $listaPrecioId);
            }
        }

        // Solo mostrar los productos PRESENTES en la ubicación/sede asignada (con stock
        // disponible en esa sede). Se conservan los que no controlan stock o que permiten
        // venta sin stock, porque son vendibles a propósito sin estar atados a la sede.
        if ($ubicacionId > 0) {
            $filas = array_values(array_filter($filas, function ($f) {
                return empty($f['controla_stock'])
                    || !empty($f['permite_sin_stock'])
                    || $f['stock_disponible'] > 0;
            }));
        }

        if ($pareceCodigoBarras) {
            usort($filas, function ($a, $b) use ($terminoTrim) {
                $ma = $a['codigo_barras'] === $terminoTrim ? 1 : 0;
                $mb = $b['codigo_barras'] === $terminoTrim ? 1 : 0;
                return $mb - $ma;
            });
        }

        // Promoción por tiempo de la feria (por ubicación): si hay una promo vigente AHORA,
        // se muestra ese precio en el buscador del POS (y luego se cobra igual).
        if ($ubicacionId > 0) {
            $feria = \App\Models\Feria::activas()->where('ubicacion_id', $ubicacionId)->latest('id')->first();
            if ($feria) {
                foreach ($filas as &$f) {
                    $promo = \App\Models\FeriaPromocion::precioVigente(
                        $feria->id,
                        (int) $f['producto_id'],
                        !empty($f['variante_producto_id']) ? (int) $f['variante_producto_id'] : null
                    );
                    if ($promo !== null) {
                        $f['precio'] = (float) $promo;
                    }
                }
                unset($f);
            }
        }

        return array_slice($filas, 0, $limiteFilas);
    }

    private function filaDesdeProducto(Producto $producto, ?int $listaPrecioId): array
    {
        $precio = $listaPrecioId
            ? $producto->getPrecioPorLista($listaPrecioId)
            : ($producto->precios->first()->precio ?? 0);

        $stockDisponible = 0;
        if ($producto->controlar_stock) {
            $stock = $producto->stock->first();
            $stockDisponible = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;
        }

        return [
            'producto_id'          => $producto->id,
            'variante_producto_id' => null,
            'referencia'           => $producto->referencia,
            'nombre_completo'      => $producto->nombre,
            'nombre_producto'      => $producto->nombre,
            'nombre_variante'      => null,
            'codigo_barras'        => $producto->codigo_barras,
            'sku'                  => null,
            'precio'               => (float) ($precio ?? 0),
            'stock_disponible'     => $stockDisponible,
            'controla_stock'       => (bool) $producto->controlar_stock,
            'permite_sin_stock'    => (bool) $producto->permitir_venta_sin_stock,
            'tiene_variante'       => false,
            'imagen_url'           => $producto->url_imagen_principal,
            'siigo_product_code'   => $producto->siigo_product_code,
        ];
    }

    private function filaDesdeVariante(Producto $producto, VarianteProducto $variante, int $ubicacionId, ?int $listaPrecioId): array
    {
        $precio = $listaPrecioId
            ? $variante->getPrecioFinal($listaPrecioId)
            : ($producto->precios->first()->precio ?? 0);

        $stockDisponible = 0;
        if ($producto->controlar_stock) {
            $stockVariante = StockProducto::where('producto_id', $producto->id)
                ->where('variante_producto_id', $variante->id)
                ->where('ubicacion_id', $ubicacionId)
                ->first();
            $stockDisponible = $stockVariante
                ? ($stockVariante->cantidad_disponible - $stockVariante->cantidad_reservada)
                : 0;
        }

        $nombreVariante = $variante->nombre_variante;

        return [
            'producto_id'          => $producto->id,
            'variante_producto_id' => $variante->id,
            'referencia'           => $variante->referencia_variante ?: $producto->referencia,
            'nombre_completo'      => $nombreVariante !== ''
                ? "{$producto->nombre} - {$nombreVariante}"
                : $producto->nombre,
            'nombre_producto'      => $producto->nombre,
            'nombre_variante'      => $nombreVariante !== '' ? $nombreVariante : null,
            'codigo_barras'        => $variante->codigo_barras ?: $producto->codigo_barras,
            'sku'                  => $variante->sku,
            'precio'               => (float) ($precio ?? 0),
            'stock_disponible'     => $stockDisponible,
            'controla_stock'       => (bool) $producto->controlar_stock,
            'permite_sin_stock'    => (bool) $producto->permitir_venta_sin_stock,
            'tiene_variante'       => true,
            'imagen_url'           => $producto->url_imagen_principal,
            'siigo_product_code'   => $variante->siigo_product_code ?? $producto->siigo_product_code,
        ];
    }

    private function crearItemYDescontarStock(VentaPdv $venta, array $item): ItemVentaPdv
    {
        $descuentoValor = $item['descuento_valor'] ?? ($item['descuento'] ?? 0);
        $lineaConIvaNeto = ($item['precio_unitario'] * $item['cantidad']) - $descuentoValor;
        // El iva ya viene recalculado desde crearVenta (descompuesto del precio con IVA)
        $ivaItem = round((float) ($item['iva'] ?? 0), 2);
        $subtotalItem = round($lineaConIvaNeto - $ivaItem, 2); // base gravable de la línea
        $totalItem = round($lineaConIvaNeto, 2);

        $itemVenta = ItemVentaPdv::create([
            'venta_pdv_id' => $venta->id,
            'producto_id' => $item['producto_id'],
            'variante_producto_id' => $item['variante_producto_id'] ?? null,
            'cantidad' => $item['cantidad'],
            'precio_unitario' => $item['precio_unitario'],
            'precio_original' => $item['precio_original'] ?? $item['precio_unitario'],
            'descuento' => $descuentoValor,
            'descuento_porcentaje' => $item['descuento_porcentaje'] ?? 0,
            'descuento_valor' => $descuentoValor,
            'subtotal' => $subtotalItem,
            'iva' => $ivaItem,
            'total' => $totalItem,
        ]);

        $producto = Producto::find($item['producto_id']);
        if ($producto && $producto->controlar_stock) {
            $stock = $this->obtenerStock($item['producto_id'], $item['variante_producto_id'] ?? null, $venta->ubicacion_id);

            if ($stock) {
                $stockAnterior = $stock->cantidad_disponible;
                $stock->decrement('cantidad_disponible', $item['cantidad']);

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

    public function devolverParcial(VentaPdv $venta, int $usuarioId, string $motivo, array $itemsDevolucion): array
    {
        if ($venta->estado !== 'completada') {
            return ['exito' => false, 'mensaje' => 'Solo se pueden devolver productos de ventas completadas'];
        }

        if ($venta->sesionCaja && !$venta->sesionCaja->estaAbierta()) {
            return ['exito' => false, 'mensaje' => 'No se puede hacer devolución en una sesión cerrada'];
        }

        // Cargar items con devoluciones previas
        $venta->loadMissing('items.devolucionesItems');

        // Validar items
        $itemsVenta = $venta->items->keyBy('id');
        foreach ($itemsDevolucion as $itemDev) {
            $itemVenta = $itemsVenta->get($itemDev['item_venta_pdv_id']);
            if (!$itemVenta) {
                return ['exito' => false, 'mensaje' => "Item de venta #{$itemDev['item_venta_pdv_id']} no pertenece a esta venta"];
            }
            $disponible = $itemVenta->cantidad_disponible_devolucion;
            if ($itemDev['cantidad'] > $disponible) {
                $nombre = $itemVenta->producto->nombre ?? 'Producto';
                return ['exito' => false, 'mensaje' => "No se puede devolver {$itemDev['cantidad']} de '{$nombre}' (disponible: {$disponible})"];
            }
            if ($itemDev['cantidad'] <= 0) {
                return ['exito' => false, 'mensaje' => 'La cantidad a devolver debe ser mayor a 0'];
            }
        }

        DB::beginTransaction();
        try {
            $subtotalDevolucion = 0;
            $ivaDevolucion = 0;

            // Crear registro de devolución
            $devolucion = DevolucionParcialPdv::create([
                'venta_pdv_id' => $venta->id,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo,
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0,
            ]);

            foreach ($itemsDevolucion as $itemDev) {
                $itemVenta = $itemsVenta->get($itemDev['item_venta_pdv_id']);

                // Calcular proporcionales basados en el item original
                $precioUnitario = (float) $itemVenta->precio_unitario;
                $descuentoPorcentaje = (float) ($itemVenta->descuento_porcentaje ?? 0);
                $cantidadDevuelta = (int) $itemDev['cantidad'];

                $subtotalItem = $precioUnitario * $cantidadDevuelta;
                $descuentoValorItem = $descuentoPorcentaje > 0
                    ? round($subtotalItem * ($descuentoPorcentaje / 100), 2)
                    : 0;
                $subtotalConDescuento = $subtotalItem - $descuentoValorItem;

                // Calcular IVA proporcional
                $ivaItem = 0;
                if ((float) $itemVenta->iva > 0 && $itemVenta->cantidad > 0) {
                    $ivaPorUnidad = (float) $itemVenta->iva / $itemVenta->cantidad;
                    $ivaItem = round($ivaPorUnidad * $cantidadDevuelta, 2);
                }

                $totalItem = $subtotalConDescuento + $ivaItem;

                ItemDevolucionParcialPdv::create([
                    'devolucion_parcial_pdv_id' => $devolucion->id,
                    'item_venta_pdv_id' => $itemVenta->id,
                    'producto_id' => $itemVenta->producto_id,
                    'variante_producto_id' => $itemVenta->variante_producto_id,
                    'cantidad_devuelta' => $cantidadDevuelta,
                    'precio_unitario' => $precioUnitario,
                    'descuento_porcentaje' => $descuentoPorcentaje,
                    'descuento_valor' => $descuentoValorItem,
                    'subtotal' => $subtotalConDescuento,
                    'iva' => $ivaItem,
                    'total' => $totalItem,
                ]);

                $subtotalDevolucion += $subtotalConDescuento;
                $ivaDevolucion += $ivaItem;

                // Restaurar stock
                $this->restaurarStockItem($venta, $itemVenta, $cantidadDevuelta, 'Devolución Parcial');
            }

            $totalDevolucion = $subtotalDevolucion + $ivaDevolucion;

            $devolucion->update([
                'subtotal' => round($subtotalDevolucion, 2),
                'iva' => round($ivaDevolucion, 2),
                'total' => round($totalDevolucion, 2),
            ]);

            // Actualizar total_devoluciones en la venta
            $venta->increment('total_devoluciones', round($totalDevolucion, 2));

            // Actualizar totales de sesión
            if ($venta->sesion_caja_id) {
                $sesion = $venta->sesionCaja;
                if ($sesion) {
                    $sesion->increment('total_anulaciones', round($totalDevolucion, 2));
                    $sesion->decrement('total_ventas', round($totalDevolucion, 2));
                    if ($venta->metodo_pago === 'efectivo') {
                        $sesion->decrement('total_ventas_efectivo', round($totalDevolucion, 2));
                    } elseif ($venta->metodo_pago === 'transferencia') {
                        $sesion->decrement('total_ventas_transferencia', round($totalDevolucion, 2));
                    } elseif ($venta->metodo_pago === 'mixto') {
                        // Proporcional al peso de cada método
                        $pesoEfectivo = $venta->total > 0 ? (float) $venta->monto_efectivo / (float) $venta->total : 0;
                        $sesion->decrement('total_ventas_efectivo', round($totalDevolucion * $pesoEfectivo, 2));
                        $sesion->decrement('total_ventas_transferencia', round($totalDevolucion * (1 - $pesoEfectivo), 2));
                    }
                }
            }

            DB::commit();

            return [
                'exito' => true,
                'mensaje' => "Devolución parcial procesada exitosamente por $" . number_format($totalDevolucion, 2, ',', '.'),
                'devolucion' => $devolucion->load('items'),
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error en devolución parcial venta {$venta->numero_venta}: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al procesar la devolución: ' . $e->getMessage()];
        }
    }

    private function restaurarStockItem(VentaPdv $venta, ItemVentaPdv $item, ?int $cantidadOverride = null, string $motivoPrefijo = 'Anulación'): void
    {
        $producto = $item->producto;
        if (!$producto || !$producto->controlar_stock) return;

        $cantidad = $cantidadOverride ?? $item->cantidad;
        if ($cantidad <= 0) return;

        $stock = $this->obtenerStock($item->producto_id, $item->variante_producto_id, $venta->ubicacion_id);

        if ($stock) {
            $stockAnterior = $stock->cantidad_disponible;
            $stock->increment('cantidad_disponible', $cantidad);

            $origen = $cantidadOverride !== null ? 'devolucion' : 'ajuste_inventario';

            MovimientoStock::create([
                'producto_id' => $item->producto_id,
                'variante_producto_id' => $item->variante_producto_id,
                'ubicacion_id' => $venta->ubicacion_id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stock->cantidad_disponible,
                'origen' => $origen,
                'referencia' => $venta->numero_venta,
                'motivo' => "{$motivoPrefijo} Venta PdV - {$venta->numero_venta}",
                'usuario_id' => auth()->id(),
            ]);
        }
    }

    private function obtenerStock(int $productoId, ?int $varianteId, int $ubicacionId): ?StockProducto
    {
        $query = StockProducto::where('producto_id', $productoId)->where('ubicacion_id', $ubicacionId);
        if ($varianteId) {
            $query->where('variante_producto_id', $varianteId);
        } else {
            $query->whereNull('variante_producto_id');
        }
        return $query->first();
    }
}
