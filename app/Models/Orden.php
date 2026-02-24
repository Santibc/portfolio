<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'ordenes';

    protected $fillable = [
        'numero_orden', 'cliente_id', 'creado_por', 'estado_trabajo', 'estado_entrega',
        'estado_pago', 'fecha_entrega', 'hora_entrega', 'ruta_firma_cliente', 'notas',
        'subtotal', 'monto_iva', 'total', 'total_pagado', 'saldo',
        'clonada_de_id', 'bloqueada_por', 'bloqueada_en',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'bloqueada_en' => 'datetime',
        'subtotal' => 'decimal:2',
        'monto_iva' => 'decimal:2',
        'total' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    // === Relaciones ===

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function ordenOriginal()
    {
        return $this->belongsTo(Orden::class, 'clonada_de_id');
    }

    public function bloqueadaPorUsuario()
    {
        return $this->belongsTo(User::class, 'bloqueada_por');
    }

    public function items()
    {
        return $this->hasMany(OrdenItem::class, 'orden_id');
    }

    public function bosquejos()
    {
        return $this->hasMany(OrdenBosquejo::class, 'orden_id');
    }

    public function piezas()
    {
        return $this->hasMany(OrdenPieza::class, 'orden_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'orden_id');
    }

    public function fotos()
    {
        return $this->hasMany(OrdenFoto::class, 'orden_id');
    }

    public function comentarios()
    {
        return $this->hasMany(OrdenComentario::class, 'orden_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionPieza::class, 'orden_id');
    }

    public function actividades()
    {
        return $this->hasMany(RegistroActividad::class, 'orden_id');
    }

    public function garantias()
    {
        return $this->hasMany(DevolucionGarantia::class, 'orden_id');
    }

    // === Scopes ===

    public function scopeBorradores($query)
    {
        return $query->where('estado_trabajo', 'borrador');
    }

    public function scopeGeneradas($query)
    {
        return $query->where('estado_trabajo', 'generada');
    }

    public function scopeEnEjecucion($query)
    {
        return $query->where('estado_trabajo', 'en_ejecucion');
    }

    public function scopeEjecutadas($query)
    {
        return $query->where('estado_trabajo', 'ejecutada');
    }

    public function scopeAnuladas($query)
    {
        return $query->where('estado_trabajo', 'anulada');
    }

    public function scopeConSaldoPendiente($query)
    {
        return $query->where('estado_pago', 'saldo_pendiente');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado_pago', 'pagado');
    }

    public function scopeNoAnuladas($query)
    {
        return $query->where('estado_trabajo', '!=', 'anulada');
    }

    public function scopeNoBorradores($query)
    {
        return $query->where('estado_trabajo', '!=', 'borrador');
    }
}
