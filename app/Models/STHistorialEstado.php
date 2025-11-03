<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STHistorialEstado extends Model
{
    use HasFactory;

    protected $table = 'st_historial_estados';

    protected $fillable = [
        'st_orden_servicio_id',
        'estado_anterior',
        'estado_nuevo',
        'observaciones',
        'user_id'
    ];

    // Relaciones
    public function ordenServicio()
    {
        return $this->belongsTo(STOrdenServicio::class, 'st_orden_servicio_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
