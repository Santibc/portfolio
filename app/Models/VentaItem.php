<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaItem extends Model
{
    protected $table = 'venta_items';

    protected $fillable = [
        'venta_id',
        'menu_item_id',
        'nombre_snapshot',
        'precio_unitario',
        'cantidad',
        'subtotal',
    ];

    protected $casts = [
        'precio_unitario' => 'integer',
        'cantidad'        => 'integer',
        'subtotal'        => 'integer',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getSubtotalFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->subtotal, 0, ',', '.');
    }

    public function getPrecioUnitarioFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->precio_unitario, 0, ',', '.');
    }
}
