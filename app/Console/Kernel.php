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
