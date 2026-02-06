<?php

namespace App\Console\Commands;

use App\Models\Trabajador;
use App\Models\User;
use App\Models\EmailLog;
use App\Notifications\BienvenidaTrabajadorNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CrearUsuariosTrabajadores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trabajadores:crear-usuarios
                            {--dry-run : Simular sin hacer cambios}
                            {--sin-email : No enviar emails de bienvenida}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuarios para trabajadores existentes que no tienen acceso al portal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $sinEmail = $this->option('sin-email');

        if ($dryRun) {
            $this->warn('Modo simulación activado. No se realizarán cambios.');
        }

        $this->info('Buscando trabajadores sin usuario asociado...');

        // Obtener trabajadores activos sin usuario y con email
        $trabajadores = Trabajador::whereNull('user_id')
            ->where('activo', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($trabajadores->isEmpty()) {
            $this->info('No hay trabajadores pendientes de crear usuario.');
            return 0;
        }

        $this->info("Encontrados {$trabajadores->count()} trabajadores sin usuario.");

        $creados = 0;
        $asociados = 0;
        $errores = 0;

        $this->newLine();
        $this->table(
            ['ID', 'Nombre', 'Email', 'DNI', 'Acción'],
            $trabajadores->map(function ($t) {
                $userExiste = User::where('email', $t->email)->first();
                return [
                    $t->id,
                    $t->nombre . ' ' . $t->apellidos,
                    $t->email,
                    $t->dni,
                    $userExiste ? 'ASOCIAR (user_id: ' . $userExiste->id . ')' : 'CREAR'
                ];
            })->toArray()
        );

        if (!$dryRun && !$this->confirm('¿Continuar con el proceso?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($trabajadores->count());
        $progressBar->start();

        foreach ($trabajadores as $trabajador) {
            try {
                // Verificar si ya existe un usuario con ese email
                $userExistente = User::where('email', $trabajador->email)->first();

                if ($userExistente) {
                    // Asociar usuario existente al trabajador
                    if (!$dryRun) {
                        $trabajador->update(['user_id' => $userExistente->id]);

                        // Asegurar que tenga rol Trabajador
                        if (!$userExistente->hasRole('Trabajador')) {
                            $userExistente->assignRole('Trabajador');
                        }

                        // Enviar email de bienvenida (sin contraseña temporal ya que el usuario ya existía)
                        if (!$sinEmail) {
                            $this->enviarEmailBienvenida($userExistente, $trabajador, null);
                        }
                    }
                    $asociados++;
                } else {
                    // Crear nuevo usuario
                    if (!$dryRun) {
                        $user = User::create([
                            'name' => $trabajador->nombre . ' ' . $trabajador->apellidos,
                            'email' => $trabajador->email,
                            'password' => Hash::make($trabajador->dni),
                        ]);

                        $user->assignRole('Trabajador');
                        $trabajador->update(['user_id' => $user->id]);

                        // Enviar email de bienvenida con la contraseña (DNI)
                        if (!$sinEmail) {
                            $this->enviarEmailBienvenida($user, $trabajador, $trabajador->dni);
                        }
                    }
                    $creados++;
                }
            } catch (\Exception $e) {
                $errores++;
                Log::error("Error procesando trabajador {$trabajador->id}", [
                    'error' => $e->getMessage(),
                    'trabajador' => $trabajador->toArray()
                ]);
                $this->newLine();
                $this->error("Error con trabajador {$trabajador->nombre}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Resumen
        $this->info('=== RESUMEN ===');
        $this->line("Usuarios creados: {$creados}");
        $this->line("Usuarios asociados: {$asociados}");

        if ($errores > 0) {
            $this->error("Errores: {$errores}");
        }

        // Trabajadores sin email
        $sinEmailCount = Trabajador::whereNull('user_id')
            ->where('activo', true)
            ->where(function ($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            ->count();

        if ($sinEmailCount > 0) {
            $this->warn("Trabajadores activos sin email (no se procesaron): {$sinEmailCount}");
        }

        if ($dryRun) {
            $this->warn('Modo simulación: ningún cambio fue realizado.');
        }

        return 0;
    }

    /**
     * Enviar email de bienvenida al trabajador
     */
    private function enviarEmailBienvenida(User $user, Trabajador $trabajador, ?string $password): void
    {
        try {
            $user->notify(new BienvenidaTrabajadorNotification($trabajador, $password));

            EmailLog::logEnviado(
                EmailLog::TIPO_BIENVENIDA,
                $user->email,
                "Bienvenido al Portal del Trabajador - Manzer ERP",
                $trabajador,
                $user->id
            );

            $this->line(" -> Email enviado a {$user->email}");
        } catch (\Exception $e) {
            Log::error("Error enviando email de bienvenida", [
                'trabajador_id' => $trabajador->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            $this->warn(" -> Error enviando email a {$user->email}: {$e->getMessage()}");
        }
    }
}
