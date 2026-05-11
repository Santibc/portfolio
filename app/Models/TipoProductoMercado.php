<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProductoMercado extends Model
{
    use HasFactory;

    protected $table = 'tipos_producto_mercado';

    protected $fillable = ['nombre', 'slug'];

    public function productos(): HasMany
    {
        return $this->hasMany(ProductoMercado::class, 'tipo_id');
    }
}
