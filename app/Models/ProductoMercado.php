<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoMercado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos_mercado';

    protected $fillable = [
        'nombre',
        'unidad_empaque',
        'imagen',
        'tipo_id',
        'activo',
        'controla_inventario',
    ];

    protected $casts = [
        'activo'              => 'boolean',
        'controla_inventario' => 'boolean',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoProductoMercado::class, 'tipo_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_mercado_id');
    }

    /** Componentes de items de menú en los que este producto es una opción de descuento. */
    public function componentesMenu(): BelongsToMany
    {
        return $this->belongsToMany(
            MenuItemComponente::class,
            'menu_item_componente_opciones',
            'producto_mercado_id',
            'menu_item_componente_id'
        );
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) {
            return asset('uploads/productos-mercado/' . $this->imagen);
        }

        return '';
    }

    public function hasImagen(): bool
    {
        return !empty($this->imagen);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeConInventario(Builder $query): Builder
    {
        return $query->where('controla_inventario', true);
    }

    /** Cantidad sin decimales si es entera; con hasta 2 decimales (sin ceros de relleno) si no. */
    public static function formatearCantidad(float $cantidad): string
    {
        if (floor($cantidad) === $cantidad) {
            return number_format($cantidad, 0, ',', '.');
        }

        return rtrim(rtrim(number_format($cantidad, 2, ',', '.'), '0'), ',');
    }
}
