<?php

namespace App\Console\Commands;

use App\Models\FacturaSiigo;
use App\Models\MovimientoStock;
use App\Models\StockProducto;
use App\Models\VentaPdv;
use App\Services\Siigo\SiigoFacturacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReactivarAnulacionesSinDian extends Command
{
    protected $signature = 'pdv:reactivar-anulaciones
                            {--apply : Aplicar los cambios (sin esto solo muestra el dry-run)}
                            {--venta= : Reactivar solo una venta específica por numero_venta}
                            {--limpiar-ncs-error : También elimina notas crédito en estado error/rechazada de ventas no anuladas}';

    protected $description = 'Reactiva ventas anuladas localmente que NO tienen nota crédito aprobada/pendiente en SIIGO. Las regresa a estado completada para poder reintentar la anulación.';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $numeroVenta = $this->option('venta');
        $limpiarNcs = (bool) $this->option('limpiar-ncs-error');

        if (!$apply) {
            $this->warn('DRY-RUN: no se aplicarán cambios. Use --apply para ejecutar.');
        }

        $ventasAfectadas = $this->buscarVentasAReactivar($numeroVenta);

        if ($ventasAfectadas->isEmpty()) {
            if ($numeroVenta) {
                $venta = VentaPdv::where('numero_venta', $numeroVenta)->first();
                if (!$venta) {
                    $this->error("Venta '{$numeroVenta}' no existe.");
                } elseif ($venta->estado !== 'anulada') {
                    $this->info("La venta '{$numeroVenta}' está en estado '{$venta->estado}', no necesita reactivación.");
                } else {
                    $this->info("La venta '{$numeroVenta}' ya tiene una NC aprobada/pendiente. No requiere acción.");
                }
            } else {
                $this->info('No hay ventas anuladas sin NC aprobada/pendiente.');
            }
        } else {
            $this->info("Ventas a reactivar: {$ventasAfectadas->count()}");
            foreach ($ventasAfectadas as $venta) {
                $this->procesarVenta($venta, $apply);
            }
        }

        if ($limpiarNcs) {
            $this->line('');
            $this->info('Limpiando NCs en error/rechazada de ventas no anuladas...');
            $eliminadas = $this->limpiarNcsHuerfanas($apply);
            $this->info("NCs huérfanas {$eliminadas}: " . ($apply ? 'eliminadas' : 'serían eliminadas'));
        }

        return self::SUCCESS;
    }

    private function buscarVentasAReactivar(?string $numeroVenta)
    {
        $query = VentaPdv::query()
            ->where('estado', 'anulada')
            ->whereNotNull('factura_siigo_id')
            ->whereDoesntHave('facturaSiigo.notasCredito', function ($q) {
                $q->whereIn('estado_dian', ['aprobada', 'pendiente']);
            });

        if ($numeroVenta) {
            $query->where('numero_venta', $numeroVenta);
        }

        return $query->with(['items.producto', 'sesionCaja', 'facturaSiigo'])
            ->orderBy('anulada_en', 'desc')
            ->get();
    }

    private function procesarVenta(VentaPdv $venta, bool $apply): void
    {
        $this->line('');
        $this->line("→ <fg=yellow>{$venta->numero_venta}</> (id={$venta->id}) anulada el {$venta->anulada_en} motivo: {$venta->motivo_anulacion}");

        // Antes de revertir, consultar SIIGO: si ya existe una NC aprobada para
        // esa factura (creada manualmente desde el panel SIIGO o por otra vía),
        // la importamos y mantenemos la venta como anulada en lugar de reactivarla.
        if ($venta->facturaSiigo && $venta->facturaSiigo->siigo_invoice_id) {
            $this->line("   consultando SIIGO por NC existente para factura {$venta->facturaSiigo->numero_factura}...");
            try {
                $svc = app(SiigoFacturacionService::class);
                $ncEnSiigo = $svc->buscarNotaCreditoExistenteEnSiigo($venta->facturaSiigo);
                if ($ncEnSiigo) {
                    $this->info("   ✓ SIIGO ya tiene NC aprobada: " . ($ncEnSiigo['name'] ?? '?'));
                    if (!$apply) {
                        $this->line("   [DRY-RUN] importará la NC localmente y dejará la venta como anulada (NO reactiva).");
                        return;
                    }
                    DB::transaction(function () use ($svc, $venta, $ncEnSiigo) {
                        $svc->importarNotaCreditoExistente($venta->facturaSiigo, $ncEnSiigo);
                    });
                    $this->info("   ✓ NC importada al sistema local. Venta queda anulada con NC válida.");
                    return;
                }
                $this->line("   SIIGO no tiene NC aprobada para esta factura.");
            } catch (\Exception $e) {
                $this->warn("   No se pudo consultar SIIGO ({$e->getMessage()}). Continuando con reactivación local.");
            }
        }

        // Detectar movimientos de stock por anulación de esta venta.
        // El campo referencia_documento queda NULL en el código actual (pasan 'referencia' que no está
        // en $fillable). La forma confiable es buscar por motivo, que incluye el numero_venta.
        $movimientos = MovimientoStock::where('motivo', "Anulación Venta PdV - {$venta->numero_venta}")
            ->where('tipo_movimiento', 'entrada')
            ->get();

        $this->line("   movs stock por anulación: {$movimientos->count()}");

        if (!$apply) {
            $this->line('   [DRY-RUN] revertirá estado→completada, limpiará anulada_por/anulada_en/motivo, y revertirá stock si aplica.');
            return;
        }

        DB::transaction(function () use ($venta, $movimientos) {
            // 1) Revertir movimientos de stock (restar lo que se sumó al anular)
            foreach ($movimientos as $mov) {
                $stock = StockProducto::where('producto_id', $mov->producto_id)
                    ->where('ubicacion_id', $mov->ubicacion_id)
                    ->when($mov->variante_producto_id,
                        fn($q) => $q->where('variante_producto_id', $mov->variante_producto_id),
                        fn($q) => $q->whereNull('variante_producto_id')
                    )->first();

                if ($stock) {
                    $anterior = $stock->cantidad_disponible;
                    $nuevo = max(0, $anterior - $mov->cantidad);
                    $stock->update(['cantidad_disponible' => $nuevo]);

                    MovimientoStock::create([
                        'producto_id' => $mov->producto_id,
                        'variante_producto_id' => $mov->variante_producto_id,
                        'ubicacion_id' => $mov->ubicacion_id,
                        'tipo_movimiento' => 'salida',
                        'cantidad' => $mov->cantidad,
                        'stock_anterior' => $anterior,
                        'stock_nuevo' => $nuevo,
                        'referencia_documento' => $venta->numero_venta,
                        'origen' => 'ajuste_inventario',
                        'tipo_operacion' => 'general',
                        'motivo' => "Reversa de anulación venta {$venta->numero_venta} (NC SIIGO no aplicada)",
                        'usuario_id' => $venta->usuario_id,
                    ]);
                }
            }

            // 2) Si la sesión está abierta, revertir totales que la anulación había modificado
            if ($venta->sesion_caja_id && $venta->sesionCaja && $venta->sesionCaja->estaAbierta()) {
                $s = $venta->sesionCaja;
                $s->decrement('total_anulaciones', $venta->total);
                $s->increment('total_ventas', $venta->total);
                $s->increment('cantidad_ventas');
                if ($venta->monto_efectivo) {
                    $s->increment('total_ventas_efectivo', $venta->monto_efectivo);
                }
                if ($venta->monto_transferencia) {
                    $s->increment('total_ventas_transferencia', $venta->monto_transferencia);
                }
            }

            // 3) Volver la venta a completada
            $venta->update([
                'estado' => 'completada',
                'anulada_por' => null,
                'anulada_en' => null,
                'motivo_anulacion' => null,
            ]);
        });

        $this->info("   ✓ reactivada");
    }

    private function limpiarNcsHuerfanas(bool $apply): int
    {
        // NCs en error/rechazada cuya venta NO está anulada y donde no existe otra NC aprobada/pendiente.
        $ncs = FacturaSiigo::query()
            ->where('tipo_documento', 'nota_credito')
            ->whereIn('estado_dian', ['error', 'rechazada'])
            ->whereHas('ventaPdv', function ($q) {
                $q->where('estado', '!=', 'anulada');
            })
            ->get();

        if ($apply) {
            foreach ($ncs as $nc) {
                $nc->delete();
            }
        } else {
            foreach ($ncs as $nc) {
                $this->line("   [DRY-RUN] eliminaría NC id={$nc->id} venta_id={$nc->venta_pdv_id} estado={$nc->estado_dian}");
            }
        }

        return $ncs->count();
    }
}
