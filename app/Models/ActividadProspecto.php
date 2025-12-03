<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadProspecto extends Model
{
    use HasFactory;

    protected $table = 'actividades_prospecto';

    protected $fillable = [
        'prospecto_id',
        'usuario_id',
        'tipo_actividad',
        'asunto',
        'descripcion',
        'fecha_actividad',
        'hora_actividad',
        'resultado',
        'fecha_seguimiento'
    ];

    protected $casts = [
        'fecha_actividad' => 'date',
        'fecha_seguimiento' => 'date'
    ];

    // Relaciones
    public function prospecto()
    {
        return $this->belongsTo(Prospecto::class, 'prospecto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_actividad', $tipo);
    }

    public function scopePendientes($query)
    {
        return $query->where('resultado', 'pendiente');
    }
}
