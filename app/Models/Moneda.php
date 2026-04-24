<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    use HasFactory;

    protected $table = 'monedas';

    protected $fillable = [
        'codigo',
        'nombre',
        'simbolo',
        'es_predeterminada',
        'activa',
    ];

    protected $casts = [
        'es_predeterminada' => 'bool',
        'activa' => 'bool',
    ];

    /**
     * @param  Builder<Moneda>  $query
     * @return Builder<Moneda>
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }

    /**
     * @param  Builder<Moneda>  $query
     * @return Builder<Moneda>
     */
    public function scopePredeterminada(Builder $query): Builder
    {
        return $query->where('es_predeterminada', true);
    }
}
