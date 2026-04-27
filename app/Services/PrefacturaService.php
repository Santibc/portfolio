<?php

namespace App\Services;

use App\Models\Prefactura;
use App\Models\ItemPrefactura;
use App\Models\SesionCaja;
use App\Models\StockProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PrefacturaService
{
    protected VentaPdvServiceV2 $ventaService;

    public function __construct(VentaPdvServiceV2 $ventaService)
    {
        $this->ventaService = $ventaService;
    }

    public function crear(array $datos, array $items, int $usuarioId): array
    {
        if (empty($items)) {
            return ['exito' => false, 'mensaje' => 'No se puede crear una prefactura sin productos'];
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['precio_unitario'] * $item['cantidad'];
            }

            $descuentoGlobal = $datos['descuento_global'] ?? 0;
            $iva = $datos['iva'] ?? 0;
            $total = $subtotal - $descuentoGlobal + $iva;

            $prefactura = Prefactura::create([
                'numero_prefactura' => Prefactura::generarNumeroPrefactura(),
                'cliente_id' => $datos['cliente_id'] ?? null,
                'nombre_cliente' => $datos['nombre_cliente'] ?? null,
                'lista_precio_id' => $datos['lista_precio_id'],
                'ubicacion_id' => $datos['ubicacion_id'],
                'subtotal' => round($subtotal, 2),
                'descuento_global' => round($descuentoGlobal, 2),
                'iva' => round($iva, 2),
                'total' => round($total, 2),
                'observaciones' => $datos['observaciones'] ?? null,
                'usuario_creador_id' => $usuarioId,
            ]);

            foreach ($items as $item) {
                ItemPrefactura::create([
                    'prefactura_id' => $prefactura->id,
                    'producto_id' => $item['producto_id'],
                    'variante_producto_id' => $item['variante_producto_id'] ?? null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'precio_original' => $item['precio_original'] ?? $item['precio_unitario'],
                    'descuento_porcentaje' => $item['descuento_porcentaje'] ?? 0,
                    'descuento_valor' => $item['descuento_valor'] ?? 0,
                    'subtotal' => ($item['precio_unitario'] * $item['cantidad']) - ($item['descuento_valor'] ?? 0),
                    'iva' => $item['iva'] ?? 0,
                    'total' => ($item['precio_unitario'] * $item['cantidad']) - ($item['descuento_valor'] ?? 0) + ($item['iva'] ?? 0),
                    'observaciones' => $item['observaciones'] ?? null,
                ]);
            }

            DB::commit();

            return [
                'exito' => true,
                'prefactura' => $prefactura->load('items.producto', 'items.variante'),
                'mensaje' => "Prefactura {$prefactura->numero_prefactura} creada exitosamente",
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al crear prefactura: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al crear la prefactura: ' . $e->getMessage()];
        }
    }

    public function aceptar(int $prefacturaId, int $cajeroId, int $sesionCajaId, ?array $datosModificados = null): array
    {
        $prefactura = Prefactura::with('items')->findOrFail($prefacturaId);

        if ($prefactura->estado !== 'pendiente') {
            return ['exito' => false, 'mensaje' => 'Solo se pueden aceptar prefacturas pendientes'];
        }

        $sesion = SesionCaja::with('caja')->findOrFail($sesionCajaId);
        if (!$sesion->estaAbierta()) {
            return ['exito' => false, 'mensaje' => 'La sesión de caja no está abierta'];
        }

        DB::beginTransaction();
        try {
            // Build sale data from prefactura
            $datosVenta = [
                'ubicacion_id' => $sesion->caja->ubicacion_id,
                'cliente_id' => $prefactura->cliente_id,
                'nombre_cliente' => $prefactura->nombre_cliente ?? 'Consumidor Final',
                'lista_precio_id' => $prefactura->lista_precio_id,
                'descuento_global' => $datosModificados['descuento_global'] ?? $prefactura->descuento_global,
                'metodo_pago' => $datosModificados['metodo_pago'] ?? 'efectivo',
                'monto_efectivo' => $datosModificados['monto_efectivo'] ?? null,
                'monto_transferencia' => $datosModificados['monto_transferencia'] ?? null,
                'monto_recibido' => $datosModificados['monto_recibido'] ?? null,
                'cambio' => $datosModificados['cambio'] ?? null,
                'tipo_transferencia' => $datosModificados['tipo_transferencia'] ?? null,
                'comprobante_pago' => $datosModificados['comprobante_pago'] ?? null,
                'notas' => $prefactura->observaciones,
                'prefactura_id' => $prefactura->id,
            ];

            // Use modified items or original
            $items = [];
            if (!empty($datosModificados['items'])) {
                $items = $datosModificados['items'];
            } else {
                foreach ($prefactura->items as $item) {
                    $items[] = [
                        'producto_id' => $item->producto_id,
                        'variante_producto_id' => $item->variante_producto_id,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->precio_unitario,
                        'precio_original' => $item->precio_original,
                        'descuento_porcentaje' => $item->descuento_porcentaje,
                        'descuento_valor' => $item->descuento_valor,
                        'iva' => $item->iva,
                    ];
                }
            }

            $resultado = $this->ventaService->crearVenta($datosVenta, $items, $cajeroId, $sesionCajaId);

            if (!$resultado['exito']) {
                DB::rollBack();
                return $resultado;
            }

            // Update prefactura
            $prefactura->update([
                'estado' => 'aceptada',
                'usuario_cajero_id' => $cajeroId,
                'venta_pdv_id' => $resultado['venta']->id,
                'aceptada_en' => now(),
            ]);

            DB::commit();

            return [
                'exito' => true,
                'venta' => $resultado['venta'],
                'prefactura' => $prefactura->fresh(),
                'mensaje' => "Prefactura {$prefactura->numero_prefactura} aceptada y convertida en venta {$resultado['venta']->numero_venta}",
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al aceptar prefactura: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al aceptar la prefactura: ' . $e->getMessage()];
        }
    }

    public function anular(int $prefacturaId, int $usuarioId, string $motivo): array
    {
        $prefactura = Prefactura::findOrFail($prefacturaId);

        if ($prefactura->estado !== 'pendiente') {
            return ['exito' => false, 'mensaje' => 'Solo se pueden anular prefacturas pendientes'];
        }

        $prefactura->anular($usuarioId, $motivo);

        return [
            'exito' => true,
            'mensaje' => "Prefactura {$prefactura->numero_prefactura} anulada exitosamente",
        ];
    }
}
