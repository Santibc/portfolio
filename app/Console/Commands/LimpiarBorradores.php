<?php

namespace App\Console\Commands;

use App\Models\ConfiguracionSistema;
use App\Models\Orden;
use App\Models\RegistroActividad;
use App\Services\NotificacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LimpiarBorradores extends Command
{
    protected $signature = 'ordenes:limpiar-borradores
                            {--dry-run : Mostrar que se eliminaria sin ejecutar}
                            {--force : Ejecutar sin confirmacion}';

    protected $description = 'Eliminar ordenes en estado borrador que han expirado por inactividad';

    public function handle(): int
    {
        $diasExpiracion = ConfiguracionSistema::get('dias_expiracion_borradores', 30);
        $fechaLimite = now()->subDays($diasExpiracion);

        $this->info("Buscando borradores sin actividad desde: {$fechaLimite->format('d/m/Y H:i')} ({$diasExpiracion} dias)");

        // 1. Notificar borradores proximos a expirar (3 dias)
        $this->notificarProximosAExpirar($diasExpiracion);

        // 2. Buscar borradores expirados
        $borradoresExpirados = Orden::where('estado_trabajo', 'borrador')
            ->where('updated_at', '<', $fechaLimite)
            ->get();

        if ($borradoresExpirados->isEmpty()) {
            $this->info('No hay borradores expirados para eliminar.');
            return self::SUCCESS;
        }

        $this->info("Se encontraron {$borradoresExpirados->count()} borrador(es) expirado(s):");
        $this->newLine();

        foreach ($borradoresExpirados as $orden) {
            $dias = $orden->updated_at->diffInDays(now());
            $cliente = $orden->cliente->nombre ?? 'Sin cliente';
            $this->line("  - Orden ID #{$orden->id} | Cliente: {$cliente} | Ultima actividad: {$orden->updated_at->format('d/m/Y')} ({$dias} dias)");
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('Modo --dry-run: No se eliminaron registros.');
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Desea eliminar estos borradores?')) {
            $this->info('Operacion cancelada.');
            return self::SUCCESS;
        }

        // 3. Eliminar borradores
        $idsEliminados = [];
        $errores = 0;

        foreach ($borradoresExpirados as $orden) {
            try {
                DB::beginTransaction();

                $ordenId = $orden->id;

                // Eliminar registros dependientes en orden de FK
                // (OrdenComentario y RegistroActividad overriden delete(), usar query builder)
                $orden->asignaciones()->delete();
                $orden->fotos()->delete();
                DB::table('orden_comentarios')->where('orden_id', $ordenId)->delete();
                DB::table('registro_actividades')->where('orden_id', $ordenId)->delete();
                $orden->pagos()->forceDelete();
                $orden->piezas()->each(function ($pieza) {
                    $pieza->historialAvances()->delete();
                    $pieza->asignaciones()->delete();
                    $pieza->fotos()->delete();
                    $pieza->garantias()->delete();
                    $pieza->delete();
                });
                $orden->bosquejos()->delete();
                $orden->items()->delete();
                $orden->garantias()->delete();
                DB::table('entrega_piezas')->whereIn('entrega_id', function ($q) use ($ordenId) {
                    $q->select('id')->from('entregas')->where('orden_id', $ordenId);
                })->delete();
                DB::table('entregas')->where('orden_id', $ordenId)->delete();

                // Eliminar archivos fisicos
                $carpetaOrden = public_path("uploads/ordenes/{$ordenId}");
                if (File::isDirectory($carpetaOrden)) {
                    File::deleteDirectory($carpetaOrden);
                }

                // Eliminar carpeta temporal si existe
                $carpetaTemp = public_path("uploads/ordenes/temp_{$ordenId}");
                if (File::isDirectory($carpetaTemp)) {
                    File::deleteDirectory($carpetaTemp);
                }

                $orden->delete();

                DB::commit();

                $idsEliminados[] = $ordenId;
                $this->info("  Eliminada: Orden ID #{$ordenId}");
            } catch (\Exception $e) {
                DB::rollBack();
                $errores++;
                $this->error("  Error eliminando Orden ID #{$orden->id}: {$e->getMessage()}");
            }
        }

        // 4. Registrar actividad
        if (!empty($idsEliminados)) {
            RegistroActividad::create([
                'usuario_id' => null,
                'orden_id' => null,
                'accion' => 'sistema.borradores_eliminados',
                'descripcion' => count($idsEliminados) . ' borrador(es) eliminado(s) por expiracion (' . $diasExpiracion . ' dias)',
                'datos_extra' => [
                    'ids_eliminados' => $idsEliminados,
                    'cantidad' => count($idsEliminados),
                    'dias_expiracion' => $diasExpiracion,
                ],
            ]);
        }

        $this->newLine();
        $this->info("Resumen: " . count($idsEliminados) . " eliminado(s), {$errores} error(es).");

        return $errores > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Notificar a los creadores de borradores que expiran en los proximos 3 dias.
     */
    private function notificarProximosAExpirar(int $diasExpiracion): void
    {
        $fechaExpiracionEn3Dias = now()->subDays($diasExpiracion - 3);
        $fechaExpiracion = now()->subDays($diasExpiracion);

        $proximosAExpirar = Orden::where('estado_trabajo', 'borrador')
            ->where('updated_at', '<', $fechaExpiracionEn3Dias)
            ->where('updated_at', '>=', $fechaExpiracion)
            ->with('creador')
            ->get();

        foreach ($proximosAExpirar as $orden) {
            $diasRestantes = $diasExpiracion - $orden->updated_at->diffInDays(now());
            if ($diasRestantes < 0) $diasRestantes = 0;

            NotificacionService::borradorExpirando($orden, $diasRestantes);
        }

        if ($proximosAExpirar->isNotEmpty()) {
            $this->info("Notificaciones enviadas: {$proximosAExpirar->count()} borrador(es) proximos a expirar.");
        }
    }
}
