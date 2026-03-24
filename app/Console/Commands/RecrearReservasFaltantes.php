<?php

namespace App\Console\Commands;

use App\Models\ReservaStock;
use App\Models\SolicitudCotizacion;
use App\Models\StockProducto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecrearReservasFaltantes extends Command
{
    protected $signature = 'reservas:recrear-faltantes
                            {--dry-run : Simular sin ejecutar cambios}';

    protected $description = 'Recrea reservas de stock faltantes para cotizaciones aplicadas sin descontar que aún no han expirado';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $horasExpiracion = 72;

        $this->info('=== Recrear Reservas de Stock Faltantes ===');
        $this->info('Fecha/Hora: ' . now()->format('Y-m-d H:i:s'));

        if ($dryRun) {
            $this->warn('Modo simulación (dry-run) - No se realizarán cambios');
        }

        // Buscar cotizaciones que califican
        $hace72h = now()->subHours($horasExpiracion);

        $solicitudes = SolicitudCotizacion::where('estado', 'aplicada')
            ->where(function ($q) {
                $q->where('stock_descontado', false)->orWhereNull('stock_descontado');
            })
            ->where('created_at', '>=', $hace72h)
            ->with('items')
            ->get()
            ->filter(function ($solicitud) use ($horasExpiracion) {
                // Solo las que aún no habrían expirado
                return $solicitud->created_at->addHours($horasExpiracion)->isFuture();
            });

        if ($solicitudes->isEmpty()) {
            $this->info('No hay cotizaciones que necesiten recrear reservas.');
            return Command::SUCCESS;
        }

        $this->info("Cotizaciones encontradas: {$solicitudes->count()}");
        $this->newLine();

        $totalReservasCreadas = 0;
        $totalItemsSinStock = 0;

        foreach ($solicitudes as $solicitud) {
            $expiraEn = $solicitud->created_at->addHours($horasExpiracion);

            // Verificar si ya tiene reservas activas
            $reservasExistentes = ReservaStock::where('solicitud_cotizacion_id', $solicitud->id)
                ->where('estado', ReservaStock::ESTADO_ACTIVA)
                ->count();

            if ($reservasExistentes > 0) {
                $this->line("  [{$solicitud->numero_solicitud}] Ya tiene {$reservasExistentes} reservas activas, omitiendo.");
                continue;
            }

            $this->info("[{$solicitud->numero_solicitud}] Creada: {$solicitud->created_at->format('d/m/Y H:i')} | Expira: {$expiraEn->format('d/m/Y H:i')} | Items: {$solicitud->items->count()}");

            $reservasSolicitud = 0;

            foreach ($solicitud->items as $item) {
                // Buscar stock en bodega (misma lógica que obtenerStock corregido)
                $stockQuery = StockProducto::where('producto_id', $item->producto_id)
                    ->where(function ($q) {
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
                    continue; // Sin control de stock
                }

                $disponibleReal = $stock->cantidad_disponible - $stock->cantidad_reservada;
                $cantidadAReservar = min($item->cantidad, max(0, $disponibleReal));

                if ($cantidadAReservar <= 0) {
                    $totalItemsSinStock++;
                    $this->line("    - {$item->nombre_producto}: Sin stock disponible (disp: {$disponibleReal})");
                    continue;
                }

                // Verificar que no exista reserva duplicada
                $existeDuplicada = ReservaStock::where('solicitud_cotizacion_id', $solicitud->id)
                    ->where('item_solicitud_id', $item->id)
                    ->where('stock_producto_id', $stock->id)
                    ->where('estado', ReservaStock::ESTADO_ACTIVA)
                    ->exists();

                if ($existeDuplicada) {
                    continue;
                }

                if (!$dryRun) {
                    DB::beginTransaction();
                    try {
                        ReservaStock::create([
                            'solicitud_cotizacion_id' => $solicitud->id,
                            'item_solicitud_id' => $item->id,
                            'stock_producto_id' => $stock->id,
                            'cantidad_reservada' => $cantidadAReservar,
                            'expira_en' => $expiraEn,
                            'estado' => ReservaStock::ESTADO_ACTIVA,
                        ]);

                        $stock->increment('cantidad_reservada', $cantidadAReservar);
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("    Error: {$e->getMessage()}");
                        continue;
                    }
                }

                $parcial = $cantidadAReservar < $item->cantidad ? ' (parcial)' : '';
                $this->line("    + {$item->nombre_producto}: Reservado {$cantidadAReservar} de {$item->cantidad}{$parcial}");
                $reservasSolicitud++;
                $totalReservasCreadas++;
            }

            // Actualizar solicitud
            if (!$dryRun && $reservasSolicitud > 0) {
                $solicitud->update([
                    'tiene_reserva_stock' => true,
                    'reserva_expira_en' => $expiraEn,
                    'reserva_liberada_en' => null,
                ]);
            }

            $this->line("  -> Reservas creadas: {$reservasSolicitud}");
            $this->newLine();
        }

        $this->newLine();
        $this->info("=== Resumen ===");
        $this->info("Reservas creadas: {$totalReservasCreadas}");
        $this->info("Items sin stock disponible: {$totalItemsSinStock}");

        if ($dryRun) {
            $this->warn('Ejecute sin --dry-run para aplicar los cambios.');
        }

        return Command::SUCCESS;
    }
}
