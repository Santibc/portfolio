<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaContrato extends Model
{
    use HasFactory;

    protected $table = 'plantillas_contrato';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_contrato',
        'version',
        'contenido',
        'variables_requeridas',
        'activo',
        'fecha_vigencia',
        'fecha_expiracion',
    ];

    protected $casts = [
        'variables_requeridas' => 'array',
        'activo' => 'boolean',
        'fecha_vigencia' => 'date',
        'fecha_expiracion' => 'date',
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaProyecto::class, 'categoria_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function inversiones()
    {
        return $this->hasMany(Inversion::class, 'contrato_id');
    }

    public function aceptaciones()
    {
        return $this->hasMany(AceptacionContrato::class, 'plantilla_contrato_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
