<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoCrossFund extends Model
{
    use HasFactory;

    protected $table = 'proyectos_cross_fund';

    protected $fillable = [
        'paquete_id',
        'proyecto_id',
        'porcentaje_asignacion',
        'monto_asignado',
        'orden'
    ];

    protected $casts = [
        'porcentaje_asignacion' => 'decimal:2',
        'monto_asignado' => 'decimal:2',
        'orden' => 'integer'
    ];

    // Relaciones
    public function paquete()
    {
        return $this->belongsTo(PaqueteCrossFund::class, 'paquete_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }
}
