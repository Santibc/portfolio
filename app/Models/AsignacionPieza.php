<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionPieza extends Model
{
    protected $table = 'asignaciones_piezas';

    const UPDATED_AT = null;

    protected $fillable = [
        'orden_pieza_id', 'orden_id', 'asignado_desde_id', 'asignado_a_id',
        'asignado_por_id', 'tipo_asignacion', 'porcentaje_al_asignar', 'notas', 'activa',
    ];

    protected $casts = [
        'porcentaje_al_asignar' => 'decimal:2',
        'activa' => 'boolean',
    ];

    public function pieza()
    {
        return $this->belongsTo(OrdenPieza::class, 'orden_pieza_id');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function asignadoDesde()
    {
        return $this->belongsTo(User::class, 'asignado_desde_id');
    }

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a_id');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'asignado_por_id');
    }
}
