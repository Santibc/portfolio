<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenPieza extends Model
{
    protected $table = 'orden_piezas';

    protected $fillable = [
        'orden_id', 'orden_bosquejo_id', 'nombre', 'nombre_automatico', 'cantidad',
        'cantidad_entregada', 'material', 'calibre', 'especificacion', 'notas',
        'porcentaje_avance', 'operario_actual_id', 'estado', 'requiere_operario',
        'entregada', 'entregada_en', 'entregada_por', 'orden_visual',
    ];

    protected $casts = [
        'porcentaje_avance' => 'decimal:2',
        'requiere_operario' => 'boolean',
        'entregada' => 'boolean',
        'entregada_en' => 'datetime',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function bosquejo()
    {
        return $this->belongsTo(OrdenBosquejo::class, 'orden_bosquejo_id');
    }

    public function operarioActual()
    {
        return $this->belongsTo(User::class, 'operario_actual_id');
    }

    public function entregadaPorUsuario()
    {
        return $this->belongsTo(User::class, 'entregada_por');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionPieza::class, 'orden_pieza_id');
    }

    public function historialAvances()
    {
        return $this->hasMany(HistorialAvance::class, 'orden_pieza_id');
    }

    public function fotos()
    {
        return $this->hasMany(OrdenFoto::class, 'orden_pieza_id');
    }

    public function observaciones()
    {
        return $this->hasMany(OrdenPiezaObservacion::class, 'orden_pieza_id');
    }

    public function garantias()
    {
        return $this->hasMany(DevolucionGarantia::class, 'orden_pieza_id');
    }

    public function entregasPieza()
    {
        return $this->hasMany(EntregaPieza::class, 'orden_pieza_id');
    }
}
