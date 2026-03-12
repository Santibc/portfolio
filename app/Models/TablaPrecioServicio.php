<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablaPrecioServicio extends Model
{
    protected $table = 'tabla_precios_servicios';

    protected $fillable = [
        'tipo_servicio', 'etiqueta_servicio', 'clave_calibre', 'calibre_mm',
        'largo_rango_min', 'largo_rango_max', 'cantidad_rango_min', 'cantidad_rango_max',
        'precio', 'precio_minimo',
    ];

    protected $casts = [
        'calibre_mm' => 'decimal:2',
        'precio' => 'decimal:2',
        'precio_minimo' => 'decimal:2',
    ];

    // ─── Scopes ─────────────────────────────────────────

    public function scopeForServicio($query, string $tipoServicio)
    {
        return $query->where('tipo_servicio', $tipoServicio);
    }

    public function scopeForLargoRange($query, int $min, ?int $max)
    {
        return $query->where('largo_rango_min', $min)
            ->where('largo_rango_max', $max);
    }

    // ─── Static Helpers ─────────────────────────────────

    /**
     * Retorna tipos de servicio distintos con etiqueta y precio_minimo.
     */
    public static function getDistinctServicios()
    {
        return static::selectRaw('tipo_servicio, etiqueta_servicio, MIN(precio_minimo) as precio_minimo')
            ->groupBy('tipo_servicio', 'etiqueta_servicio')
            ->orderBy('etiqueta_servicio')
            ->get();
    }

    /**
     * Retorna rangos de largo distintos.
     */
    public static function getDistinctLargoRangos()
    {
        return static::selectRaw('DISTINCT largo_rango_min, largo_rango_max')
            ->orderBy('largo_rango_min')
            ->get();
    }

    /**
     * Retorna rangos de cantidad distintos.
     */
    public static function getDistinctCantidadRangos()
    {
        return static::selectRaw('DISTINCT cantidad_rango_min, cantidad_rango_max')
            ->orderBy('cantidad_rango_min')
            ->get();
    }

    /**
     * Retorna calibres distintos ordenados por mm.
     */
    public static function getDistinctCalibres()
    {
        return static::selectRaw('DISTINCT clave_calibre, calibre_mm')
            ->orderBy('calibre_mm')
            ->get();
    }

    /**
     * Consulta de precio: busca el registro que coincida con los parametros dados.
     */
    public static function lookup(string $tipoServicio, string $claveCalibe, $largo, $cantidad)
    {
        return static::where('tipo_servicio', $tipoServicio)
            ->where('clave_calibre', $claveCalibe)
            ->where('largo_rango_min', '<=', $largo)
            ->where(fn($q) => $q->whereNull('largo_rango_max')->orWhere('largo_rango_max', '>=', $largo))
            ->where('cantidad_rango_min', '<=', $cantidad)
            ->where(fn($q) => $q->whereNull('cantidad_rango_max')->orWhere('cantidad_rango_max', '>=', $cantidad))
            ->first();
    }
}
