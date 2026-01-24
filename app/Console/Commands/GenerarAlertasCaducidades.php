<?php

namespace App\Console\Commands;

use App\Services\AlertaService;
use Illuminate\Console\Command;

class GenerarAlertasCaducidades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertas:generar
                            {--tipo= : Generar solo alertas de un tipo específico}
                            {--forzar : Generar alertas aunque ya existan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera alertas automáticas para caducidades próximas';

    /**
     * Execute the console command.
     */
    public function handle(AlertaService $alertaService): int
    {
        $this->info('Iniciando generación de alertas de caducidades...');
        $this->newLine();

        $tipo = $this->option('tipo');
        $forzar = $this->option('forzar');

        if ($tipo) {
            $this->info("Generando alertas solo para tipo: {$tipo}");
        }

        $resultado = $alertaService->generarAlertasCaducidades($tipo);

        // Mostrar resumen
        $this->newLine();
        $this->info('=== RESUMEN DE GENERACIÓN ===');
        $this->newLine();

        $table = [];
        $totalGeneradas = 0;

        foreach ($resultado as $tipoAlerta => $count) {
            $table[] = [
                'Tipo' => AlertaService::getTipoLabel($tipoAlerta),
                'Alertas Generadas' => $count,
            ];
            $totalGeneradas += $count;
        }

        $this->table(['Tipo de Alerta', 'Alertas Generadas'], $table);

        $this->newLine();

        if ($totalGeneradas > 0) {
            $this->info("Total de alertas generadas: {$totalGeneradas}");
        } else {
            $this->info('No se generaron nuevas alertas. Todas las caducidades ya tienen alertas activas o están fuera del rango de días de antelación.');
        }

        $this->newLine();
        $this->info('Proceso completado.');

        return Command::SUCCESS;
    }
}
