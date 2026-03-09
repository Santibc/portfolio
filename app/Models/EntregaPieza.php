<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaPieza extends Model
{
    const UPDATED_AT = null;

    protected $table = 'entrega_piezas';

    protected $fillable = [
        'entrega_id', 'orden_pieza_id', 'cantidad',
    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    public function ordenPieza()
    {
        return $this->belongsTo(OrdenPieza::class, 'orden_pieza_id');
    }
}
