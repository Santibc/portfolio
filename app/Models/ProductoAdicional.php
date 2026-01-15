<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class ProductoAdicional extends Model
{
    use HasFactory;

    protected $table = 'productos_adicionales';

    protected $fillable = [
        'nombre',
        'categoria',
        'tipo_complementario',
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

    // Categorías de cross-selling (productos complementarios generales)
    const CATEGORIAS = [
        'bombones' => 'Bombones y Chocolates',
        'peluche' => 'Peluches',
        'globo' => 'Globos',
        'vino' => 'Vinos y Espumantes',
        'licor' => 'Licores',
        'florero' => 'Floreros', // Para complementarios específicos de ramos
        'preservante' => 'Preservantes', // Para complementarios específicos de ramos
        'otro' => 'Otros',
    ];

    // Tipos de complementarios
    const TIPO_CROSS_SELLING = 'cross_selling'; // Se muestra a todos los productos
    const TIPO_ESPECIFICO = 'especifico'; // Solo se muestra si está asignado al producto

    // === RELACIONES ===

    /**
     * Productos que tienen este complementario asignado
     */
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'productos_complementarios_sugeridos',
            'producto_adicional_id',
            'producto_id'
        )->withPivot('orden', 'activo')->withTimestamps();
    }

    // === SCOPES ===

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

    public function scopeCrossSelling($query)
    {
        return $query->where('tipo_complementario', self::TIPO_CROSS_SELLING);
    }

    public function scopeEspecificos($query)
    {
        return $query->where('tipo_complementario', self::TIPO_ESPECIFICO);
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
