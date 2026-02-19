<?php

namespace App\Services;

use App\Models\ReservaStock;
use App\Models\SolicitudCotizacion;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio para gestionar las reservas de stock de cotizaciones.
 *
 * Maneja la creación, liberación y aplicación de reservas de stock,
 * así como la verificación de disponibilidad.
 */
class ReservaStockService
{
    /**
     * Horas por defecto para expiración de reservas
     */
    const HORAS_EXPIRACION_DEFAULT = 24;

    /**
     * Verificar disponibilidad de stock para los items
     *
     * @param array $items Array de items con producto_id, variante_id (opcional), cantidad
     * @return array ['disponible' => bool, 'errores' => array, 'detalles' => array]
     */
    public function verificarDisponibilidad(array $items): array
    {
        $resultado = [
            'disponible' => true,
            'errores' => [],
            'detalles' => [],
        ];

        foreach ($items as $index => $item) {
            $productoId = $item['producto_id'];
            $varianteId = $item['variante_id'] ?? null;
            $cantidadSolicitada = $item['cantidad'];

            $stock = $this->obtenerStock($productoId, $varianteId);

            if (!$stock) {
                $resultado['detalles'][$index] = [
                    'producto_id' => $productoId,
                    'variante_id' => $varianteId,
                    'cantidad_solicitada' => $cantidadSolicitada,
                    'stock_disponible' => 0,
                    'stock_reservado' => 0,
                    'puede_reservar' => false,
                    'sin_control_stock' => true,
                ];
                continue;
            }

            $disponibleReal = $stock->cantidad_disponible - $stock->cantidad_reservada;
            $puedeReservar = $disponibleReal >= $cantidadSolicitada;

            // Verificar si el producto permite venta sin stock
            $producto = $stock->producto;
            $permiteVentaSinStock = $producto && $producto->permitir_venta_sin_stock;

            if (!$puedeReservar && !$permiteVentaSinStock) {
                $resultado['disponible'] = false;
                $resultado['errores'][] = [
                    'index' => $index,
                    'producto_id' => $productoId,
                    'variante_id' => $varianteId,
                    'mensaje' => "Stock insuficiente. Disponible: {$disponibleReal}, Solicitado: {$cantidadSolicitada}",
                ];
            }

            $resultado['detalles'][$index] = [
                'producto_id' => $productoId,
                'variante_id' => $varianteId,
                'cantidad_solicitada' => $cantidadSolicitada,
                'stock_disponible' => $stock->cantidad_disponible,
                'stock_reservado' => $stock->cantidad_reservada,
                'disponible_real' => $disponibleReal,
                'puede_reservar' => $puedeReservar || $permiteVentaSinStock,
                'permite_venta_sin_stock' => $permiteVentaSinStock,
                'stock_id' => $stock->id,
            ];
        }

        return $resultado;
    }

