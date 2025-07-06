<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LlamadaRespuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'llamada_id',
        'pregunta',
        'respuesta',
    ];

    // Respuesta pertenece a una llamada
    public function llamada()
    {
        return $this->belongsTo(Llamada::class);
    }
}
