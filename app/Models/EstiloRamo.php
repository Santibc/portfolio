<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EstiloRamo extends Model
{
    use HasFactory;

    protected $table = 'estilos_ramo';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'precio_base',
        'flores_minimo',
        'flores_maximo',
        'icono',
        'color_principal',
        'activo',
        'orden',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Accessors
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset($this->imagen);
        }
        return asset('images/estilos/default.png');
    }

    // Boot method for auto-generating slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($estilo) {
            if (empty($estilo->slug)) {
                $estilo->slug = Str::slug($estilo->nombre);
            }
        });

        static::updating(function ($estilo) {
            if ($estilo->isDirty('nombre') && empty($estilo->slug)) {
                $estilo->slug = Str::slug($estilo->nombre);
            }
        });
    }

    // Relationships
    public function ramosPersonalizados()
    {
        return $this->hasMany(RamoPersonalizado::class, 'estilo_ramo_id');
    }
}
