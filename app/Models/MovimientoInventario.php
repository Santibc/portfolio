<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoMovimientoInventario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_mercado_id',
        'tipo',
        'cantidad',
        'registro_mercado_id',
        'venta_item_id',
        'user_id',
        'motivo',
    ];

    protected $casts = [
        'tipo'     => TipoMovimientoInventario::class,
        'cantidad' => 'decimal:2',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(ProductoMercado::class, 'producto_mercado_id')->withTrashed();
    }

    public function registroMercado(): BelongsTo
    {
        return $this->belongsTo(RegistroMercado::class, 'registro_mercado_id');
    }

    public function ventaItem(): BelongsTo
    {
        return $this->belongsTo(VentaItem::class, 'venta_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCantidadFormateadaAttribute(): string
    {
        $cantidad = (float) $this->cantidad;
        $signo    = $cantidad > 0 ? '+' : '';

        return $signo.ProductoMercado::formatearCantidad($cantidad);
    }
}
