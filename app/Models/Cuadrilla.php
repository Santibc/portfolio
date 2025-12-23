<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cuadrilla extends Model
{
    protected $table = 'cuadrillas';

    protected $fillable = [
        'nombre',
        'capataz_id',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function capataz(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'capataz_id');
    }

    public function trabajadores(): BelongsToMany
    {
        return $this->belongsToMany(Trabajador::class, 'cuadrilla_trabajadores')
            ->withPivot(['fecha_incorporacion', 'fecha_salida', 'activo'])
            ->withTimestamps();
    }

    public function trabajadoresActivos(): BelongsToMany
    {
        return $this->trabajadores()->wherePivot('activo', true);
    }

    public function obras(): BelongsToMany
    {
        return $this->belongsToMany(Obra::class, 'obra_cuadrillas')
            ->withPivot(['fecha_inicio', 'fecha_fin', 'activo'])
            ->withTimestamps();
    }

    // Accessors
    public function getNumeroMiembrosAttribute(): int
    {
        return $this->trabajadoresActivos()->count();
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
