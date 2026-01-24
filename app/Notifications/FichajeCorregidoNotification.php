<?php

namespace App\Notifications;

use App\Models\Fichaje;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FichajeCorregidoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Fichaje $fichaje;

    /**
     * Create a new notification instance.
     */
    public function __construct(Fichaje $fichaje)
    {
        $this->fichaje = $fichaje;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $fechaFormateada = $this->fichaje->fecha->format('d/m/Y');
        $horaEntrada = $this->fichaje->hora_entrada ?? 'No registrada';
        $horaSalida = $this->fichaje->hora_salida ?? 'No registrada';
        $horasTrabajadas = $this->fichaje->horas_trabajadas ?? 0;

        $nombreCorregidoPor = $this->fichaje->corregidoPor?->name ?? 'Administración';

        return (new MailMessage)
            ->subject("Tu fichaje del {$fechaFormateada} ha sido corregido - Manzer ERP")
            ->greeting("Hola {$notifiable->name},")
            ->line("Se ha realizado una corrección en tu fichaje del **{$fechaFormateada}**:")
            ->line("---")
            ->line("**Hora de entrada:** {$horaEntrada}")
            ->line("**Hora de salida:** {$horaSalida}")
            ->line("**Horas trabajadas:** " . number_format($horasTrabajadas, 2, ',', '.') . " h")
            ->when($this->fichaje->obra, function ($message) {
                return $message->line("**Obra:** {$this->fichaje->obra->codigo} - {$this->fichaje->obra->nombre}");
            })
            ->line("---")
            ->when($this->fichaje->motivo_correccion, function ($message) {
                return $message->line("**Motivo de la corrección:** {$this->fichaje->motivo_correccion}");
            })
            ->line("**Corregido por:** {$nombreCorregidoPor}")
            ->action('Ver mis fichajes', route('trabajador.dashboard'))
            ->line('Si tienes alguna duda sobre esta corrección, contacta con tu encargado.')
            ->salutation('Atentamente, Manzer Agroforestal');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'fichaje_id' => $this->fichaje->id,
            'fecha' => $this->fichaje->fecha->format('Y-m-d'),
            'motivo' => $this->fichaje->motivo_correccion,
        ];
    }
}
