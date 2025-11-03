<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STRepuesto extends Model
{
    use HasFactory;

    protected $table = 'st_repuestos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'marca',
        'modelo_compatible',
        'precio_costo',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'ubicacion_bodega',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer'
    ];

    // Relaciones
    public function repuestosUsados()
    {
        return $this->hasMany(STRepuestoUsado::class, 'st_repuesto_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo');
    }

    public function scopeSinStock($query)
    {
        return $query->where('stock_actual', '<=', 0);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Accessors
    public function getStockBajoAttribute()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function getSinStockAttribute()
    {
        return $this->stock_actual <= 0;
    }

    // Métodos auxiliares
    public function ajustarStock($cantidad, $tipo = 'salida')
    {
        if ($tipo === 'entrada') {
            $this->stock_actual += $cantidad;
        } else {
            $this->stock_actual -= $cantidad;
        }

        $this->save();
    }
}
