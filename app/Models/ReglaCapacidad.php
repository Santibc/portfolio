<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReglaCapacidad extends Model
{
    use HasFactory;

    protected $table = 'reglas_capacidad';

    protected $fillable = [
        'empresa_id',
        'fecha',
        'capacidad_total_dia',
        'capacidad_por_hora',
        'notas',
        'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'capacidad_total_dia' => 'integer',
        'capacidad_por_hora' => 'integer',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Métodos de verificación
    public function capacidadDisponible($fecha)
    {
        $pedidosDelDia = Compra::where('fecha_entrega_deseada', $fecha)
            ->whereHas('empresa', function($q) {
                $q->where('id', $this->empresa_id);
            })
            ->whereIn('estado', ['pendiente', 'procesando', 'pagada', 'enviada'])
            ->count();

        return max(0, $this->capacidad_total_dia - $pedidosDelDia);
    }

    public function tieneCapacidad($fecha)
    {
        return $this->capacidadDisponible($fecha) > 0;
    }
}
