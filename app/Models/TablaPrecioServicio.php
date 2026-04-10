<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablaPrecioServicio extends Model
{
    protected $table = 'tabla_precios_servicios';

    protected $fillable = [
        'tipo_servicio', 'etiqueta_servicio', 'clave_calibre', 'calibre_mm',
        'cantidad_servicios_min', 'cantidad_servicios_max',
        'largo_mm_min', 'largo_mm_max',
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

    public function scopeForCantidadServicios($query, int $min, ?int $max)
    {
        return $query->where('cantidad_servicios_min', $min)
            ->where('cantidad_servicios_max', $max);
    }

    public function scopeForLargoMm($query, int $min, ?int $max)
    {
        return $query->where('largo_mm_min', $min)
            ->where('largo_mm_max', $max);
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
     * Retorna rangos distintos de cantidad de servicios.
     */
    public static function getDistinctCantidadesServicios()
    {
        return static::selectRaw('DISTINCT cantidad_servicios_min, cantidad_servicios_max')
            ->orderBy('cantidad_servicios_min')
            ->get();
    }

    /**
     * Retorna rangos distintos de largo en mm.
     */
    public static function getDistinctLargosMm()
    {
        return static::selectRaw('DISTINCT largo_mm_min, largo_mm_max')
            ->orderBy('largo_mm_min')
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
     *
     * @param string $tipoServicio       Clave del servicio (ej: "corte_inox").
     * @param string $claveCalibre       Clave del calibre (ej: "#18").
     * @param int|float $largoMm         Largo de la pieza en mm.
     * @param int $cantidadServicios     Numero de servicios (piezas) a procesar.
     */
    public static function lookup(string $tipoServicio, string $claveCalibre, $largoMm, int $cantidadServicios)
    {
        return static::where('tipo_servicio', $tipoServicio)
            ->where('clave_calibre', $claveCalibre)
            ->where('largo_mm_min', '<=', $largoMm)
            ->where(fn($q) => $q->whereNull('largo_mm_max')->orWhere('largo_mm_max', '>=', $largoMm))
            ->where('cantidad_servicios_min', '<=', $cantidadServicios)
            ->where(fn($q) => $q->whereNull('cantidad_servicios_max')->orWhere('cantidad_servicios_max', '>=', $cantidadServicios))
            ->first();
    }
}
