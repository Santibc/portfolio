<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table = 'tipos_pago';

    protected $fillable = [
        'codigo', 'nombre', 'icono', 'color', 'orden', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden')->orderBy('id');
    }

    /**
     * Coleccion de tipos activos para selects (cacheada por request).
     * @return \Illuminate\Support\Collection
     */
    public static function opciones()
    {
        static $cache = null;
        if ($cache === null) {
            $cache = static::activos()->get(['id', 'codigo', 'nombre', 'icono', 'color']);
        }
        return $cache;
    }

    /**
     * Mapa completo (incluye inactivos) para renderizar badges de pagos historicos.
     * Estructura: [codigo => ['color', 'icono', 'nombre']]
     * @return array
     */
    public static function mapaBadges()
    {
        static $cache = null;
        if ($cache === null) {
            $cache = static::orderBy('orden')->get()->mapWithKeys(function ($t) {
                return [$t->codigo => [
                    'color' => $t->color,
                    'icono' => $t->icono,
                    'nombre' => $t->nombre,
                ]];
            })->toArray();
        }
        return $cache;
    }
}
