<?php

namespace App\Console\Commands;

use App\Models\ReservaStock;
use App\Models\StockProducto;
use App\Models\SolicitudCotizacion;
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

        // Paso 1: Limpiar reservas huérfanas (activas pero cotización ya procesada)
        $this->info('');
        $this->info('--- Paso 1: Limpiar reservas huérfanas ---');
        $this->limpiarReservasHuerfanas($dryRun);

        // Paso 2: Limpiar reservas expiradas que nunca se liberaron
        $this->info('');
        $this->info('--- Paso 2: Limpiar reservas expiradas ---');
        $this->limpiarReservasExpiradas($dryRun);

        // Paso 3: Re-crear reservas para cotizaciones aplicadas no despachadas sin reservas
        $this->info('');
        $this->info('--- Paso 3: Re-crear reservas faltantes ---');
        $this->reCrearReservasFaltantes($dryRun);

        // Paso 4: Recalcular cantidad_reservada en stock_productos
        $this->info('');
        $this->info('--- Paso 4: Recalcular cantidad_reservada ---');
        $this->recalcularCantidadReservada($dryRun, $productoId);

        $this->info('');
        $this->info('=== Reparación completada ===');

        if ($dryRun) {
            $this->warn('Ejecute sin --dry-run para aplicar los cambios');
        }

        return Command::SUCCESS;
    }

    private function limpiarReservasHuerfanas(bool $dryRun): void
    {
        $reservasHuerfanas = ReservaStock::where('estado', ReservaStock::ESTADO_ACTIVA)
            ->whereHas('solicitudCotizacion', function ($q) {
                // Solo liberar si: rechazada O ya despachada
                $q->where('estado', 'rechazada')
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
            $this->line("  Reserva #{$reserva->id} | SC: {$sc->numero_solicitud} (estado: {$sc->estado}) | Producto: {$reserva->stockProducto->producto_id} | Qty: {$reserva->cantidad_reservada}");

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
            $this->line("  Reserva #{$reserva->id} | SC: {$sc->numero_solicitud} | Expiró: {$reserva->expira_en} | Qty: {$reserva->cantidad_reservada}");

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

    private function reCrearReservasFaltantes(bool $dryRun): void
    {
        // Cotizaciones aplicadas, no despachadas, sin reservas activas
        $cotizacionesSinReserva = SolicitudCotizacion::where('estado', 'aplicada')
            ->where('stock_descontado', 0)
            ->whereDoesntHave('reservas', function($q) {
                $q->where('estado', ReservaStock::ESTADO_ACTIVA);
            })
            ->with(['items'])
            ->get();

        if ($cotizacionesSinReserva->isEmpty()) {
            $this->info('Todas las cotizaciones pendientes tienen sus reservas');
            return;
        }

        $this->warn("Encontradas {$cotizacionesSinReserva->count()} cotizaciones sin reservas:");
        $reservasCreadas = 0;

        foreach ($cotizacionesSinReserva as $solicitud) {
            $this->line("  SC: {$solicitud->numero_solicitud} | Items: {$solicitud->items->count()}");

            foreach ($solicitud->items as $item) {
                // Buscar stock de bodega (no tienda)
                $stockQuery = StockProducto::where('producto_id', $item->producto_id)
                    ->where(function($q) {
                        $q->whereNull('ubicacion_id')
                          ->orWhereHas('ubicacionRelacion', fn($u) => $u->where('tipo', '!=', 'tienda'));
                    });

                if ($item->variante_producto_id) {
                    $stockQuery->where('variante_producto_id', $item->variante_producto_id);
                } else {
                    $stockQuery->whereNull('variante_producto_id');
                }

                $stock = $stockQuery->first();

                if (!$stock) {
                    $this->line("    Item #{$item->id} - Sin registro de stock, omitido");
                    continue;
                }

                if (!$dryRun) {
                    ReservaStock::create([
                        'solicitud_cotizacion_id' => $solicitud->id,
                        'item_solicitud_id' => $item->id,
                        'stock_producto_id' => $stock->id,
                        'cantidad_reservada' => $item->cantidad,
                        'estado' => ReservaStock::ESTADO_ACTIVA,
                        'expira_en' => now()->addHours(720), // 30 días
                    ]);
                }

                $reservasCreadas++;
                $this->line("    Item #{$item->id} | Stock #{$stock->id} | Qty: {$item->cantidad} → Reserva creada");
            }
        }

        $this->warn("Total reservas a crear: {$reservasCreadas}");
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
