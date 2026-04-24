<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaItem extends Model
{
    use HasFactory;

    protected $table = 'factura_items';

    protected $fillable = [
        'factura_id',
        'producto_id',
        'referencia',
        'descripcion',
        'color',
        'composicion',
        'codigo_pa',
        'cantidad',
        'precio_unitario',
        'descuento',
        'impuesto_porcentaje',
        'total_linea',
        'tallas_json',
        'orden',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto_porcentaje' => 'decimal:2',
        'total_linea' => 'decimal:2',
        'tallas_json' => 'array',
        'orden' => 'int',
    ];

    /**
     * @return BelongsTo<Factura, FacturaItem>
     */
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * @return BelongsTo<Producto, FacturaItem>
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
