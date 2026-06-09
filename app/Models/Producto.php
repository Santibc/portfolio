<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'referencia',
        'descripcion',
        'color',
        'composicion',
        'codigo_pa',
        'pais_origen',
        'precio_unitario',
        'unidad_medida',
        'imagen_path',
        'es_prenda',
        'activo',
        'siigo_id',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'es_prenda' => 'bool',
        'activo' => 'bool',
    ];

    /**
     * @return HasMany<ProductoTalla>
     */
    public function tallas(): HasMany
    {
        return $this->hasMany(ProductoTalla::class);
    }

    /**
     * @param  Builder<Producto>  $query
     * @return Builder<Producto>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * @param  Builder<Producto>  $query
     * @return Builder<Producto>
     */
    public function scopePrendas(Builder $query): Builder
    {
        return $query->where('es_prenda', true);
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (empty($this->imagen_path)) {
            return null;
        }

        return asset($this->imagen_path);
    }
}
