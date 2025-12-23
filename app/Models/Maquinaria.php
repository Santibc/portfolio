<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maquinaria extends Model
{
    use SoftDeletes;

    protected $table = 'maquinaria';

    protected $fillable = [
        'maquinaria_tipo_id',
        'codigo_interno',
        'marca',
        'modelo',
        'numero_serie',
        'numero_bastidor',
        'fecha_compra',
        'coste_adquisicion',
        'vida_util_meses',
        'amortizacion_dia',
        'coste_hora',
        'estado',
        'obra_asignada_id',
        'trabajador_asignado_id',
        'tiene_marcado_ce',
        'tiene_manual',
        'notas',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'coste_adquisicion' => 'decimal:2',
        'amortizacion_dia' => 'decimal:2',
        'coste_hora' => 'decimal:2',
        'tiene_marcado_ce' => 'boolean',
        'tiene_manual' => 'boolean',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(MaquinariaTipo::class, 'maquinaria_tipo_id');
    }

    public function obraAsignada(): BelongsTo
    {
        return $this->belongsTo(Obra::class, 'obra_asignada_id');
    }

    public function trabajadorAsignado(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_asignado_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(MaquinariaAsignacion::class);
    }

    public function inspecciones(): HasMany
    {
        return $this->hasMany(MaquinariaInspeccion::class);
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(MaquinariaMantenimiento::class);
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        $nombre = $this->tipo->nombre ?? '';
        if ($this->marca) $nombre .= " {$this->marca}";
        if ($this->modelo) $nombre .= " {$this->modelo}";
        if ($this->codigo_interno) $nombre .= " ({$this->codigo_interno})";
        return trim($nombre);
    }

    // Scopes
    public function scopeOperativas($query)
    {
        return $query->where('estado', 'operativa');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'operativa')->whereNull('obra_asignada_id');
    }
}
