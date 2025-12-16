<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FarmerWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param User $farmer
     * @param string $temporaryPassword
     * @return void
     */
    public function __construct(
        private User $farmer,
        private string $temporaryPassword
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $loginUrl = route('login');

        return (new MailMessage)
            ->subject('¡Bienvenido a AgroMarket! - Tu cuenta ha sido creada')
            ->greeting("¡Hola {$this->farmer->name}!")
            ->line('Tu cuenta de agricultor ha sido creada exitosamente en la plataforma AgroMarket.')
            ->line('A continuación encontrarás tus credenciales de acceso:')
            ->line("**Email:** {$this->farmer->email}")
            ->line("**Contraseña temporal:** {$this->temporaryPassword}")
            ->action('Iniciar Sesión', $loginUrl)
            ->line('Por seguridad, te recomendamos cambiar tu contraseña después de iniciar sesión por primera vez.')
            ->line('Si tienes alguna pregunta, no dudes en contactarnos.')
            ->salutation('¡Bienvenido a la familia AgroMarket!');
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
            'titulo' => '¡Bienvenido a AgroMarket!',
            'mensaje' => 'Tu cuenta de agricultor ha sido creada exitosamente. Revisa tu correo para obtener tus credenciales de acceso.',
            'tipo' => 'bienvenida_agricultor',
            'farmer_id' => $this->farmer->id,
            'farmer_name' => $this->farmer->name,
            'farmer_email' => $this->farmer->email,
            'url' => route('farmer.dashboard'),
        ];
    }
}
