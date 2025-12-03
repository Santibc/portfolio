<?php

namespace App\Notifications;

use App\Models\Proyecto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProyectoAprobadoNotification extends Notification
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
            'titulo' => '¡Proyecto aprobado!',
            'mensaje' => "Tu proyecto '{$this->proyecto->nombre}' ({$this->proyecto->codigo}) ha sido aprobado y está ahora en recaudación.",
            'tipo' => 'proyecto_aprobado',
            'proyecto_id' => $this->proyecto->id,
            'proyecto_codigo' => $this->proyecto->codigo,
            'proyecto_nombre' => $this->proyecto->nombre,
            'aprobado_por' => $this->proyecto->aprobador->name ?? 'Administrador',
            'notas_aprobacion' => $this->proyecto->notas_aprobacion,
            'url' => route('farmer.projects.show', $this->proyecto->id),
        ];
    }
}
