<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioFechaEspecial;
use App\Models\FechaEspecialCliente;
use App\Models\RecordatorioEnviado;
use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            DB::beginTransaction();
            try {
                // Obtener productos recomendados
                $productosRecomendados = $this->obtenerProductosRecomendados($fecha->tipo);

                // Generar cupón de descuento si tiene descuento configurado
                $cuponGenerado = null;
                if ($fecha->tieneDescuento()) {
                    $cuponGenerado = $fecha->generarCuponDescuento();
                }

                if ($testMode) {
                    $descuentoInfo = $fecha->tieneDescuento() ? " (Descuento: {$fecha->descuento_especial}%, Cupón: {$cuponGenerado})" : "";
                    $this->line("  [TEST] Recordatorio para: {$fecha->user->email} - {$fecha->nombre}{$descuentoInfo}");
                } else {
                    // Enviar email
                    Mail::to($fecha->user->email)
                        ->send(new RecordatorioFechaEspecial($fecha, $productosRecomendados));

                    // Registrar el envío del recordatorio
                    $recordatorio = RecordatorioEnviado::create([
                        'fecha_especial_id' => $fecha->id,
                        'user_id' => $fecha->user_id,
                        'enviado_en' => now(),
                        'tipo_recordatorio' => 'email',
                        'productos_sugeridos' => $productosRecomendados->pluck('id')->toArray(),
                        'descuento_ofrecido' => $fecha->descuento_especial ?? 0,
                        'cupon_generado' => $cuponGenerado,
                    ]);

                    // Marcar como enviado
                    $fecha->update(['ultimo_recordatorio' => now()]);

                    $descuentoInfo = $fecha->tieneDescuento() ? " (Descuento: {$fecha->descuento_especial}%)" : "";
                    $this->line("  ✓ Enviado a: {$fecha->user->email} - {$fecha->nombre}{$descuentoInfo}");
                    Log::info("Recordatorio enviado", [
                        'recordatorio_id' => $recordatorio->id,
                        'user_id' => $fecha->user_id,
                        'fecha_especial' => $fecha->nombre,
                        'dias_restantes' => $fecha->dias_restantes,
                        'descuento' => $fecha->descuento_especial,
                        'cupon' => $cuponGenerado,
                    ]);
                }

                DB::commit();
                $enviados++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errores++;
                $this->error("  ✗ Error enviando a {$fecha->user->email}: {$e->getMessage()}");
                Log::error("Error enviando recordatorio", [
                    'user_id' => $fecha->user_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
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
