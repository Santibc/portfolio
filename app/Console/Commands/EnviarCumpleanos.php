<?php

namespace App\Console\Commands;

use App\Mail\CumpleanosMail;
use App\Models\CumpleanosConfiguracion;
use App\Models\EmailLog;
use App\Models\Trabajador;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarCumpleanos extends Command
{
    protected $signature = 'cumpleanos:enviar
                            {--forzar : Enviar aunque ya se haya enviado hoy}
                            {--test-email= : Enviar email de prueba a esta direccion}';

    protected $description = 'Envia emails de felicitacion de cumpleanos a trabajadores activos';

    public function handle(): int
    {
        $this->info('Iniciando envio de emails de cumpleaños...');
        $this->newLine();

        $config = CumpleanosConfiguracion::obtener();

        // Modo prueba
        if ($testEmail = $this->option('test-email')) {
            return $this->enviarPrueba($config, $testEmail);
        }

        if (!$config->activa) {
            $this->warn('El envio de emails de cumpleaños esta desactivado.');
            $this->info('Activalo desde Configuración > Emails Cumpleaños.');
            return Command::SUCCESS;
        }

        $hoy = now();

        // Trabajadores activos con email y fecha_nacimiento que cumplen hoy
        $cumpleaneros = Trabajador::activos()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', $hoy->month)
            ->whereDay('fecha_nacimiento', $hoy->day)
            ->get();

        if ($cumpleaneros->isEmpty()) {
            $this->info("No hay trabajadores que cumplan años hoy ({$hoy->format('d/m')}).");
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$cumpleaneros->count()} cumpleañero(s) hoy ({$hoy->format('d/m/Y')}):");
        $this->newLine();

        $enviados = 0;
        $fallidos = 0;
        $omitidos = 0;

        foreach ($cumpleaneros as $trabajador) {
            // Verificar si ya se envió hoy
            if (!$this->option('forzar')) {
                $yaEnviado = EmailLog::where('tipo', EmailLog::TIPO_CUMPLEANOS)
                    ->where('destinatario_email', $trabajador->email)
                    ->where('estado', EmailLog::ESTADO_ENVIADO)
                    ->whereDate('created_at', $hoy->toDateString())
                    ->exists();

                if ($yaEnviado) {
                    $this->line("  [SKIP] {$trabajador->nombre_completo} - Ya enviado hoy");
                    $omitidos++;
                    continue;
                }
            }

            try {
                Mail::to($trabajador->email)->send(new CumpleanosMail($trabajador, $config));

                EmailLog::logEnviado(
                    EmailLog::TIPO_CUMPLEANOS,
                    $trabajador->email,
                    $config->resolverAsunto($trabajador),
                    $trabajador,
                    $trabajador->user_id
                );

                $this->info("  [OK] {$trabajador->nombre_completo} ({$trabajador->email})");
                $enviados++;
            } catch (\Exception $e) {
                EmailLog::logFallido(
                    EmailLog::TIPO_CUMPLEANOS,
                    $trabajador->email,
                    $config->resolverAsunto($trabajador),
                    $e->getMessage(),
                    $trabajador,
                    $trabajador->user_id
                );

                $this->error("  [ERROR] {$trabajador->nombre_completo}: {$e->getMessage()}");
                $fallidos++;
            }
        }

        $this->newLine();
        $this->info('=== RESUMEN ===');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Cumpleañeros encontrados', $cumpleaneros->count()],
                ['Emails enviados', $enviados],
                ['Omitidos (ya enviados)', $omitidos],
                ['Emails fallidos', $fallidos],
            ]
        );

        return Command::SUCCESS;
    }

    protected function enviarPrueba(CumpleanosConfiguracion $config, string $email): int
    {
        $this->info("Enviando email de prueba a: {$email}");

        $trabajador = new Trabajador([
            'nombre' => 'Nombre',
            'apellidos' => 'de Prueba',
            'email' => $email,
            'fecha_nacimiento' => now(),
        ]);

        try {
            Mail::to($email)->send(new CumpleanosMail($trabajador, $config));
            $this->info('Email de prueba enviado correctamente.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error al enviar: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
