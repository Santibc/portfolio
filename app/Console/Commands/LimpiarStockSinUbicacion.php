<?php

namespace App\Console\Commands;

use App\Models\ReservaStock;
use App\Models\StockProducto;
use App\Models\Ubicacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Limpia los registros de stock "fantasma" sin ubicación (ubicacion_id NULL):
 *  - Si ya existe un registro del mismo producto/variante en la bodega principal -> FUSIONA
 *    (suma disponible y reservada, repunta las reservas y elimina el registro NULL).
 *  - Si NO existe -> ADOPTA el registro asignándolo a la bodega principal (no se pierde stock).
 *
 * Es idempotente: si no hay registros NULL, no hace nada.
 */
class LimpiarStockSinUbicacion extends Command
{
    protected $signature = 'stock:limpiar-sin-ubicacion {--dry-run : Solo mostrar qué se haría, sin modificar datos}';

    protected $description = 'Reasigna/fusiona los registros de stock sin ubicación (ubicacion_id NULL) hacia la bodega principal, repuntando sus reservas. Idempotente.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $principal = Ubicacion::principal();
        if (!$principal) {
            $this->error('No hay bodega principal (es_principal=1, tipo=bodega). Abortado.');
            return self::FAILURE;
        }
        $this->info("Bodega principal destino: [{$principal->id}] {$principal->nombre}");

        $sinUbicacion = StockProducto::whereNull('ubicacion_id')->get();
        if ($sinUbicacion->isEmpty()) {
            $this->info('No hay registros de stock sin ubicación. Nada que hacer.');
            return self::SUCCESS;
        }

        $this->info("Registros sin ubicación a procesar: {$sinUbicacion->count()}");
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: no se modificará nada.');
        }

        $fusionados = 0;
        $adoptados = 0;
        $reservasRepuntadas = 0;
        $filas = [];

        $ejecutar = function () use ($sinUbicacion, $principal, $dryRun, &$fusionados, &$adoptados, &$reservasRepuntadas, &$filas) {
            foreach ($sinUbicacion as $n) {
                // Registro equivalente que ya esté en la bodega principal
                $b1Query = StockProducto::where('producto_id', $n->producto_id)
                    ->where('ubicacion_id', $principal->id);
                if (is_null($n->variante_producto_id)) {
                    $b1Query->whereNull('variante_producto_id');
                } else {
                    $b1Query->where('variante_producto_id', $n->variante_producto_id);
                }
                $b1 = $b1Query->first();

                $reservasN = ReservaStock::where('stock_producto_id', $n->id)->count();

                if ($b1 && $b1->id !== $n->id) {
                    // FUSIONAR n -> b1
                    if (!$dryRun) {
                        $b1->cantidad_disponible += $n->cantidad_disponible;
                        $b1->cantidad_reservada  += $n->cantidad_reservada;
                        $b1->save();

                        ReservaStock::where('stock_producto_id', $n->id)
                            ->update(['stock_producto_id' => $b1->id]);

                        $n->delete();
                    }
                    $fusionados++;
                    $reservasRepuntadas += $reservasN;
                    $filas[] = [$n->id, $n->producto_id, $n->variante_producto_id ?? '-', 'FUSIONADO -> '.$b1->id, $n->cantidad_disponible, $n->cantidad_reservada, $reservasN];
                } else {
                    // ADOPTAR: asignar el registro a la bodega principal
                    if (!$dryRun) {
                        $n->ubicacion_id = $principal->id;
                        $n->save();
                    }
                    $adoptados++;
                    $filas[] = [$n->id, $n->producto_id, $n->variante_producto_id ?? '-', 'ADOPTADO (-> bodega)', $n->cantidad_disponible, $n->cantidad_reservada, $reservasN];
                }
            }
        };

        if ($dryRun) {
            $ejecutar();
        } else {
            DB::transaction($ejecutar);
        }

        $this->table(
            ['stock_id', 'producto', 'variante', 'accion', 'disp', 'reserv', 'reservas'],
            $filas
        );

        $this->info("Fusionados: {$fusionados} | Adoptados: {$adoptados} | Reservas repuntadas: {$reservasRepuntadas}");

        $restantes = $dryRun
            ? $sinUbicacion->count()
            : StockProducto::whereNull('ubicacion_id')->count();
        $this->info("Registros sin ubicacion restantes: {$restantes}");

        return self::SUCCESS;
    }
}
