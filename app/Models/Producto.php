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
        'marca',
        'unidad_venta',
        'unidad_empaque',
        'extension',
        'categoria_id',
        'activo',
        'tiene_variantes',
        'controlar_stock',
        'permitir_venta_sin_stock',
        'eliminado'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'tiene_variantes' => 'boolean',
        'controlar_stock' => 'boolean',
        'permitir_venta_sin_stock' => 'boolean',
        'eliminado' => 'boolean',
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
        return $this->hasOne(ImagenProducto::class, 'producto_id')
                    ->whereNull('variante_producto_id')
                    ->where('es_principal', true);
    }

    public function todasImagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id')->orderBy('orden');
    }

    public function getMejorImagenAttribute()
    {
        // 1) Product-level principal image
        if ($this->relationLoaded('imagenPrincipal') && $this->imagenPrincipal) {
            return $this->imagenPrincipal;
        }

        $imgs = $this->relationLoaded('todasImagenes')
            ? $this->todasImagenes
            : ($this->relationLoaded('imagenes') ? $this->imagenes : null);

        if ($imgs && $imgs->count()) {
            $productLevel = $imgs->whereNull('variante_producto_id');
            $principal = $productLevel->where('es_principal', true)->first();
            if ($principal) return $principal;
            if ($productLevel->count()) return $productLevel->first();

            // 2) Any variant's principal image
            $variantPrincipal = $imgs->where('es_principal', true)->first();
            if ($variantPrincipal) return $variantPrincipal;

            // 3) First image of any kind
            return $imgs->first();
        }

        // Fallback: query DB
        return ImagenProducto::where('producto_id', $this->id)
            ->orderByRaw('variante_producto_id IS NOT NULL, es_principal DESC, orden ASC')
            ->first();
    }

    public function precios()
    {
        return $this->hasMany(PrecioProducto::class, 'producto_id');
    }

    public function variantes()
    {
        return $this->hasMany(VarianteProducto::class, 'producto_id')->where('activo', true);
    }

    public function stock()
    {
        return $this->hasMany(StockProducto::class, 'producto_id');
    }

    // Stock del producto sin variantes
    public function stockPrincipal()
    {
        return $this->hasOne(StockProducto::class, 'producto_id')->whereNull('variante_producto_id');
    }

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'producto_id');
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
        $imagen = $this->mejor_imagen;
        return $imagen ? asset($imagen->ruta_imagen) : asset('images/no-image.png');
    }

    // Obtener stock total del producto (suma de todas las variantes o stock principal)
    public function getStockTotalAttribute()
    {
        if ($this->tiene_variantes) {
            return $this->stock()->sum('cantidad_disponible');
        } else {
            $stockPrincipal = $this->stockPrincipal;
            return $stockPrincipal ? $stockPrincipal->cantidad_disponible : 0;
        }
    }

    // Obtener stock disponible (considerando reservas)
    public function getStockDisponibleAttribute()
    {
        if ($this->tiene_variantes) {
            return $this->stock()->selectRaw('SUM(cantidad_disponible - cantidad_reservada) as total')->value('total') ?? 0;
        } else {
            $stockPrincipal = $this->stockPrincipal;
            return $stockPrincipal ? $stockPrincipal->stock_real : 0;
        }
    }

    // Verificar si hay stock bajo
    public function getTieneStockBajoAttribute()
    {
        return $this->stock()->where('alerta_stock_bajo', true)
                              ->whereRaw('(cantidad_disponible - cantidad_reservada) <= stock_minimo')
                              ->exists();
    }

    // Verificar si hay stock disponible
    public function hayStock($cantidad = 1, $varianteId = null)
    {
        if (!$this->controlar_stock || $this->permitir_venta_sin_stock) {
            return true;
        }

        if ($varianteId) {
            $stock = $this->stock()->where('variante_producto_id', $varianteId)->first();
        } else {
            $stock = $this->stockPrincipal;
        }

        return $stock && $stock->hayDisponibilidad($cantidad);
    }

    // Inicializar stock si no existe
    public function inicializarStock()
    {
        if ($this->tiene_variantes) {
            foreach ($this->variantes as $variante) {
                StockProducto::firstOrCreate(
                    [
                        'producto_id' => $this->id,
                        'variante_producto_id' => $variante->id
                    ],
                    [
                        'cantidad_disponible' => 0,
                        'cantidad_reservada' => 0,
                        'stock_minimo' => 0,
                        'alerta_stock_bajo' => true
                    ]
                );
            }
        } else {
            StockProducto::firstOrCreate(
                [
                    'producto_id' => $this->id,
                    'variante_producto_id' => null
                ],
                [
                    'cantidad_disponible' => 0,
                    'cantidad_reservada' => 0,
                    'stock_minimo' => 0,
                    'alerta_stock_bajo' => true
                ]
            );
        }
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->where('eliminado', false);
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

    public function scopeConStock($query)
    {
        return $query->whereHas('stock', function($q) {
            $q->whereRaw('(cantidad_disponible - cantidad_reservada) > 0');
        });
    }

    public function scopeSinStock($query)
    {
        return $query->whereHas('stock', function($q) {
            $q->whereRaw('(cantidad_disponible - cantidad_reservada) <= 0');
        })->orWhereDoesntHave('stock');
    }

    public function scopeConStockBajo($query)
    {
        return $query->whereHas('stock', function($q) {
            $q->where('alerta_stock_bajo', true)
              ->whereRaw('(cantidad_disponible - cantidad_reservada) <= stock_minimo');
        });
    }
}