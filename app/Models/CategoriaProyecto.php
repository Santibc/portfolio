<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProyecto extends Model
{
    use HasFactory;

    protected $table = 'categorias_proyecto';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'roi_minimo',
        'roi_maximo',
        'duracion_minima_meses',
        'duracion_maxima_meses',
        'inversion_minima',
        'inversion_maxima',
        'permite_retiro_anticipado',
        'permite_trading',
        'activo',
        'orden'
    ];

    protected $casts = [
        'roi_minimo' => 'decimal:2',
        'roi_maximo' => 'decimal:2',
        'duracion_minima_meses' => 'integer',
        'duracion_maxima_meses' => 'integer',
        'inversion_minima' => 'decimal:2',
        'inversion_maxima' => 'decimal:2',
        'permite_retiro_anticipado' => 'boolean',
        'permite_trading' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    // Relaciones
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'categoria_id');
    }

    public function reglasPenalizacion()
    {
        return $this->hasMany(ReglaPenalizacion::class, 'categoria_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorOrden($query)
    {
        return $query->orderBy('orden');
    }
}
