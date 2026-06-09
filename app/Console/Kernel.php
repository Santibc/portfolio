<?php

namespace App\Console;

use App\Models\CumpleanosConfiguracion;
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
        try {
            $hora = CumpleanosConfiguracion::obtener()->hora_envio;
        } catch (\Exception $e) {
            $hora = '08:00';
        }

        $schedule->command('cumpleanos:enviar')->dailyAt($hora);

        // Generar alertas de caducidades y vencimientos (facturas, contratos, ITV, etc.)
        $schedule->command('alertas:generar')->dailyAt('07:00');

        // Recordatorios de fichaje (entrada/salida) según configuración
        try {
            $fichajeConfig = \App\Models\FichajeConfiguracion::obtener();
            if ($fichajeConfig->activo) {
                $schedule->command('fichajes:recordatorio entrada')->dailyAt($fichajeConfig->horaEntradaCorta());
                $schedule->command('fichajes:recordatorio salida')->dailyAt($fichajeConfig->horaSalidaCorta());
            }
        } catch (\Throwable $e) {
            // La tabla puede no existir todavía durante migraciones; se ignora.
        }
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
