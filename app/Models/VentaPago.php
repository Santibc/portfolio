<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaPago extends Model
{
    protected $table = 'venta_pagos';

    protected $fillable = [
        'venta_id',
        'metodo_pago_id',
        'monto',
        'referencia',
    ];

    protected $casts = [
        'monto' => 'integer',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function metodo(): BelongsTo
    {
        // withTrashed: un método deshabilitado (soft delete) debe seguir
        // resolviendo su nombre en el histórico de ventas.
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id')->withTrashed();
    }

    public function getMontoFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->monto, 0, ',', '.');
    }
}
