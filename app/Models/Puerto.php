<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puerto extends Model
{
    use HasFactory;

    protected $table = 'puertos';

    protected $fillable = [
        'nombre',
        'pais',
        'activo',
    ];

    protected $casts = [
        'activo' => 'bool',
    ];

    /**
     * @param  Builder<Puerto>  $query
     * @return Builder<Puerto>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * @param  Builder<Puerto>  $query
     * @return Builder<Puerto>
     */
    public function scopeDelPais(Builder $query, string $pais): Builder
    {
        return $query->where('pais', $pais);
    }
}
