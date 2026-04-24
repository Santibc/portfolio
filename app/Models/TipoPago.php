<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    use HasFactory;

    protected $table = 'tipos_pago';

    protected $fillable = [
        'nombre',
        'dias_credito',
        'codigo_siigo',
        'activo',
    ];

    protected $casts = [
        'dias_credito' => 'integer',
        'activo' => 'bool',
    ];

    /**
     * @param  Builder<TipoPago>  $query
     * @return Builder<TipoPago>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * @param  Builder<TipoPago>  $query
     * @return Builder<TipoPago>
     */
    public function scopeContado(Builder $query): Builder
    {
        return $query->where('dias_credito', 0);
    }

    /**
     * @param  Builder<TipoPago>  $query
     * @return Builder<TipoPago>
     */
    public function scopeCredito(Builder $query): Builder
    {
        return $query->where('dias_credito', '>', 0);
    }
}
