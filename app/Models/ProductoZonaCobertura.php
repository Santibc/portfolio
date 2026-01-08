<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoZonaCobertura extends Model
{
    use HasFactory;

    protected $table = 'productos_zonas_cobertura';

    protected $fillable = [
        'producto_id',
        'zona_cobertura_id',
        'stock_local',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'stock_local' => 'integer',
    ];

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con ZonaCobertura
     */
    public function zonaCobertura()
    {
        return $this->belongsTo(ZonaCobertura::class);
    }

    /**
     * Scope para filtrar solo activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por producto
     */
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope para filtrar por zona
     */
    public function scopePorZona($query, $zonaId)
    {
        return $query->where('zona_cobertura_id', $zonaId);
    }
}
