<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Parte de un item de menú que descuenta inventario. Con una sola opción el descuento es fijo;
 * con varias, el cajero elige cuál al vender (ej. "Sabor de Doritos": normales / picantes).
 */
class MenuItemComponente extends Model
{
    protected $table = 'menu_item_componentes';

    protected $fillable = [
        'menu_item_id',
        'nombre',
        'cantidad',
        'orden',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'orden'    => 'integer',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function opciones(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductoMercado::class,
            'menu_item_componente_opciones',
            'menu_item_componente_id',
            'producto_mercado_id'
        )->withTimestamps()->orderBy('nombre');
    }

    public function esElegible(): bool
    {
        return $this->opciones->count() > 1;
    }
}
