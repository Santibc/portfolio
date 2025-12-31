<?php

namespace App\Console\Commands;

use App\Models\Carrito;
use App\Models\CarritoAbandonadoLog;
use App\Mail\CarritoAbandonadoMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EnviarRecordatoriosCarritoAbandonado extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'carritos:recordatorios
                            {--test : Ejecutar en modo test sin enviar emails}
                            {--empresa= : Procesar solo una empresa específica}';

    /**
     * The console command description.
     */
    protected $description = 'Envía recordatorios de carritos abandonados (1h, 24h, 72h)';

    /**
     * Configuración de tiempos para recordatorios
     */
    private $recordatorios = [
        1 => ['horas' => 1, 'campo' => 'recordatorio_1_enviado'],
        2 => ['horas' => 24, 'campo' => 'recordatorio_2_enviado'],
        3 => ['horas' => 72, 'campo' => 'recordatorio_3_enviado'],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Iniciando envío de recordatorios de carritos abandonados ===');
        $this->newLine();

        $testMode = $this->option('test');
        $empresaId = $this->option('empresa');

        if ($testMode) {
            $this->warn('>>> MODO TEST: No se enviarán emails reales <<<');
            $this->newLine();
        }

        $totalEnviados = 0;
        $totalErrores = 0;

        foreach ($this->recordatorios as $numero => $config) {
            $this->info("Procesando recordatorio #{$numero} ({$config['horas']} horas)...");

            $carritos = $this->obtenerCarritosParaRecordatorio($numero, $config, $empresaId);

            $this->info("  Encontrados: {$carritos->count()} carritos");

            foreach ($carritos as $carrito) {
                try {
                    $resultado = $this->enviarRecordatorio($carrito, $numero, $config, $testMode);

                    if ($resultado) {
                        $totalEnviados++;
                        $this->line("  ✓ Enviado a: {$carrito->email_cliente} (Carrito #{$carrito->id})");
                    }
                } catch (\Exception $e) {
                    $totalErrores++;
                    $this->error("  ✗ Error carrito #{$carrito->id}: {$e->getMessage()}");
                    Log::error('Error enviando recordatorio carrito abandonado', [
                        'carrito_id' => $carrito->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->newLine();
        }

        $this->newLine();
        $this->info("=== Resumen ===");
        $this->info("Total enviados: {$totalEnviados}");
        $this->info("Total errores: {$totalErrores}");

        return Command::SUCCESS;
    }

    /**
     * Obtener carritos que necesitan recordatorio
     */
    private function obtenerCarritosParaRecordatorio($numero, $config, $empresaId = null)
    {
        $horasAtras = now()->subHours($config['horas']);
        $campoRecordatorio = $config['campo'];

        // El carrito anterior debe haberse enviado (o ser el primer recordatorio)
        $campoAnterior = $numero > 1 ? $this->recordatorios[$numero - 1]['campo'] : null;

        $query = Carrito::query()
            // Tiene items
            ->whereNotNull('items')
            ->whereRaw("JSON_LENGTH(items) > 0")
            // Tiene email del cliente
            ->whereNotNull('email_cliente')
            ->where('email_cliente', '!=', '')
            // No ha sido recuperado
            ->where('recuperado', false)
            // Este recordatorio aún no se ha enviado
            ->whereNull($campoRecordatorio)
            // La última actividad fue hace más de X horas
            ->where('ultima_actividad', '<=', $horasAtras)
            // No ha comprado (no tiene orden asociada)
            ->whereDoesntHave('compras');

        // Si no es el primer recordatorio, verificar que el anterior se haya enviado
        if ($campoAnterior) {
            $query->whereNotNull($campoAnterior);
        }

        // Filtrar por empresa si se especifica
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->with('empresa')->get();
    }

    /**
     * Enviar recordatorio
     */
    private function enviarRecordatorio($carrito, $numero, $config, $testMode = false)
    {
        // Generar token único para tracking
        $token = Str::random(64);

        // Crear registro de log
        $log = CarritoAbandonadoLog::create([
            'carrito_id' => $carrito->id,
            'empresa_id' => $carrito->empresa_id,
            'email_cliente' => $carrito->email_cliente,
            'valor_carrito' => $carrito->total,
            'cantidad_items' => count($carrito->items ?? []),
            'recordatorio_numero' => $numero,
            'canal' => 'email',
            'estado' => 'enviado',
            'enviado_at' => now(),
            'token_recuperacion' => $token,
        ]);

        if (!$testMode) {
            // Enviar email
            Mail::to($carrito->email_cliente)
                ->send(new CarritoAbandonadoMail($carrito, $numero, $token));
        }

        // Actualizar carrito con timestamp del recordatorio
        $carrito->update([
            $config['campo'] => now(),
        ]);

        return true;
    }
}
