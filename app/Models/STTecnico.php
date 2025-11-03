<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STTecnico extends Model
{
    use HasFactory;

    protected $table = 'st_tecnicos';

    protected $fillable = [
        'codigo',
        'nombre_completo',
        'documento',
        'email',
        'telefono',
        'celular',
        'especialidad',
        'certificaciones',
        'observaciones',
        'activo',
        'fecha_ingreso'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_ingreso' => 'date'
    ];

    // Relaciones
    public function ordenesServicio()
    {
        return $this->hasMany(STOrdenServicio::class, 'st_tecnico_id');
    }

    public function diagnosticos()
    {
        return $this->hasMany(STDiagnostico::class, 'st_tecnico_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEspecialidad($query, $especialidad)
    {
        return $query->where('especialidad', $especialidad);
    }

    // Accessors
    public function getOrdenesActivasCountAttribute()
    {
        return $this->ordenesServicio()
            ->whereIn('estado', ['asignada', 'en_proceso', 'pendiente_repuestos'])
            ->count();
    }
}