    /**
     * Reservar stock para una cotización
     *
     * @param SolicitudCotizacion $solicitud
     * @param int $horasExpiracion
     * @return array ['exito' => bool, 'reservas_creadas' => int, 'errores' => array]
     */
    public function reservarParaCotizacion(
        SolicitudCotizacion $solicitud,
        int $horasExpiracion = self::HORAS_EXPIRACION_DEFAULT
    ): array {
        $resultado = [
            'exito' => true,
            'reservas_creadas' => 0,
            'errores' => [],
        ];

        $expiraEn = now()->addHours($horasExpiracion);

        DB::beginTransaction();

        try {
            foreach ($solicitud->items as $item) {
                $stock = $this->obtenerStock($item->producto_id, $item->variante_producto_id);

                if (!$stock) {
                    // Producto sin control de stock, omitir reserva
                    continue;
                }

                $disponibleReal = $stock->cantidad_disponible - $stock->cantidad_reservada;
                $producto = $stock->producto;
                $permiteVentaSinStock = $producto && $producto->permitir_venta_sin_stock;

                // Si no hay suficiente stock y no permite venta sin stock
                if ($disponibleReal < $item->cantidad && !$permiteVentaSinStock) {
                    $resultado['errores'][] = [
                        'item_id' => $item->id,
                        'producto_id' => $item->producto_id,
                        'mensaje' => "Stock insuficiente para {$item->nombre_producto}",
                    ];
                    $resultado['exito'] = false;
                    continue;
                }

                // Calcular cantidad a reservar (solo lo disponible si es necesario)
                $cantidadAReservar = min($item->cantidad, $disponibleReal);

                if ($cantidadAReservar <= 0) {
                    // No hay nada que reservar pero se permite la venta
                    continue;
                }

                // Crear la reserva
                $reserva = ReservaStock::create([
                    'solicitud_cotizacion_id' => $solicitud->id,
                    'item_solicitud_id' => $item->id,
                    'stock_producto_id' => $stock->id,
                    'cantidad_reservada' => $cantidadAReservar,
                    'expira_en' => $expiraEn,
                    'estado' => ReservaStock::ESTADO_ACTIVA,
                ]);

                // Actualizar cantidad reservada en el stock
                $stock->increment('cantidad_reservada', $cantidadAReservar);

                $resultado['reservas_creadas']++;
            }

            if ($resultado['exito'] || $resultado['reservas_creadas'] > 0) {
                // Actualizar la solicitud
                $solicitud->update([
                    'tiene_reserva_stock' => true,
                    'reserva_expira_en' => $expiraEn,
                    'reserva_liberada_en' => null,
                ]);

                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al reservar stock para cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = [
                'mensaje' => 'Error interno al procesar las reservas',
                'detalle' => $e->getMessage(),
            ];
        }

        return $resultado;
    }

    /**
     * Liberar todas las reservas de una cotización
     *
     * @param SolicitudCotizacion $solicitud
     * @param string $motivo
     * @param int|null $usuarioId
     * @return int Número de reservas liberadas
     */
    public function liberarReservasCotizacion(
        SolicitudCotizacion $solicitud,
        string $motivo,
        ?int $usuarioId = null
    ): int {
        $liberadas = 0;

        DB::beginTransaction();

        try {
            $reservasActivas = $solicitud->reservas()
                ->where('estado', ReservaStock::ESTADO_ACTIVA)
                ->get();

            foreach ($reservasActivas as $reserva) {
                $nuevoEstado = $usuarioId
                    ? ReservaStock::ESTADO_LIBERADA_MANUAL
                    : ReservaStock::ESTADO_EXPIRADA;

                $reserva->update([
                    'estado' => $nuevoEstado,
                    'liberada_en' => now(),
                    'motivo_liberacion' => $motivo,
                    'liberada_por' => $usuarioId,
                ]);

                // Liberar en el stock
                $stock = $reserva->stockProducto;
                if ($stock) {
                    $stock->decrement('cantidad_reservada', $reserva->cantidad_reservada);
                }

                $liberadas++;
            }

            // Actualizar la solicitud
            $solicitud->update([
                'tiene_reserva_stock' => false,
                'reserva_liberada_en' => now(),
            ]);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al liberar reservas de cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
        }

        return $liberadas;
    }

    /**
     * Aplicar reservas (convertir a salida real de stock)
     *
     * @param SolicitudCotizacion $solicitud
     * @return array ['exito' => bool, 'movimientos_creados' => int, 'errores' => array]
     */
    public function aplicarReservas(SolicitudCotizacion $solicitud): array
    {
        $resultado = [
            'exito' => true,
            'movimientos_creados' => 0,
            'errores' => [],
        ];

        DB::beginTransaction();

        try {
            $reservasActivas = $solicitud->reservas()
                ->where('estado', ReservaStock::ESTADO_ACTIVA)
                ->get();

            foreach ($reservasActivas as $reserva) {
                $stock = $reserva->stockProducto;

                if (!$stock) {
                    continue;
                }

                $stockAnterior = $stock->cantidad_disponible;

                // Descontar del stock disponible
                $stock->decrement('cantidad_disponible', $reserva->cantidad_reservada);

                // Liberar de reservado (ya se aplicó)
                $stock->decrement('cantidad_reservada', $reserva->cantidad_reservada);

                // Crear movimiento de stock
                MovimientoStock::create([
                    'producto_id' => $stock->producto_id,
                    'variante_producto_id' => $stock->variante_producto_id,
                    'ubicacion_id' => $stock->ubicacion_id,
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $reserva->cantidad_reservada,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stock->cantidad_disponible,
                    'origen' => 'venta',
                    'referencia' => $solicitud->numero_solicitud,
                    'motivo' => "Venta - Cotización {$solicitud->numero_solicitud}",
                    'usuario_id' => auth()->id(),
                ]);

                // Marcar reserva como aplicada
                $reserva->update(['estado' => ReservaStock::ESTADO_APLICADA]);

                $resultado['movimientos_creados']++;
            }

            // Para items sin reserva (productos sin control de stock o que permiten venta sin stock)
            foreach ($solicitud->items as $item) {
                $tieneReserva = $solicitud->reservas()
                    ->where('item_solicitud_id', $item->id)
                    ->where('estado', ReservaStock::ESTADO_APLICADA)
                    ->exists();

                if ($tieneReserva) {
                    continue; // Ya procesado arriba
                }

                $stock = $this->obtenerStock($item->producto_id, $item->variante_producto_id);

                if (!$stock) {
                    continue; // Sin control de stock
                }

                $stockAnterior = $stock->cantidad_disponible;
                $cantidadASacar = $item->cantidad;

                // Descontar del stock disponible
                $stock->decrement('cantidad_disponible', $cantidadASacar);

                // Crear movimiento de stock
                MovimientoStock::create([
                    'producto_id' => $stock->producto_id,
                    'variante_producto_id' => $stock->variante_producto_id,
                    'ubicacion_id' => $stock->ubicacion_id,
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $cantidadASacar,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stock->cantidad_disponible,
                    'origen' => 'venta',
                    'referencia' => $solicitud->numero_solicitud,
                    'motivo' => "Venta - Cotización {$solicitud->numero_solicitud} (sin reserva previa)",
                    'usuario_id' => auth()->id(),
                ]);

                $resultado['movimientos_creados']++;
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al aplicar reservas de cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = [
                'mensaje' => 'Error interno al procesar la aplicación de reservas',
                'detalle' => $e->getMessage(),
            ];
        }

        return $resultado;
    }

    /**
     * Liberar todas las reservas expiradas del sistema
     *
     * @return int Número de reservas liberadas
     */
    public function liberarReservasExpiradas(): int
    {
        $liberadas = 0;

        $reservasExpiradas = ReservaStock::where('estado', ReservaStock::ESTADO_ACTIVA)
            ->where('expira_en', '<', now())
            ->get();

        foreach ($reservasExpiradas as $reserva) {
            DB::beginTransaction();

            try {
                $reserva->update([
                    'estado' => ReservaStock::ESTADO_EXPIRADA,
                    'liberada_en' => now(),
                    'motivo_liberacion' => 'Expiración automática',
                ]);

                // Liberar en el stock
                $stock = $reserva->stockProducto;
                if ($stock) {
                    $stock->decrement('cantidad_reservada', $reserva->cantidad_reservada);
                }

                // Actualizar solicitud si todas sus reservas están liberadas
                $solicitud = $reserva->solicitudCotizacion;
                $reservasActivasRestantes = $solicitud->reservas()
                    ->where('estado', ReservaStock::ESTADO_ACTIVA)
                    ->count();

                if ($reservasActivasRestantes === 0) {
                    $solicitud->update([
                        'tiene_reserva_stock' => false,
                        'reserva_liberada_en' => now(),
                    ]);
                }

                DB::commit();
                $liberadas++;

            } catch (Exception $e) {
                DB::rollBack();
                Log::error("Error al liberar reserva expirada {$reserva->id}: " . $e->getMessage());
            }
        }

        if ($liberadas > 0) {
            Log::info("Liberadas {$liberadas} reservas expiradas de stock");
        }

        return $liberadas;
    }

    /**
     * Renovar reservas de una cotización
     *
     * @param SolicitudCotizacion $solicitud
     * @param int $horasAdicionales
     * @return bool
     */
    public function renovarReservas(SolicitudCotizacion $solicitud, int $horasAdicionales = 24): bool
    {
        if ($solicitud->estado !== SolicitudCotizacion::ESTADO_PENDIENTE) {
            return false;
        }

        $nuevaExpiracion = now()->addHours($horasAdicionales);

        DB::beginTransaction();

        try {
            // Renovar reservas activas
            $solicitud->reservas()
                ->where('estado', ReservaStock::ESTADO_ACTIVA)
                ->update(['expira_en' => $nuevaExpiracion]);

            // Actualizar solicitud
            $solicitud->update([
                'reserva_expira_en' => $nuevaExpiracion,
                'reserva_liberada_en' => null,
                'tiene_reserva_stock' => true,
            ]);

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al renovar reservas de cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener stock de un producto/variante
     *
     * @param int $productoId
     * @param int|null $varianteId
     * @return StockProducto|null
     */
    private function obtenerStock(int $productoId, ?int $varianteId = null): ?StockProducto
    {
        $query = StockProducto::where('producto_id', $productoId);

        if ($varianteId) {
            $query->where('variante_producto_id', $varianteId);
        } else {
            $query->whereNull('variante_producto_id');
        }

        return $query->first();
    }

    /**
     * Liberar reservas directamente del stock (sin depender de registros ReservaStock)
     * Útil para cotizaciones aplicadas que tienen cantidad_reservada pero sin registros de reserva
     *
     * @param SolicitudCotizacion $solicitud
     * @return bool
     */
    public function liberarReservasDirectamente(SolicitudCotizacion $solicitud): bool
    {
        DB::beginTransaction();

        try {
            foreach ($solicitud->items as $item) {
                $stockQuery = StockProducto::where('producto_id', $item->producto_id);

                if ($item->variante_id) {
                    $stockQuery->where('variante_producto_id', $item->variante_id);
                } else {
                    $stockQuery->whereNull('variante_producto_id');
                }

                $stock = $stockQuery->lockForUpdate()->first();

                if ($stock && $stock->cantidad_reservada > 0) {
                    $cantidadALiberar = min($item->cantidad, $stock->cantidad_reservada);

                    if ($cantidadALiberar > 0) {
                        $reservadoAntes = $stock->cantidad_reservada;
                        $stock->cantidad_reservada -= $cantidadALiberar;
                        $stock->save();

                        Log::info("Liberada reserva directa: Producto {$item->producto_id}, Cantidad {$cantidadALiberar}, Reservado antes: {$reservadoAntes}, Reservado después: {$stock->cantidad_reservada}");
                    }
                }
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error liberando reservas directamente cotización {$solicitud->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reservar stock dentro de una transacción existente (sin iniciar nueva transacción)
     * Para usar cuando ya estamos dentro de un DB::beginTransaction()
     *
     * @param SolicitudCotizacion $solicitud
     * @return bool
     */
    public function reservarParaCotizacionEnTransaccion(SolicitudCotizacion $solicitud): bool
    {
        // NO usar DB::beginTransaction() - ya estamos en una transacción

        try {
            foreach ($solicitud->items as $item) {
                $stockQuery = StockProducto::where('producto_id', $item->producto_id);

                if ($item->variante_id) {
                    $stockQuery->where('variante_producto_id', $item->variante_id);
                } else {
                    $stockQuery->whereNull('variante_producto_id');
                }

                $stock = $stockQuery->first();

                if (!$stock) {
                    // Producto sin control de stock, omitir
                    continue;
                }

                $disponibleReal = $stock->cantidad_disponible - $stock->cantidad_reservada;
                $producto = $stock->producto;
                $permiteVentaSinStock = $producto && $producto->permitir_venta_sin_stock;

                if ($disponibleReal < $item->cantidad && !$permiteVentaSinStock) {
                    $nombreProducto = $item->nombre_producto ?? ($producto->nombre ?? "ID {$item->producto_id}");
                    $infoVariante = $item->info_variante ? " ({$item->info_variante})" : '';
                    throw new Exception("Stock insuficiente para {$nombreProducto}{$infoVariante}. Disponible: {$disponibleReal}, Solicitado: {$item->cantidad}");
                }

                // Calcular cantidad a reservar
                $cantidadAReservar = min($item->cantidad, $disponibleReal);

                if ($cantidadAReservar > 0) {
                    $stock->cantidad_reservada += $cantidadAReservar;
                    $stock->save();

                    Log::info("Reservado en transacción: Producto {$item->producto_id}, Cantidad {$cantidadAReservar}");
                }
            }

            return true;

        } catch (Exception $e) {
            Log::error("Error reservando stock en transacción: " . $e->getMessage());
            throw $e; // Re-lanzar para que la transacción padre haga rollback
        }
    }

    /**
     * Obtener resumen de reservas activas del sistema
     *
     * @return array
     */
    public function obtenerResumenReservas(): array
    {
        return [
            'total_activas' => ReservaStock::activas()->count(),
            'total_expiradas' => ReservaStock::expiradas()->count(),
            'proximas_expirar' => ReservaStock::proximasAExpirar(2)->count(),
            'cotizaciones_con_reserva' => SolicitudCotizacion::conReservaActiva()->count(),
            'cotizaciones_reserva_expirada' => SolicitudCotizacion::conReservaExpirada()->count(),
        ];
    }
}
