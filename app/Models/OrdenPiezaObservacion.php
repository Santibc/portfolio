<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenPiezaObservacion extends Model
{
    protected $table = 'orden_pieza_observaciones';

    protected $fillable = [
        'orden_id', 'orden_pieza_id', 'user_id', 'observacion',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function pieza()
    {
        return $this->belongsTo(OrdenPieza::class, 'orden_pieza_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
