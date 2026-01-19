<?php

namespace App\Events;

use App\Models\SolicitudCotizacion;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando se crea una nueva cotización
 * Se utiliza para crear automáticamente cuenta de cliente si no existe
 */
class CotizacionCreada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SolicitudCotizacion $solicitud;

    /**
     * Create a new event instance.
     */
    public function __construct(SolicitudCotizacion $solicitud)
    {
        $this->solicitud = $solicitud;
    }
}
