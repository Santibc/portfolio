<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'iso2',
        'iso3',
        'activo',
    ];

    protected $casts = [
        'activo' => 'bool',
    ];

    /**
     * @param  Builder<Pais>  $query
     * @return Builder<Pais>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
