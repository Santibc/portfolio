<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STDiagnostico extends Model
{
    use HasFactory;

    protected $table = 'st_diagnosticos';

    protected $fillable = [
        'st_orden_servicio_id',
        'st_tecnico_id',
        'diagnostico_tecnico',
        'fallas_encontradas',
        'reparaciones_realizadas',
        'recomendaciones',
        'requiere_repuestos',
        'repuestos_necesarios',
        'tiempo_estimado_horas',
        'costo_estimado',
        'aprobado_por_cliente',
        'fecha_diagnostico'
    ];

    protected $casts = [
        'requiere_repuestos' => 'boolean',
        'aprobado_por_cliente' => 'boolean',
        'fecha_diagnostico' => 'date',
        'tiempo_estimado_horas' => 'decimal:2',
        'costo_estimado' => 'decimal:2'
    ];

    // Relaciones
    public function ordenServicio()
    {
        return $this->belongsTo(STOrdenServicio::class, 'st_orden_servicio_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(STTecnico::class, 'st_tecnico_id');
    }
}
