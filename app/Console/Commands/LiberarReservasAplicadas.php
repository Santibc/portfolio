<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SolicitudCotizacion;
use App\Services\ReservaStockService;

class LiberarReservasAplicadas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:liberar-aplicadas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera reservas de stock de cotizaciones ya aplicadas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Buscando cotizaciones aplicadas con reservas pendientes...');

        $solicitudesAplicadas = SolicitudCotizacion::where('estado', 'aplicada')->get();

        $this->info("📊 Encontradas {$solicitudesAplicadas->count()} cotizaciones aplicadas");

        if ($solicitudesAplicadas->count() === 0) {
            $this->info('✅ No hay cotizaciones aplicadas para procesar');
            return 0;
        }

        $reservaService = new ReservaStockService();
        $liberadas = 0;
        $errores = 0;

        $progressBar = $this->output->createProgressBar($solicitudesAplicadas->count());
        $progressBar->start();

        foreach ($solicitudesAplicadas as $solicitud) {
            try {
                if ($reservaService->liberarReservasDirectamente($solicitud)) {
                    $liberadas++;
                } else {
                    $errores++;
                    $this->newLine();
                    $this->warn("⚠️  Error al liberar reservas de {$solicitud->numero_solicitud}");
                }
            } catch (\Exception $e) {
                $errores++;
                $this->newLine();
                $this->error("❌ Error en {$solicitud->numero_solicitud}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Proceso completado:");
        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Cotizaciones procesadas', $solicitudesAplicadas->count()],
                ['Liberadas exitosamente', $liberadas],
                ['Errores', $errores],
            ]
        );

        if ($liberadas > 0) {
            $this->info("✨ Se liberaron reservas de {$liberadas} cotizaciones");
        }

        if ($errores > 0) {
            $this->warn("⚠️  Hubo {$errores} errores. Revisa los logs para más detalles.");
            return 1;
        }

        return 0;
    }
}
