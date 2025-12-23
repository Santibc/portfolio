<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // MÓDULO 9: Procesamiento de Dividendos
        // Procesar y pagar dividendos programados - diario a las 6:00 AM
        $schedule->command('dividendos:procesar')
            ->dailyAt('06:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/dividendos.log'))
            ->onSuccess(function () {
                \Log::info('Dividendos procesados exitosamente');
            })
            ->onFailure(function () {
                \Log::error('Error procesando dividendos');
            });

        // Notificar dividendos próximos (3 días antes) - diario a las 9:00 AM
        $schedule->command('dividendos:notificar --dias=3')
            ->dailyAt('09:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
