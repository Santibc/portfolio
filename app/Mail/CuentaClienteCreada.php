<?php

namespace App\Mail;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email enviado al cliente cuando se crea su cuenta automáticamente
 * Contiene las credenciales temporales para acceder al portal
 */
class CuentaClienteCreada extends Mailable
{
    use Queueable, SerializesModels;

    public Cliente $cliente;
    public User $usuario;
    public string $passwordTemporal;
    public string $urlLogin;

    /**
     * Create a new message instance.
     */
    public function __construct(Cliente $cliente, User $usuario, string $passwordTemporal)
    {
        $this->cliente = $cliente;
        $this->usuario = $usuario;
        $this->passwordTemporal = $passwordTemporal;
        $this->urlLogin = route('login');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Bienvenido a Miracle - Tu cuenta ha sido creada')
                    ->view('emails.cuenta-creada');
    }
}
