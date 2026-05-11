<?php

namespace App\Console\Commands;

use App\Models\HistorialEstadoSolicitud;
use App\Models\SolicitudCotizacion;
use App\Services\ReservaStockService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarcarPendientesComoAplicadas extends Command
{
    protected $signature = 'cotizaciones:marcar-aplicadas
                            {--solo-clonadas : Solo procesar cotizaciones cuyas notas indiquen que son clonadas}
                            {--user-id=1 : ID de usuario al que se le atribuirá la acción de aplicación}
                            {--dry-run : Simular sin ejecutar cambios}
                            {--force : Ejecutar sin pedir confirmación}';

    protected $description = 'Marca como APLICADAS todas las cotizaciones que se encuentran en estado PENDIENTE, liberando sus reservas de stock';

    public function handle(): int
    {
        $soloClonadas = (bool) $this->option('solo-clonadas');
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $userId = (int) $this->option('user-id');

        $this->info('=== Marcar Cotizaciones Pendientes como Aplicadas ===');
        $this->info('Fecha/Hora: ' . now()->format('Y-m-d H:i:s'));

        if ($dryRun) {
            $this->warn('Modo simulación (dry-run) - No se realizarán cambios');
        }

        $query = SolicitudCotizacion::where('estado', SolicitudCotizacion::ESTADO_PENDIENTE);

        if ($soloClonadas) {
            $query->where('notas_cliente', 'like', 'Clonada de %');
            $this->info('Filtro: solo cotizaciones clonadas');
        }

        $totalPendientes = (clone $query)->count();

        if ($totalPendientes === 0) {
            $this->info('No hay cotizaciones pendientes para procesar.');
            return 0;
        }

        $this->info("Se encontraron {$totalPendientes} cotizaciones en estado PENDIENTE.");

        if (!$dryRun && !$force) {
            if (!$this->confirm("¿Confirma marcar {$totalPendientes} cotizaciones como APLICADAS? (atribuido al usuario ID {$userId})", false)) {
                $this->warn('Operación cancelada por el usuario.');
                return 1;
            }
        }

        $reservaService = new ReservaStockService();
        $procesadas = 0;
        $errores = 0;

        $progressBar = $this->output->createProgressBar($totalPendientes);
        $progressBar->start();

        $query->chunkById(100, function ($solicitudes) use (&$procesadas, &$errores, $progressBar, $reservaService, $userId, $dryRun) {
            foreach ($solicitudes as $solicitud) {
                try {
                    if ($dryRun) {
                        $procesadas++;
                        $progressBar->advance();
                        continue;
                    }

                    DB::beginTransaction();

                    if ($solicitud->tiene_reserva_stock) {
                        $reservaService->liberarReservasCotizacion(
                            $solicitud,
                            'Marcada como aplicada por comando de migración',
                            $userId
                        );
                        $solicitud->refresh();
                    }

                    $solicitud->update([
                        'estado' => SolicitudCotizacion::ESTADO_APLICADA,
                        'aplicada_en' => $solicitud->aplicada_en ?? now(),
                        'aplicada_por' => $solicitud->aplicada_por ?? $userId,
                    ]);

                    HistorialEstadoSolicitud::create([
                        'solicitud_cotizacion_id' => $solicitud->id,
                        'tipo_cambio' => HistorialEstadoSolicitud::TIPO_ESTADO,
                        'estado_anterior' => SolicitudCotizacion::ESTADO_PENDIENTE,
                        'estado_nuevo' => SolicitudCotizacion::ESTADO_APLICADA,
                        'observaciones' => 'Aplicación masiva mediante comando cotizaciones:marcar-aplicadas',
                        'user_id' => $userId,
                    ]);

                    DB::commit();
                    $procesadas++;
                } catch (Exception $e) {
                    DB::rollBack();
                    $errores++;
                    Log::error("Error al aplicar cotización {$solicitud->numero_solicitud}: " . $e->getMessage());
                    $this->newLine();
                    $this->error("Error en {$solicitud->numero_solicitud}: {$e->getMessage()}");
                }

                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Cotizaciones encontradas', $totalPendientes],
                [$dryRun ? 'Simuladas' : 'Aplicadas exitosamente', $procesadas],
                ['Errores', $errores],
            ]
        );

        if ($dryRun) {
            $this->warn('Ejecución en modo dry-run: no se aplicaron cambios reales.');
        } else {
            $this->info("Proceso completado. {$procesadas} cotizaciones marcadas como APLICADAS.");
        }

        return $errores > 0 ? 1 : 0;
    }
}
