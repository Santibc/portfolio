<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeriaPromocion extends Model
{
    protected $table = 'feria_promociones';

    protected $fillable = [
        'feria_id',
        'producto_id',
        'variante_producto_id',
        'precio',
        'precio_referencia',
        'inicia_en',
        'termina_en',
        'activo',
    ];

    protected $casts = [
        'precio' => 'float',
        'precio_referencia' => 'float',
        'inicia_en' => 'datetime',
        'termina_en' => 'datetime',
        'activo' => 'boolean',
    ];

    public function feria()
    {
        return $this->belongsTo(Feria::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    /** Promos que están corriendo AHORA (activas, dentro de su ventana horaria). */
    public function scopeVigentes($query, $now = null)
    {
        $now = $now ?: now();
        return $query->where('activo', true)
            ->where('inicia_en', '<=', $now)
            ->where('termina_en', '>=', $now);
    }

    /**
     * Precio promo VIGENTE para un producto/variante de una feria en este momento; null si no hay.
     * Se usa en el POS para sobrescribir el precio normal de la feria durante la ventana.
     */
    public static function precioVigente(int $feriaId, int $productoId, ?int $varianteId, $now = null): ?float
    {
        $q = static::query()->vigentes($now)
            ->where('feria_id', $feriaId)
            ->where('producto_id', $productoId);

        if ($varianteId) {
            $q->where('variante_producto_id', $varianteId);
        } else {
            $q->whereNull('variante_producto_id');
        }

        $promo = $q->orderByDesc('id')->first();

        return $promo ? (float) $promo->precio : null;
    }

    /** Estado legible según la hora actual. */
    public function getEstadoAttribute(): string
    {
        if (!$this->activo) {
            return 'cancelada';
        }
        $now = now();
        if ($now->lt($this->inicia_en)) {
            return 'programada';
        }
        if ($now->gt($this->termina_en)) {
            return 'vencida';
        }
        return 'activa';
    }
}
