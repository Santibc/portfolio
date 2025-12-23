<?php

namespace App\Console\Commands;

use App\Services\Dividend\DividendPaymentService;
use Illuminate\Console\Command;

class ProcesarDividendos extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dividendos:procesar
                            {--dry-run : Mostrar qué se procesaría sin hacer cambios}';

    /**
     * The console command description.
     */
    protected $description = 'Procesa y paga todos los dividendos programados para hoy';

    /**
     * Execute the console command.
     */
    public function handle(DividendPaymentService $paymentService): int
    {
        $this->info('=== Procesando Dividendos ===');
        $this->newLine();

        // 1. Marcar dividendos atrasados
        $this->info('Marcando dividendos atrasados...');
        $atrasados = $paymentService->markOverdueDividends();
        $this->line("  - {$atrasados} dividendos marcados como atrasados");
        $this->newLine();

        // 2. Obtener dividendos pendientes
        $dividendosPendientes = $paymentService->getDueDividends();
        $total = $dividendosPendientes->count();

        if ($total === 0) {
            $this->info('No hay dividendos pendientes de pago.');
            return Command::SUCCESS;
        }

        $this->info("Dividendos pendientes de pago: {$total}");
        $this->newLine();

        // Mostrar tabla de dividendos
        $this->table(
            ['Código', 'Usuario', 'Proyecto', 'Monto', 'Fecha Prog.', 'Estado'],
            $dividendosPendientes->map(fn($d) => [
                $d->codigo_dividendo,
                $d->usuario->name,
                substr($d->proyecto->nombre, 0, 20) . '...',
                '$' . number_format($d->monto, 0, ',', '.'),
                $d->fecha_programada->format('d/m/Y'),
                $d->estado,
            ])
        );

        // Si es dry-run, no procesar
        if ($this->option('dry-run')) {
            $this->warn('Modo dry-run: No se realizaron cambios.');
            return Command::SUCCESS;
        }

        // 3. Procesar pagos
        $this->newLine();
        $this->info('Procesando pagos...');

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $resultado = $paymentService->processAllDueDividends();

        $bar->finish();
        $this->newLine(2);

        // 4. Mostrar resultado
        $this->info("=== Resultado ===");
        $this->line("  - Pagados: {$resultado['paid']}");

        if ($resultado['failed'] > 0) {
            $this->error("  - Fallidos: {$resultado['failed']}");

            $this->newLine();
            $this->warn('Errores:');
            foreach ($resultado['errors'] as $error) {
                $this->line("  [{$error['codigo']}] {$error['error']}");
            }
        }

        $montoTotal = $dividendosPendientes->where('estado', '!=', 'pagado')->sum('monto');
        $this->newLine();
        $this->info('Monto total pagado: $' . number_format($montoTotal, 0, ',', '.'));

        return $resultado['failed'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
