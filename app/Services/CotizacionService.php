<?php

namespace App\Services;

use App\Events\CotizacionCreada;
use App\Models\Cliente;
use App\Models\EnlaceAcceso;
use App\Models\ItemSolicitudCotizacion;
use App\Models\Producto;
use App\Models\SolicitudCotizacion;
use App\Models\VarianteProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

/**
 * Servicio para gestionar cotizaciones.
 *
 * Maneja la creación, actualización, cambio de estado y eliminación
 * de cotizaciones, integrando el sistema de reservas de stock.
 */
class CotizacionService
{
    protected ReservaStockService $reservaService;

    public function __construct(ReservaStockService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * Crear una nueva cotización con reserva de stock
     *
     * @param array $datos Datos de la cotización
     * @param Cliente $cliente
     * @param EnlaceAcceso|null $enlace
     * @param int|null $usuarioId Usuario que crea la cotización
     * @return array ['exito' => bool, 'solicitud' => SolicitudCotizacion|null, 'errores' => array]
     */
    public function crear(
        array $datos,
        Cliente $cliente,
        ?EnlaceAcceso $enlace = null,
        ?int $usuarioId = null
    ): array {
        $resultado = [
            'exito' => true,
            'solicitud' => null,
            'errores' => [],
            'advertencias' => [],
        ];

        // Verificar disponibilidad de stock primero
        $verificacion = $this->reservaService->verificarDisponibilidad($datos['items']);

        if (!$verificacion['disponible']) {
            // Hay items sin stock suficiente
            foreach ($verificacion['errores'] as $error) {
                $resultado['advertencias'][] = $error['mensaje'];
            }
        }

        DB::beginTransaction();

        try {
            // Crear la solicitud de cotización
            $solicitud = SolicitudCotizacion::create([
                'numero_solicitud' => $this->generarNumeroSolicitud(),
                'cliente_id' => $cliente->id,
                'enlace_acceso_id' => $enlace?->id,
                'created_by' => $usuarioId,
                'estado' => SolicitudCotizacion::ESTADO_PENDIENTE,
                'monto_total' => 0,
                'valor_flete' => $datos['valor_flete'] ?? 0,
                'descuento_total' => $datos['descuento_total'] ?? 0,
                'notas_cliente' => $datos['notas_cliente'] ?? null,
                'observaciones_vendedor' => $datos['observaciones_vendedor'] ?? null,
            ]);

            // Crear los items de la cotización
            $montoTotal = 0;
            foreach ($datos['items'] as $itemData) {
                $item = $this->crearItem($solicitud, $itemData, $cliente);
                $montoTotal += $item->precio_total;
            }

            // Agregar flete y restar descuento
            $montoTotal += ($datos['valor_flete'] ?? 0);
            $montoTotal -= ($datos['descuento_total'] ?? 0);

            $solicitud->update(['monto_total' => max(0, $montoTotal)]);

            // Intentar reservar stock
            $reservaResultado = $this->reservaService->reservarParaCotizacion($solicitud);

            if (!$reservaResultado['exito']) {
                foreach ($reservaResultado['errores'] as $error) {
                    $resultado['advertencias'][] = $error['mensaje'];
                }
            }

            $resultado['solicitud'] = $solicitud->fresh(['items', 'cliente', 'reservas']);

            DB::commit();

            // Disparar evento para crear cuenta de cliente automáticamente
            event(new CotizacionCreada($solicitud));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al crear cotización: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Error al crear la cotización: ' . $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Actualizar una cotización existente
     *
     * @param SolicitudCotizacion $solicitud
     * @param array $datos
     * @param int $usuarioId
     * @return array
     */
    public function actualizar(SolicitudCotizacion $solicitud, array $datos, int $usuarioId): array
    {
        $resultado = [
            'exito' => true,
            'solicitud' => null,
            'errores' => [],
            'advertencias' => [],
        ];

        if (!auth()->user()->hasAnyRole(['facturacion', 'auxiliar_administrativo']) && !$solicitud->esEditable()) {
            $resultado['exito'] = false;
            $resultado['errores'][] = 'La cotización no puede ser editada en su estado actual';
            return $resultado;
        }

        DB::beginTransaction();

        try {
            // Liberar reservas actuales
            if ($solicitud->tiene_reserva_stock) {
                $this->reservaService->liberarReservasCotizacion(
                    $solicitud,
                    'Edición de cotización',
                    $usuarioId
                );
            }

            // Eliminar items actuales
            $solicitud->items()->delete();

            // Crear nuevos items
            $cliente = $solicitud->cliente;
            $montoTotal = 0;

            foreach ($datos['items'] as $itemData) {
                $item = $this->crearItem($solicitud, $itemData, $cliente);
                $montoTotal += $item->precio_total;
            }

            // Agregar flete y restar descuento
            $subtotalProductos = $montoTotal; // Guardamos el subtotal de productos para calcular IVA
            $montoTotal += ($datos['valor_flete'] ?? $solicitud->valor_flete ?? 0);
            $montoTotal -= ($datos['descuento_total'] ?? $solicitud->descuento_total ?? 0);

            // Calcular IVA si aplica
            $porcentajeIva = isset($datos['porcentaje_iva']) && $datos['porcentaje_iva'] !== '' && $datos['porcentaje_iva'] !== null
                ? (float) $datos['porcentaje_iva']
                : null;
            $valorIva = null;

            if ($porcentajeIva && $porcentajeIva > 0) {
                // El IVA se calcula sobre el subtotal de productos (sin flete ni descuento)
                $valorIva = $subtotalProductos * ($porcentajeIva / 100);
            }

            // Calcular fecha de vencimiento a partir de días
            $fechaVencimiento = $solicitud->fecha_vencimiento;
            if (isset($datos['dias_vencimiento']) && $datos['dias_vencimiento'] !== null) {
                $fechaVencimiento = $datos['dias_vencimiento'] > 0
                    ? now()->addDays((int) $datos['dias_vencimiento'])->toDateString()
                    : null;
            }

            // Actualizar solicitud
            $solicitud->update([
                'monto_total' => max(0, $montoTotal),
                'valor_flete' => $datos['valor_flete'] ?? $solicitud->valor_flete,
                'descuento_total' => $datos['descuento_total'] ?? $solicitud->descuento_total,
                'porcentaje_iva' => $porcentajeIva,
                'valor_iva' => $valorIva,
                'notas_cliente' => $datos['notas_cliente'] ?? $solicitud->notas_cliente,
                'observaciones_vendedor' => $datos['observaciones_vendedor'] ?? $solicitud->observaciones_vendedor,
                'forma_pago_factura' => $datos['forma_pago_factura'] ?? $solicitud->forma_pago_factura,
                'fecha_vencimiento' => $fechaVencimiento,
                'editada_en' => now(),
                'editada_por' => $usuarioId,
            ]);

            // Crear nuevas reservas
            $reservaResultado = $this->reservaService->reservarParaCotizacion($solicitud);

            if (!$reservaResultado['exito']) {
                foreach ($reservaResultado['errores'] as $error) {
                    $resultado['advertencias'][] = $error['mensaje'];
                }
            }

            $resultado['solicitud'] = $solicitud->fresh(['items', 'cliente', 'reservas']);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Error al actualizar la cotización: ' . $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Cambiar el estado de una cotización
     *
     * @param SolicitudCotizacion $solicitud
     * @param string $nuevoEstado
     * @param array $datos Datos adicionales (observaciones, motivo, procesar_stock)
     * @param int $usuarioId
     * @return array
     */
    public function cambiarEstado(
        SolicitudCotizacion $solicitud,
        string $nuevoEstado,
        array $datos,
        int $usuarioId
    ): array {
        $resultado = [
            'exito' => true,
            'solicitud' => null,
            'errores' => [],
        ];

        if ($solicitud->estado !== SolicitudCotizacion::ESTADO_PENDIENTE) {
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Solo se pueden cambiar cotizaciones pendientes';
            return $resultado;
        }

        DB::beginTransaction();

        try {
            switch ($nuevoEstado) {
                case SolicitudCotizacion::ESTADO_APLICADA:
                    $resultado = $this->aplicarCotizacion($solicitud, $datos, $usuarioId);
                    break;

                case SolicitudCotizacion::ESTADO_RECHAZADA:
                    $resultado = $this->rechazarCotizacion($solicitud, $datos, $usuarioId);
                    break;

                default:
                    $resultado['exito'] = false;
                    $resultado['errores'][] = 'Estado no válido: ' . $nuevoEstado;
            }

            if ($resultado['exito']) {
                DB::commit();
            } else {
                DB::rollBack();
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al cambiar estado de cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Error al cambiar el estado: ' . $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Aplicar (aprobar) una cotización
     */
    private function aplicarCotizacion(SolicitudCotizacion $solicitud, array $datos, int $usuarioId): array
    {
        $resultado = [
            'exito' => true,
            'solicitud' => null,
            'errores' => [],
        ];

        // Verificar observaciones obligatorias del vendedor
        if (empty($datos['observaciones_vendedor'])) {
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Las observaciones del vendedor son obligatorias al aprobar';
            return $resultado;
        }

        // Procesar stock si está indicado
        $procesarStock = $datos['procesar_stock'] ?? true;

        if ($procesarStock) {
            $reservaResultado = $this->reservaService->aplicarReservas($solicitud);

            if (!$reservaResultado['exito']) {
                $resultado['errores'] = array_merge($resultado['errores'], $reservaResultado['errores']);
                // No detenemos, solo advertimos
            }
        } else {
            // Si no se procesa stock, liberar las reservas
            $this->reservaService->liberarReservasCotizacion(
                $solicitud,
                'Aprobación sin procesamiento de stock',
                $usuarioId
            );
        }

        // Marcar como aplicada
        $solicitud->update([
            'estado' => SolicitudCotizacion::ESTADO_APLICADA,
            'aplicada_en' => now(),
            'aplicada_por' => $usuarioId,
            'observaciones_admin' => $datos['observaciones_admin'] ?? null,
            'observaciones_vendedor' => $datos['observaciones_vendedor'],
        ]);

        $resultado['solicitud'] = $solicitud->fresh();

        return $resultado;
    }

    /**
     * Rechazar una cotización
     */
    private function rechazarCotizacion(SolicitudCotizacion $solicitud, array $datos, int $usuarioId): array
    {
        $resultado = [
            'exito' => true,
            'solicitud' => null,
            'errores' => [],
        ];

        if (empty($datos['motivo_rechazo'])) {
            $resultado['exito'] = false;
            $resultado['errores'][] = 'El motivo de rechazo es obligatorio';
            return $resultado;
        }

        // Liberar reservas
        if ($solicitud->tiene_reserva_stock) {
            $this->reservaService->liberarReservasCotizacion(
                $solicitud,
                'Cotización rechazada: ' . $datos['motivo_rechazo'],
                $usuarioId
            );
        }

        // Marcar como rechazada
        $solicitud->update([
            'estado' => SolicitudCotizacion::ESTADO_RECHAZADA,
            'rechazada_en' => now(),
            'rechazada_por' => $usuarioId,
            'motivo_rechazo' => $datos['motivo_rechazo'],
        ]);

        $resultado['solicitud'] = $solicitud->fresh();

        return $resultado;
    }

    /**
     * Clonar una cotización existente
     *
     * @param SolicitudCotizacion $solicitudOriginal
     * @param int $usuarioId
     * @return array
     */
    public function clonar(SolicitudCotizacion $solicitudOriginal, int $usuarioId): array
    {
        $resultado = [
            'exito' => true,
            'solicitud' => null,
            'errores' => [],
        ];

        DB::beginTransaction();

        try {
            // Crear nueva solicitud
            $nuevaSolicitud = SolicitudCotizacion::create([
                'numero_solicitud' => $this->generarNumeroSolicitud(),
                'cliente_id' => $solicitudOriginal->cliente_id,
                'enlace_acceso_id' => null, // Nueva cotización, sin enlace
                'created_by' => $usuarioId,
                'estado' => SolicitudCotizacion::ESTADO_PENDIENTE,
                'monto_total' => 0,
                'valor_flete' => $solicitudOriginal->valor_flete,
                'descuento_total' => $solicitudOriginal->descuento_total,
                'notas_cliente' => "Clonada de {$solicitudOriginal->numero_solicitud}",
            ]);

            // Clonar items (con precios actualizados si es posible)
            $montoTotal = 0;
            $cliente = $solicitudOriginal->cliente;

            foreach ($solicitudOriginal->items as $itemOriginal) {
                $producto = Producto::find($itemOriginal->producto_id);

                if (!$producto || !$producto->activo) {
                    // Producto ya no existe o está inactivo, omitir
                    $resultado['advertencias'][] = "Producto {$itemOriginal->nombre_producto} ya no está disponible";
                    continue;
                }

                // Obtener precio actual
                $precioActual = $this->obtenerPrecioProducto($producto, $itemOriginal->variante_producto_id, $cliente);

                $item = ItemSolicitudCotizacion::create([
                    'solicitud_cotizacion_id' => $nuevaSolicitud->id,
                    'producto_id' => $producto->id,
                    'variante_producto_id' => $itemOriginal->variante_producto_id,
                    'cantidad' => $itemOriginal->cantidad,
                    'precio_unitario' => $precioActual,
                    'precio_total' => $precioActual * $itemOriginal->cantidad,
                    'precio_original' => $precioActual,
                    'precio_editado_manualmente' => false,
                    'referencia_producto' => $producto->referencia,
                    'nombre_producto' => $producto->nombre,
                    'marca_producto' => $producto->marca,
                    'info_variante' => $itemOriginal->info_variante,
                ]);

                $montoTotal += $item->precio_total;
            }

            // Agregar flete y restar descuento
            $montoTotal += ($nuevaSolicitud->valor_flete ?? 0);
            $montoTotal -= ($nuevaSolicitud->descuento_total ?? 0);

            $nuevaSolicitud->update(['monto_total' => max(0, $montoTotal)]);

            // Crear reservas para la nueva cotización
            $this->reservaService->reservarParaCotizacion($nuevaSolicitud);

            $resultado['solicitud'] = $nuevaSolicitud->fresh(['items', 'cliente', 'reservas']);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al clonar cotización {$solicitudOriginal->numero_solicitud}: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Error al clonar la cotización: ' . $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Eliminar (soft delete) una cotización
     *
     * @param SolicitudCotizacion $solicitud
     * @param int $usuarioId
     * @return array
     */
    public function eliminar(SolicitudCotizacion $solicitud, int $usuarioId): array
    {
        $resultado = [
            'exito' => true,
            'errores' => [],
        ];

        if (!$solicitud->esEliminable()) {
            $resultado['exito'] = false;
            $resultado['errores'][] = 'La cotización no puede ser eliminada en su estado actual';
            return $resultado;
        }

        DB::beginTransaction();

        try {
            // Liberar reservas si las tiene
            if ($solicitud->tiene_reserva_stock) {
                $this->reservaService->liberarReservasCotizacion(
                    $solicitud,
                    'Cotización eliminada',
                    $usuarioId
                );
            }

            // Soft delete
            $solicitud->delete();

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
            $resultado['exito'] = false;
            $resultado['errores'][] = 'Error al eliminar la cotización: ' . $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Crear un item de cotización
     */
    private function crearItem(SolicitudCotizacion $solicitud, array $itemData, Cliente $cliente): ItemSolicitudCotizacion
    {
        $producto = Producto::with(['variantes'])->findOrFail($itemData['producto_id']);
        $variante = null;
        $infoVariante = null;

        if (!empty($itemData['variante_id'])) {
            $variante = VarianteProducto::find($itemData['variante_id']);
            if ($variante) {
                $infoVariante = trim(($variante->referencia_variante ?? '') . ' ' . ($variante->color ?? ''));
            }
        }

        // Determinar precio
        $precioLista = $this->obtenerPrecioProducto($producto, $variante?->id, $cliente);

        $precioUnitario = $itemData['precio_manual'] ?? $precioLista;
        $precioEditado = isset($itemData['precio_manual']) && $itemData['precio_manual'] != $precioLista;

        $cantidad = $itemData['cantidad'];

        return ItemSolicitudCotizacion::create([
            'solicitud_cotizacion_id' => $solicitud->id,
            'producto_id' => $producto->id,
            'variante_producto_id' => $variante?->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'precio_total' => $precioUnitario * $cantidad,
            'precio_original' => $precioLista,
            'precio_editado_manualmente' => $precioEditado,
            'referencia_producto' => $producto->referencia,
            'nombre_producto' => $producto->nombre,
            'marca_producto' => $producto->marca,
            'info_variante' => $infoVariante,
        ]);
    }

    /**
     * Obtener precio de un producto según la lista del cliente
     */
    private function obtenerPrecioProducto(Producto $producto, ?int $varianteId, Cliente $cliente): float
    {
        $listaPrecioId = $cliente->lista_precio_id;

        if ($varianteId) {
            // Buscar precio de variante
            $precioVariante = $producto->variantes()
                ->where('id', $varianteId)
                ->first()
                ?->precios()
                ->where('lista_precio_id', $listaPrecioId)
                ->first();

            if ($precioVariante && $precioVariante->precio > 0) {
                return $precioVariante->precio;
            }
        }

        // Buscar precio del producto
        $precioProducto = $producto->precios()
            ->where('lista_precio_id', $listaPrecioId)
            ->first();

        return $precioProducto?->precio ?? 0;
    }

    /**
     * Generar número único de solicitud
     */
    private function generarNumeroSolicitud(): string
    {
        return 'SC-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
    }
}
