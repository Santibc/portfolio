<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandingEvento extends Model
{
    use HasFactory;

    protected $table = 'landing_eventos';

    protected $fillable = [
        'slug',
        'titulo',
        'extracto',
        'contenido',
        'imagen',
        'fecha',
        'hora',
        'ubicacion',
        'precio',
        'estado',
        'incluye',
        'galeria',
        'orden',
        'activo',
    ];

    protected $casts = [
        'incluye' => 'array',
        'galeria' => 'array',
        'activo' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($evento) {
            if (empty($evento->slug)) {
                $evento->slug = Str::slug($evento->titulo);
            }
        });

        static::updating(function ($evento) {
            if ($evento->isDirty('titulo') && empty($evento->slug)) {
                $evento->slug = Str::slug($evento->titulo);
            }
        });
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    public function scopeProximos($query)
    {
        return $query->where('estado', 'proximo');
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Accessor para obtener el texto del estado
     */
    public function getEstadoTextoAttribute()
    {
        return match($this->estado) {
            'proximo' => 'Próximo',
            'activo' => 'Activo',
            'finalizado' => 'Finalizado',
            default => ucfirst($this->estado),
        };
    }
}
