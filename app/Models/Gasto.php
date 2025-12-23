<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = [
        'gasto_categoria_id',
        'obra_id',
        'proveedor',
        'concepto',
        'descripcion',
        'importe',
        'iva_porcentaje',
        'iva_importe',
        'importe_total',
        'fecha',
        'fecha_vencimiento',
        'fecha_pago',
        'estado',
        'forma_pago',
        'documento_path',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'importe' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_importe' => 'decimal:2',
        'importe_total' => 'decimal:2',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(GastoCategoria::class, 'gasto_categoria_id');
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeDirectos($query)
    {
        return $query->whereHas('categoria', fn($q) => $q->where('tipo', 'directo'));
    }

    public function scopeIndirectos($query)
    {
        return $query->whereHas('categoria', fn($q) => $q->where('tipo', 'indirecto'));
    }
}
