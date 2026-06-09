<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FichajeRecordatorioNotification extends Notification
{
    protected string $tipo;

    public function __construct(string $tipo)
    {
        $this->tipo = $tipo; // 'entrada' | 'salida'
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $accion = $this->tipo === 'entrada' ? 'entrada' : 'salida';

        return (new MailMessage)
            ->subject('Recordatorio: ficha tu ' . $accion . ' - Manzer ERP')
            ->greeting("Hola {$notifiable->name},")
            ->line("Te recordamos que debes registrar tu **hora de {$accion}** en el portal de Manzer Agroforestal.")
            ->action('Ir a fichar', route('trabajador.dashboard'))
            ->line('Gracias por mantener tus fichajes al día.')
            ->salutation('Atentamente, Manzer Agroforestal');
    }
}
