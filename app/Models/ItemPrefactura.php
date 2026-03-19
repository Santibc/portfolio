<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPrefactura extends Model
{
    use HasFactory;

    protected $table = 'items_prefactura';

    protected $fillable = [
        'prefactura_id',
        'producto_id',
        'variante_producto_id',
        'cantidad',
        'precio_unitario',
        'precio_original',
        'descuento_porcentaje',
        'descuento_valor',
        'subtotal',
        'iva',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'precio_original' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_valor' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function prefactura()
    {
        return $this->belongsTo(Prefactura::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    // Accessors
    public function getNombreCompletoProductoAttribute()
    {
        $nombre = $this->producto->referencia . ' - ' . $this->producto->nombre;
        if ($this->variante) {
            $nombre .= ' (' . $this->variante->nombre_completo . ')';
        }
        return $nombre;
    }

    public function getSkuAttribute()
    {
        return $this->variante ? $this->variante->sku : $this->producto->referencia;
    }
}
