<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospecto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prospectos';

    protected $fillable = [
        'codigo_prospecto',
        'nombre',
        'email',
        'telefono',
        'tipo',
        'estado',
        'origen',
        'asignado_a',
        'fecha_contacto',
        'fecha_conversion',
        'usuario_convertido_id',
        'notas',
        'datos_adicionales'
    ];

    protected $casts = [
        'fecha_contacto' => 'date',
        'fecha_conversion' => 'date',
        'datos_adicionales' => 'array'
    ];

    // Relaciones
    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function usuarioConvertido()
    {
        return $this->belongsTo(User::class, 'usuario_convertido_id');
    }

    public function actividades()
    {
        return $this->hasMany(ActividadProspecto::class, 'prospecto_id');
    }

    public function scopeNuevos($query)
    {
        return $query->where('estado', 'nuevo');
    }

    public function scopeConvertidos($query)
    {
        return $query->where('estado', 'convertido');
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
}
