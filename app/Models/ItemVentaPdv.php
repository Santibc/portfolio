<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVentaPdv extends Model
{
    use HasFactory;

    protected $table = 'items_venta_pdv';

    protected $fillable = [
        'venta_pdv_id',
        'producto_id',
        'variante_producto_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'iva',
        'total',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function venta()
    {
        return $this->belongsTo(VentaPdv::class, 'venta_pdv_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    // Métodos
    public function getNombreCompletoProductoAttribute()
    {
        $nombre = $this->producto->nombre;

        if ($this->variante) {
            $nombre .= ' - ' . $this->variante->referencia_variante;
            if ($this->variante->color) {
                $nombre .= ' (' . $this->variante->color . ')';
            }
        }

        return $nombre;
    }

    public function getSkuAttribute()
    {
        if ($this->variante) {
            return $this->variante->sku;
        }
        return $this->producto->referencia;
    }

    // Calcular totales del item
    public function calcularTotales()
    {
        $this->subtotal = ($this->precio_unitario * $this->cantidad) - $this->descuento;
        // IVA del 19% si aplica (se puede configurar)
        $this->iva = 0; // Por defecto sin IVA, ajustar según necesidad
        $this->total = $this->subtotal + $this->iva;

        return $this;
    }
}
