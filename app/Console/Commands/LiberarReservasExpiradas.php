<?php

namespace App\Console\Commands;

use App\Services\ReservaStockService;
use Illuminate\Console\Command;

/**
 * Comando para liberar reservas de stock expiradas.
 *
 * Ejecutar cada hora via scheduler o manualmente:
 * php artisan reservas:liberar-expiradas
 */
class LiberarReservasExpiradas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:liberar-expiradas
                            {--dry-run : Simular sin ejecutar cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera las reservas de stock que han expirado (más de 72h sin aplicar)';

    protected ReservaStockService $reservaService;

    public function __construct(ReservaStockService $reservaService)
    {
        parent::__construct();
        $this->reservaService = $reservaService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $verbose = $this->getOutput()->isVerbose();

        $this->info('=== Liberación de Reservas de Stock Expiradas ===');
        $this->info('Fecha/Hora: ' . now()->format('Y-m-d H:i:s'));

        if ($dryRun) {
            $this->warn('⚠️  Modo simulación (dry-run) - No se realizarán cambios');
        }

        // Obtener resumen antes de procesar
        $resumen = $this->reservaService->obtenerResumenReservas();

        if ($verbose) {
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Reservas activas', $resumen['total_activas']],
                    ['Reservas expiradas (pendientes)', $resumen['total_expiradas']],
                    ['Próximas a expirar (2h)', $resumen['proximas_expirar']],
                    ['Cotizaciones con reserva activa', $resumen['cotizaciones_con_reserva']],
                    ['Cotizaciones con reserva expirada', $resumen['cotizaciones_reserva_expirada']],
                ]
            );
        }

        if ($resumen['total_expiradas'] === 0) {
            $this->info('✓ No hay reservas expiradas para liberar');
            return Command::SUCCESS;
        }

        $this->info("Reservas expiradas encontradas: {$resumen['total_expiradas']}");

        if ($dryRun) {
            $this->info('Simulación completada. Use sin --dry-run para ejecutar los cambios.');
            return Command::SUCCESS;
        }

        // Ejecutar liberación
        $this->info('Liberando reservas...');

        $liberadas = $this->reservaService->liberarReservasExpiradas();

        if ($liberadas > 0) {
            $this->info("✓ Se liberaron {$liberadas} reservas expiradas");
        } else {
            $this->warn('No se liberaron reservas (posible error o ya fueron procesadas)');
        }

        // Mostrar resumen final
        if ($verbose) {
            $resumenFinal = $this->reservaService->obtenerResumenReservas();
            $this->newLine();
            $this->info('Resumen final:');
            $this->table(
                ['Métrica', 'Antes', 'Después'],
                [
                    ['Reservas activas', $resumen['total_activas'], $resumenFinal['total_activas']],
                    ['Cotizaciones con reserva', $resumen['cotizaciones_con_reserva'], $resumenFinal['cotizaciones_con_reserva']],
                ]
            );
        }

        $this->info('=== Proceso completado ===');

        return Command::SUCCESS;
    }
}
