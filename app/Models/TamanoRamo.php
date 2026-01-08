<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TamanoRamo extends Model
{
    use HasFactory;

    protected $table = 'tamanos_ramo';

    protected $fillable = [
        'nombre',
        'cantidad_flores_min',
        'cantidad_flores_max',
        'precio_base',
        'imagen',
        'descripcion',
        'activo',
        'orden',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('cantidad_flores_min');
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        return asset('images/ramos/default.png');
    }

    // Relaciones
    public function ramosPersonalizados()
    {
        return $this->hasMany(RamoPersonalizado::class, 'tamano_ramo_id');
    }
}
