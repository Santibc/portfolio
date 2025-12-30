<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAdicional extends Model
{
    use HasFactory;

    protected $table = 'productos_adicionales';

    protected $fillable = [
        'nombre',
        'categoria',
        'precio',
        'imagen',
        'descripcion',
        'disponible',
        'stock',
        'mostrar_en_checkout',
        'orden',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
        'mostrar_en_checkout' => 'boolean',
    ];

    const CATEGORIAS = [
        'chocolate' => 'Chocolates',
        'peluche' => 'Peluches',
        'globo' => 'Globos',
        'vino' => 'Vinos',
        'otro' => 'Otros',
    ];

    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    public function scopeParaCheckout($query)
    {
        return $query->where('mostrar_en_checkout', true)->where('disponible', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset($this->imagen);
        }
        return asset('images/adicionales/default.png');
    }

    public function getCategoriaNombreAttribute()
    {
        return self::CATEGORIAS[$this->categoria] ?? $this->categoria;
    }
}
