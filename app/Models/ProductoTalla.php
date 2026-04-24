<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoTalla extends Model
{
    use HasFactory;

    protected $table = 'producto_tallas';

    protected $fillable = [
        'producto_id',
        'talla',
        'stock',
        'orden',
    ];

    protected $casts = [
        'stock' => 'int',
        'orden' => 'int',
    ];

    /**
     * @return BelongsTo<Producto, ProductoTalla>
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
