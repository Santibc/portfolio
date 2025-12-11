<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalificacionProducto extends Model
{
    use HasFactory;

    protected $table = 'calificaciones_productos';

    protected $fillable = [
        'producto_id',
        'user_id',
        'compra_id',
        'item_compra_id',
        'estrellas',
        'titulo',
        'comentario',
        'verificada',
        'aprobada',
    ];

    protected $casts = [
        'estrellas' => 'integer',
        'verificada' => 'boolean',
        'aprobada' => 'boolean',
    ];

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con Usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Compra
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Relación con ItemCompra
     */
    public function itemCompra()
    {
        return $this->belongsTo(ItemCompra::class, 'item_compra_id');
    }

    /**
     * Scope para calificaciones aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }

    /**
     * Scope para calificaciones por producto
     */
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope para calificaciones por usuario
     */
    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para calificaciones verificadas
     */
    public function scopeVerificadas($query)
    {
        return $query->where('verificada', true);
    }

    /**
     * Obtener promedio de estrellas para un producto
     */
    public static function getPromedioEstrellas($productoId)
    {
        return self::porProducto($productoId)
            ->aprobadas()
            ->avg('estrellas') ?? 0;
    }

    /**
     * Obtener distribución de calificaciones para un producto
     */
    public static function getDistribucion($productoId)
    {
        $distribucion = [];

        for ($i = 5; $i >= 1; $i--) {
            $count = self::porProducto($productoId)
                ->aprobadas()
                ->where('estrellas', $i)
                ->count();
            $distribucion[$i] = $count;
        }

        return $distribucion;
    }

    /**
     * Obtener total de calificaciones para un producto
     */
    public static function getTotalCalificaciones($productoId)
    {
        return self::porProducto($productoId)->aprobadas()->count();
    }
}
