<?php

namespace App\Notifications;

use App\Models\Trabajador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BienvenidaTrabajadorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Trabajador $trabajador;
    protected ?string $passwordTemporal;

    /**
     * Create a new notification instance.
     */
    public function __construct(Trabajador $trabajador, ?string $passwordTemporal = null)
    {
        $this->trabajador = $trabajador;
        $this->passwordTemporal = $passwordTemporal;
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
        $mail = (new MailMessage)
            ->subject('Bienvenido al Portal del Trabajador - Manzer ERP')
            ->greeting("¡Bienvenido/a {$this->trabajador->nombre}!")
            ->line("Se ha creado tu acceso al Portal del Trabajador de Manzer Agroforestal.")
            ->line("Desde tu portal podrás:")
            ->line("✓ Consultar tus fichajes y horas trabajadas")
            ->line("✓ Ver tus documentos (nóminas, contratos, certificados)")
            ->line("✓ Consultar tus EPIs asignados")
            ->line("✓ Ver tus formaciones y caducidades")
            ->line("✓ Revisar tus primas y bonos")
            ->line("---")
            ->line("**Datos de acceso:**")
            ->line("**Usuario/Email:** {$notifiable->email}");

        if ($this->passwordTemporal) {
            $mail->line("**Contraseña temporal:** {$this->passwordTemporal}")
                 ->line("⚠️ Por seguridad, te recomendamos cambiar tu contraseña en el primer acceso.");
        }

        return $mail
            ->action('Acceder al Portal', route('login'))
            ->line('Si tienes alguna duda, contacta con el departamento de RRHH.')
            ->salutation('¡Bienvenido al equipo! Manzer Agroforestal');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'trabajador_id' => $this->trabajador->id,
            'tipo' => 'bienvenida',
        ];
    }
}
