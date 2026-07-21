<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVenta extends Model
{
    use HasFactory;

    protected $table = 'items_venta';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'variante_producto_id',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'referencia_producto',
        'nombre_producto',
        'info_variante',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function (ItemVenta $item) {
            $item->precio_total = round(((float) $item->cantidad) * ((float) $item->precio_unitario), 2);
        });

        static::saved(function (ItemVenta $item) {
            $item->venta?->recalcularMonto();
        });

        static::deleted(function (ItemVenta $item) {
            $item->venta?->recalcularMonto();
        });
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
