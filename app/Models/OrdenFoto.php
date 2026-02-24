<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenFoto extends Model
{
    protected $table = 'orden_fotos';

    const UPDATED_AT = null;

    protected $fillable = [
        'orden_id', 'orden_pieza_id', 'tipo_foto', 'ruta_archivo',
        'ruta_miniatura', 'subido_por', 'aprobada', 'aprobada_por',
    ];

    protected $casts = [
        'aprobada' => 'boolean',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function pieza()
    {
        return $this->belongsTo(OrdenPieza::class, 'orden_pieza_id');
    }

    public function subidoPorUsuario()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function aprobadaPorUsuario()
    {
        return $this->belongsTo(User::class, 'aprobada_por');
    }
}
