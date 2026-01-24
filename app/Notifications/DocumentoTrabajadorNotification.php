<?php

namespace App\Notifications;

use App\Models\TrabajadorDocumento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentoTrabajadorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected TrabajadorDocumento $documento;

    /**
     * Create a new notification instance.
     */
    public function __construct(TrabajadorDocumento $documento)
    {
        $this->documento = $documento;
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
        $tipoDocumento = $this->getTipoDocumentoTexto();

        return (new MailMessage)
            ->subject('Nuevo documento disponible - Manzer ERP')
            ->greeting("Hola {$notifiable->name},")
            ->line("Se ha subido un nuevo documento a tu perfil de trabajador:")
            ->line("**{$this->documento->nombre}**")
            ->line("Tipo: {$tipoDocumento}")
            ->when($this->documento->fecha_caducidad, function ($message) {
                return $message->line("Fecha de caducidad: {$this->documento->fecha_caducidad->format('d/m/Y')}");
            })
            ->when($this->documento->requiere_lectura, function ($message) {
                return $message->line("⚠️ Este documento requiere tu confirmación de lectura.");
            })
            ->action('Ver en mi Portal', route('trabajador.dashboard'))
            ->line('Este documento está disponible en tu portal de trabajador.')
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
            'documento_id' => $this->documento->id,
            'nombre' => $this->documento->nombre,
            'tipo' => $this->documento->tipo,
        ];
    }

    /**
     * Obtener texto legible del tipo de documento.
     */
    private function getTipoDocumentoTexto(): string
    {
        $tipos = [
            'contrato' => 'Contrato',
            'nomina' => 'Nómina',
            'dni' => 'DNI',
            'ss' => 'Seguridad Social',
            'certificado_formacion' => 'Certificado de Formación',
            'apto_medico' => 'Apto Médico',
            'otro' => 'Otro',
        ];

        return $tipos[$this->documento->tipo] ?? 'Documento';
    }
}
