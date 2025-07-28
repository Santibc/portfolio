<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'referencia',
        'nombre',
        'descripcion',
        'unidad_venta',
        'unidad_empaque',
        'extension',
        'categoria_id',
        'activo',
        'tiene_variantes'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'tiene_variantes' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id')->orderBy('orden');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenProducto::class, 'producto_id')->where('es_principal', true);
    }

    public function precios()
    {
        return $this->hasMany(PrecioProducto::class, 'producto_id');
    }

    public function variantes()
    {
        return $this->hasMany(VarianteProducto::class, 'producto_id')->where('activo', true);
    }

    public function itemsSolicitudCotizacion()
    {
        return $this->hasMany(ItemSolicitudCotizacion::class, 'producto_id');
    }

    // Obtener precio por lista de precios
    public function getPrecioPorLista($listaPrecioId)
    {
        $precio = $this->precios()->where('lista_precio_id', $listaPrecioId)->where('activo', true)->first();
        return $precio ? $precio->precio : null;
    }

    // Obtener URL de imagen principal
    public function getUrlImagenPrincipalAttribute()
    {
        $imagenPrincipal = $this->imagenPrincipal ?? $this->imagenes->first();
        return $imagenPrincipal ? Storage::url($imagenPrincipal->ruta_imagen) : asset('images/no-image.png');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('referencia', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }
}
