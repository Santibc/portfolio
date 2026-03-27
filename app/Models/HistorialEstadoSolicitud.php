<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstadoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'historial_estados_solicitud';

    const TIPO_ESTADO = 'estado';
    const TIPO_ENVIO = 'envio';
    const TIPO_PAGO = 'pago';

    protected $fillable = [
        'solicitud_cotizacion_id',
        'tipo_cambio',
        'estado_anterior',
        'estado_nuevo',
        'observaciones',
        'datos_adicionales',
        'user_id',
    ];

    protected $casts = [
        'datos_adicionales' => 'array',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCotizacion::class, 'solicitud_cotizacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
