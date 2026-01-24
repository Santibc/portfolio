<?php

namespace App\Notifications;

use App\Models\Alerta;
use App\Services\AlertaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertaNotification extends Notification
{
    use Queueable;

    protected Alerta $alerta;

    /**
     * Create a new notification instance.
     */
    public function __construct(Alerta $alerta)
    {
        $this->alerta = $alerta;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $prioridadColors = [
            'critica' => '#dc3545',
            'alta' => '#ffc107',
            'media' => '#17a2b8',
            'baja' => '#6c757d',
        ];

        $prioridadLabels = [
            'critica' => 'CRITICA',
            'alta' => 'Alta',
            'media' => 'Media',
            'baja' => 'Baja',
        ];

        $tipoLabel = AlertaService::getTipoLabel($this->alerta->tipo);
        $prioridadLabel = $prioridadLabels[$this->alerta->prioridad] ?? ucfirst($this->alerta->prioridad);

        $fechaVencimiento = $this->alerta->fecha_vencimiento
            ? $this->alerta->fecha_vencimiento->format('d/m/Y')
            : 'No especificada';

        $mailMessage = (new MailMessage)
            ->subject("[{$prioridadLabel}] {$this->alerta->titulo} - Manzer ERP")
            ->greeting("Nueva Alerta: {$tipoLabel}")
            ->line("**{$this->alerta->titulo}**")
            ->line($this->alerta->mensaje)
            ->line("---")
            ->line("**Prioridad:** {$prioridadLabel}")
            ->line("**Fecha de vencimiento:** {$fechaVencimiento}");

        // Agregar botón de acción
        $mailMessage->action('Ver Alerta', route('alertas.show', $this->alerta));

        // Mensaje de urgencia si es crítica
        if ($this->alerta->prioridad === 'critica') {
            $mailMessage->line('**ATENCIÓN:** Esta alerta requiere acción inmediata.');
        }

        $mailMessage->line('---')
            ->line('Este correo fue enviado automáticamente por el sistema de alertas de Manzer ERP.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'alerta_id' => $this->alerta->id,
            'titulo' => $this->alerta->titulo,
            'tipo' => $this->alerta->tipo,
            'prioridad' => $this->alerta->prioridad,
        ];
    }
}
