<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EnvolturaRamo extends Model
{
    use HasFactory;

    protected $table = 'envolturas_ramo';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'precio_adicional',
        'icono',
        'activo',
        'orden',
    ];

    protected $casts = [
        'precio_adicional' => 'decimal:2',
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
        return asset('images/envolturas/default.png');
    }

    // Boot method for auto-generating slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($envoltura) {
            if (empty($envoltura->slug)) {
                $envoltura->slug = Str::slug($envoltura->nombre);
            }
        });

        static::updating(function ($envoltura) {
            if ($envoltura->isDirty('nombre') && empty($envoltura->slug)) {
                $envoltura->slug = Str::slug($envoltura->nombre);
            }
        });
    }

    // Relationships
    public function ramosPersonalizados()
    {
        return $this->hasMany(RamoPersonalizado::class, 'envoltura_ramo_id');
    }
}
