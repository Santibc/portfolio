<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'empresa_id',
        'items',
        'subtotal',
        'ultima_actividad'
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'ultima_actividad' => 'datetime'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function agregarItem($productoId, $cantidad, $varianteId = null, $precio = null)
    {
        $items = $this->items ?? [];
        $key = $varianteId ? "{$productoId}-{$varianteId}" : $productoId;
        
        if (isset($items[$key])) {
            $items[$key]['cantidad'] += $cantidad;
        } else {
            $producto = Producto::find($productoId);
            $items[$key] = [
                'producto_id' => $productoId,
                'variante_id' => $varianteId,
                'cantidad' => $cantidad,
                'precio' => $precio ?? $producto->precio,
                'nombre' => $producto->nombre,
                'referencia' => $producto->referencia
            ];
            
            if ($varianteId) {
                $variante = VarianteProducto::find($varianteId);
                $items[$key]['info_variante'] = [
                    'talla' => $variante->talla,
                    'color' => $variante->color,
                    'sku' => $variante->sku
                ];
            }
        }
        
        $this->items = $items;
        $this->calcularSubtotal();
        $this->ultima_actividad = now();
        $this->save();
    }

    public function quitarItem($key)
    {
        $items = $this->items ?? [];
        
        if (isset($items[$key])) {
            unset($items[$key]);
            $this->items = $items;
            $this->calcularSubtotal();
            $this->save();
        }
    }

    public function actualizarCantidad($key, $cantidad)
    {
        $items = $this->items ?? [];
        
        if (isset($items[$key]) && $cantidad > 0) {
            $items[$key]['cantidad'] = $cantidad;
            $this->items = $items;
            $this->calcularSubtotal();
            $this->save();
        }
    }

    public function vaciar()
    {
        $this->items = [];
        $this->subtotal = 0;
        $this->save();
    }

    public function calcularSubtotal()
    {
        $subtotal = 0;
        
        foreach ($this->items ?? [] as $item) {
            $subtotal += $item['cantidad'] * $item['precio'];
        }
        
        $this->subtotal = $subtotal;
        return $subtotal;
    }

    public function getTotalItemsAttribute()
    {
        return collect($this->items ?? [])->sum('cantidad');
    }

    public static function obtenerOCrear($sessionId, $empresaId)
    {
        return static::firstOrCreate(
            [
                'session_id' => $sessionId,
                'empresa_id' => $empresaId
            ],
            [
                'items' => [],
                'subtotal' => 0,
                'ultima_actividad' => now()
            ]
        );
    }

    public function scopeAbandonados($query, $horas = 24)
    {
        return $query->where('ultima_actividad', '<', now()->subHours($horas))
                    ->whereJsonLength('items', '>', 0);
    }

    protected static function boot()
    {
        parent::boot();

        // Limpiar carritos abandonados después de 30 días
        static::created(function () {
            static::where('ultima_actividad', '<', now()->subDays(30))->delete();
        });
    }
}