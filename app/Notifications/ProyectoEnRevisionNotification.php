<?php

namespace App\Notifications;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProyectoEnRevisionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private Proyecto $proyecto
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'titulo' => 'Nuevo proyecto pendiente de revisión',
            'mensaje' => "El proyecto '{$this->proyecto->nombre}' ({$this->proyecto->codigo}) ha sido enviado a revisión por {$this->proyecto->agricultor->name}.",
            'tipo' => 'proyecto_revision',
            'proyecto_id' => $this->proyecto->id,
            'proyecto_codigo' => $this->proyecto->codigo,
            'proyecto_nombre' => $this->proyecto->nombre,
            'agricultor_id' => $this->proyecto->agricultor_id,
            'agricultor_nombre' => $this->proyecto->agricultor->name,
            'url' => route('admin.projects.review.show', $this->proyecto->id),
        ];
    }
}
