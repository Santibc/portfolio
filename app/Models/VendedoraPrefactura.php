<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendedoraPrefactura extends Model
{
    use HasFactory;

    protected $table = 'vendedoras_prefactura';

    protected $fillable = [
        'nombre',
        'ubicacion_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    /**
     * Nombres de las vendedoras activas (para el select y la validación).
     * Sin argumento = todas (uso admin).
     */
    public static function nombresActivos(): array
    {
        return static::activas()->orderBy('nombre')->pluck('nombre')->all();
    }

    /**
     * Nombres de vendedoras activas visibles para una sede: SOLO las asignadas a
     * esa ubicación. Las que no tienen sede asignada NO aparecen en ninguna
     * tienda (deben asignarse a una sede para poder usarse). Si $ubicacionId es
     * null (p. ej. admin), devuelve todas las activas.
     */
    public static function nombresActivosPorUbicacion(?int $ubicacionId): array
    {
        if (!$ubicacionId) {
            return static::nombresActivos();
        }

        return static::activas()
            ->where('ubicacion_id', $ubicacionId)
            ->orderBy('nombre')
            ->pluck('nombre')
            ->all();
    }
}
