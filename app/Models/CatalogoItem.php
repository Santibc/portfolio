<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoItem extends Model
{
    protected $table = 'catalogo_items';

    protected $fillable = [
        'codigo', 'descripcion', 'precio_unitario', 'porcentaje_iva', 'categoria', 'activo',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'porcentaje_iva' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function ordenItems()
    {
        return $this->hasMany(OrdenItem::class, 'catalogo_item_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
