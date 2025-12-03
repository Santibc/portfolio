<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteCrossFund extends Model
{
    use HasFactory;

    protected $table = 'paquetes_cross_fund';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'monto_minimo',
        'monto_maximo',
        'roi_estimado',
        'duracion_dias',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'activo'
    ];

    protected $casts = [
        'monto_minimo' => 'decimal:2',
        'monto_maximo' => 'decimal:2',
        'roi_estimado' => 'decimal:2',
        'duracion_dias' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function proyectos()
    {
        return $this->hasMany(ProyectoCrossFund::class, 'paquete_id');
    }

    public function compras()
    {
        return $this->hasMany(CompraCrossFund::class, 'paquete_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible')->where('activo', true);
    }
}
