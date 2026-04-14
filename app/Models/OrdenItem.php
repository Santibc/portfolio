<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenItem extends Model
{
    protected $table = 'orden_items';

    protected $fillable = [
        'orden_id', 'catalogo_item_id', 'codigo', 'descripcion', 'cantidad',
        'precio_unitario', 'porcentaje_iva', 'descuento_porcentaje', 'descuento_monto',
        'categoria', 'subtotal', 'monto_iva', 'total',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'porcentaje_iva' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'monto_iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function catalogoItem()
    {
        return $this->belongsTo(CatalogoItem::class, 'catalogo_item_id');
    }
}
