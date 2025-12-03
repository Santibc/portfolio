<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'codigo_reporte',
        'generado_por',
        'tipo_reporte',
        'nombre',
        'descripcion',
        'filtros',
        'columnas',
        'fecha_inicio',
        'fecha_fin',
        'formato',
        'ruta_archivo',
        'estado',
        'mensaje_error',
        'generado_at',
        'expira_at'
    ];

    protected $casts = [
        'filtros' => 'array',
        'columnas' => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'generado_at' => 'datetime',
        'expira_at' => 'datetime'
    ];

    // Relaciones
    public function generadoPor()
    {
        return $this->belongsTo(User::class, 'generado_por');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'generando');
    }

    public function scopeConError($query)
    {
        return $query->where('estado', 'error');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_reporte', $tipo);
    }
}
