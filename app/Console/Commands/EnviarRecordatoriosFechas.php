<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioFechaEspecial;
use App\Models\FechaEspecialCliente;
use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarRecordatoriosFechas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordatorios:enviar {--test : Modo de prueba sin enviar emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia recordatorios de fechas especiales a los clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testMode = $this->option('test');

        $this->info('Buscando fechas especiales que requieren recordatorio...');

        // Obtener fechas que necesitan recordatorio hoy
        $fechas = FechaEspecialCliente::with('user')
            ->activas()
            ->get()
            ->filter(function ($fecha) {
                return $fecha->debeEnviarRecordatorio();
            });

        if ($fechas->isEmpty()) {
            $this->info('No hay recordatorios para enviar hoy.');
            return 0;
        }

        $this->info("Se encontraron {$fechas->count()} recordatorios para enviar.");

        $enviados = 0;
        $errores = 0;

        foreach ($fechas as $fecha) {
            try {
                // Obtener productos recomendados
                $productosRecomendados = $this->obtenerProductosRecomendados($fecha->tipo);

                if ($testMode) {
                    $this->line("  [TEST] Recordatorio para: {$fecha->user->email} - {$fecha->nombre}");
                } else {
                    Mail::to($fecha->user->email)
                        ->send(new RecordatorioFechaEspecial($fecha, $productosRecomendados));

                    // Marcar como enviado
                    $fecha->update(['ultimo_recordatorio' => now()]);

                    $this->line("  Enviado a: {$fecha->user->email} - {$fecha->nombre}");
                    Log::info("Recordatorio enviado", [
                        'user_id' => $fecha->user_id,
                        'fecha_especial' => $fecha->nombre,
                        'dias_restantes' => $fecha->dias_restantes
                    ]);
                }

                $enviados++;
            } catch (\Exception $e) {
                $errores++;
                $this->error("  Error enviando a {$fecha->user->email}: {$e->getMessage()}");
                Log::error("Error enviando recordatorio", [
                    'user_id' => $fecha->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Proceso completado. Enviados: {$enviados}, Errores: {$errores}");

        return 0;
    }

    /**
     * Obtener productos recomendados segun el tipo de ocasion
     */
    private function obtenerProductosRecomendados($tipo)
    {
        $query = Producto::where('activo', true)
            ->where('eliminado', false);

        // Buscar por palabras clave segun el tipo
        $keywords = match ($tipo) {
            'cumpleanos' => ['cumpleanos', 'celebracion', 'feliz'],
            'aniversario' => ['amor', 'romantico', 'aniversario', 'rosas'],
            'dia_madre' => ['madre', 'mama', 'especial'],
            'dia_padre' => ['padre', 'papa'],
            default => []
        };

        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('nombre', 'like', "%{$keyword}%")
                      ->orWhere('descripcion', 'like', "%{$keyword}%");
                }
            });
        }

        $productos = $query->inRandomOrder()->limit(4)->get();

        // Si no hay productos especificos, mostrar productos destacados
        if ($productos->isEmpty()) {
            $productos = Producto::where('activo', true)
                ->where('eliminado', false)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        return $productos;
    }
}
