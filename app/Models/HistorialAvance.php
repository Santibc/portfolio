<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialAvance extends Model
{
    protected $table = 'historial_avances';

    const UPDATED_AT = null;

    protected $fillable = [
        'orden_pieza_id', 'operario_id', 'porcentaje_desde', 'porcentaje_hasta',
        'contribucion', 'notas', 'asignado_en', 'completado_en',
    ];

    protected $casts = [
        'porcentaje_desde' => 'decimal:2',
        'porcentaje_hasta' => 'decimal:2',
        'contribucion' => 'decimal:2',
        'asignado_en' => 'datetime',
        'completado_en' => 'datetime',
    ];

    public function pieza()
    {
        return $this->belongsTo(OrdenPieza::class, 'orden_pieza_id');
    }

    public function operario()
    {
        return $this->belongsTo(User::class, 'operario_id');
    }
}
