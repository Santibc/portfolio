<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConceptoGastoFijo extends Model
{
    use SoftDeletes;

    protected $table = 'conceptos_gasto_fijo';

    protected $fillable = [
        'nombre',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function gastosFijos(): HasMany
    {
        return $this->hasMany(GastoFijo::class, 'concepto_gasto_fijo_id');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
