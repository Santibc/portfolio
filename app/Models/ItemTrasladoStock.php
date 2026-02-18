<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTrasladoStock extends Model
{
    use HasFactory;

    protected $table = 'items_traslado_stock';

    protected $fillable = [
        'traslado_stock_id',
        'producto_id',
        'variante_producto_id',
        'cantidad',
    ];

    public function traslado()
    {
        return $this->belongsTo(TrasladoStock::class, 'traslado_stock_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function varianteProducto()
    {
        return $this->belongsTo(VarianteProducto::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        $nombre = $this->producto->referencia . ' - ' . $this->producto->nombre;
        if ($this->varianteProducto) {
            $nombre .= ' (' . $this->varianteProducto->nombre_variante . ')';
        }
        return $nombre;
    }
}
