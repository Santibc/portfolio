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
     * Nombres de vendedoras activas visibles para una sede: las asignadas a esa
     * ubicación + las globales (sin sede asignada). Si $ubicacionId es null,
     * devuelve todas las activas.
     */
    public static function nombresActivosPorUbicacion(?int $ubicacionId): array
    {
        return static::activas()
            ->when($ubicacionId, function ($q) use ($ubicacionId) {
                $q->where(function ($w) use ($ubicacionId) {
                    $w->where('ubicacion_id', $ubicacionId)
                      ->orWhereNull('ubicacion_id');
                });
            })
            ->orderBy('nombre')
            ->pluck('nombre')
            ->all();
    }
}
