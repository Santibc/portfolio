<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'orden_id', 'entregada_por', 'notas',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function piezas()
    {
        return $this->hasMany(EntregaPieza::class, 'entrega_id');
    }

    public function fotos()
    {
        return $this->hasMany(OrdenFoto::class, 'entrega_id');
    }

    public function entregadaPorUsuario()
    {
        return $this->belongsTo(User::class, 'entregada_por');
    }
}
