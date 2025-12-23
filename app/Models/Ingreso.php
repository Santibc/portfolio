<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    protected $table = 'ingresos';

    protected $fillable = [
        'obra_id',
        'cliente_id',
        'factura_id',
        'concepto',
        'descripcion',
        'importe',
        'iva_porcentaje',
        'iva_importe',
        'retencion_porcentaje',
        'retencion_importe',
        'importe_total',
        'fecha',
        'fecha_prevista_cobro',
        'fecha_cobro',
        'estado',
        'forma_pago',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_prevista_cobro' => 'date',
        'fecha_cobro' => 'date',
        'importe' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_importe' => 'decimal:2',
        'retencion_porcentaje' => 'decimal:2',
        'retencion_importe' => 'decimal:2',
        'importe_total' => 'decimal:2',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCobrados($query)
    {
        return $query->where('estado', 'cobrado');
    }
}
