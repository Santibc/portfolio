<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar un correo de prueba para verificar la configuración';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info('Enviando correo de prueba a: ' . $email);

        try {
            Mail::raw('Este es un correo de prueba desde Laravel. Si recibes este mensaje, tu configuración de correo está correcta.', function($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Laravel Portfolio');
            });

            $this->info('✓ Correo enviado exitosamente!');
            $this->info('Revisa tu bandeja de entrada y carpeta de spam.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Error al enviar el correo:');
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
