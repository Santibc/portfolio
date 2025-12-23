<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrimaConfiguracion extends Model
{
    protected $table = 'prima_configuraciones';

    protected $fillable = [
        'nombre',
        'obra_tipo_id',
        'unidad_medida',
        'minimo_por_trabajador',
        'tramo_prima',
        'importe_prima_por_trabajador',
        'activa',
    ];

    protected $casts = [
        'minimo_por_trabajador' => 'decimal:2',
        'tramo_prima' => 'decimal:2',
        'importe_prima_por_trabajador' => 'decimal:2',
        'activa' => 'boolean',
    ];

    public function obraTipo(): BelongsTo
    {
        return $this->belongsTo(ObraTipo::class, 'obra_tipo_id');
    }

    public function primas(): HasMany
    {
        return $this->hasMany(PrimaTrabajador::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
