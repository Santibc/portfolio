<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlorDisponible extends Model
{
    use HasFactory;

    protected $table = 'flores_disponibles';

    protected $fillable = [
        'nombre',
        'color',
        'precio_unidad',
        'cantidad_minima',
        'cantidad_maxima',
        'imagen',
        'descripcion',
        'disponible',
        'stock',
        'orden',
    ];

    protected $casts = [
        'precio_unidad' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true)->where('stock', '>', 0);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset($this->imagen);
        }
        return asset('images/flores/default.png');
    }
}
