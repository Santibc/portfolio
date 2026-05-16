<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarantiaProductoLiberacion extends Model
{
    use HasFactory;

    protected $table = 'garantia_productos_liberacion';

    protected $fillable = [
        'garantia_id',
        'producto_id',
        'variante_producto_id',
        'ubicacion_id',
        'cantidad',
        'movimiento_stock_id',
    ];

    public function garantia()
    {
        return $this->belongsTo(Garantia::class, 'garantia_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function ubicacionRelacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    public function movimientoStock()
    {
        return $this->belongsTo(MovimientoStock::class, 'movimiento_stock_id');
    }
}
