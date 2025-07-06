<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llamada extends Model
{
    use HasFactory;

    protected $fillable = [
        'uri',
        'lead_id',
        'user_id',
        'nombre_evento',
        'status',
        'start_time',
        'end_time',
        'join_url',
        'event_type_uri',
        'cancelado_por',
        'motivo_cancelacion',
        'pipeline_status',
        'comentarios',
    ];

    // Llamada pertenece a un lead
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    // Llamada puede tener un closer (usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Llamada tiene muchas respuestas
    public function respuestas()
    {
        return $this->hasMany(LlamadaRespuesta::class);
    }
}
