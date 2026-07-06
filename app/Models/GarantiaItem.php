<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarantiaItem extends Model
{
    use HasFactory;

    protected $table = 'garantia_items';

    protected $fillable = [
        'garantia_id',
        'producto_id',
        'variante_producto_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
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
}
