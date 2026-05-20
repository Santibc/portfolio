<?php

namespace App\Console\Commands;

use App\Models\Orden;
use App\Models\OrdenItem;
use App\Services\OrdenEstadoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalcularTotalesOrdenes extends Command
{
    protected $signature = 'ordenes:recalcular-totales
                            {--dry-run : Mostrar cambios sin guardar}
                            {--force : Ejecutar sin confirmacion}';

    protected $description = 'Recalcula subtotal/IVA/total de orden_items y ordenes con la nueva formula (descuento como retencion al final, IVA sobre base bruta)';

    public function handle(OrdenEstadoService $estadoService): int
    {
        $dryRun = $this->option('dry-run');

        $totalOrdenes = Orden::count();
        $totalItems = OrdenItem::count();

        $this->info("Ordenes a procesar: {$totalOrdenes}");
        $this->info("Items a procesar: {$totalItems}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('DRY-RUN activado: no se guardaran cambios.');
        } elseif (! $this->option('force')) {
            if (! $this->confirm('Esto recalculara y SOBRESCRIBIRA los totales de todas las ordenes. Continuar?')) {
                $this->warn('Cancelado.');

                return self::SUCCESS;
            }
        }

        $bar = $this->output->createProgressBar($totalOrdenes);
        $bar->start();

        $procesadas = 0;
        $itemsRecalculados = 0;

        Orden::with('items')->chunkById(50, function ($ordenes) use (&$procesadas, &$itemsRecalculados, $dryRun, $estadoService, $bar) {
            foreach ($ordenes as $orden) {
                DB::beginTransaction();
                try {
                    foreach ($orden->items as $item) {
                        $base = round($item->cantidad * $item->precio_unitario, 2);
                        $descuentoMonto = round($base * $item->descuento_porcentaje / 100, 2);
                        $montoIva = round($base * ($item->porcentaje_iva / 100), 2);

                        $item->subtotal = $base;
                        $item->monto_iva = $montoIva;
                        $item->descuento_monto = $descuentoMonto;
                        $item->total = round($base + $montoIva - $descuentoMonto, 2);

                        if (! $dryRun) {
                            $item->save();
                        }
                        $itemsRecalculados++;
                    }

                    $estadoService->recalcularTotales($orden);
                    if (! $dryRun) {
                        $orden->save();
                    }
                    $procesadas++;

                    if ($dryRun) {
                        DB::rollBack();
                    } else {
                        DB::commit();
                    }
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $this->error("Error en orden #{$orden->id}: ".$e->getMessage());
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Ordenes procesadas: {$procesadas}");
        $this->info("Items recalculados: {$itemsRecalculados}");

        if ($dryRun) {
            $this->warn('DRY-RUN: ningun cambio fue persistido.');
        } else {
            $this->info('Recalculo completado correctamente.');
        }

        return self::SUCCESS;
    }
}
