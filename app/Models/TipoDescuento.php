<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDescuento extends Model
{
    use HasFactory;

    protected $table = 'tipos_descuento';

    protected $fillable = [
        'nombre',
        'alcance',
        'modalidad',
        'activo',
    ];

    protected $casts = [
        'activo' => 'bool',
    ];

    /**
     * @param  Builder<TipoDescuento>  $query
     * @return Builder<TipoDescuento>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * @param  Builder<TipoDescuento>  $query
     * @return Builder<TipoDescuento>
     */
    public function scopeDeAlcance(Builder $query, string $alcance): Builder
    {
        return $query->where('alcance', $alcance);
    }
}
