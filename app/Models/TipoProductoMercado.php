<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoProductoMercado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_producto_mercado';

    protected $fillable = ['nombre', 'slug'];

    public function productos(): HasMany
    {
        return $this->hasMany(ProductoMercado::class, 'tipo_id');
    }
}
