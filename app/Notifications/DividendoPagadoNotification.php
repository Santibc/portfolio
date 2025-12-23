<?php

namespace App\Notifications;

use App\Models\Dividendo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DividendoPagadoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Dividendo $dividendo
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
        $inversion = $this->dividendo->inversion;

        return [
            'titulo' => 'Dividendo Pagado',
            'mensaje' => "Se ha acreditado un dividendo de {$montoFormateado} a tu billetera del proyecto '{$proyecto->nombre}'.",
            'tipo' => 'dividendo_pagado',
            'dividendo_id' => $this->dividendo->id,
            'dividendo_codigo' => $this->dividendo->codigo_dividendo,
            'monto' => $this->dividendo->monto,
            'monto_formateado' => $montoFormateado,
            'numero_periodo' => $this->dividendo->numero_periodo,
            'proyecto_id' => $proyecto->id,
            'proyecto_nombre' => $proyecto->nombre,
            'inversion_id' => $inversion->id,
            'inversion_codigo' => $inversion->codigo_inversion,
            'url' => route('inversionista.dividends.index'),
        ];
    }
}
