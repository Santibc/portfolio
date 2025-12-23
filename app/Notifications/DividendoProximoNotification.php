<?php

namespace App\Notifications;

use App\Models\Dividendo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DividendoProximoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Dividendo $dividendo,
        private int $diasRestantes
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $montoFormateado = '$' . number_format($this->dividendo->monto, 0, ',', '.');
        $proyecto = $this->dividendo->proyecto;
        $fechaFormateada = $this->dividendo->fecha_programada->format('d/m/Y');

        $diasTexto = $this->diasRestantes === 1 ? '1 día' : "{$this->diasRestantes} días";

        return [
            'titulo' => 'Dividendo Próximo',
            'mensaje' => "En {$diasTexto} recibirás un dividendo de {$montoFormateado} del proyecto '{$proyecto->nombre}'.",
            'tipo' => 'dividendo_proximo',
            'dividendo_id' => $this->dividendo->id,
            'dividendo_codigo' => $this->dividendo->codigo_dividendo,
            'monto' => $this->dividendo->monto,
            'monto_formateado' => $montoFormateado,
            'numero_periodo' => $this->dividendo->numero_periodo,
            'fecha_programada' => $fechaFormateada,
            'dias_restantes' => $this->diasRestantes,
            'proyecto_id' => $proyecto->id,
            'proyecto_nombre' => $proyecto->nombre,
            'url' => route('inversionista.dividends.index'),
        ];
    }
}
