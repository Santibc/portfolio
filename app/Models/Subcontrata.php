<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcontrata extends Model
{
    use SoftDeletes;

    protected $table = 'subcontratas';

    protected $fillable = [
        'nombre',
        'razon_social',
        'cif',
        'direccion',
        'telefono',
        'email',
        'persona_contacto',
        'tarifa_hora',
        'tarifa_dia',
        'activa',
        'homologada',
        'fecha_homologacion',
        'notas',
    ];

    protected $casts = [
        'tarifa_hora' => 'decimal:2',
        'tarifa_dia' => 'decimal:2',
        'activa' => 'boolean',
        'homologada' => 'boolean',
        'fecha_homologacion' => 'date',
    ];

    public function trabajadores(): HasMany
    {
        return $this->hasMany(Trabajador::class);
    }

    public function documentosCae(): HasMany
    {
        return $this->hasMany(SubcontrataDocumentoCae::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function obras()
    {
        return $this->belongsToMany(Obra::class, 'obra_subcontratas')
            ->withPivot(['fecha_inicio', 'fecha_fin', 'importe_contratado', 'activa'])
            ->withTimestamps();
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return $this->razon_social ?? $this->nombre;
    }
}
