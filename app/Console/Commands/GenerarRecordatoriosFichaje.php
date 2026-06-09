<?php

namespace App\Console\Commands;

use App\Models\Alerta;
use App\Models\Fichaje;
use App\Models\FichajeConfiguracion;
use App\Models\Trabajador;
use App\Notifications\FichajeRecordatorioNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerarRecordatoriosFichaje extends Command
{
    protected $signature = 'fichajes:recordatorio {tipo : entrada o salida}';

    protected $description = 'Envía recordatorios (email + aviso interno) para que los trabajadores fichen entrada/salida';

    public function handle(): int
    {
        $tipo = $this->argument('tipo');
        if (!in_array($tipo, ['entrada', 'salida'], true)) {
            $this->error('Tipo inválido. Usa: entrada o salida.');
            return self::FAILURE;
        }

        $config = FichajeConfiguracion::obtener();
        if (!$config->activo) {
            $this->info('Los recordatorios de fichaje están desactivados.');
            return self::SUCCESS;
        }

        $hoy = now()->toDateString();
        $titulo = $tipo === 'entrada' ? 'Recuerda fichar tu entrada' : 'Recuerda fichar tu salida';
        $mensaje = $tipo === 'entrada'
            ? 'Aún no has registrado tu hora de entrada de hoy. Hazlo desde tu portal.'
            : 'Has fichado la entrada pero no la salida. No olvides registrarla.';

        $count = 0;
        $trabajadores = Trabajador::where('activo', true)->whereNotNull('user_id')->with('user')->get();

        foreach ($trabajadores as $t) {
            if (!$t->user) {
                continue;
            }

            $fichaje = Fichaje::where('trabajador_id', $t->id)->whereDate('fecha', $hoy)->first();

            if ($tipo === 'entrada') {
                // Solo a quien NO ha fichado entrada
                if ($fichaje && $fichaje->hora_entrada) {
                    continue;
                }
            } else {
                // Solo a quien fichó entrada pero NO salida
                if (!$fichaje || !$fichaje->hora_entrada || $fichaje->hora_salida) {
                    continue;
                }
            }

            // Evitar duplicar el aviso el mismo día (el modelo Alerta no usa timestamps,
            // por eso deduplicamos por fecha_vencimiento, que sí se guarda)
            $yaAvisado = Alerta::where('tipo', "fichaje_{$tipo}")
                ->where('para_usuario_id', $t->user->id)
                ->whereDate('fecha_vencimiento', $hoy)
                ->exists();
            if ($yaAvisado) {
                continue;
            }

            // Aviso interno (campanita)
            $alerta = new Alerta([
                'tipo' => "fichaje_{$tipo}",
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'prioridad' => 'media',
                'alertable_type' => Trabajador::class,
                'alertable_id' => $t->id,
                'para_usuario_id' => $t->user->id,
                'fecha_vencimiento' => $hoy,
            ]);
            $alerta->created_at = now();
            $alerta->save();

            // Email
            try {
                $t->user->notify(new FichajeRecordatorioNotification($tipo));
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar email de recordatorio de fichaje', ['user' => $t->user->id, 'error' => $e->getMessage()]);
            }

            $count++;
        }

        $this->info("Recordatorios de {$tipo} enviados: {$count}");
        return self::SUCCESS;
    }
}
