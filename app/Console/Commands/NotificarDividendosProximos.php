<?php

namespace App\Console\Commands;

use App\Models\Dividendo;
use App\Notifications\DividendoProximoNotification;
use App\Services\Dividend\DividendPaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotificarDividendosProximos extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dividendos:notificar
                            {--dias=3 : Días de anticipación para notificar}
                            {--dry-run : Mostrar qué se notificaría sin enviar}';

    /**
     * The console command description.
     */
    protected $description = 'Notifica a usuarios sobre dividendos próximos a pagar';

    /**
     * Execute the console command.
     */
    public function handle(DividendPaymentService $paymentService): int
    {
        $dias = (int) $this->option('dias');
        $dryRun = $this->option('dry-run');

        $this->info("=== Notificando Dividendos Próximos (próximos {$dias} días) ===");
        $this->newLine();

        // Obtener dividendos próximos
        $dividendosProximos = $paymentService->getUpcomingDividends($dias);

        if ($dividendosProximos->isEmpty()) {
            $this->info('No hay dividendos próximos para notificar.');
            return Command::SUCCESS;
        }

        $this->info("Dividendos próximos encontrados: {$dividendosProximos->count()}");

        // Agrupar por fecha para evitar spam de notificaciones
        $porFecha = $dividendosProximos->groupBy(fn($d) => $d->fecha_programada->format('Y-m-d'));

        $notificados = 0;
        $omitidos = 0;

        foreach ($porFecha as $fecha => $dividendos) {
            $diasRestantes = Carbon::parse($fecha)->diffInDays(Carbon::today());

            foreach ($dividendos as $dividendo) {
                // Verificar si ya se notificó hoy (evitar duplicados)
                $yaNotificado = $this->yaNotificadoHoy($dividendo);

                if ($yaNotificado) {
                    $omitidos++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [DRY-RUN] Notificaría: {$dividendo->codigo_dividendo} a {$dividendo->usuario->name}");
                } else {
                    try {
                        $dividendo->usuario->notify(
                            new DividendoProximoNotification($dividendo, $diasRestantes)
                        );
                        $notificados++;

                        $this->line("  Notificado: {$dividendo->codigo_dividendo} - {$dividendo->usuario->name}");
                    } catch (\Exception $e) {
                        Log::error("Error notificando dividendo {$dividendo->codigo_dividendo}: " . $e->getMessage());
                        $this->error("  Error: {$dividendo->codigo_dividendo} - {$e->getMessage()}");
                    }
                }
            }
        }

        $this->newLine();
        $this->info("=== Resultado ===");
        $this->line("  - Notificados: {$notificados}");
        $this->line("  - Omitidos (ya notificados hoy): {$omitidos}");

        if ($dryRun) {
            $this->warn('Modo dry-run: No se enviaron notificaciones.');
        }

        return Command::SUCCESS;
    }

    /**
     * Verificar si el dividendo ya fue notificado hoy
     */
    private function yaNotificadoHoy(Dividendo $dividendo): bool
    {
        // Buscar en notificaciones del usuario de hoy
        $notificaciones = $dividendo->usuario->notifications()
            ->whereDate('created_at', Carbon::today())
            ->where('type', DividendoProximoNotification::class)
            ->get();

        foreach ($notificaciones as $notif) {
            $data = $notif->data;
            if (isset($data['dividendo_id']) && $data['dividendo_id'] === $dividendo->id) {
                return true;
            }
        }

        return false;
    }
}
