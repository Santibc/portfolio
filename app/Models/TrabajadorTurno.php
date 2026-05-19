<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrabajadorTurno extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores_turno';

    protected $fillable = [
        'nombre',
        'valor_turno_default',
        'activo',
    ];

    protected $casts = [
        'valor_turno_default' => 'integer',
        'activo'              => 'boolean',
    ];

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function getValorTurnoDefaultFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->valor_turno_default, 0, ',', '.');
    }
}
