<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HorarioEntrega extends Model
{
    use HasFactory;

    protected $table = 'horarios_entrega';

    protected $fillable = [
        'zona_cobertura_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'nombre',
        'capacidad_pedidos',
        'activo',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'capacidad_pedidos' => 'integer',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function zona()
    {
        return $this->belongsTo(ZonaCobertura::class, 'zona_cobertura_id');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    // Métodos de verificación
    public function estaDisponible($fecha, $hora = null)
    {
        if (!$this->activo) {
            return false;
        }

        $diaSemana = strtolower(Carbon::parse($fecha)->locale('es')->dayName);

        if ($this->dia_semana !== $diaSemana) {
            return false;
        }

        if ($hora) {
            $horaEntrega = Carbon::parse($hora);
            $horaInicio = Carbon::parse($this->hora_inicio);
            $horaFin = Carbon::parse($this->hora_fin);

            return $horaEntrega->between($horaInicio, $horaFin);
        }

        return true;
    }

    public function tieneCapacidad($fecha)
    {
        // Contar pedidos ya programados para esta fecha y horario
        $pedidosExistentes = Compra::where('fecha_entrega_deseada', $fecha)
            ->where('horario_entrega', $this->nombre)
            ->whereIn('estado', ['pendiente', 'procesando', 'pagada', 'enviada'])
            ->count();

        return $pedidosExistentes < $this->capacidad_pedidos;
    }
}
