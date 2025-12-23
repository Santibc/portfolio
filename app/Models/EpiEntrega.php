<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpiEntrega extends Model
{
    protected $table = 'epi_entregas';

    protected $fillable = [
        'epi_inventario_id',
        'trabajador_id',
        'fecha_entrega',
        'fecha_devolucion',
        'motivo_devolucion',
        'firma_trabajador_path',
        'entregado_por',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'fecha_devolucion' => 'date',
    ];

    public function inventario(): BelongsTo
    {
        return $this->belongsTo(EpiInventario::class, 'epi_inventario_id');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    // Accessors para simplificar acceso a datos
    public function getNombreEpiAttribute(): string
    {
        return $this->inventario?->catalogo?->nombre ?? 'EPI desconocido';
    }

    public function getCategoriaEpiAttribute(): ?string
    {
        return $this->inventario?->catalogo?->categoria;
    }

    public function getFechaCaducidadEpiAttribute()
    {
        return $this->inventario?->fecha_caducidad;
    }
}
