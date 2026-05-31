<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodoPago extends Model
{
    use SoftDeletes;

    protected $table = 'metodos_pago';

    protected $fillable = [
        'codigo',
        'nombre',
        'es_efectivo',
        'orden',
        'activo',
    ];

    protected $casts = [
        'es_efectivo' => 'boolean',
        'activo'      => 'boolean',
        'orden'       => 'integer',
    ];

    public function pagos(): HasMany
    {
        return $this->hasMany(VentaPago::class, 'metodo_pago_id');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeEfectivo(Builder $query): Builder
    {
        return $query->where('es_efectivo', true);
    }
}
