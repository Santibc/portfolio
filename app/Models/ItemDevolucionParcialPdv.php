<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemDevolucionParcialPdv extends Model
{
    protected $table = 'items_devolucion_parcial_pdv';

    protected $fillable = [
        'devolucion_parcial_pdv_id',
        'item_venta_pdv_id',
        'producto_id',
        'variante_producto_id',
        'cantidad_devuelta',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_valor',
        'subtotal',
        'iva',
        'total',
    ];

    protected $casts = [
        'cantidad_devuelta' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_valor' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function devolucionParcial()
    {
        return $this->belongsTo(DevolucionParcialPdv::class, 'devolucion_parcial_pdv_id');
    }

    public function itemVenta()
    {
        return $this->belongsTo(ItemVentaPdv::class, 'item_venta_pdv_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }
}
