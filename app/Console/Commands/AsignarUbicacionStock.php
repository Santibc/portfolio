<?php

namespace App\Console\Commands;

use App\Models\StockProducto;
use App\Models\Ubicacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AsignarUbicacionStock extends Command
{
    protected $signature = 'stock:asignar-ubicacion
                            {--ubicacion=1 : ID de la ubicación a asignar (default: 1 = Bodega Principal)}
                            {--dry-run : Mostrar cambios sin ejecutar}';

    protected $description = 'Asigna una ubicación a todos los registros de stock que tienen ubicacion_id NULL';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $ubicacionId = (int) $this->option('ubicacion');

        $ubicacion = Ubicacion::find($ubicacionId);
        if (!$ubicacion) {
            $this->error("No se encontró la ubicación con ID {$ubicacionId}");
            return 1;
        }

        $registrosSinUbicacion = StockProducto::whereNull('ubicacion_id')->count();

        $this->info('=== Asignar Ubicación a Stock sin ubicación ===');
        $this->info('Fecha: ' . now()->format('Y-m-d H:i:s'));
        $this->info("Ubicación destino: {$ubicacion->nombre} (ID: {$ubicacion->id}, Tipo: {$ubicacion->tipo})");
        $this->info("Registros sin ubicación: {$registrosSinUbicacion}");

        if ($registrosSinUbicacion === 0) {
            $this->info('No hay registros sin ubicación. Nada que hacer.');
            return 0;
        }

        if ($dryRun) {
            $this->warn('MODO DRY-RUN - No se realizarán cambios');
            $this->table(
                ['Producto ID', 'Variante ID', 'Cantidad Disponible', 'Cantidad Reservada'],
                StockProducto::whereNull('ubicacion_id')
                    ->select('producto_id', 'variante_producto_id', 'cantidad_disponible', 'cantidad_reservada')
                    ->limit(50)
                    ->get()
                    ->map(fn($s) => [
                        $s->producto_id,
                        $s->variante_producto_id ?? 'NULL',
                        $s->cantidad_disponible,
                        $s->cantidad_reservada,
                    ])
                    ->toArray()
            );
            if ($registrosSinUbicacion > 50) {
                $this->info("... y " . ($registrosSinUbicacion - 50) . " registros más");
            }
            return 0;
        }

        if (!$this->confirm("¿Asignar '{$ubicacion->nombre}' a {$registrosSinUbicacion} registros de stock?")) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $actualizados = DB::table('stock_productos')
            ->whereNull('ubicacion_id')
            ->update(['ubicacion_id' => $ubicacionId]);

        $this->info("Registros actualizados: {$actualizados}");
        $this->info('Operación completada exitosamente.');

        return 0;
    }
}
