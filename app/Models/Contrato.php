<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use SoftDeletes;

    protected $table = 'contratos';

    protected $fillable = [
        'contrato_tipo_id',
        'codigo',
        'titulo',
        'descripcion',
        'cliente_id',
        'subcontrata_id',
        'fecha_inicio',
        'fecha_fin',
        'fecha_firma',
        'importe',
        'iva_porcentaje',
        'tiene_retencion',
        'retencion_porcentaje',
        'importe_retenido',
        'fecha_liberacion_garantia',
        'estado',
        'documento_path',
        'notas',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_firma' => 'date',
        'fecha_liberacion_garantia' => 'date',
        'importe' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'retencion_porcentaje' => 'decimal:2',
        'importe_retenido' => 'decimal:2',
        'tiene_retencion' => 'boolean',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ContratoTipo::class, 'contrato_tipo_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function subcontrata(): BelongsTo
    {
        return $this->belongsTo(Subcontrata::class);
    }

    public function obras(): HasMany
    {
        return $this->hasMany(Obra::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
