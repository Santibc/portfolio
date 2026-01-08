<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonaCobertura extends Model
{
    use HasFactory;

    protected $table = 'zonas_cobertura';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
        'ciudades_ids',
        'barrios',
        'codigo_postal_desde',
        'codigo_postal_hasta',
        'activo',
        'orden',
    ];

    protected $casts = [
        'ciudades_ids' => 'array',
        'barrios' => 'array',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tarifas()
    {
        return $this->hasMany(TarifaZona::class);
    }

    public function horarios()
    {
        return $this->hasMany(HorarioEntrega::class);
    }

    public function ciudades()
    {
        return Ciudad::whereIn('id', $this->ciudades_ids ?? [])->get();
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Relación con productos disponibles en esta zona
     */
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'productos_zonas_cobertura',
            'zona_cobertura_id',
            'producto_id'
        )
        ->withPivot('stock_local', 'activo')
        ->withTimestamps()
        ->wherePivot('activo', true);
    }

    // Métodos de verificación
    public function cubreCiudad($ciudadId)
    {
        return in_array($ciudadId, $this->ciudades_ids ?? []);
    }

    /**
     * Obtener total de productos disponibles en esta zona
     */
    public function getTotalProductosAttribute()
    {
        return $this->productos()->count();
    }
}
