<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionGarantia extends Model
{
    protected $table = 'devoluciones_garantia';

    protected $fillable = [
        'orden_id', 'orden_pieza_id', 'cantidad_devuelta', 'motivo', 'cobrable',
        'monto_cobro', 'estado', 'operario_asignado_id', 'registrado_por',
        'completada_en', 'reentregada_en',
    ];

    protected $casts = [
        'cobrable' => 'boolean',
        'monto_cobro' => 'decimal:2',
        'completada_en' => 'datetime',
        'reentregada_en' => 'datetime',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function pieza()
    {
        return $this->belongsTo(OrdenPieza::class, 'orden_pieza_id');
    }

    public function operarioAsignado()
    {
        return $this->belongsTo(User::class, 'operario_asignado_id');
    }

    public function registradoPorUsuario()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
