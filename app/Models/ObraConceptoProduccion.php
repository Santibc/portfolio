<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObraConceptoProduccion extends Model
{
    use HasFactory;

    protected $table = 'obra_conceptos_produccion';

    protected $fillable = [
        'obra_id',
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'unidad',
        'precio_unitario',
        'activo',
        'orden',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    // Relaciones
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function producciones(): HasMany
    {
        return $this->hasMany(ParteDiarioProduccion::class, 'concepto_produccion_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    // Accessors
    public function getPrecioFormateadoAttribute(): string
    {
        return number_format($this->precio_unitario, 2, ',', '.') . ' €';
    }

    public function getUnidadFormateadaAttribute(): string
    {
        $unidades = [
            'm2' => 'm²',
            'unidades' => 'Uds',
            'hectareas' => 'Ha',
            'jornal' => 'Jornal',
        ];

        return $unidades[$this->unidad] ?? $this->unidad;
    }
}
