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
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Nombres de las vendedoras activas (para el select y la validación).
     */
    public static function nombresActivos(): array
    {
        return static::activas()->orderBy('nombre')->pluck('nombre')->all();
    }
}
