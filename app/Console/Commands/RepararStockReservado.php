<?php

namespace App\Console\Commands;

use App\Models\ReservaStock;
use App\Models\StockProducto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepararStockReservado extends Command
{
    protected $signature = 'stock:reparar-reservado
                            {--dry-run : Mostrar cambios sin ejecutar}
                            {--producto= : Reparar solo un producto específico (producto_id)}';

    protected $description = 'Repara cantidad_reservada en stock_productos recalculando desde reservas_stock activas';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $productoId = $this->option('producto');

        $this->info('=== Reparación de Stock Reservado ===');
        $this->info('Fecha: ' . now()->format('Y-m-d H:i:s'));

        if ($dryRun) {
            $this->warn('MODO DRY-RUN - No se realizarán cambios');
        }

        // Paso 0: Limpiar reservas sin solicitud (solicitud eliminada)
        $this->info('');
        $this->info('--- Paso 0: Limpiar reservas sin solicitud ---');
        $this->limpiarReservasSinSolicitud($dryRun);

        // Paso 1: Limpiar reservas huérfanas (activas pero cotización ya procesada)
        $this->info('');
        $this->info('--- Paso 1: Limpiar reservas huérfanas ---');
        $this->limpiarReservasHuerfanas($dryRun);

        // Paso 1.5: Limpiar reservas duplicadas (misma cotización + item + stock)
        $this->info('');
        $this->info('--- Paso 1.5: Limpiar reservas duplicadas ---');
        $this->limpiarReservasDuplicadas($dryRun);

        // Paso 2: Limpiar reservas expiradas que nunca se liberaron
        $this->info('');
        $this->info('--- Paso 2: Limpiar reservas expiradas ---');
        $this->limpiarReservasExpiradas($dryRun);

        // Paso 3: Recalcular cantidad_reservada en stock_productos
        $this->info('');
        $this->info('--- Paso 3: Recalcular cantidad_reservada ---');
        $this->recalcularCantidadReservada($dryRun, $productoId);

        $this->info('');
        $this->info('=== Reparación completada ===');

        if ($dryRun) {
            $this->warn('Ejecute sin --dry-run para aplicar los cambios');
        }

        return Command::SUCCESS;
    }

    private function limpiarReservasSinSolicitud(bool $dryRun): void
    {
        $reservasSinSolicitud = ReservaStock::where('estado', ReservaStock::ESTADO_ACTIVA)
            ->whereDoesntHave('solicitudCotizacion')
            ->with(['stockProducto:id,producto_id'])
            ->get();

        if ($reservasSinSolicitud->isEmpty()) {
            $this->info('No hay reservas sin solicitud');
            return;
        }

        $this->warn("Encontradas {$reservasSinSolicitud->count()} reservas sin solicitud:");

        foreach ($reservasSinSolicitud as $reserva) {
            $productoId = $reserva->stockProducto ? $reserva->stockProducto->producto_id : 'N/A';
            $this->line("  Reserva #{$reserva->id} | Producto: {$productoId} | Qty: {$reserva->cantidad_reservada}");

            if (!$dryRun) {
                DB::beginTransaction();
                try {
                    $reserva->update([
                        'estado' => ReservaStock::ESTADO_EXPIRADA,
                        'liberada_en' => now(),
                        'motivo_liberacion' => 'Reparación automática - solicitud eliminada',
                    ]);

                    $stock = $reserva->stockProducto;
                    if ($stock && $stock->cantidad_reservada > 0) {
                        $cantidadALiberar = min($reserva->cantidad_reservada, $stock->cantidad_reservada);
                        $stock->decrement('cantidad_reservada', $cantidadALiberar);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("  Error: {$e->getMessage()}");
                }
            }
        }
    }

    private function limpiarReservasDuplicadas(bool $dryRun): void
    {
        // Buscar grupos de reservas activas duplicadas (mismo item + mismo stock)
        $duplicados = ReservaStock::where('estado', ReservaStock::ESTADO_ACTIVA)
            ->select('solicitud_cotizacion_id', 'item_solicitud_id', 'stock_producto_id', DB::raw('COUNT(*) as total'), DB::raw('GROUP_CONCAT(id ORDER BY id ASC) as ids'))
            ->groupBy('solicitud_cotizacion_id', 'item_solicitud_id', 'stock_producto_id')
            ->having('total', '>', 1)
            ->get();

        if ($duplicados->isEmpty()) {
            $this->info('No hay reservas duplicadas');
            return;
        }

        $this->warn("Encontrados {$duplicados->count()} grupos de reservas duplicadas:");
        $eliminadas = 0;

        foreach ($duplicados as $grupo) {
            $ids = explode(',', $grupo->ids);
            $idOriginal = array_shift($ids); // Conservar la primera (más antigua)
            $idsAEliminar = $ids; // Eliminar las demás

            $sc = \App\Models\SolicitudCotizacion::find($grupo->solicitud_cotizacion_id);
            $scNumero = $sc ? $sc->numero_solicitud : 'N/A';
            $this->line("  SC: {$scNumero} | Item: {$grupo->item_solicitud_id} | Stock: {$grupo->stock_producto_id} | Repetidas: {$grupo->total} | Conservar: #{$idOriginal} | Eliminar: #" . implode(', #', $idsAEliminar));

            if (!$dryRun) {
                foreach ($idsAEliminar as $idDuplicado) {
                    DB::beginTransaction();
                    try {
                        $reserva = ReservaStock::find($idDuplicado);
                        if (!$reserva) continue;

                        $reserva->update([
                            'estado' => ReservaStock::ESTADO_EXPIRADA,
                            'liberada_en' => now(),
                            'motivo_liberacion' => "Reparación automática - reserva duplicada (original: #{$idOriginal})",
                        ]);

                        $stock = $reserva->stockProducto;
                        if ($stock && $stock->cantidad_reservada > 0) {
                            $cantidadALiberar = min($reserva->cantidad_reservada, $stock->cantidad_reservada);
                            $stock->decrement('cantidad_reservada', $cantidadALiberar);
                        }

                        DB::commit();
                        $eliminadas++;
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("  Error eliminando reserva #{$idDuplicado}: {$e->getMessage()}");
                    }
                }
            } else {
                $eliminadas += count($idsAEliminar);
            }
        }

        $this->warn("Total reservas duplicadas a eliminar: {$eliminadas}");
    }

    private function limpiarReservasHuerfanas(bool $dryRun): void
    {
        $reservasHuerfanas = ReservaStock::where('estado', ReservaStock::ESTADO_ACTIVA)
            ->whereHas('solicitudCotizacion', function ($q) {
                // Liberar si: rechazada, ya despachada, o ya aplicada (aplicarReservas ya descontó stock)
                $q->where('estado', 'rechazada')
                  ->orWhere('estado', 'aplicada')
                  ->orWhere('stock_descontado', 1);
            })
            ->with(['solicitudCotizacion:id,numero_solicitud,estado', 'stockProducto:id,producto_id'])
            ->get();

        if ($reservasHuerfanas->isEmpty()) {
            $this->info('No hay reservas huérfanas');
            return;
        }

        $this->warn("Encontradas {$reservasHuerfanas->count()} reservas huérfanas:");

        foreach ($reservasHuerfanas as $reserva) {
            $sc = $reserva->solicitudCotizacion;
            $scInfo = $sc ? "{$sc->numero_solicitud} (estado: {$sc->estado})" : 'SIN SOLICITUD';
            $productoId = $reserva->stockProducto ? $reserva->stockProducto->producto_id : 'N/A';
            $this->line("  Reserva #{$reserva->id} | SC: {$scInfo} | Producto: {$productoId} | Qty: {$reserva->cantidad_reservada}");

            if (!$dryRun) {
                DB::beginTransaction();
                try {
                    $reserva->update([
                        'estado' => ReservaStock::ESTADO_EXPIRADA,
                        'liberada_en' => now(),
                        'motivo_liberacion' => 'Reparación automática - cotización ya procesada',
                    ]);

                    $stock = $reserva->stockProducto;
                    if ($stock && $stock->cantidad_reservada > 0) {
                        $cantidadALiberar = min($reserva->cantidad_reservada, $stock->cantidad_reservada);
                        $stock->decrement('cantidad_reservada', $cantidadALiberar);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("  Error: {$e->getMessage()}");
                }
            }
        }
    }

    private function limpiarReservasExpiradas(bool $dryRun): void
    {
        $reservasExpiradas = ReservaStock::where('estado', ReservaStock::ESTADO_ACTIVA)
            ->where('expira_en', '<', now())
            ->with(['solicitudCotizacion:id,numero_solicitud,estado', 'stockProducto:id,producto_id'])
            ->get();

        if ($reservasExpiradas->isEmpty()) {
            $this->info('No hay reservas expiradas pendientes');
            return;
        }

        $this->warn("Encontradas {$reservasExpiradas->count()} reservas expiradas sin liberar:");

        foreach ($reservasExpiradas as $reserva) {
            $sc = $reserva->solicitudCotizacion;
            $scNumero = $sc ? $sc->numero_solicitud : 'SIN SOLICITUD';
            $this->line("  Reserva #{$reserva->id} | SC: {$scNumero} | Expiró: {$reserva->expira_en} | Qty: {$reserva->cantidad_reservada}");

            if (!$dryRun) {
                DB::beginTransaction();
                try {
                    $reserva->update([
                        'estado' => ReservaStock::ESTADO_EXPIRADA,
                        'liberada_en' => now(),
                        'motivo_liberacion' => 'Reparación automática - reserva expirada',
                    ]);

                    $stock = $reserva->stockProducto;
                    if ($stock && $stock->cantidad_reservada > 0) {
                        $cantidadALiberar = min($reserva->cantidad_reservada, $stock->cantidad_reservada);
                        $stock->decrement('cantidad_reservada', $cantidadALiberar);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("  Error: {$e->getMessage()}");
                }
            }
        }
    }

    private function recalcularCantidadReservada(bool $dryRun, ?string $productoId): void
    {
        $query = StockProducto::query();

        if ($productoId) {
            $query->where('producto_id', $productoId);
        }

        $stocks = $query->get();
        $corregidos = 0;

        foreach ($stocks as $stock) {
            // Calcular la suma real de reservas activas para este stock
            $sumaReservasActivas = ReservaStock::where('stock_producto_id', $stock->id)
                ->where('estado', ReservaStock::ESTADO_ACTIVA)
                ->sum('cantidad_reservada');

            $reservadoActual = $stock->cantidad_reservada;

            if ((int) $reservadoActual !== (int) $sumaReservasActivas) {
                $producto = $stock->producto;
                $nombreProducto = $producto ? $producto->nombre : "ID {$stock->producto_id}";

                $this->warn("  CORREGIR: [{$stock->producto_id}] {$nombreProducto}");
                $this->line("    Stock #{$stock->id} | reservada actual: {$reservadoActual} → debe ser: {$sumaReservasActivas} (diferencia: " . ($reservadoActual - $sumaReservasActivas) . ")");

                if (!$dryRun) {
                    $stock->update(['cantidad_reservada' => $sumaReservasActivas]);
                    Log::info("Stock reparado: producto_id={$stock->producto_id}, stock_id={$stock->id}, reservada {$reservadoActual} → {$sumaReservasActivas}");
                }

                $corregidos++;
            }
        }

        if ($corregidos === 0) {
            $this->info('Todos los registros de stock tienen cantidad_reservada correcta');
        } else {
            $this->warn("Total registros a corregir: {$corregidos}");
        }
    }
}
